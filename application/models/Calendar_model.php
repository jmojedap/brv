<?php
class Calendar_model extends CI_Model{


// Exploración de eventos programados
//-----------------------------------------------------------------------------

    /**
     * Array con listado de events, filtrados por búsqueda y num página, más datos adicionales sobre
     * la búsqueda, filtros aplicados, total resultados, página máxima.
     * 2020-08-01
     */
    function get($filters, $num_page, $per_page = 100)
    {
        //Referencia
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Búsqueda y Resultados
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['filters'] = $filters;
            $data['list'] = $this->list($filters, $per_page, $offset);          //Resultados para página
            $data['str_filters'] = $this->Search_model->str_filters();          //String con filtros en formato GET de URL
            $data['search_num_rows'] = $this->search_num_rows($data['filters']);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de usuarios
     * 2020-12-12
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'events.*, users.display_name AS user_display_name';
        $arr_select['trainings'] = 'events.id, related_1 AS day_id, start, events.status, element_id AS room_id, integer_1 AS total_spots, integer_2 AS available_spots';
        $arr_select['reservations'] = 'events.id, events.status, related_1 AS day_id, start, element_id AS training_id, user_id, related_2 AS room_id, users.display_name AS user_display_name, users.url_thumbnail AS user_thumbnail';

        //$arr_select[''] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de events, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $select_format = 'general';
            $select_format = ( $filters['sf'] != '' ) ? $filters['sf'] : 'general' ;
            $this->db->select($this->select($select_format));
            $this->db->join('users', 'events.user_id = users.id', 'left');
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('events.start', 'ASC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('events', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar events
     * 2021-07-23
     */
    function search_condition($filters)
    {
        $condition = '';

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('content', 'ip_address'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['type'] != '' ) { $condition .= "events.type_id = {$filters['type']} AND "; }
        if ( $filters['cat_1'] != '' ) { $condition .= "events.element_id = {$filters['cat_1']} AND "; }
        if ( $filters['u'] != '' ) { $condition .= "events.user_id = {$filters['u']} AND "; }
        if ( $filters['d1'] != '' ) { $condition .= "events.start >= '{$filters['d1']} 00:00:00' AND "; }
        if ( $filters['d1'] != '' ) { $condition .= "events.start <= '{$filters['d1']} 23:59:59' AND "; }
        
        //Quitar cadena final de ' AND '
        if ( strlen($condition) > 0 ) { $condition = substr($condition, 0, -5);}
        
        return $condition;
    }

    /**
     * Array Listado elemento resultado de la búsqueda (filtros).
     * 2020-06-19
     */
    function list($filters, $per_page = NULL, $offset = NULL)
    {
        $query = $this->search($filters, $per_page, $offset);
        $list = array();

        foreach ($query->result() as $row)
        {
            /*$row->qty_students = $this->Db_model->num_rows('group_user', "group_id = {$row->id}");  //Cantidad de estudiantes*/
            /*if ( $row->image_id == 0 )
            {
                $first_image = $this->first_image($row->id);
                $row->url_image = $first_image['url'];
                $row->url_thumbnail = $first_image['url_thumbnail'];
            }*/
            $list[] = $row;
        }

        return $list;
    }
    
    /**
     * Devuelve la cantidad de registros encontrados en la tabla con los filtros
     * establecidos en la búsqueda
     */
    function search_num_rows($filters)
    {
        $this->db->select('id');
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('events'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de usuarios según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún user, se obtendrían cero events.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los user
            $condition = 'events.id > 0';
        }
        
        return $condition;
    }

// Sesiones de entrenamiento
//-----------------------------------------------------------------------------

    /**
     * Registro sesión de entrenamiento, tabla events.
     * 2021-07-23
     */
    function row_training($training_id)
    {
        $this->db->select($this->select('trainings'));
        $this->db->where('id', $training_id);
        $trainings = $this->db->get('events');

        if ( $trainings->num_rows() ) {
            $row_training = $trainings->row();
            $row_training->room_name = $this->Item_model->name(520, $row_training->room_id);
            return $row_training;
        } else {
            return null;
        }
    }

    /**
     * Query con reservas hechas para una sesión de entrenamiento
     * 2021-07-23
     */
    function training_reservations($training_id)
    {
        $this->db->select($this->select('reservations'));
        $this->db->join('users', 'events.user_id = users.id', 'left');
        $this->db->where('element_id', $training_id);
        $reservations = $this->db->get('events');

        return $reservations;
    }

    /**
     * Array con los datos para la vista de exploración de trainings de entrenamiento
     */
    function trainings_data($filters, $num_page, $per_page = 100)
    {
        //Data inicial, de la tabla
            //$this->load->model('Event_model');
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'calendar';                      //Nombre del controlador
            $data['cf'] = 'calendar/trainings/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'trainings/explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Sesiones de entrenamiento';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = 'admin/calendar/menu_v';
        
        return $data;
    }

    /**
     * Query tabla periods, con días en los cuales se puede programar una reservación
     * 2021-07-23
     */
    function trainings_days()
    {
        $today_id = date('Ymd');

        $this->db->select('id, period_name, start');
        $this->db->where('id >=', $today_id);
        $this->db->where('type_id', 9); //Periodo tipo día
        $this->db->where('id IN (SELECT related_1 FROM events WHERE type_id = 203)');   //Día en los que haya trainings
        $this->db->order_by('id', 'ASC');
        $periods = $this->db->get('periods', 2);

        return $periods;
    }

    /**
     * Zonas de entrenamiento, marcadas activas o inactivas según el usuario
     * 2021-08-04
     * 
     */
    function available_rooms($day_id, $user_id)
    {
        $query = $this->App_model->rooms();

        $rooms = array();
        foreach ($query->result() as $room) {
            $room->active = 1;
            $rooms[] = $room;
        }

        return $rooms;
    }

    /**
     * Boolean, si una zona de entrenamiento está activa o no para un usuario en una fecha
     */
    function available_room($day_id, $room_id, $user_id)
    {
        $available = 1;
        $day = $this->Db_model->row_id('periods', $day_id);
    
        //Buscar otras reservas de la misma zona en la misma semana, sin que sea sábado
        $condition = "type_id = 213 AND related_2 = {$room_id} AND related_1 IN (SELECT id FROM periods WHERE week_number = {$day->week_number})";

        return $available;
    }

    /**
     * Array, con trainings para una fecha y zona de entrenamiento específica.
     * 2021-07-23
     */
    function get_trainings($day_id, $room_id)
    {
        $now = new DateTime('now');
        $now->add(new DateInterval('PT1H'));

        $this->db->select($this->select('trainings'));
        $this->db->where('type_id', 203);           //Sesión de entrenamiento presencial
        $this->db->where('element_id', $room_id);   //Zona de entrenamiento
        $this->db->where('related_1', $day_id);     //Día de la sesión de entrenamiento
        $this->db->where('start >', date('Y-m-d') . ' 00:00:00');   //Posteriores a la fecha de hoy
        $this->db->order_by('start', 'ASC');
        $query = $this->db->get('events');

        $trainings = array();
        foreach ($query->result() as $training) {
            $training->taken_spots = $training->total_spots - $training->available_spots;
            $training->active = 1;
            if ( $training->start < $now->format('Y-m-d H:i:s') ) $training->active = 0;

            $trainings[] = $training;
        }

        return $trainings;
    }

    /**
     * Programar automáticamente trainings de entrenamiento entre dos fechas
     */
    function programar_trainings($start, $end)
    {
        $trainings = array();

        $days = $this->Period_model->days($start, $end, 'business_day = 1');    //Días laborales
        $rooms = $this->App_model->rooms();             //Zonas de entrenamiento
        $schedules = $this->App_model->schedules();     //Horarios

        //Recorrer cada día, zona y horario y crear registro
        foreach ($days->result() as $day) {
            foreach ($rooms->result() as $room) {
                foreach ($schedules->result() as $schedule) {
                    $trainings[] = $this->programar_training($day, $room->room_id, $schedule->hour);
                }
            }
        }

        //Preparar respuesta
        $data['trainings'] = $trainings;
        $data['message'] = 'Sesiones programadas: ' . count($trainings);

        return $data;
    }

    /**
     * Crea un registro en la tabla events, tipo 203, sesión de entrenamiento
     */
    function programar_training($day, $room_id, $hour)
    {
        $arr_row['type_id'] = 203;  //Sesión de entrenamiento
        $arr_row['start'] = $day->start . ' ' . $hour;   //Día y hora
        $arr_row['element_id'] = $room_id;                  //Zona de entrenamiento
        $arr_row['related_1'] = $day->id;
        $arr_row['integer_1'] = 10;     //Cupos totales
        $arr_row['integer_2'] = 10;      //Cupos disponibles

        $this->load->model('Event_model');

        $condition_add = "start = '{$arr_row['start']}'";
        $event_id = $this->Event_model->save($arr_row, $condition_add);

        return $event_id;
    }

    /**
     * Actualiza la cantidad de cupos disponibles en una sesión de entrenamiento
     * 2021-07-22
     * 
     */
    function update_spots($training_id)
    {
        $qty_reservations = $this->Db_model->num_rows('events', "type_id = 213 AND element_id = {$training_id}");
        $this->db->query("UPDATE events SET integer_2 = (integer_1 - $qty_reservations) WHERE id = {$training_id}");

        return $this->db->affected_rows();
    }

// Reservas
//-----------------------------------------------------------------------------

    /**
     * Guardar una reserva de cupo en una sesión de entrenamiento por parte de un usuario
     * 2021-08-04
     */
    function save_reservation($training_id, $user_id)
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
            if ( $training->start > $user->expiration_at . ' 23:59:59' ) {
                $data['error'] = 'La suscripción del usuario está vencida: ' . $user->expiration_at;
            }
        }

        //Si no hay errores, se guarda
        if ( strlen($data['error']) == 0 )
        {
            $arr_row = $this->arr_row_reservation($training, $user);
            $condition = "type_id = 213 AND element_id = {$arr_row['element_id']} AND user_id = {$arr_row['user_id']}";

            $reservation_id = $this->Db_model->insert_if('events', $condition, $arr_row);

            if ( $reservation_id > 0 ) {
                //Actualizar número de cupos disponibles
                $this->update_spots($training_id);

                $data['saved_id'] = $reservation_id;
            }
        }

        return $data;
    }

