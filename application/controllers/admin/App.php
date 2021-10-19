<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/app/';
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
    function index()
    {
        if ( $this->session->userdata('logged') )
        {
            $this->logged();
        } else {
            redirect('app/accounts/login');
        }    
    }

    function dashboard()
    {
        $data['summary'] = $this->App_model->summary();
        $data['head_title'] = APP_NAME;
        $data['view_a'] = $this->views_folder . 'dashboard_v';
        $this->App_model->view(TPL_ADMIN, $data);

        //$this->output->enable_profiler(TRUE);
    }

// HELP
//-----------------------------------------------------------------------------

    function help($post_id = 0)
    {
        $data['head_title'] = 'Ayuda';
        $data['view_a'] = $this->views_folder . 'help/help_v';
        $this->App_model->view(TPL_ADMIN, $data);
}
}