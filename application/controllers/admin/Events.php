<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/events/';
    public $url_controller = URL_ADMIN . 'events/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Event_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($user_id)
    {
        redirect('events/explore');
    }
    
//EXPLORE
//---------------------------------------------------------------------------------------------------
            
    /**
     * Exploración y búsqueda de usuarios
     * 2020-08-01
     */
    function explore($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->Event_model->explore_data($filters, $num_page);
        
        //Opciones de filtros de búsqueda
            $data['options_type'] = $this->Item_model->options('category_id = 13', 'Todos');
            
        //Arrays con valores para contenido en lista
            $data['arr_types'] = $this->Item_model->arr_cod('category_id = 13');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Listado de users, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Event_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Eliminar un conjunto de users seleccionados
     * 2021-02-20
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) $data['qty_deleted'] += $this->Event_model->delete($row_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function summary($qty_days = 7)
    {
        $data['events'] = $this->Event_model->summary($qty_days);
        $data['qty_days'] = $qty_days;

        $data['view_a'] = $this->views_folder . 'summary_v';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['head_title'] = 'Eventos';

        $this->App_model->view(TPL_ADMIN, $data);
    }
}