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
     * Datos de un usero
     */
    function get_info($user_id)
    {
        $data['user'] = $this->Db_model->row_id('users', $user_id);
        
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}