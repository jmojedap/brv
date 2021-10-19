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
     * Información sobre una medición InBody específica
     * 2021-09-30
     */
    function get_info($inbody_id, $user_id)
    {
        $data = $this->Inbody_model->info($inbody_id, $user_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}