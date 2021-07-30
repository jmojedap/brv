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

    //Opciones de filtros de búsqueda
        $data['options_room'] = $this->Item_model->options('category_id = 520');
        $data['arr_rooms'] = $this->Item_model->arr_cod('category_id = 520');

    //Detalle periodos
        $data['day_start'] = $row_month->start;
        $data['year'] = $year;
        $data['month'] = $month;

    //Vista
        $data['head_title'] = 'Calendario';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'calendar_v';

    $this->App_model->view(TPL_ADMIN, $data);
}


// Sesiones de entrenamiento presencial
//-----------------------------------------------------------------------------

    /**
     * Exploración y búsqueda de trainings de entrenamiento
     * 2020-08-01
     */
    function trainings($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();
            $filters['type'] = 203;

        //Datos básicos de la exploración
            $data = $this->Calendar_model->trainings_data($filters, $num_page);
        
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
        $filters['type'] = 203;
        $data = $this->Calendar_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function get_training_reservations($training_id)
    {
        $reservations = $this->Calendar_model->training_reservations($training_id);
        $data['reservations'] = $reservations->result();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Sesiones
//-----------------------------------------------------------------------------

    /**
     * Vista, información sobre la sesión de entrenamiento
     */
    function training($training_id)
    {
        $data['training'] = $this->Calendar_model->row_training($training_id);
        $data['head_title'] = 'Sesión ' . $data['training']->id;
        $data['view_a'] = $this->views_folder. 'trainings/training_v';
        $data['nav_2'] = $this->views_folder . 'trainings/menu_v';
        $data['back_link'] = $this->url_controller . 'trainings/';

        //Salida JSON
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function get_trainings_days()
    {
        $days = $this->Calendar_model->trainings_days();
        $data['days'] = $days->result();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Array listado de entrenamientos para un día y una zona determinada
     * 2021-07-30
     */
    function get_trainings($day_id, $room_id)
    {        
        $trainings = $this->Calendar_model->get_trainings($day_id, $room_id);
        $data['list'] = $trainings;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Programación automática de trainings de entrenamiento
//-----------------------------------------------------------------------------

    /**
     * Formulario para crear programación automático de trainings de entrenamiento presencial
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
     * Ejecuta la programación automática de trainings de entrenamiento
     */
    function programar_trainings()
    {
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $data = $this->Calendar_model->programar_trainings($date_start, $date_end);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Reservaciones
//-----------------------------------------------------------------------------

    /**
     * Insertar una reservación a una sesión de entrenamiento, tabla events tipo 213
     */
    function save_reservation($training_id, $user_id)
    {
        $data = $this->Calendar_model->save_reservation($training_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Eliminar reservación, recalcular cupos disponibles
     * 2021-07-23
     */
    function delete_reservation($reservation_id, $training_id)
    {
        $data = $this->Calendar_model->delete_reservation($reservation_id, $training_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Testing
//-----------------------------------------------------------------------------

    function reset_trainings()
    {
        $this->db->query('DELETE FROM events WHERE type_id = 203'); //Eliminar trainings programadas

        $qty_deleted = $this->db->affected_rows();

        $data['messages'][] = 'Entrenamientos eliminados:' . $qty_deleted;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function simular_reservas()
    {
        $fecha_desde = date('Y-m-d H:i:s');

        $data['users'] = $this->db->get_where('users', 'role = 21');
        $data['trainings'] = $this->db->get_where('events', "type_id = 203 AND start >= '{$fecha_desde}'");

        $data['head_title'] = 'Simulación reservas';
        $data['view_a'] = $this->views_folder . 'trainings/simular_reservas_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    function reset_reservas()
    {
        $this->db->query('DELETE FROM events WHERE type_id = 213'); //Eliminar reservas programadas

        $qty_deleted = $this->db->affected_rows();

        $this->db->query('UPDATE events SET integer_2 = integer_1 WHERE type_id = 203'); //Restaurar cupos

        $qty_restored_spots = $this->db->affected_rows();

        $data['messages'][] = 'Reservas eliminadas:' . $qty_deleted;
        $data['messages'][] = 'Cupos restaurados:' . $qty_restored_spots;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}