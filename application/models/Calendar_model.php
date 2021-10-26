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
     * Segmento Select SQL, con diferentes formatos, consulta de eventos
     * 2021-10-08
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'events.*, users.display_name AS user_display_name';
        $arr_select['trainings'] = 'events.id, related_1 AS day_id, start, events.status, element_id AS room_id, integer_1 AS total_spots, integer_2 AS available_spots';
        
        $arr_select['appointments'] = 'events.id, period_id AS day_id, start, events.status';
        $arr_select['appointments'] .= ', events.type_id, items.item_name AS event_type';
        $arr_select['appointments'] .= ', events.user_id, users.display_name AS user_display_name, users.url_thumbnail AS user_thumbnail';
        
        $arr_select['nutritional_appointments'] = 'events.id, period_id AS day_id, start, events.status, user_id';
        $arr_select['reservations'] = 'events.id, events.title, events.type_id, events.status, related_1 AS day_id, start, element_id AS training_id, user_id, related_2 AS room_id, users.display_name AS user_display_name, users.url_thumbnail AS user_thumbnail, items.item_name AS event_type';
        $arr_select['events'] = 'events.id, title, events.type_id, start, end, period_id AS day_id, related_2, items.item_name AS event_type';

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

// Info
//-----------------------------------------------------------------------------

    function weeks_qty_events($date_1, $date_2)
    {
        $weeks_pre = $this->Period_model->weeks($date_1, $date_2);
        $weeks = array();
        foreach ($weeks_pre as $week_pre) {
            $week = $week_pre;

            $days = array();
            foreach( $week_pre['days'] as $day ){
                $condition_date = "period_id = {$day->id}";
                //$arr_qty_events_day_type = $this->qty_events_day_type($day->id);

                $day->qty_events_type = $this->qty_events_day_type($day->id);

                $days[] = $day;
            }
            $week['days'] = $days;
            $weeks[] = $week;
        }

        return $weeks;
    }

    function qty_events_day_type($day_id)
    {
        $qty_events_type = array(203 => 0, 221 => 0, 223 => 0, 225 => 0);

        $this->db->select('type_id, COUNT(ID) as qty_events');
        $this->db->where('period_id', $day_id);
        $this->db->where('type_id IN (203,221,223,225)');
        $this->db->group_by('type_id');

        $query = $this->db->get('events');

        foreach( $query->result() as $row_type ) {
            $qty_events_type[$row_type->type_id] = $row_type->qty_events;
        }

        return $qty_events_type;
    }

    /**
     * Eventos de un usuario para mostrarse en el calendario
     */
    function user_events($user_id)
    {
        $this->db->select('events.id, title, events.type_id, start, end, period_id AS day_id, related_2, items.item_name AS event_type');
        $this->db->join('items', 'items.cod = events.type_id AND items.category_id = 13', 'left');
        $this->db->where('events.type_id IN (213, 221, 223, 225)');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('period_id', 'ASC');
        $events = $this->db->get('events');

        return $events;
    }

    /**
     * Array con días y subarray de eventos de calendario de cada día.
     * 2021-10-12
     */
    function user_events_per_day($user_id)
    {   
        $user_events_index = array();
        $user_events = array();

        $events = $this->user_events($user_id);

        //Crear lista de días, con índice específico (YYYYMMDD)
        foreach ( $events->result() as $event ) {
            $user_events_index[$event->day_id] = array(
                'id' => $event->day_id,
                'date' => substr($event->start,0,10),
                'events' => array()
            );
        }

        //Llenar los eventos en el array de eventos de cada día corresponidente
        foreach ( $events->result() as $event ) {
            if ( $event->type_id == 221 ) $event->title = 'Cita de control nutricional';
            if ( $event->type_id == 223 ) $event->title = 'Sesión estética - masajes';
            if ( $event->type_id == 225 ) $event->title = 'Sesión estética - máquina';
            $user_events_index[$event->day_id]['events'][] = $event;
        }

        //Crear nuevo array de días, sin índice específico
        foreach ($user_events_index as $day) {
            $user_events[] = $day;
        }

        return $user_events;
    }

    /**
     * Información sobre un evento de un usuario
     * 2021-08-13
     */
    function event_info($event_id, $user_id, $format)
    {
        $event = ['id' => '0'];

        $this->db->select($this->select($format));
        $this->db->join('users', 'events.user_id = users.id', 'left');
        $this->db->join('items', 'items.cod = events.type_id AND items.category_id = 13', 'left');
        $this->db->where('events.id', $event_id);
        $this->db->where('user_id', $user_id);
        $events = $this->db->get('events');

        if ( $events->num_rows() ) {
            $event = $events->row();
            $event->color_key = $event->type_id;
            //Si es reserva entrenamiento, la clave color es room_id
            if ( $event->type_id == 213 ) $event->color_key = $event->room_id;
            if ( strlen($event->title) == 0 ) $event->title = $event->event_type;
        }

        return $event;
    }

