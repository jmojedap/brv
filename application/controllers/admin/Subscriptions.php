<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends CI_Controller{

// Controller Suscripciones a entrenamiento Brave, almacenadas en tabla events

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/subscriptions/';
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
        $arr_row['content'] = $this->input->post('content');
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

// Generación de órdenes de pago
//-----------------------------------------------------------------------------

    /**
     * Vista, herramienta para generación de órdenes de pago de subscripciones
     * 2022-01-03
     */
    function orders_generation()
    {
        $data['head_title'] = 'Generar órdenes de pago';
        $data['view_a'] = $this->views_folder . 'orders_generation/orders_generation_v';
        $data['nav_2'] = 'admin/orders/explore/menu_v';

        $data['products'] = $this->Subscription_model->products();

        //Identificar filtros de búsqueda
        $this->load->model('Search_model');
        $data['filters'] = $this->Search_model->filters();

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Crear una orden de pago, para un usuario y producto de subscripción
     * determinado
     * 2021-01-04
     */
    function create_order()
    {
        $creation_data = $this->input->post();
        $data['order'] = $this->Subscription_model->create_order($creation_data);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));

    }

}