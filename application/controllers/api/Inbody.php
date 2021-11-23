<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbody extends CI_Controller{

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {        
        parent::__construct();

        $this->load->model('Inbody_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    /**
     * JSON
     * Listado de users, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Inbody_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Información sobre una medición InBody específica
     * 2021-09-30
     */
    function get_info($inbody_id, $user_id)
    {
        $data = $this->Inbody_model->info($inbody_id, $user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }


}