// Programación de citas
//-----------------------------------------------------------------------------
    /**
     * Ejecutar la creación de citas de control nutricional, tabla events, tipo 221
     * Recibe datos desde calendar/schedlule
     * 2021-10-21
     */
    function schedule_appointments($type_id, $date_start, $date_end, $hours)
    {
        //Resultado inicial
        $data = array('status' => 0, 'message' => 'No se programadas citas');

        $days = $this->Period_model->days($date_start, $date_end);
        
        $events = array();
        //Recorrer días y horas, y crear citas
        foreach ($days->result() as $day) {
            foreach ($hours as $hour) {
                $events[] = $this->save_appointment($type_id, $day, $hour);
            }
        }

        //Verificar resultado
        if ( count($events) > 0 ) {
            $data = array('status' => 1, 'message' => count($events) . ' citas programadas');
            $data['events'] = $events;
        }

        return $data;
    }

    /**
     * Construye registro para tabla events, tipo 221, cita de control nutricional
     * Guarda el registro, devuelve ID de registro guardado.
     * 2021-10-21
     * @return int $saved_id
     */
    function save_appointment($type_id, $period, $hour)
    {
        $arr_row['type_id'] = $type_id;
        $arr_row['period_id'] = $period->id;
        $arr_row['start'] = $period->start . ' ' . $hour->start;
        $arr_row['end'] = $period->start . ' ' . $hour->end;
        $arr_row['status'] = 0;

        $condition = "type_id = {$arr_row['type_id']} AND period_id = {$arr_row['period_id']} AND start = '{$arr_row['start']}'";
        $saved_id = $this->Db_model->insert_if('events', $condition, $arr_row);

        return $saved_id;
    }

