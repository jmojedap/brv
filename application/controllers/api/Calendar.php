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
}