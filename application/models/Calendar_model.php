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
        $arr_select['sesiones'] = 'events.id, related_1 AS day_id, start, events.status, element_id AS room_id, integer_1 AS total_spots, integer_2 AS available_spots';

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
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = 'events.type_id IN (203, 205, 213) AND ';

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
     * Array con los datos para la vista de exploración de sesiones de entrenamiento
     */
    function sesiones_data($filters, $num_page, $per_page = 100)
    {
        //Data inicial, de la tabla
            //$this->load->model('Event_model');
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'calendar';                      //Nombre del controlador
            $data['cf'] = 'calendar/sesiones/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'sesiones/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Sesiones de entrenamiento';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = 'admin/calendar/menu_v';
        
        return $data;
    }

    function sesiones_days()
    {
        $today_id = date('Ymd');

        //$this->db->select('');
        $this->db->where('id >=', $today_id);
        $this->db->where('type_id', 9); //Periodo tipo día
        $this->db->where('id IN (SELECT related_1 FROM events WHERE type_id = 203)');   //Día en los que haya sesiones
        $this->db->order_by('id', 'ASC');
        $periods = $this->db->get('periods', 2);

        return $periods;
    }

    function get_sesiones($day_id, $room_id)
    {
        $now = new DateTime('now');
        $now->add(new DateInterval('PT1H'));

        $this->db->select($this->select('sesiones'));
        $this->db->where('type_id', 203);   //Sesión de entrenamiento presencial
        $this->db->where('element_id', $room_id);   //Zona de entrenamiento
        $this->db->where('related_1', $day_id);   //Día de la sesión de entrenamiento
        $this->db->where('start >', date('Y-m-d') . ' 00:00:00');   //Sesión de entrenamiento presencial
        $this->db->order_by('start', 'ASC');
        $query = $this->db->get('events');

        $sesiones = array();
        foreach ($query->result() as $sesion) {
            $sesion->active = 1;
            if ( $sesion->start < $now->format('Y-m-d H:i:s') ) $sesion->active = 0;

            $sesiones[] = $sesion;
        }

        return $sesiones;
    }

    function programar_sesiones($start, $end)
    {
        $sesiones = array();

        $days = $this->Period_model->days($start, $end, 'business_day = 1');
        $rooms = $this->App_model->rooms(); //Zonas de entrenamiento
        $schedules = $this->App_model->schedules(); 

        //$qty_rows = $days->num_rows() * $rooms->num_rows() * $schedules->num_rows();

        foreach ($days->result() as $day) {
            foreach ($rooms->result() as $room) {
                foreach ($schedules->result() as $schedule) {
                    $sesiones[] = $this->programar_sesion($day, $room->room_id, $schedule->hour);
                }
            }
        }

        $data['sesiones'] = $sesiones;
        $data['message'] = 'Sesiones programadas: ' . count($sesiones);

        return $data;
    }

    function programar_sesion($day, $room_id, $hour)
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
    function update_spots($sesion_id)
    {
        $qty_reservations = $this->Db_model->num_rows('events', "type_id = 213 AND element_id = {$sesion_id}");
        $this->db->query("UPDATE events SET integer_2 = (integer_1 - $qty_reservations) WHERE id = {$sesion_id}");

        return $this->db->affected_rows();
    }

// Reservas
//-----------------------------------------------------------------------------

    /**
     * Guardar una reserva de cupo en una sesión de entrenamiento por parte de un usuario
     * 2021-07-22
     */
    function save_reservation($sesion_id, $user_id)
    {
        $reservation_id = 0;

        $sesion = $this->Db_model->row_id('events', $sesion_id);

        if ( ! is_null($sesion) )
        {
            $arr_row['type_id'] = 213;  //Reseva entrenamiento presencial
            $arr_row['start'] = $sesion->start;
            $arr_row['status'] = 0;     //Reservado
            $arr_row['element_id'] = $sesion_id;    //ID Evento sesión de entrenamiento
            $arr_row['user_id'] = $user_id;         //Usuario para el que se reserva la sesión
            $arr_row['related_1'] = $sesion->related_1;     //ID día de sesión
            $arr_row['related_2'] = $sesion->element_id;    //Cód zona de entrenamiento
            $arr_row['created_at'] = date('Y-m-d H:i:s');
            $arr_row['creator_id'] = $user_id;

            $condition = "type_id = 213 AND element_id = {$arr_row['element_id']} AND user_id = {$arr_row['user_id']}";

            $reservation_id = $this->Db_model->insert_if('events', $condition, $arr_row);

            //Actualizar número de cupos disponibles
            if ( $reservation_id > 0 ) $this->update_spots($sesion_id);
        }

        return $reservation_id;
    }

    /**
     * Elimina una reserva de cupo de entrenamiento, tabla events.
     * 2021-07-01
     */
    function delete_reservation($reservation_id, $sesion_id)
    {
        $data = array('qty_deleted' => 0, 'error' => '');

        $reservation = $this->Db_model->row('events', "id = {$reservation_id} AND element_id = {$sesion_id}");

        //Si existe reserva
        if ( ! is_null($reservation) )
        {
            //Revisión de condiciones
            if ( $reservation->start < date('Y-m-d H:i:s') ) $data['error'] = 'No se puede eliminar una reserva del pasado. ';

            //Verificar que no haya errores
            if ( strlen($data['error']) == 0 )
            {
                $this->db->where('id', $reservation_id);
                $this->db->where('element_id', $sesion_id);
                $this->db->delete('events');
                
                $data['qty_deleted'] = $this->db->affected_rows();
        
                //Actualizar número de cupos disponibles
                if ( $data['qty_deleted'] > 0 ) $this->update_spots($sesion_id);
            }
        } else {
            $data['error'] = 'No existe reserva con ID: ' . $reservation_id;
        }

        return $data;
    }


}