// Reserva de citas
//-----------------------------------------------------------------------------

    /**
     * Array con días en los que hay programadas citas de un tipo específico
     * 2021-10-22
     * @return array $periods
     */
    function get_appointments_days($user_id, $type_id)
    {
        $today_id = date('Ymd');

        $this->db->select('id, period_name, start');
        $this->db->where('id >=', $today_id);
        $this->db->where('type_id', 9); //Periodo tipo día
        //Día en los que haya citas programadas
        $this->db->where("id IN (SELECT period_id FROM events WHERE type_id = {$type_id})");
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('periods', 5);  //Hasta 5 días en el futuro

        $periods = array();
        foreach ( $query->result() as $day ) {
            $condition = "type_id = {$type_id} AND user_id = {$user_id} AND period_id = {$day->id}";
            $day->qty_user_reservations = $this->Db_model->num_rows('events', $condition);
            $periods[] = $day;
        }

        return $periods;
    }

    /**
     * Array, con citas para una fecha y zona de entrenamiento específica.
     * 2021-07-23
     */
    function get_appointments($day_id, $event_type_id = 0)
    {
        $now = new DateTime('now');
        $now->add(new DateInterval('PT1H'));

        $this->db->select($this->select('appointments'));
        if ( $event_type_id > 0 ) $this->db->where('events.type_id', $event_type_id);   //Tipo de cita
        $this->db->where('events.type_id IN (221,223,225)');           //Sesión de entrenamiento presencial
        $this->db->where('period_id', $day_id);     //Día de la sesión de entrenamiento
        //$this->db->where('start >', date('Y-m-d') . ' 00:00:00');   //Posteriores a la fecha de hoy
        $this->db->join('items', 'items.cod = events.type_id AND items.category_id = 13');
        $this->db->join('users', 'users.id = events.user_id', 'LEFT');
        $this->db->order_by('start', 'ASC');
        
        $query = $this->db->get('events');

        $appointments = array();
        foreach ($query->result() as $appointment) {
            $appointment->active = 1;
            if ( $appointment->start < $now->format('Y-m-d H:i:s') ) $appointment->active = 0;

            $appointments[] = $appointment;
        }

        return $appointments;
    }

    /**
     * Reservar una cita a un usuario, asignando campo events->user_id
     * 2021-10-22
     */
    function reservate_appointment($event_id, $user_id)
    {
        $data = array('status' => 0, 'error' => '');

        //Identificar evento tipo cita
        $event = $this->Db_model->row('events', "id = {$event_id} AND type_id IN (213, 221, 223, 225)");

        if ( ! is_null($event) ) {
            if ( $event->user_id > 0 ) $data['error'] = 'La cita ya está asignada';
            
            $now = new DateTime('now');
            $now->add(new DateInterval('PT1H'));
            if ( $event->start < $now->format('Y-m-d H:i:s') ) $data['error'] = 'No se puede reservar una cita pasada';
            
            // Que no tenga otra reserva de este tipo en el mismo mes
            $month = substr($event->period_id, 0,6);
            $condition = "type_id = {$event->type_id} AND user_id = {$user_id} AND LEFT(period_id,6) = '{$month}'";
            $qty_user_appointments = $this->Db_model->num_rows('events', $condition);
            if ( $qty_user_appointments > 0 ) $data['error'] = 'Ya tienes una reserva en este mismo mes';
        } else {
            //Si el evento no existe
            $data['error'] = 'El evento o cita no existe';
        }

        //No hay error, se asigna evento
        if ( strlen($data['error']) == 0 ) {
            $data['status'] = $this->set_user_to_appointment($event_id, $user_id);
        }

        return $data;
    }

    /**
     * Asignar un usuario a un evento cita
     * 2021-10-22
     */
    function set_user_to_appointment($event_id, $user_id)
    {
        $status = 0;

        $arr_row['status'] = 1; //Tomada
        $arr_row['user_id'] = $user_id;

        $this->db->where('id', $event_id);
        $this->db->update('events', $arr_row);

        if ( $this->db->affected_rows() > 0 ) {
            $status = 1;
        }

        return $status;
    }

    /**
     * Cancelar una cita a un usuario, quitando campo events->user_id
     * 2021-10-12
     */
    function cancel_appointment($event_id, $user_id)
    {
        $data = array('status' => 0, 'error' => '');

        //Identificar evento tipo cita
        $condition = "id = {$event_id} AND type_id IN (213, 221, 223, 225) AND user_id = {$user_id}";
        $event = $this->Db_model->row('events', $condition);

        //Validar
        if ( is_null($event) ) {
            //Si el evento no existe
            $data['error'] = 'La reserva de cita no existe';
        } else {
            if ( $event->user_id == 0 ) $data['error'] = 'La cita no está asignada';

            $now = new DateTime('now');
            $now->add(new DateInterval('PT1H'));
            if ( $event->start < $now->format('Y-m-d H:i:s') ) $data['error'] = 'No se puede cancelar una cita pasada';
        }

        //No hay error, se cancela la cita, quitando usuario
        if ( strlen($data['error']) == 0 ) {
            $arr_row['status'] = 0; //Disponible
            $arr_row['user_id'] = 0;

            $this->db->where('id', $event_id);
            $this->db->update('events', $arr_row);

            if ( $this->db->affected_rows() > 0 ) {
                $data['status'] = 1;
            }
        }

        return $data;
    }

