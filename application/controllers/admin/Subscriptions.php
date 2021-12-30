<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends CI_Controller{

// Controller Suscripciones a entrenamiento Brave, almacenadas en tabla events

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/calendar/subscriptions/';
    public $url_controller = URL_ADMIN . 'subscriptions/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Subscription_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($subscription_id = null)
    {
        if ( is_null($subscription_id) ) {
            redirect('admin/subscriptions/explore');
        } else {
            redirect("admin/subscriptions/info/{$subscription_id}");
        }
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Guardar un registro tabla event, tipo subscription
     * 2021-12-12
     */
    function save()
    {
        $arr_row['user_id'] = $this->input->post('user_id');
        $arr_row['start'] = $this->input->post('start') . ' 00:00:00';
        $arr_row['end'] = $this->input->post('end') . ' 23:59:59';
        $arr_row['element_id'] = $this->input->post('user_id');

        $data['saved_id']= $this->Subscription_model->save($arr_row);

        // Actualizar fecha de vencimiento de suscripción de usuario
        if ( $data['saved_id'] > 0 ) {
            $data['expiration_update'] = $this->Subscription_model->update_user_expiration_at($arr_row['user_id']);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Eliminar una suscripción
     * 2021-08-01
     */
    function delete($subscription_id, $user_id)
    {
        $data['qty_deleted'] = $this->Subscription_model->delete($subscription_id, $user_id);

        // Actualizar fecha de vencimiento de suscripción de usuario
        if ( $data['qty_deleted'] > 0 ) {
            $data['expiration_update'] = $this->Subscription_model->update_user_expiration_at($user_id);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Información y datos
//-----------------------------------------------------------------------------

    function get_user_subscriptions($user_id)
    {
        $subscriptions = $this->Subscription_model->user_susbscriptions($user_id);
        $data['list'] = $subscriptions->result();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}