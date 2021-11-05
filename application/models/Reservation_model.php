<?php
class Reservation_model extends CI_Model{

// General
//-----------------------------------------------------------------------------

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de usuarios
     * 2020-12-12
     */
    function select($format = 'reservations')
    {
        $arr_select['reservations'] = 'events.id, events.title, events.status, period_id AS day_id, start, end, element_id AS training_id, user_id, related_2 AS room_id, users.display_name AS user_display_name, users.url_thumbnail AS user_thumbnail, events.created_at';

        return $arr_select[$format];
    }

// Datos para construcción de reservación
//-----------------------------------------------------------------------------

    /**
     * Query tabla periods, con días en los cuales se puede schedule una reservación
     * 2021-07-23
     */
    function training_days($user_id)
    {
        $today_id = date('Ymd');

        $this->db->select('id, period_name, start');
        $this->db->where('id >=', $today_id);
        $this->db->where('type_id', 9); //Periodo tipo día
        $this->db->where('id IN (SELECT period_id FROM events WHERE type_id = 203)');   //Día en los que haya trainings
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('periods', 4);

        $periods = array();
        foreach ( $query->result() as $day ) {
            $day->qty_user_reservations = $this->Db_model->num_rows('events', "type_id = 213 AND user_id = {$user_id} AND period_id = {$day->id}");
            $periods[] = $day;
        }

        return $periods;
    }

    /**
     * Zonas de entrenamiento, especificando disponibilidad para ser reservadas
     * por un usuario en una fecha determinada.
     * 2021-08-04
     */
    function available_rooms($day_id, $user_id)
    {
        $query = $this->App_model->rooms();

        $rooms = array();
        foreach ($query->result() as $room) {
            $room->available = $this->available_room($day_id, $room->room_id, $user_id);
            $rooms[] = $room;
        }

        return $rooms;
    }

    /**
     * Boolean, zona de entrenamiento disponible o no para un usuario en una fecha
     * 2021-08-05
     */
    function available_room($day_id, $room_id, $user_id)
    {
        $available = 1;

        $qty_reservations = $this->qty_user_week_reservations($day_id, $room_id, $user_id);
        //Si ya existen reservas, la zona no está disponible
        if ( $qty_reservations > 0 ) $available = 0;

        $qty_room_trainings = $this->qty_room_trainings($day_id, $room_id);
        //Si no hay entrenamientos programados, la zona no está disponible
        if ( $qty_room_trainings == 0 ) $available = 0;

        return $available;
    }

    /**
     * Cantidad de reservaciones que tiene un usuario para una zona, en una semana
     * 2021-08-17
     */
    function qty_user_week_reservations($day_id, $room_id, $user_id)
    {
        $day = $this->Db_model->row_id('periods', $day_id);

        //Buscar otras reservas de la misma zona, en la misma semana, sin que sea sábado
        $condition = "type_id = 213";                   //Evento tipo reserva
        $condition .= " AND user_id = {$user_id} ";     //Reserva del mismo usuario
        $condition .= " AND related_2 = {$room_id} ";   //Reserva en la misma zona
        //En la misma semana (period_id => day_id) y no sea sábado (week_day 6):
        $condition .= " AND period_id IN (SELECT id FROM periods WHERE week_number = {$day->week_number} AND week_day <> 6)";

        $qty_reservations = $this->Db_model->num_rows('events', $condition);

        return $qty_reservations;
    }

    /**
     * Cantidad de entrenamientos programados en un día específico, para una zona de entrenamiento
     * 2021-08-17
     */
    function qty_room_trainings($day_id, $room_id) {
        $condition = "type_id = 203 AND period_id = {$day_id} AND element_id = {$room_id}";
        $qty_room_trainings = $this->Db_model->num_rows('events', $condition);

        return $qty_room_trainings;
    }

// CRUD reservas
//-----------------------------------------------------------------------------

    /**
     * Información sobre una reservación
     * 2021-08-13
     */
    function info($reservation_id, $user_id)
    {
        $reservation = ['id' => '0'];

        $this->db->select($this->select());
        $this->db->join('users', 'events.user_id = users.id', 'left');
        $this->db->where('events.id', $reservation_id);
        $this->db->where('user_id', $user_id);
        $events = $this->db->get('events');

        if ( $events->num_rows() ) {
            $reservation = $events->row();
        }

        return $reservation;
    }

