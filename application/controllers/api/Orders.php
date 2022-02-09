<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller{

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

// Gestión de compras
//-----------------------------------------------------------------------------

    /**
     * Crear order, agregar producto
     * 2022-01-26
     */
    function create()
    {
        $user_request = $this->App_model->user_request();

        //Resultado por defecto
        $data = array('saved_id' => 0);

        //Crear order
            $user_id = $user_request->id;
            $data_order = $this->Order_model->create($user_id);
            $order_code = $data_order['order_code'];

        //Agregar producto
            $product_id = $this->input->post('product_id');
            $quantity = $this->input->post('quantity');

            $data_product = $this->Order_model->add_product($product_id, $quantity, $data_order['order_id']);

        //Respuesta
            $data['saved_id'] = $data_order['order_id'];
            $data['order'] = $data_order;
            $data['product'] = $data_product;
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Información de pagos sobre el usuario en sesión
//-----------------------------------------------------------------------------

    /**
     * Listado de orders del usuario en sesión
     * 2022-01-25
     */
    function my_orders()
    {
        $user_request = $this->App_model->user_request();

        if ( ! is_null($user_request) ) {
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();
            $filters['status'] = 1; //Pago confirmado
            $filters['u'] = $user_request->id;
            $filters['sf'] = 'personal';
            $data = $this->Order_model->get($filters, 1, 100);
        }
        
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Detalle sobre una compra (order), datos y productos
     * 2022-01-31
     */
    function get_info($order_code)
    {
        $data = array('order' => null, 'products' => null);

        $order = $this->Order_model->row_by_code($order_code);
        if ( ! is_null($order) ) {
            $data['order'] = $order;
            $data['products'] = $this->Order_model->products($order->id)->result();
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}