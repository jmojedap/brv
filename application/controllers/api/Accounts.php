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

    /**
     * Crear una cuenta de usuario
     * 2021-08-03
     */
    function register()
    {
        $data = array('status' => 0, 'message' => 'La cuenta no fue creada');  //Initial result values
        $data['validation_data'] = $this->Account_model->validate_form();
        $this->load->model('Validation_model');
            
        if ( $data['validation_data']['status'] == 1 )
        {
            //Construir registro de usuarios
                $arr_row['display_name'] = $this->input->post('display_name');
                $arr_row['email'] = $this->input->post('email');
                $arr_row['username'] = explode('@', $this->input->post('email'))[0] . rand(10,99);
                $arr_row['password'] = $this->Account_model->crypt_pw($this->input->post('new_password'));
                $arr_row['status'] = 2;     //Registrado sin confirmar email
                $arr_row['role'] = 21;      //21: Rol por defecto

            //Insert user
                $insert_data = $this->User_model->save($arr_row);
            
            //Cargar datos de usuario
                if ( $insert_data['saved_id'] > 0 ) {
                    $data['user_info'] = $this->Account_model->session_data($this->input->post('email'));
                    $data['status'] = 1;
                    $data['message'] = 'Usuario creado';
                }
                
            //Enviar email con código de activación
                //$this->Account_model->activation_key($data['saved_id']);
                //if ( ENV == 'production' ) $this->Account_model->email_activation($data['saved_id']);                
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * POST JSON
     * Actualiza los datos del usuario.
     */
    function update($user_id)
    {
        
        $data = array('status' => 0, 'message' => 'Los datos no se guardaron');  //Initial result values
        $data['validation_data'] = $this->User_model->validate($user_id);
        
        if ( $data['validation_data']['status'] == 1 ) 
        {
            $arr_row = $this->input->post();
            $arr_row['id'] = $user_id;

            $saved_id = $this->User_model->save($arr_row);
            
            if ( $saved_id ) {
                $data['status'] = 1;
                $data['message'] = 'Los cambios fueron guardados';
            }
        } else {
            $data['message'] = $data['validation_data']['error'];
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Información de un usuario en un formato específico
     * 2021-08-03
     */
    function profile_info($user_id, $format)
    {
        //$user_id = $this->input->post('user_id');
        //Resultado por defecto
        $data['user'] = array('id' => '0');

        //Buscar usuario
        $this->db->select($this->User_model->select($format));
        $this->db->where('id', $user_id);
        $users = $this->db->get('users');

        if ( $users->num_rows() ) $data['user'] = $users->row();

        if ( ! is_null($data['user']) ) {
            //$condition = ""
            $data['user']->qty_posts = '150';
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}