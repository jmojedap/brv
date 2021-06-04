<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/config/';
    public $url_controller = URL_ADMIN . 'config/';


// Constructor
//-----------------------------------------------------------------------------
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Admin_model');
        
        //Para formato de horas
        date_default_timezone_set("America/Bogota");

    }
        
    function index()
    {
        redirect('admin/acl');
    }
        
// SIS OPTION 2019-06-15
//---------------------------------------------------------------------------------------------------

    /**
     * Listas de documentos, creación, edición y eliminación de opciones
     */
    function options()
    {
        $data['head_title'] = 'Opciones del sistema';
        $data['nav_2'] = $this->views_folder . 'menu_v';        
        $data['view_a'] = $this->views_folder . 'options_v';        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX - JSON
     * Listado de las opciones de documentos (posts.type_id = 7022)
     */
    function get_options()
    {
        $data['options'] = $this->db->get('sis_option')->result();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX - JSON
     * Inserta o actualiza una opcione de documentos (posts.type_id = 7022)
     */
    function save_option($option_id = 0)
    {
        $option_id = $this->Admin_model->save_option($option_id);

        $data = array('status' => 0, 'message' => 'La opción no fue guardada');
        if ( ! is_null($option_id) ) { $data = array('status' => 1, 'message' => 'Opción guardada: ' . $option_id); }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Elimina una opcione de documentos, registro de la tabla post
     */
    function delete_option($option_id)
    {
        $data = $this->Admin_model->delete_option($option_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Colores
//-----------------------------------------------------------------------------

    /**
     * Conjunto de colores de la herramienta
     * 2020-03-18
     */
    function colors()
    {
        $data['head_title'] = 'Colores';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'colors_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }
    
//Login maestro
//---------------------------------------------------------------------------------------------------
    
    /**
     * ml > master login
     * Función para el login de administradores ingresando con otro user
     * 
     * @param type $user_id
     */
    function ml($user_id)
    {
        $this->load->model('Account_model');
        $username = $this->Db_model->field_id('users', $user_id, 'username');
        if ( in_array($this->session->userdata('role'), array(1,2)) ) { $this->Account_model->create_session($username, FALSE); }
        
        redirect('start/logged');
    }

// Procesos
//-----------------------------------------------------------------------------

    function processes()
    {    
        $data['processes'] = $this->App_model->processes();
    
        $data['head_title'] = 'Procesos del sistema';
        $data['view_a'] = $this->views_folder .  'processes_v';
        $data['nav_2'] = $this->views_folder .  'menu_v';        
        $this->App_model->view(TPL_ADMIN, $data);
    }

// Funciones de ayuda
//-----------------------------------------------------------------------------

    /**
     * AJAX - POST
     * Return String, with unique slut
     */
    function unique_slug()
    {
        $text = $this->input->post('text');
        $table = $this->input->post('table');
        $field = $this->input->post('field');
        
        $unique_slug = $this->Db_model->unique_slug($text, $table, $field);
        
        $this->output->set_content_type('application/json')->set_output($unique_slug);
    }

// Pruebas y desarrollo
//-----------------------------------------------------------------------------

    /**
     * Reestablecer sistema para pruebas
     * 2019-07-19
     */
    function reset()
    {
        //IMPORT USERS
        $this->db->query('DELETE FROM users WHERE id != 200002;');

        //FOLLOWERS
        //$this->db->query('DELETE FROM users_meta WHERE type_id = 1011;');
        //$this->db->query('UPDATE users SET qty_followers = 0, qty_following = 0;');

        $data = array('status' => 1, 'message' => 'Listo');
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function test()
    {
        $data['head_title'] = 'Test';
        $data['view_a'] = 'app/test_v';
        $data['cant_resultados'] = '50';

        $this->db->select('*');
        $data['pictures'] = $this->db->get('pictures');

        $this->App_model->view('templates/remark/main_v', $data);
    }

    function test_ajax()
    {
        $data['view_a'] = '<h1>Hola desde ajax</h1>';
        $data['status'] = 1;

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}