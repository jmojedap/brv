<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firebase extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        //$this->load->model('Admin_model');
        
        //Para formato de horas
        date_default_timezone_set("America/Bogota");

    }
        
    function signin()
    {
        $data['head_title'] = 'VeBonit con Firebase';
        $data['view_a'] = 'firebase/signin_v';
        $this->load->view('templates/firebase/public_v', $data);
    }

    function messages()
    {
        $data['head_title'] = 'Mensajes';
        $data['view_a'] = 'firebase/messages/messages_v';
        $data['view_script'] = 'firebase/messages/script_v';
        $this->load->view('templates/firebase/main_v', $data);
    }

    function validate_token()
    {
        $id_token = $this->input->post('id_token');
        $firebase_user_id = $this->input->post('firebase_user_id');
        $this->load->model('Firebase_model');        
        $validation = $this->Firebase_model->validate_token($id_token, $firebase_user_id);

        if ( $validation['status'] == 1 ) {
            $this->Firebase_model->create_session($validation['payload']);
        }

        $data['validation'] = $validation;

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}