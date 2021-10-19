<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller{



// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('User_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($user_id)
    {
        redirect("users/info/{$user_id}");
    }

    /**
     * JSON
     * Listado de users, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->User_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Datos de un usuario. Para App 1.1
     * 2021-10-16
     */
    function get_info($user_id, $format = 'general')
    {
        //$user_id = $this->input->post('user_id');
        //Resultado por defecto
        $data['user'] = array('id' => '0');

        //Buscar usuario
        $this->db->select($this->User_model->select($format));
        $this->db->where('id', $user_id);
        $users = $this->db->get('users');

        if ( $users->num_rows() ) $data['user'] = $users->row();

        /*if ( ! is_null($data['user']) ) {
            //$condition = ""
            $data['user']->qty_posts = '150';
        }*/

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Datos de un usuario.
     * 2021-10-16
     */
    function get_info_secure($user_id, $format = 'general')
    {
        //Resultado por defecto
        $data['user'] = array('id' => '0');

        //Buscar usuario
        $this->db->select($this->User_model->select($format));
        $this->db->where('id', $user_id);
        $users = $this->db->get('users');

        if ( $users->num_rows() ) $data['user'] = $users->row();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}