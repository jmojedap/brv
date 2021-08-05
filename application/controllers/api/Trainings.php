<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainings extends CI_Controller{

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        //$this->load->model('Period_model');
        $this->load->model('Training_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
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

    /**
     * JSON
     * Listado de usuarios con reservación en un entrenamiento determinado
     * 2021-08-05
     */
    function get_reservations($training_id)
    {
        $reservations = $this->Training_model->reservations($training_id);
        $data['reservations'] = $reservations->result();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}