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
}