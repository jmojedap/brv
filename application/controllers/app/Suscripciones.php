<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Suscripciones extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
public $views_folder = 'app/suscripciones/';
public $url_controller = URL_APP . 'suscripciones/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        //$this->load->model('Tienda_model');
        $this->load->model('Product_model');
        $this->load->model('Order_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($product_id)
    {
        redirect("tienda/producto/{$product_id}");
    }

    /**
     *
     */
    function pago($order_code = '', $redirect = 0)
    {
        $order = $this->Order_model->row_by_code($order_code);

        $data['redirect'] = $redirect;
        $data['order'] = $order;
        $data['products'] = $this->Order_model->products($order->id);
        $data['extras'] = $this->Order_model->extras($order->id);

        $data['options_document_type'] = $this->Item_model->options('category_id = 53');
        $data['options_region'] = $this->App_model->options_place("country_id = {$order->country_id} AND type_id = 3 AND status = 1", 'full_name', 'Departamento');
        $data['options_city'] = $this->App_model->options_place("region_id = '{$order->region_id}' AND type_id = 4 AND status = 1", 'place_name', 'Ciudad');

        $this->load->model('Wompi_model');
        $data['form_destination'] = $this->Wompi_model->form_destination();
        $data['form_data'] = $this->Wompi_model->form_data($order);

        $data['head_title'] = 'Proceso de pago';
        $data['view_a'] = $this->views_folder . "pago/pago_v";
        $this->App_model->view(TPL_FRONT, $data);
    }

    /**
     * Vista HTML, Página de respuesta, redireccionada desde Wompi para mostrar el resultado
     * de una transacción de pago. Toma los datos desde la API de Wompi.
     */
    function resultado_pago()
    {
        $this->load->model('Wompi_model');
        $data = $this->Wompi_model->result_data();

        //Si se identifica la compra
        if ( ! is_null($data['order_code']) ) {
            $this->Order_model->unset_session();
        }

        $data['step'] = 'resultado';  //Cuarto y último paso, resultado
        $data['view_a'] = $this->views_folder . 'pago/resultado_wompi_v';
        $this->App_model->view(TPL_FRONT, $data);
    }
}