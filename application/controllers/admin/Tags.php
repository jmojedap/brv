<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tags extends CI_Controller{
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Tag_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
// CRUD
//-----------------------------------------------------------------------------
    /**
     * POST JSON
     * Toma datos de POST e inserta un registro en la tabla "tag". Devuelve
     * $data del proceso en JSON
     */ 
    function insert()
    {
        $data = $this->Tag_model->insert();
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}