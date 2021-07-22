<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller{

    function __construct() 
    {
        parent::__construct();
        
        $this->load->model('Account_model');
        $this->load->model('User_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Recibe datos POST, verifica si usuario y contraseña son válidos
     * 2021-07-13
     */
    function validate_login()
    {
        //Setting variables
            $userlogin = $this->input->post('username');
            $password = $this->input->post('password');

            $data = $this->Account_model->validate_login($userlogin, $password);

            if ( $data['status'] ) {
                $data['user_info'] = $this->Account_model->session_data($userlogin);
            }
            
        //Salida
            $this->output->set_content_type('application/json')->set_output(json_encode($data));      
    }
}