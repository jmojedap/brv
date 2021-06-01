<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'app/accounts/';
    public $url_controller = URL_APP . 'accounts/';

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
            redirect('app/application/logged');
        } else {
            redirect('app/accounts/login');
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
                //$this->load->view('templates/admin_pml/start', $data);
            }
    }
    
    /**
     * Destroy session and redirect to login, start.
     */
    function logout()
    {
        $this->Account_model->logout();
        redirect('app/accounts/login');
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

// ACTIVATION
//-----------------------------------------------------------------------------

    /**
     * Vista del resultado de activación de cuenta de usuario
     */
    function activation($activation_key = '')
    {
        $data['head_title'] = 'Activación de cuenta';
        $data['activation_key'] = $activation_key;
        $data['view_a'] = $this->views_folder . 'activation_v';

        $this->App_model->view('templates/admin_pml/start', $data);
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
            redirect('');
        } else {
            $data['head_title'] = 'Restaurar cuenta';
            $data['view_a'] = $this->views_folder . 'recovery_v';
            $data['recaptcha_sitekey'] = K_RCSK;    //config/constants.php
            $this->load->view('templates/admin_pml/start', $data);
        }
    }

    /**
     * Vista previa del mensaje de correo electrónico para recuperación de cuentas
     */
    function preview_recovery_email($user_id, $activation_type = 'activation')
    {
        echo $this->Account_model->activation_message($user_id, $activation_type);
    }

    /**
     * Formulario para reestablecer contraseña, se solicita nueva contraseña y confirmación
     * 2020-08-21
     */
    function recover($activation_key)
    {
        //Valores por defecto
            $data['head_title'] = 'Usuario no identificado';
            $data['user_id'] = 0;
        
        //Variables
            $row_user = $this->Db_model->row('users', "activation_key = '{$activation_key}'");        
            $data['activation_key'] = $activation_key;
            $data['row'] = $row_user;
        
        //Verificar que usuario haya sido identificado
            if ( ! is_null($row_user) ) 
            {
                $data['head_title'] = $row_user->display_name;
                $data['user_id'] = $row_user->id;
            }

        //Verificar que no tenga sesión iniciada
            if ( $this->session->userdata('logged') ) redirect('app/logged');

        //Cargar vista
            $data['view_a'] = $this->views_folder . 'recover_v';
            $this->load->view('templates/admin_pml/start', $data);
    }

// ADMINISTRACIÓN DE CUENTA
//-----------------------------------------------------------------------------

    /** Perfil del usuario en sesión */
    function profile()
    {        
        $data = $this->User_model->basic($this->session->userdata('user_id'));
        
        //Variables específicas
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'profile_v';
        
        $this->App_model->view('templates/admin_pml/main', $data);
    }

// ACTUALIZACIÓN DE DATOS
//-----------------------------------------------------------------------------

    /**
     * Formulario edición datos usuario en sessión. Los datos que se
     * editan dependen de la $section elegida.
     */
    function edit($section = 'basic')
    {
        //Datos básicos
        $user_id = $this->session->userdata('user_id');

        $data = $this->User_model->basic($user_id);

        $data['options_document_type'] = $this->Item_model->options('category_id = 53');
        $data['options_gender'] = $this->Item_model->options('category_id = 59 AND cod <= 2');
        $data['options_privacy'] = $this->Item_model->options('category_id = 66');
        $data['options_city_id'] = $this->App_model->options_place('type_id = 4 AND status = 1');
        
        $view_a = $this->views_folder . "edit/{$section}_v";
        if ( $section == 'cropping' )
        {
            $view_a = 'files/cropping_v';
            $data['image_id'] = $data['row']->image_id;
            $data['url_image'] = $data['row']->url_image;
            $data['back_destination'] = "accounts/edit/image";
        }
        
        //Array data espefícicas
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['nav_3'] = $this->views_folder . 'edit/menu_v';
            $data['view_a'] = $view_a;
        
        $this->App_model->view('templates/admin_pml/main', $data);
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

// SEGUIDORES
//-----------------------------------------------------------------------------

    function following()
    {
        $user_id = $this->session->userdata('user_id');
        $data = $this->User_model->basic($user_id);
        unset($data['nav_2']);

        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->following($user_id);

        $data['view_a'] = 'users/following_v';
        $data['back_link'] = 'accounts/profile';
        $this->App_model->view('templates/admin_pml/main', $data);
    }

// Otros
//-----------------------------------------------------------------------------

    function solicitudes()
    {
        //Datos básicos
        $user_id = $this->session->userdata('user_id');

        $data = $this->User_model->basic($user_id);
        $data['view_a'] = $this->views_folder . 'solicitudes_v.php';
        
        $this->App_model->view('templates/admin_pml/main', $data);
    }
}