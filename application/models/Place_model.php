<?php
class   Place_model extends CI_Model{

    function basic($place_id)
    {
        $data['place_id'] = $place_id;
        $data['row'] = $this->Db_model->row_id('places', $place_id);
        $data['head_title'] = $data['row']->place_name;
        $data['view_a'] = $this->views_folder . 'info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - places/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 15)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'places';                      //Nombre del controlador
            $data['cf'] = 'places/explore/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Lugares';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de places, filtrados por búsqueda y num página, más datos adicionales sobre
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
     * Segmento Select SQL, con diferentes formatos, consulta de lugares
     * 2020-12-12
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'places.*';

        //$arr_select['export'] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de places, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $this->db->select($this->select());
            //$this->db->join('users', 'places.user_id = users.id', 'left');
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('places.population', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('places', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar places
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('place_name', 'full_name', 'keywords', 'cod', 'cod_full', 'cod_official', 'zone'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['type'] != '' ) { $condition .= "places.type_id = {$filters['type']} AND "; }
        if ( $filters['status'] != '' ) { $condition .= "places.status = {$filters['status']} AND "; }
        if ( $filters['fe1'] != '' ) { $condition .= "places.country_id = {$filters['fe1']} AND "; }
        if ( $filters['fe2'] != '' ) { $condition .= "places.region_id = {$filters['fe2']} AND "; }
        
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
        $query = $this->db->get('places'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de lugares según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún user, se obtendrían cero places.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los user
            $condition = 'places.id > 0';
        }
        
        return $condition;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Determina si un user tiene el permiso para eliminar un registro de place
     * 2021-03-17
     */
    function deletable($place_id)
    {   
        $deletable = FALSE;
        
        //El user es aministrador
        if ( $this->session->userdata('role') == 0 ) { $deletable = TRUE; }
            
        return $deletable;
    }
    
    /**
     * Elimina un registro de places y sus registros relacionados en otras tablas
     * 2021-03-17
     */
    function delete($place_id)
    {
        $qty_deleted = 0;
        $deletable = $this->deletable($place_id);
        
        if ( $deletable ) 
        {
            $this->db->where('id', $place_id)->delete('places');
            $qty_deleted = $this->db->affected_rows();
        }
            
        return $qty_deleted;
    }
    
    /**
     * Guarda un registro en la tabla places
     * 2021-03-17
     */
    function save($arr_row, $place_id = 0)
    {
        //Complementar registro
        $arr_row['country'] = $this->Db_model->field_id('places', $arr_row['country_id'], 'place_name');
        if ( isset($arr_row['region_id']) ) {
            $arr_row['region'] = $this->Db_model->field_id('places', $arr_row['region_id'], 'place_name');
        }
        $arr_row['slug'] = $this->Db_model->unique_slug($arr_row['place_name'], 'places');

        //Condición para identificar el registro del place
        $saved_id = $this->Db_model->save('places', "id = {$place_id}", $arr_row);
        
        return $saved_id;
    }
}