<?php
class   Event_model extends CI_Model{

// EXPLORE FUNCTIONS - events/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'events';                      //Nombre del controlador
            $data['cf'] = 'events/explore/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Eventos';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de events, filtrados por búsqueda y num página, más datos adicionales sobre
     * la búsqueda, filtros aplicados, total resultados, página máxima.
     * 2020-08-01
     */
    function get($filters, $num_page, $per_page)
    {
        //Referencia
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Búsqueda y Resultados
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['filters'] = $filters;
            $data['list'] = $this->list($filters, $per_page, $offset);    //Resultados para página
            $data['str_filters'] = $this->Search_model->str_filters();      //String con filtros en formato GET de URL
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

        //$arr_select['export'] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de events, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $this->db->select($this->select());
            $this->db->join('users', 'events.user_id = users.id', 'left');
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('events.id', 'DESC');
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
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('content', 'ip_address'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['type'] != '' ) { $condition .= "events.type_id = {$filters['type']} AND "; }
        if ( $filters['u'] != '' ) { $condition .= "events.user_id = {$filters['u']} AND "; }
        if ( $filters['fe3'] != '' ) { $condition .= "events.element_id = {$filters['fe3']} AND "; }
        if ( $filters['fe1'] != '' ) { $condition .= "events.related_1 = {$filters['fe1']} AND "; }
        if ( $filters['d1'] != '' ) { $condition .= "events.created_at >= '{$filters['d1']} 00:00:00' AND "; }
        if ( $filters['d2'] != '' ) { $condition .= "events.created_at <= '{$filters['d2']} 23:59:59' AND "; }
        
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

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Determina si un user tiene el permiso para eliminar un registro de event
     * 
     * @param type $event_id
     * @return boolean
     */
    function deletable($event_id)
    {   
        $deletable = FALSE;
        $row_event = $this->Db_model->row_id('events', $event_id);
        
        //El user creó el event
        if ( $row_event->creator_id == $this->session->userdata('user_id') ) {
            $deletable = TRUE;
        }
        
    //El user es aministrador
        if ( $this->session->userdata('role') <= 1 ) { $deletable = TRUE; }
            
        return $deletable;
    }
    
    /**
     * Elimina un registro de event y sus registros relacionados en otras tablas
     * 
     * @param type $event_id
     * @return type
     */
    function delete($event_id)
    {
        $qty_deleted = 0;
        $deletable = $this->deletable($event_id);
        
        if ( $deletable ) 
        {
            //Tabla
                $this->db->where('id', $event_id);
                $this->db->delete('events');
                
            $qty_deleted = $this->db->affected_rows();
        }
            
        return $qty_deleted;
    }
    
    /**
     * Modifica el campo events.status para un registro específico
     * 
     * @param type $type_id
     * @param type $element_id
     * @param type $estado
     */
    function update_status($type_id, $element_id, $estado)
    {
        $arr_row['status'] = $status;
        
        $this->db->where('type_id', $type_id);
        $this->db->where('element_id', $element_id);
        $this->db->update('events', $arr_row);
    }
    
    /**
     * Guarda un registro en la tabla event
     * 2021-07-19
     */
    function save($arr_row, $condition_add = NULL)
    {
        //Condición para identificar el registro del event
            $condition = "type_id = {$arr_row['type_id']} AND element_id = {$arr_row['element_id']}";
            if ( ! is_null($condition_add) )
            {
                $condition .= " AND " . $condition_add;
            }
        
            $event_id = $this->Db_model->exists('events', $condition);
        
        //Guardar el event
        if ( $event_id == 0 )
        {
            //No existe, se inserta
            $arr_row['ip_address'] = $this->input->ip_address();
            $arr_row['created_at'] = date('Y-m-d H:i:s');
            $arr_row['creator_id'] = $this->pml->if_strlen($this->session->userdata('user_id'), 0);
            
            $this->db->insert('events', $arr_row);
            $event_id = $this->db->insert_id();
        } else {
            //Ya existe, editar
            $this->db->where('id', $event_id);
            $this->db->update('events', $arr_row);
        }
        
        return $event_id;
    }
    
    /**
     * Devuelve array con datos registro base, para crear o editar un registro
     * de event, datos primordiales, comunes.
     * 
     */
    function basic_row()
    {
        $user_id = 0;
        if ($this->session->userdata('logged') ) { $user_id = $this->session->userdata('user_id'); }
        
        $start = date('Y-m-d H:i:s');
        
        $arr_row['user_id'] = $user_id;
        $arr_row['ip_address'] = $this->input->ip_address();
        $arr_row['start'] = $start;
        $arr_row['created_at'] = $start;
        $arr_row['creator_id'] = $user_id;
        
        return $arr_row;
    }
    
// DATOS
//---------------------------------------------------------------------------------------------------------
    
    function qty_events($filters)
    {
        if ( $filters['u'] != '' ) { $this->db->where('user_id', $filters['u']); }      //Usuario
        if ( $filters['tp'] != '' ) { $this->db->where('type_id', $filters['tp']); }    //Tipo
        if ( $filters['d1'] != '' ) { $this->db->where('start >=', $filters['d1']); }    //Fecha inicial
        
        $query = $this->db->get('events');
        
        return $query->num_rows();
    }
    
    function row_event($arr_conditions)
    {
        //Valor por defecto
        $row = NULL;
        
        $this->db->where($arr_conditions);
        $query = $this->db->get('events');
        if ( $query->num_rows() > 0 ){ $row = $query->row(); }
        
        return $row;
    }
    
    /**
     * Cantidad de seconds entre la fecha y hora de start, y una fecha y hora 
     * determinados
     */
    function seconds($row_event, $end_date)
    {
        $seconds = $this->pml->seconds($row_event->start, $end_date);
        
        return $seconds;
    }
    
// GESTIÓN DE EVENTO LOGIN
//-----------------------------------------------------------------------------
    
    /**
     * Crea un registro en la tabla event, asociado al start de sesión
     * @return type
     */
    function save_ev_login()
    {
        $row_user = $this->Db_model->row_id('users', $this->session->userdata('user_id'));
        
        //Registro, valores generales
            $arr_row['type_id'] = 101;   //Login de usuario
            $arr_row['start'] = date('Y-m-d H:i:s');
            $arr_row['element_id'] = $row_user->id;
            $arr_row['user_id'] = $row_user->id;
            $arr_row['status'] = 1;    //Login iniciado

            $condition_add = 'id = 0';  //Se pone una condición adicional incumplible, para que siempre agregue el registro
            $event_id = $this->save($arr_row, $condition_add);
        
        //Agregar event_id a los datos de sesión
            $this->session->set_userdata('login_id', $event_id);
        
        return $event_id;
    }

// RESUMEN
//-----------------------------------------------------------------------------

    function summary($qty_days)
    {
        $date = date('Y-m-d');
        $qty_days_mod = $qty_days - 1;
        $start = date('Y-m-d', strtotime($date . " - {$qty_days_mod} days"));

        $this->db->select('type_id, item_name AS event_type, COUNT(events.id) AS qty_events');
        $this->db->join('items', 'items.cod = events.type_id AND items.category_id = 13');
        $this->db->where('start >=', "{$start} 00:00:00");
        $this->db->group_by('type_id');
        $this->db->order_by('COUNT(events.id)', 'DESC');
        $events = $this->db->get('events');

        return $events;
    }
}