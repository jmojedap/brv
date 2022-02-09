<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends CI_Controller{

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Subscription_model');
        $this->load->model('Order_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

// Información de pagos del usuario
//-----------------------------------------------------------------------------

    /**
     * Información del plan comercial del usuario
     * 2022-01-25
     */
    function my_commercial_plan()
    {
        $data = array('status' => 0, 'product' => NULL, 'user_expiration_at' => NULL);

        $user_request = $this->App_model->user_request();

        if ( ! is_null($user_request) ) {
            $data['product'] = $this->Db_model->row_id('products', $user_request->commercial_plan);
            $data['user_expiration_at'] = $user_request->expiration_at;
            $data['new_user_expiration_at'] = $this->Subscription_model->subscription_end_date($user_request->expiration_at);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}