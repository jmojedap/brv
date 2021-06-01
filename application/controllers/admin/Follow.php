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
    
    function index($user_id)
    {
        redirect("admin/users/profile/{$user_id}");
    }

// SEGUIDORES
//-----------------------------------------------------------------------------

    function following($user_id)
    {
        $data = $this->User_model->basic($user_id);

        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->following($user_id);

        $data['back_link'] = $this->url_controller . 'explore';
        $data['view_a'] = $this->views_folder . 'following_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Vista listado de seguidores que tiene el usuario
     * 2021-05-27
     */
    function followers($user_id)
    {
        $data = $this->User_model->basic($user_id);

        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->followers($user_id);

        $data['back_link'] = $this->url_controller . 'explore';
        $data['view_a'] = $this->views_folder . 'followers_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /** Alternar seguir o dejar de seguir un usuario por parte del usuario en sesión */
    function toggle($user_id)
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->toggle($user_id);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

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

    /**
     * Proceso del sistema, masivo
     * Calcula y actualiza el número de seguidores de un usuario
     * 2021-05-25
     */
    function calculate_qty_followers()
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->calculate_qty_followers();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Proceso del sistema, masivo
     * Calcula y actualiza el número de usuarios a los que se está siguiendo
     * 2021-05-25
     */
    function calculate_qty_following()
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->calculate_qty_following();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}