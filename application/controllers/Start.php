<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Start extends CI_Controller{
    
    function __construct() 
    {
        parent::__construct();
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

    function index()
    {
        redirect('app/accounts');
    }

    /**
     * Destinos a los que se redirige después de validar el login de usuario
     * según el rol de usuario (índice del array)
     */
    function logged()
    {
        $destination = 'app/accounts/login';
        if ( $this->session->userdata('logged') )
        {
            $arr_destination = array(
                1 => 'admin/app/dashboard/',  //Desarrollador
                2 => 'admin/app/dashboard/',  //Administrador
                13 => 'app/accounts/profile/',    //Instructor
                21 => 'app/accounts/profile/'     //Cliente
            );
                
            $destination = $arr_destination[$this->session->userdata('role')];
        }
        
        redirect($destination);
    }
}