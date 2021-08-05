<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainings extends CI_Controller{

// Controller Sesiones de entrenamiento, almacenadas en tabla events

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/calendar/trainings/';
    public $url_controller = URL_ADMIN . 'trainings/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Period_model');
        $this->load->model('Calendar_model');
        $this->load->model('Training_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($training_id = null)
    {
        if ( is_null($training_id) ) {
            redirect('admin/trainings/explore');
        } else {
            redirect("admin/trainings/info/{$training_id}");
        }
    }

// Información y procesos
//-----------------------------------------------------------------------------

    /**
     * Vista, información sobre la sesión de entrenamiento
     */
    function info($training_id)
    {
        $training = $this->Training_model->row($training_id);

        $data['training'] = $training;
        $data['head_title'] = 'Entrenamiento ' . $data['training']->id;
        $data['view_a'] = $this->views_folder. 'info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';

        $data['back_link'] = URL_ADMIN . 'calendar/calendar/' . $training->day_id . '/' . $training->room_id;

        //Salida JSON
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Lista próximos días en los que hay entrenamientos programados
     * 2021-08-05
     */
    function get_training_days()
    {
        $days = $this->Training_model->days();
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
        $trainings = $this->Training_model->get_trainings($day_id, $room_id);
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
    function schedule_generator()
    {
        $data['head_title'] = 'Programar';
        $data['nav_2'] = 'admin/calendar/menu_v';
        $data['view_a'] = $this->views_folder . 'schedule_generator_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Ejecuta la programación automática de trainings de entrenamiento
     */
    function schedule()
    {
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $data = $this->Training_model->schedule_trainings($date_start, $date_end);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}