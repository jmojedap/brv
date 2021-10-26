<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbody extends CI_Controller {
        
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/inbody/';
    public $url_controller = URL_APP . 'inbody/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct()
    {
        parent::__construct();

        $this->load->model('Info_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función
     */
    function index()
    {
        $this->inicio();
    }

    /**
     * Vista resultados de inbody de un usuario, se incrusta en la aplicación
     * BraveApp
     * 2021-10-26
     */
    function user_report()
    {
        $user = $this->App_model->user_request();

        if ( ! is_null($user) ) {
            echo '<h1>Inbody User</h1>';
        } else {
            redirect('app/app/denied');
        }
    }

// FUNCIONES FRONT INFO
//-----------------------------------------------------------------------------

    
}