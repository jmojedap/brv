<?php
class App_model extends CI_Model{
    
    /* Application model,
     * Functions to Brave Admin Application
     * 
     */
    
    function __construct(){
        parent::__construct();
        
    }
    
//SYSTEM
//---------------------------------------------------------------------------------------------------------
    
    /**
     * Carga la view solicitada, si por get se solicita una view específica
     * se devuelve por secciones el html de la view, por JSON.
     * 
     * @param type $view
     * @param type $data
     */
    function view($view, $data)
    {
        if ( $this->input->get('json') )
        {
            //Sende sections JSON
            $result['head_title'] = $data['head_title'];
            $result['head_subtitle'] = '';
            $result['nav_2'] = '';
            $result['nav_3'] = '';
            $result['view_a'] = '';
            
            if ( isset($data['head_subtitle']) ) { $result['head_subtitle'] = $data['head_subtitle']; }
            if ( isset($data['view_a']) ) { $result['view_a'] = $this->load->view($data['view_a'], $data, TRUE); }
            if ( isset($data['nav_2']) ) { $result['nav_2'] = $this->load->view($data['nav_2'], $data, TRUE); }
            if ( isset($data['nav_3']) ) { $result['nav_3'] = $this->load->view($data['nav_3'], $data, TRUE); }
            
            $this->output->set_content_type('application/json')->set_output(json_encode($result));
            //echo trim(json_encode($result));
        } else {
            //Cargar view completa de forma normal
            $this->load->view($view, $data);
        }
    }
    
    /**
     * Devuelve el valor del campo sis_option.valor
     * @param type $option_id
     * @return type
     */
    function option_value($option_id)
    {
        $option_value = $this->Db_model->field_id('sis_option', $option_id, 'value');
        return $option_value;
    }

    /**
     * Array con datos de sesión adicionales específicos para la aplicación actual.
     * 2019-06-23
     */
    function app_session_data($row_user)
    {
        //$this->load->model('Order_model');
        //$data['credit'] = $this->Order_model->credit($row_user->id);
        //$data['credit'] = 38000;
        $data = array();

        return $data;
    }

// NOMBRES
//-----------------------------------------------------------------------------

    /**
     * Devuelve el nombre de un user ($user_id) en un format específico ($format)
     */
    function name_user($user_id, $format = 'd')
    {
        $name_user = 'ND';
        $row = $this->Db_model->row_id('users', $user_id);

        if ( ! is_null($row) ) 
        {
            $name_user = $row->username;

            if ($format == 'u') {
                $name_user = $row->username;
            } elseif ($format == 'FL') {
                $name_user = "{$row->first_name} {$row->last_name}";
            } elseif ($format == 'LF') {
                $name_user = "{$row->last_name} {$row->first_name}";
            } elseif ($format == 'FLU') {
                $name_user = "{$row->first_name} {$row->last_name} | {$row->username}";
            } elseif ($format == 'd') {
                $name_user = $row->display_name;
            }
        }

        return $name_user;
    }

    /**
     * Devuelve el nombre de una registro ($place_id) en un format específico ($format)
     */
    function place_name($place_id, $format = 1)
    {
        
        $place_name = 'ND';
        
        if ( strlen($place_id) > 0 )
        {
            $this->db->select("places.id, places.place_name, region, country"); 
            $this->db->where('places.id', $place_id);
            $row = $this->db->get('places')->row();

            if ( $format == 1 ){
                $place_name = $row->place_name;
            } elseif ( $format == 'CR' ) {
                $place_name = $row->place_name . ', ' . $row->region;
            } elseif ( $format == 'CRP' ) {
                $place_name = $row->place_name . ' - ' . $row->region . ' - ' . $row->country;
            }
        }
        
        return $place_name;
    }

// OPCIONES
//-----------------------------------------------------------------------------

    /** Devuelve un array con las opciones de la tabla place, limitadas por una condición definida
    * en un formato ($format) definido    
    */
    function options_place($condition, $value_field = 'full_name', $empty_text = 'Lugar')
    {
        
        $this->db->select("CONCAT('0', places.id) AS place_id, place_name, full_name, CONCAT((place_name), ', ', (region)) AS cr", FALSE); 
        $this->db->where($condition);
        $this->db->order_by('places.place_name', 'ASC');
        $query = $this->db->get('places');
        
        $options_place = array_merge(array('' => '[ ' . $empty_text . ' ]'), $this->pml->query_to_array($query, $value_field, 'place_id'));
        
        return $options_place;
    }

    /* Devuelve un array con las opciones de la tabla place, limitadas por una condición definida
    * en un format ($format) definido
    */
    function options_user($condition, $empty_text = 'Usuario', $value_field = 'display_name')
    {
        
        $this->db->select("CONCAT('0', users.id) AS user_id, display_name, username", FALSE); 
        $this->db->where($condition);
        $this->db->order_by('users.display_name', 'ASC');
        $query = $this->db->get('users');
        
        $options_user = array_merge(array('' => '[ ' . $empty_text . ' ]'), $this->pml->query_to_array($query, $value_field, 'user_id'));
        
        return $options_user;
    }

    /* Devuelve un array con las opciones de la tabla post, limitadas por una condición definida
    * en un formato ($formato) definido
    */
    function options_post($condition, $format = 'n', $empty_text = 'posts')
    {
        
        $this->db->select("CONCAT('0', posts.id) AS post_id, post_name", FALSE); 
        $this->db->where($condition);
        $this->db->order_by('posts.id', 'ASC');
        $query = $this->db->get('posts');
        
        $index_field = 'post_id';
        
        if ( $format == 'n' )
        {
            $value_field = 'post_name';
        }
        
        $options_post = array_merge(array('' => '[ ' . $empty_text . ' ]'), $this->pml->query_to_array($query, $value_field, $index_field));
        
        return $options_post;
    }

// Procesos del sistema para la aplicación
//-----------------------------------------------------------------------------

    function processes()
    {
        $processes = array(
            array(
                'process_name' => 'Actualizar URL Files', 'process_link' => 'files/update_url',
                'description' => 'Actualiza los campos url y url_thumbnail, según los parámetros base de la aplicación actual',
            ),
            array(
                'process_name' => 'Calcular seguidores', 'process_link' => 'users/calculate_qty_followers',
                'description' => 'Calcula y actualiza el número de seguidores de un usuario',
            ),
            array(
                'process_name' => 'Calcular seguidos', 'process_link' => 'follow/calculate_qty_following',
                'description' => 'Calcula y actualiza el número de seguidos por cada usuario',
            ),
            array(
                'process_name' => 'Calcular seguidores', 'process_link' => 'follow/calculate_qty_followers',
                'description' => 'Calcula y actualiza el número de seguidores de cada usuario',
            ),
        );

        return $processes;
    }
}