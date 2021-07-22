<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/calendar/';
    public $url_controller = URL_ADMIN . 'calendar/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Period_model');
        $this->load->model('Calendar_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($period_id = null)
    {
        if ( is_null($period_id) ) {
            redirect('admin/calendar/explore');
        } else {
            redirect("admin/calendar/details/{$period_id}");
        }
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
    $calendar_prefs['next_prev_url'] = URL_ADMIN . 'calendar/calendar';

    $this->load->library('calendar', $calendar_prefs);

    $data['weeks'] = $this->Period_model->weeks($row_month->start, $row_month->end);

    $data['head_title'] = 'Calendario';
    $data['nav_2'] = $this->views_folder . 'menu_v';
    $data['day_start'] = $row_month->start;
    $data['year'] = $year;
    $data['month'] = $month;
    $data['view_a'] = $this->views_folder . 'calendar_v';
    $this->App_model->view(TPL_ADMIN, $data);
}


// Sesiones de entrenamiento presencial
//-----------------------------------------------------------------------------

    /**
     * Exploración y búsqueda de sesiones de entrenamiento
     * 2020-08-01
     */
    function sesiones($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->Calendar_model->sesiones_data($filters, $num_page);
        
        //Opciones de filtros de búsqueda
            $data['options_room'] = $this->Item_model->options('category_id = 520', 'Todos');
            
        //Arrays con valores para contenido en lista
            $data['arr_rooms'] = $this->Item_model->arr_cod('category_id = 520');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Listado de eventos programados en calendario, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 100)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Calendar_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Sesiones
//-----------------------------------------------------------------------------

    function get_sesiones_days()
    {
        $days = $this->Calendar_model->sesiones_days();
        $data['days'] = $days->result();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function get_sesiones($day_id, $room_id)
    {        
        $sesiones = $this->Calendar_model->get_sesiones($day_id, $room_id);
        $data['list'] = $sesiones;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Programación automática de sesiones de entrenamiento
//-----------------------------------------------------------------------------

    /**
     * Formulario para crear programación automático de sesiones de entrenamiento presencial
     * 2021-07-19
     */
    function programacion_automatica()
    {
        $data['head_title'] = 'Programar';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'programacion_automatica_v';

        $this->App_model->view(TPL_ADMIN, $data);

    }

    /**
     * AJAX JSON
     * Ejecuta la programación automática de sesiones de entrenamiento
     */
    function programar_sesiones()
    {

        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $data = $this->Calendar_model->programar_sesiones($date_start, $date_end);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Reservaciones
//-----------------------------------------------------------------------------

    function save_reservation($sesion_id, $user_id)
    {
        $data['saved_id'] = $this->Calendar_model->save_reservation($sesion_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function delete_reservation($reservation_id, $sesion_id)
    {
        $data = $this->Calendar_model->delete_reservation($reservation_id, $sesion_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Testing
//-----------------------------------------------------------------------------

    function reset_sesiones()
    {
        $this->db->query('DELETE FROM events WHERE type_id = 203'); //Eliminar sesiones programadas

        $qty_deleted = $this->db->affected_rows();

        $data['messages'][] = 'Sesiones eliminadas:' . $qty_deleted;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }



}