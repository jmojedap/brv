<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller{



// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Product_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($product_id)
    {
        redirect("products/info/{$product_id}");
    }

    /**
     * JSON
     * Listado de users, según filtros de búsqueda
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Product_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * JSON
     * Datos de un producto
     */
    function get_info($product_id)
    {
        $data['product'] = $this->Db_model->row_id('products', $product_id);
        
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Imágenes de un producto
     * 2020-07-07
     */
    function get_images($product_id)
    {
        $images = $this->Product_model->images($product_id);
        $data['images'] = $images->result();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function get_catalog($product_family, $num_page = 1)
    {
        $data = $this->Product_model->get_catalog($product_family, $num_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}