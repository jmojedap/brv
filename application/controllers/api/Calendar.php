<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends CI_Controller{

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Calendar_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Eventos de un usuario programados en su calendario
     * 2021-08-11
     */
    function my_events($user_id)
    {
        //$user_id = $this->input->post();
        $days = $this->Calendar_model->user_events_per_day($user_id);
        //$events = $this->Calendar_model->user_events($user_id);

        $data['days'] = $days;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Información sobre un evento
     * 2021-08-13
     */
    function get_event_info($event_id, $user_id, $format = 'reservations')
    {
        $data['event'] = $this->Calendar_model->event_info($event_id, $user_id, $format);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Reservas
//-----------------------------------------------------------------------------

    /**
     * Asignar una cita a un usuario específico
     * 2021-10-11
     */
    function reservate_appointment($event_id, $user_id)
    {
        $data = ['status' => 0, 'message' => 'Reserva no autorizada'];

        $user = $this->App_model->user_request();
        if ( ! is_null($user) )
        {
            $data = $this->Calendar_model->reservate_appointment($event_id, $user_id);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
        //$this->output->enable_profiler(TRUE);
    }

    /**
     * Cancelar una cita reservada por un usuario
     * 2021-10-11
     */
    function cancel_appointment($event_id, $user_id)
    {
        $data = ['status' => 0, 'message' => 'Reserva no autorizada'];

        $user = $this->App_model->user_request();
        if ( ! is_null($user) )
        {
            $data = $this->Calendar_model->cancel_appointment($event_id, $user_id);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Citas de control nutricional (CCN)
//-----------------------------------------------------------------------------

    /**
     * JSON
     * Array días en los que hay CCN disponibles
     * 2021-10-08
     */
    function get_nutritional_appointments_days($user_id)
    {   
        $data = ['status' => 0, 'days' => array()];

        $days = $this->Calendar_model->get_nutritional_appointments_days($user_id);
        if ( count($days) > 0 ) {
            $data = ['status' => 1, 'days' => $days];
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Array listado CCN para un día determinado
     * 2021-10-08
     */
    function get_nutritional_appointments($day_id)
    {        
        $nutritional_appointments = $this->Calendar_model->get_nutritional_appointments($day_id);
        $data['list'] = $nutritional_appointments;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}