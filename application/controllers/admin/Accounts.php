<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/accounts/';
    public $url_controller = URL_ADMIN . 'accounts/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();
        
        $this->load->model('Account_model');
        $this->load->model('User_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Primera función de la aplicación
     */
    function index()
    {
        if ( $this->session->userdata('logged') )
        {
            redirect('app/logged');
        } else {
            redirect('accounts/login');
        }    
    }
    
//LOGIN
//---------------------------------------------------------------------------------------------------
    
    /**
     * Form login de users se ingresa con nombre de user y 
     * contraseña. Los datos se envían vía ajax a accounts/validate_login
     */
    function login()
    {        
        //Verificar si está logueado
            if ( $this->session->userdata('logged') )
            {
                redirect('app/application/logged');
            } else {
                $data['head_title'] = APP_NAME;
                $data['view_a'] = $this->views_folder . 'login_v';
                //$data['g_client'] = $this->Account_model->g_client(); //Para botón login con Google
                $this->load->view('templates/admin_pml/start', $data);
            }
    }

    /**
     * Recibe datos POST de accounts/login
     */
    function validate_login()
    {
        //Setting variables
            $userlogin = $this->input->post('username');
            $password = $this->input->post('password');
            
            $data = $this->Account_model->validate_login($userlogin, $password);
            
            if ( $data['status'] )
            {
                $this->Account_model->create_session($userlogin, TRUE);
            }
            
        //Salida
            $this->output->set_content_type('application/json')->set_output(json_encode($data));      
    }
    
    /**
     * Destroy session and redirect to login, start.
     */
    function logout()
    {
        $this->Account_model->logout();
        redirect('app/accounts/login');
    }

    //ML Master Login, 
    function ml($user_id)
    {
        $username = $this->Db_model->field_id('users', $user_id, 'username');
        if ( in_array($this->session->userdata('role'), array(1,2)) ) { $this->Account_model->create_session($username, FALSE); }
        
        redirect('app/logged');
    }
    
//REGISTRO DE USUARIOS
//---------------------------------------------------------------------------------------------------
    
    /**
     * Form de registro de nuevos users en la base de datos,
     * se envían los datos a accounts/register
     */
    function signup()
    {
        $data['head_title'] = 'Crear tu cuenta de ' . APP_NAME ;
        $data['view_a'] = $this->views_folder . 'signup_v';
        //$data['g_client'] = $this->Account_model->g_client(); //Para botón login con Google
        $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
        $data['max_date'] = strtotime(date('Y-m-d') . ' -18 year');
        $this->load->view('templates/admin_pml/start', $data);
    }
    
    /**
     * AJAX JSON
     * 
     * Recibe los datos POST del form en accounts/signup. Si se validan los 
     * datos, se registra el user. Se devuelve $data, con resultados de registro
     * o de validación (si falló).
     * 2021-03-09
     */
    function register()
    {
        $data = array('status' => 0, 'message' => 'La cuenta no fue creada');  //Initial result values
        $res_validation = $this->Account_model->validate_form();
        $this->load->model('Validation_model');
        $recaptcha = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3
            
        if ( $res_validation['status'] && $recaptcha->score > 0.5 )
        {
            //Construir registro del nuevo user
                $arr_row['display_name'] = $this->input->post('display_name');
                $arr_row['email'] = $this->input->post('email');
                $arr_row['username'] = explode('@', $this->input->post('email'))[0] . rand(10,99);
                $arr_row['password'] = $this->Account_model->crypt_pw($this->input->post('new_password'));
                $arr_row['status'] = 2;     //Registrado sin confirmar email
                $arr_row['role'] = 21;      //21: Cliente, default role

            //Insert user
                $data = $this->User_model->save(NULL, $arr_row);
                
            //Enviar email con código de activación
                $this->Account_model->activation_key($data['saved_id']);
                if ( ENV == 'production' ) $this->Account_model->email_activation($data['user_id']);                

            //Iniciar sesión
                $this->Account_model->create_session($arr_row['email']);
        } else {
            $data['validation'] = $res_validation['validation'];
        }

        //reCAPTCHA V3 validation
        if ( $recaptcha->score < 0.5 ) { $data['recaptcha_valid'] = FALSE; }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Validación de datos de accounts/signup
     * 2021-03-09
     */
    function validate_signup()
    {
        $data = $this->Account_model->validate_form();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Verificar si un email ya está registrado para una cuenta de usuario
     */
    function check_email()
    {
        $data = array('status' => 0, 'user' => array());

        $row = $this->Db_model->row('users', "email = '{$this->input->post('email')}'");

        if ( ! is_null($row))
        {
            $data['status'] = 1;
            $data['user']['firts_name'] = $row->first_name;
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// ACTIVATION
//-----------------------------------------------------------------------------

    /**
     * Ejecuta la activación de una cuenta de usuario ($activation_key)
     * 2020-07-20
     */
    function activate($activation_key)
    {
        $data = array('status' => 0, 'user_id' => 0, 'display_name' => '');
        $row_user = $this->Account_model->activate($activation_key);
        
        if ( ! is_null( $row_user ) )
        {
            $data['status'] = 1;
            $data['user_id'] = $row_user->id;
            $data['display_name'] = $row_user->display_name;
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

//RECUPERACIÓN DE CUENTAS
//---------------------------------------------------------------------------------------------------
    
    /**
     * Formulario para solicitar restaurar contraseña, se solicita email o nombre de usuario
     * Se genera user.activation_key, y se envía mensaje de correo eletrónico con link
     * para asignar nueva contraseña
     * 2020-07-20
     */
    function recovery()
    {
        if ( $this->session->userdata('logged') )
        {
            redirect('app');
        } else {
            $data['head_title'] = 'Accounts recovery';
            $data['view_a'] = $this->views_folder . 'recovery_v';
            $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
            $this->load->view('templates/admin_pml/start', $data);
        }
    }

    /**
     * Recibe email por post, y si encuentra usuario, envía mensaje
     * para restaurar contraseña
     * 2020-08-06
     */
    function recovery_email()
    {
        $data = ['status' => 0, 'recaptcha_valid' => FALSE];

        $this->load->model('Validation_model');
        $recaptcha = $this->Validation_model->recaptcha(); //Validación Google ReCaptcha V3

        //Identificar usuario
        $row = $this->Db_model->row('users', "email = '{$this->input->post('email')}'");

        if ( ! is_null($row) && $recaptcha->score > 0.5 ) 
        {
            //Usuario existe, se envía email para restaurar constraseña
            $this->Account_model->activation_key($row->id);
            if ( ENV == 'production') $this->Account_model->email_activation($row->id, 'recovery');
            $data = ['status' => 1, 'recaptcha_valid' => TRUE];
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Vista previa del mensaje de correo electrónico para recuperación de cuentas
     */
    function preview_recovery_email($user_id, $activation_type = 'activation')
    {
        echo $this->Account_model->activation_message($user_id, $activation_type);
    }

    /**
     * Recibe datos de POST y establece nueva contraseña a un usuario asociado a la $activation_key
     * 2020-07-20
     */
    function reset_password($activation_key)
    {
        $data = array('status' => 0, 'errors' => '');
        $row_user = $this->Db_model->row('users', "activation_key = '{$activation_key}'");        
        
        //Validar condiciones
        if ( $this->input->post('password') <> $this->input->post('passconf') ) $data['errors'] .= 'Las contraseñas no coinciden. ';
        if ( is_null($row_user) ) $data['errors'] .= 'Usuario no identificado. ';
        
        if ( strlen($data['errors']) == 0 ) 
        {
            $this->Account_model->change_password($row_user->id, $this->input->post('password'));
            $this->Account_model->create_session($row_user->username, 1);
            
            $data['status'] = 1;
            $data['message'] = $this->input->post('password') . '::' . $this->input->post('conf');
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// ACTUALIZACIÓN DE DATOS
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Se validan los datos del usuario en sesión, los datos deben cumplir varios criterios
     */
    function validate_form()
    {
        $user_id = $this->session->userdata('user_id');

        $this->load->model('Account_model');
        $data = $this->Account_model->validate_form($user_id);
        
        //Enviar result
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * POST JSON
     * Actualiza los datos del usuario en sesión.
     */
    function update()
    {
        $arr_row = $this->input->post();
        //$arr_row['display_name'] = $this->input->post('first_name') . ' ' . $this->input->post('last_name');
        $user_id = $this->session->userdata('user_id');

        $data = $this->User_model->save($user_id, $arr_row);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Ejecuta el proceso de cambio de contraseña de un usuario en sesión
     * 2021-03-11
     */
    function change_password()
    {
        //Valores iniciales para el resultado del proceso
            $row_user = $this->Db_model->row_id('users', $this->session->userdata('user_id'));
            $validation = array('current_password' => 0, 'passwords_match' => 0);
            $data = array('status' => 0, 'errors' => array(), 'validation' => $validation);
        
        //Regla 1: Verificar contraseña actual
            $validar_pw = $this->Account_model->validate_password($row_user->username, $this->input->post('current_password'));
            if ( $validar_pw['status'] == 1 ) {
                $data['validation']['current_password'] = 1;
            } else {
                $data['errors'][] = 'La contraseña actual es incorrecta';
            }
        
        //Regla 2: Verificar que contraseña nueva coincida con la confirmación
            if ( $this->input->post('password') == $this->input->post('passconf') ) {
                $data['validation']['passwords_match'] = 1;
            } else {
                $data['errors'][] = 'La contraseña de confirmación no coincide.';
            }
        
        //Verificar condiciones necesarias
            if ( count($data['errors']) == 0 )
            {
                $this->Account_model->change_password($row_user->id, $this->input->post('password'));
                $data['status'] = 1;
                $data['message'] = 'Contraseña modificada';
            }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));   
    }

//IMAGEN DE PERFIL
//---------------------------------------------------------------------------------------------------

    /**
     * Carga archivo de imagen, y se la asigna como imagen de perfil al usuario en sesión
     * 2021-02-19
     */
    function set_image()
    {
        $user_id = $this->session->userdata('user_id');

        //Cargue
        $this->load->model('File_model');
        
        $data_upload = $this->File_model->upload();
        
        $data = array('status' => 0, 'message' => 'La imagen no fue asignada');
        if ( $data_upload['status'] )
        {
            $this->User_model->remove_image($user_id);                              //Quitar image actual, si tiene una
            $data = $this->User_model->set_image($user_id, $data_upload['row']->id);   //Asignar imagen nueva
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX
     * Desasigna y elimina la imagen asociada (si la tiene) al usuario en sesión.
     */
    function remove_image()
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->User_model->remove_image($user_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// FIREBASE AUTH
//-----------------------------------------------------------------------------

    function firebase_save()
    {
        $data['firebase_id'] = $this->input->post('firebase_id');

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function firebase_auth()
    {
        $this->load->view('firebase/auth_v');
    }
}