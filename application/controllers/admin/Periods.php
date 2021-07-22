<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periods extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/periods/';
    public $url_controller = URL_ADMIN . 'periods/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Period_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($period_id = null)
    {
        if ( is_null($period_id) ) {
            redirect('admin/periods/explore');
        } else {
            redirect("admin/periods/details/{$period_id}");
        }
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
            $data = $this->Period_model->explore_data($filters, $num_page);
        
        //Opciones de filtros de búsqueda
            $data['options_type'] = $this->Item_model->options('category_id = 60', 'Todos');
            $data['options_status'] = array('' => '[ Todos los status ]', '00' => 'Inactivo', '01' => 'Activo');
            
        //Arrays con valores para contenido en lista
            $data['arr_types'] = $this->Item_model->arr_cod('category_id = 60');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Listado de periods, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 15)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Period_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Eliminar un conjunto de periods seleccionados
     * 2021-02-20
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) $data['qty_deleted'] += $this->Period_model->delete($row_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    function info($period_id)
    {
        $data = $this->Period_model->basic($period_id);
        $data['view_a'] = $this->views_folder . 'info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function details($period_id)
    {
        $data = $this->Period_model->basic($period_id);
        $data['view_a'] = 'common/row_details_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

// CREACIÓN Y EDICIÓN
//-----------------------------------------------------------------------------

    function add()
    {
        //Formulario
        $data['options_type'] = $this->Item_model->options('category_id = 70');
        $data['options_country'] = $this->App_model->options_period('type_id = 2');
        $data['options_region'] = $this->App_model->options_period('type_id = 3 AND country_id = 51', 'period_name');
        $data['options_status'] = array('00' => 'Inactivo', '01' => 'Activo');

        //Vista
        $data['view_a'] = $this->views_folder . 'add_v';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['head_title'] = 'Nuevo lugar';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function edit($period_id)
    {
        //Formulario
        $data = $this->Period_model->basic($period_id);

        $data['options_type'] = $this->Item_model->options('category_id = 70');
        $data['options_country'] = $this->App_model->options_period('type_id = 2');
        $data['options_region'] = $this->App_model->options_period('type_id = 3 AND country_id = 51', 'period_name');
        $data['options_status'] = array('00' => 'Inactivo', '01' => 'Activo');

        //Vista
        $data['view_a'] = $this->views_folder . 'edit_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Crear o actualizar registro de lugar, tabla periods
     * 2021-03-17
     */
    function save($period_id = 0)
    {
        $arr_row = $this->input->post();
        $data['saved_id'] = $this->Period_model->save($arr_row, $period_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Cambiar el estado de un lugar, campo periods.status
     * 2021-06-28
     */
    function toggle_business_day($period_id)
    {
        $data = $this->Period_model->toggle_business_day($period_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Calendario
//-----------------------------------------------------------------------------

    function calendar($year = null, $month = null)
    {
        if ( is_null($year) ) $year = date('Y');
        if ( is_null($month) ) $month = date('m');
        $row_month = $this->Db_model->row('periods', "year = {$year} AND month = {$month}");

        $calendar_prefs = $this->Period_model->calendar_prefs();
        $calendar_prefs['template'] = $this->Period_model->calendar_template();
        $calendar_prefs['next_prev_url'] = URL_ADMIN . 'periods/calendar';

        $this->load->library('calendar', $calendar_prefs);

        $data['weeks'] = $this->Period_model->weeks($row_month->start, $row_month->end);

        $data['head_title'] = 'Calendario';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['day_start'] = $row_month->start;
        $data['year'] = $year;
        $data['month'] = $month;
        $data['view_a'] = $this->views_folder . 'calendar_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

// Servicios
//-----------------------------------------------------------------------------

    /**
     * Array con opciones de lugar, formato para elemento Select de un form HTML
     * Utiliza los mismos filtros de la sección de exploración
     * 2021-03-16
     */
    function get_options($field_name = 'period_name')
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Period_model->get($filters, 1, 500);

        $options = array('' => '[ Seleccione ]');
        foreach ($data['list'] as $period)
        {
            $options['0' . $period->id] = $period->$field_name;
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($options));
    }

}