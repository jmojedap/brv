<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Follow extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/follow/';
    public $url_controller = URL_ADMIN . 'follow/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {        
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Follow_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

    /** Alternar seguir o dejar de seguir un usuario por parte del usuario en sesión */
    function toggle($user_id)
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->toggle($user_id);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /*function follow_summary($user_id)
    {
        $data['indice'] = ;
    }*/

    /**
     * AJAX JSON
     * Listado de usuarios seguidos por un usuario
     * 2021-05-24
     */
    function get_following($user_id, $num_page = 1, $per_page = 10)
    {
        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->following($user_id, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Listado de usuarios seguidos por un usuario
     * 2021-05-24
     */
    function get_followers($user_id, $num_page = 1, $per_page = 10)
    {
        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->followers($user_id, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}