    /**
     * Guardar una reserva de cupo en una sesión de entrenamiento por parte de un usuario
     * 2021-08-04
     */
    function save($training_id, $user_id)
    {
        //Resultado por defecto
        $data = array('saved_id' => 0, 'error' => '');

        //Variables referencua
        $training = $this->Db_model->row_id('events', $training_id);
        $user = $this->Db_model->row_id('users', $user_id);

        //Verificación de entrenamiento y cupos
        if ( is_null($training) ) {
            $data['error'] = 'La sesión de entrenamiento no existe: ' . $training_id;
        } else {
            if ( $training->integer_2 <= 0 ) $data['error'] = 'No hay cupos disponibles en este entrenamiento';
        }

        //Verificación de usuario
        if ( is_null($user->expiration_at) ) {
            $data['error'] = 'El usuario no tiene suscripción activa';
        } else {
            //Verifcar disponibilidad de zona para usuario
            $available_room = $this->available_room($training->period_id, $training->element_id, $user->id);
            if ( $available_room == 0 ) {
                $data['error'] = 'Zona no habilitada para el usuario en esta semana';
            }
            //Verificar suscripción vigente
            if ( $training->start > $user->expiration_at . ' 23:59:59' ) {
                $data['error'] = 'La suscripción del usuario está vencida: ' . $user->expiration_at;
            }
        }

        //Si no hay errores, se guarda
        if ( strlen($data['error']) == 0 )
        {
            $arr_row = $this->arr_row($training, $user);
            $condition = "type_id = 213 AND element_id = {$arr_row['element_id']} AND user_id = {$arr_row['user_id']}";

            $reservation_id = $this->Db_model->insert_if('events', $condition, $arr_row);

            if ( $reservation_id > 0 ) {
                //Actualizar número de cupos disponibles
                $this->load->model('Training_model');
                $this->Training_model->update_spots($training_id);

                $data['saved_id'] = $reservation_id;
            }
        }

        return $data;
    }

    /**
     * Array para guardar reservation en tabla events
     * 2021-08-04
     */
    function arr_row($training, $user)
    {
        $arr_row['title'] = $this->Item_model->name(520, $training->element_id);  //Nombre zona de entrenamiento
        $arr_row['type_id'] = 213;  //Reseva entrenamiento presencial
        $arr_row['period_id'] = $training->period_id;     //ID día de sesión
        $arr_row['start'] = $training->start;
        $arr_row['end'] = $training->end;
        $arr_row['status'] = 0;     //Reservado
        $arr_row['element_id'] = $training->id;    //ID Evento sesión de entrenamiento
        $arr_row['user_id'] = $user->id;         //Usuario para el que se reserva la sesión
        $arr_row['related_2'] = $training->element_id;    //Cód zona de entrenamiento
        $arr_row['created_at'] = date('Y-m-d H:i:s');
        $arr_row['creator_id'] = $user->id;

        return $arr_row;
    }

    /**
     * Elimina una reserva de cupo de entrenamiento, tabla events.
     * 2021-07-01
     */
    function delete($reservation_id, $training_id)
    {
        $data = array('qty_deleted' => 0, 'error' => '');

        $reservation = $this->Db_model->row('events', "id = {$reservation_id} AND element_id = {$training_id}");

        //Si existe reserva
        if ( is_null($reservation) )
        {
            $data['error'] = 'No existe reserva con ID: ' . $reservation_id;
        }

        //Si no hay error, eliminar
        if ( strlen($data['error']) == 0 )
        {
            $this->db->where('id', $reservation_id);
            $this->db->where('element_id', $training_id);
            $this->db->delete('events');
            
            $data['qty_deleted'] = $this->db->affected_rows();
    
            //Actualizar número de cupos disponibles
            if ( $data['qty_deleted'] > 0 ) $this->Training_model->update_spots($training_id);
        }

        return $data;
    }

    /**
     * Cancela una reserva de cupo de entrenamiento, la elimina de la tabla events.
     * 2021-07-01
     */
    function cancel($reservation_id, $training_id)
    {
        $data = array('qty_deleted' => 0, 'error' => '');

        $reservation = $this->Db_model->row('events', "id = {$reservation_id} AND element_id = {$training_id}");

        //Si existe reserva
        if ( is_null($reservation) )
        {
            $data['error'] = 'No existe reserva con ID: ' . $reservation_id;
        } else {
            if ( $reservation->start < date('Y-m-d H:i:s') ) {
                $data['error'] = 'No se puede cancelar una reservación con fecha y hora pasada';
            }
        }

        //Si no hay error, eliminar
        if ( strlen($data['error']) == 0 )
        {
            $data = $this->delete($reservation_id, $training_id);
        }

        return $data;
    }

// Información sobre reservaciones
//-----------------------------------------------------------------------------

    /**
     * Query reservaciones realizadas por un usuario, tabla events
     */
    function user_reservations($user_id)
    {
        $this->db->select($this->select());
        $this->db->where('user_id', $user_id);
        $this->db->where('events.type_id', 213);   //Evento tipo reservación entrenamiento presencial
        $this->db->join('users', 'events.user_id = users.id', 'left');
        $this->db->order_by('period_id', 'DESC');
        $this->db->limit(100);
        $reservations = $this->db->get('events');

        return $reservations;
    }
}