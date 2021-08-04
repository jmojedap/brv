<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainings extends CI_Controller{



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
    
    /**
     * Listado de días en los que se puede reservar cupo para entrenamiento
     * 2021-08-04
     */
    function get_training_days()
    {
        $days = $this->Calendar_model->trainings_days();
        $data['days'] = $days->result();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Listado de zonas o salas, marcando activas e inactivas, para reserva, según el usuario
     * 2021-08-04
     */
    function get_training_rooms($day_id, $user_id)
    {
        $rooms = $this->Calendar_model->available_rooms($day_id, $user_id);
        $data['rooms'] = $rooms;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}