    /**
     * Array para guardar reservation en tabla events
     * 2021-08-04
     */
    function arr_row_reservation($training, $user)
    {
        $arr_row['title'] = $this->Item_model->name(520, $training->element_id);  //Nombre zona de entrenamiento
        $arr_row['type_id'] = 213;  //Reseva entrenamiento presencial
        $arr_row['start'] = $training->start;
        $arr_row['status'] = 0;     //Reservado
        $arr_row['element_id'] = $training->id;    //ID Evento sesión de entrenamiento
        $arr_row['user_id'] = $user->id;         //Usuario para el que se reserva la sesión
        $arr_row['related_1'] = $training->related_1;     //ID día de sesión
        $arr_row['related_2'] = $training->element_id;    //Cód zona de entrenamiento
        $arr_row['created_at'] = date('Y-m-d H:i:s');
        $arr_row['creator_id'] = $user->id;

        return $arr_row;
    }

    /**
     * Elimina una reserva de cupo de entrenamiento, tabla events.
     * 2021-07-01
     */
    function delete_reservation($reservation_id, $training_id)
    {
        $data = array('qty_deleted' => 0, 'error' => '');

        $reservation = $this->Db_model->row('events', "id = {$reservation_id} AND element_id = {$training_id}");

        //Si existe reserva
        if ( ! is_null($reservation) )
        {
            //Revisión de condiciones
            if ( $reservation->start < date('Y-m-d H:i:s') ) $data['error'] = 'No se puede eliminar una reserva del pasado. ';

            //Verificar que no haya errores
            if ( strlen($data['error']) == 0 )
            {
                $this->db->where('id', $reservation_id);
                $this->db->where('element_id', $training_id);
                $this->db->delete('events');
                
                $data['qty_deleted'] = $this->db->affected_rows();
        
                //Actualizar número de cupos disponibles
                if ( $data['qty_deleted'] > 0 ) $this->update_spots($training_id);
            }
        } else {
            $data['error'] = 'No existe reserva con ID: ' . $reservation_id;
        }

        return $data;
    }
}