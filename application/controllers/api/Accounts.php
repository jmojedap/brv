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
                $arr_row['userkey'] = rand(100000,999999);
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

    /**
     * AJAX JSON
     * Ejecuta el proceso de cambio de contraseña de un usuario (user_id)
     * 2021-09-11
     */
    function change_password()
    {
        $row_user = $this->App_model->user_request();

        if ( ! is_null($row_user) )
        {
            //Valores iniciales para el resultado del proceso
                $validation = array('current_password' => 0, 'passwords_match' => 0);
                $data = array('status' => 0, 'error' => '', 'validation' => $validation);
            
            //Regla 1: Verificar contraseña actual
                $validar_pw = $this->Account_model->validate_password($row_user->username, $this->input->post('current_password'));
                if ( $validar_pw['status'] == 1 ) {
                    $data['validation']['current_password'] = 1;
                } else {
                    $data['error'] = 'La contraseña actual es incorrecta';
                }
            
            //Regla 2: Verificar que contraseña nueva coincida con la confirmación
                if ( $this->input->post('password') == $this->input->post('passconf') ) {
                    $data['validation']['passwords_match'] = 1;
                } else {
                    $data['error'] = 'La contraseña de confirmación no coincide.';
                }
            
            //Verificar que no haya error y cambiar contraseña
                if ( $data['error'] == '' )
                {
                    $this->Account_model->change_password($row_user->id, $this->input->post('password'));
                    $data['status'] = 1;
                }
            
            $this->output->set_content_type('application/json')->set_output(json_encode($data));   
        } else {
            //echo 'salida';
            $data = array('status' => 0, 'error' => 'Usuario no identificado');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }

    }

        //IMAGEN DE PERFIL
//---------------------------------------------------------------------------------------------------

    /**
     * Carga archivo de imagen, y se la asigna como imagen de perfil al usuario en sesión
     * 2021-09-20
     */
    function set_image()
    {
        $data = array('status' => 0, 'message' => 'Usuario no identificado');   //Resultado por defecto
        $user = $this->App_model->user_request();

        if ( ! is_null($user) ) {
            $previous_image_id = $user->image_id;

            //Cargue
            $this->load->model('File_model');
            
            $data_upload = $this->File_model->upload($user->id);
            
            $data['message'] = 'La imagen no fue asignada';
            if ( $data_upload['status'] )
            {
                //$this->User_model->remove_image($user->id);                               //Quitar imagen actual, si tiene una
                $data = $this->User_model->set_image($user->id, $data_upload['row']->id);   //Asignar imagen nueva

                //Eliminar imagen anterior
                $session_data = ['user_id' => $user->id, 'role' => $user->role];
                $this->File_model->delete($previous_image_id, $session_data);
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Desasigna y elimina la imagen asociada (si la tiene) al usuario en sesión.
     * 2021-09-20
     */
    function remove_image($user_id, $userkey)
    {
        $data = array('status' => 0, 'message' => 'Usuario no identificado');   //Resultado por defecto
        $user = $this->Db_model->row('users', "id = {$user_id} AND userkey = {$userkey}");

        if ( ! is_null($user) ) {
            $data = $this->User_model->remove_image($user_id);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Functión temporal para obtener users.userkey para un usuario mientras se incorpora como variable
     * en shared_preferences.
     * 2021-09-17
     */
    function get_userkey($public_key, $user_id)
    {
        $data = array('status' => 0, 'userkey' => '');
        //Debe proveer la clave que está solo en la aplicación
        if ( $public_key == '0IEM5CCSJ97LWC7L' ) {
            $user = $this->Db_model->row_id('users', $user_id);
            if ( ! is_null($user) ) {
                $data = array('status' => 1, 'userkey' => $user->userkey);
            }
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}