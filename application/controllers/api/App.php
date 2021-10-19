<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $url_controller = URL_ADMIN . 'app/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función de la aplicación de administración
     */
    function denied()
    {
        $data = array('status' => 0, 'message' => 'Access denied', 'saved_id' => 0, 'qty_deleted' => 0);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}