// Citas de control nutricional
//-----------------------------------------------------------------------------

    /**
     * Ejecutar la creación de citas de control nutricional, tabla events, tipo 221
     * Recibe datos desde calendar/schedlule
     * 2021-10-08
     */
    function schedule_nutritional_control($date_start, $date_end, $hours)
    {
        //Resultado inicial
        $data = array('status' => 0, 'message' => 'No se programadas citas');

        $days = $this->Period_model->days($date_start, $date_end);
        
        $events = array();
        //Recorrer días y horas, y crear citas
        foreach ($days->result() as $day) {
            foreach ($hours as $hour) {
                $events[] = $this->save_nutritional_control_appointment($day, $hour);
            }
        }

        //Verificar resultado
        if ( count($events) > 0 ) {
            $data = array('status' => 1, 'message' => count($events) . ' citas programadas');
            $data['events'] = $events;
        }

        return $data;
    }

    /**
     * Construye registro para tabla events, tipo 221, cita de control nutricional
     * Guarda el registro, devuelve ID de registro guardado.
     * 2021-10-08
     * @return int $saved_id
     */
    function save_nutritional_control_appointment($period, $hour)
    {
        $arr_row['type_id'] = 221;
        $arr_row['period_id'] = $period->id;
        $arr_row['start'] = $period->start . ' ' . $hour->start;
        $arr_row['end'] = $period->start . ' ' . $hour->end;
        $arr_row['status'] = 0;

        $condition = "type_id = {$arr_row['type_id']} AND period_id = {$arr_row['period_id']} AND start = '{$arr_row['start']}'";
        $saved_id = $this->Db_model->insert_if('events', $condition, $arr_row);

        return $saved_id;
    }

    /**
     * Array con días en los que hay programadas citas de control nutricional
     * 2021-10-12
     * @return array $periods
     */
    function get_nutritional_appointments_days($user_id)
    {
        $today_id = date('Ymd');

        $this->db->select('id, period_name, start');
        $this->db->where('id >=', $today_id);
        $this->db->where('type_id', 9); //Periodo tipo día
        $this->db->where('id IN (SELECT period_id FROM events WHERE type_id = 221)');   //Día en los que haya CCN
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('periods', 5);  //Hasta 5 días en el futuro

        $periods = array();
        foreach ( $query->result() as $day ) {
            $condition = "type_id = 221 AND user_id = {$user_id} AND period_id = {$day->id}";
            $day->qty_user_reservations = $this->Db_model->num_rows('events', $condition);
            $periods[] = $day;
        }

        return $periods;
    }

    /**
     * Array con citas de control nutricional en un día determinado
     * 2021-10-12
     * @param int $day_id: ID periodo, día
     * @return array $appointments
     */
    function get_nutritional_appointments($day_id)
    {
        $now = new DateTime('now');
        $now->add(new DateInterval('PT1H'));

        $this->db->select($this->select('nutritional_appointments'));
        $this->db->where('type_id', 221);           //Cita de control nutricional
        $this->db->where('period_id', $day_id);     //Día de CCN
        $this->db->where('start >', date('Y-m-d') . ' 00:00:00');   //Posteriores a la fecha de hoy
        $this->db->order_by('start', 'ASC');
        
        $query = $this->db->get('events');

        $appointments = array();
        foreach ($query->result() as $appointment) {
            $appointment->active = ($appointment->user_id > 0) ? 0 : 1 ;
            unset($appointment->user_id);
            if ( $appointment->start < $now->format('Y-m-d H:i:s') ) $appointment->active = 0;

            $appointments[] = $appointment;
        }

        return $appointments;
    }

}