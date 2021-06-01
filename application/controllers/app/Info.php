<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {
        
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/info/';
    public $url_controller = URL_APP . 'info/';

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

// FUNCIONES FRONT INFO
//-----------------------------------------------------------------------------

    function about()
    {
        //Variables generales
            $data['head_title'] = 'Sobre nosotras';
            $data['view_a'] = 'info/about_v';
            $this->App_model->view(TPL_FRONT, $data);
    }

    function contacto()
    {
        $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php

        //Variables generales
        $data['head_title'] = 'Contacto';
        $data['view_a'] = 'info/contacto_v';
        $this->App_model->view('templates/magnews/main_v', $data);
    }

    function loading()
    {
        $data['head_title'] = 'VeBonit :: Cargando...';
        $data['view_a'] = 'info/loading_v';
        $this->App_model->view('templates/magnews/main_v', $data);
    }

    function ayuda($section = 'como-comprar')
    {
        $titles['como-comprar'] = '¿Cómo comprar?';
        $titles['que-es-bonitas-vbn'] = '¿Qué es Bonitas VBN?';

        $data['head_title'] = $titles[$section];
        $data['view_a'] = $this->views_folder . 'ayuda/' . str_replace('-','_',$section) . '_v';

        $this->App_model->view('templates/admin_pml/main', $data);
    }
}