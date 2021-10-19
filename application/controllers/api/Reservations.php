<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reservations extends CI_Controller{

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Reservation_model');
        $this->load->model('Training_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * JSON
     * Listado reservaciones realizadas por un usuario
     * 2021-08-05
     */
    function user_reservations($user_id)
    {
        $reservations = $this->Reservation_model->user_reservations($user_id);
        $data['reservations'] = $reservations->result();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Información sobre una reservación de entrenamiento
     * 2021-08-13
     */
    function get_info($reservation_id, $user_id){
        $data['reservation'] = $this->Reservation_model->info($reservation_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Insertar una reservación a una sesión de entrenamiento, tabla events tipo 213
     */
    function save($training_id, $user_id)
    {
        $this->load->model('Training_model');
        $data = $this->Reservation_model->save($training_id, $user_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Eliminar reservación, recalcular cupos disponibles
     * 2021-07-23
     */
    function delete($reservation_id, $training_id)
    {
        $data = $this->Reservation_model->delete($reservation_id, $training_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Cancela reservación, recalcular cupos disponibles
     * 2021-07-23
     */
    function cancel($reservation_id, $training_id)
    {
        $data = $this->Reservation_model->cancel($reservation_id, $training_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Información y datos
//-----------------------------------------------------------------------------
    
    /**
     * Listado de días en los que se puede reservar cupo para entrenamiento
     * 2021-08-04
     */
    function get_training_days($user_id)
    {
        $days = $this->Reservation_model->training_days($user_id);
        $data['days'] = $days;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Listado de zonas o salas, marcando activas e inactivas, para reserva, según el usuario
     * 2021-08-04
     */
    function get_available_rooms($day_id, $user_id)
    {
        $rooms = $this->Reservation_model->available_rooms($day_id, $user_id);
        $data['rooms'] = $rooms;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Reservaciones de un usuario
//-----------------------------------------------------------------------------


}