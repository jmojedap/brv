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

        $this->load->model('Inbody_model');
        
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
     * Vista resultados de InBody de un usuario, se incrusta en la aplicación
     * BraveApp
     * 2021-10-26
     */
    function user_report()
    {
        $user = $this->App_model->user_request();

        if ( ! is_null($user) ) {
            $data['head_title'] = $user->display_name;
            $data['view_a'] = 'app/inbody/user_report/user_report_v';
            $data['user'] = $user;
            $this->load->view('templates/brave/public', $data);
        } else {
            echo 'no user';
            //redirect('api/app/denied');
        }
    }

// FUNCIONES FRONT INFO
//-----------------------------------------------------------------------------

    
}