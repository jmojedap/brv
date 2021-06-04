<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/products/';
    public $url_controller = URL_ADMIN . 'products/';

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
    
//EXPLORE
//---------------------------------------------------------------------------------------------------

    /**
     * Exploración y búsqueda de productos
     * 2021-02-24
     */
    function explore($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->Product_model->explore_data($filters, $num_page);
        
        //Opciones de filtros de búsqueda
            $data['options_status'] = $this->Item_model->options('category_id = 8', 'Todos');
            $data['options_category'] = $this->Item_model->options('category_id = 25', 'Todos');
            
        //Arrays con valores para contenido en lista
            $data['arr_status'] = $this->Item_model->arr_cod('category_id = 8');
            $data['arr_categories'] = $this->Item_model->arr_cod('category_id = 25');
            //$data['arr_id_number_types'] = $this->Item_model->arr_item('category_id = 53', 'cod_abr');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
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
     * AJAX JSON
     * Eliminar un conjunto de users seleccionados
     * 2021-02-20
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) $data['qty_deleted'] += $this->Product_model->delete($row_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    /**
     * Información general del producto
     */
    function info($product_id)
    {        
        //Datos básicos
        $data = $this->Product_model->basic($product_id);
        
        //Variables específicas
        $data['head_subtitle'] = 'Información general';
        $data['view_a'] = $this->views_folder . 'info_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Información detallada registro producto
     * 2021-03-19
     */
    function details($product_id)
    {        
        //Datos básicos
        $data = $this->Product_model->basic($product_id);
        
        //Variables específicas
        $data['view_a'] = 'common/row_details_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }
    
// CRUD
//-----------------------------------------------------------------------------

    /**
     * Formulario para la creación de un nuevo producto
     * 2021-02-25
     */
    function add()
    {
        //Variables generales
        $data['head_title'] = 'Productos';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['view_a'] = $this->views_folder . 'add_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }
    
// EDICIÓN Y ACTUALIZACIÓN
//-----------------------------------------------------------------------------

    /**
     * Formulario para la edición de los datos de un grupo.
     * 2016-11-05
     */
    function edit($product_id)
    {
        //Datos básicos
            $data = $this->Product_model->basic($product_id);

            $data['options_status'] = $this->Item_model->options('category_id = 8');
            $data['options_cat_1'] = $this->Item_model->options('category_id = 25 AND level = 0', 'Todos las categorías');
        
        //Variables cargue vista
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['view_a'] = $this->views_folder . 'edit_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }    

    /**
     * POST JSON
     */
    function save()
    {
        $data = $this->Product_model->save();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// IMÁGENES DEL PRODUCTO
//-----------------------------------------------------------------------------

    function images($product_id)
    {
        $data = $this->Product_model->basic($product_id);

        $data['images'] = $this->Product_model->images($product_id);

        //$data['file_form_action'] = 'send_file_form';

        $data['view_a'] = $this->views_folder . 'images/images_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $this->App_model->view(TPL_ADMIN, $data);
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

    /**
     * Asocia una imagen a un producto, lo carga en la tabla file, y lo asocia en la tabla
     * products_meta
     * 2020-07-06
     */
    function add_image($product_id)
    {
        //Cargue
        $this->load->model('File_model');
        $data_upload = $this->File_model->upload();

        $data = $data_upload;
        if ( $data_upload['status'] )
        {
            $data['meta_id'] = $this->Product_model->add_image($product_id, $data_upload['row']->id);   //Asociar en la tabla products_meta
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Establecer imagen principal de un producto
     * 2020-07-07
     */
    function set_main_image($product_id, $meta_id)
    {
        $data = $this->Product_model->set_main_image($product_id, $meta_id);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Elimina una imagen de un producto, elimina el registro de la tabla file
     * y sus archivos relacionados
     * 2020-07-08
     */
    function delete_image($product_id, $meta_id)
    {
        $data['qty_deleted'] = $this->Product_model->delete_image($product_id, $meta_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// IMPORTACIÓN DE PRODUCTOS
//-----------------------------------------------------------------------------

    /**
     * Mostrar formulario de importación de products
     * con archivo Excel. El resultado del formulario se envía a 
     * $this->views_folder . 'import_e'
     */
    function import($type = 'general')
    {
        $data = $this->Product_model->import_config($type);

        $data['url_file'] = URL_RESOURCES . 'import_templates/' . $data['template_file_name'];

        $data['head_title'] = 'Productos';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['view_a'] = 'common/import_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    //Ejecuta la importación de products con archivo Excel
    function import_e()
    {
        //Proceso
        $this->load->library('excel');            
        $imported_data = $this->excel->arr_sheet_default($this->input->post('sheet_name'));
        
        if ( $imported_data['status'] == 1 )
        {
            $data = $this->Product_model->import($imported_data['arr_sheet']);
        }

        //Cargue de variables
            $data['status'] = $imported_data['status'];
            $data['message'] = $imported_data['message'];
            $data['arr_sheet'] = $imported_data['arr_sheet'];
            $data['sheet_name'] = $this->input->post('sheet_name');
            $data['back_destination'] = "products/explore/";
        
        //Cargar vista
            $data['head_title'] = 'Productos';
            $data['head_subtitle'] = 'Resultado importación';
            $data['view_a'] = 'common/import_result_v';
            $data['nav_2'] = $this->views_folder . 'explore/menu_v';

        $this->App_model->view(TPL_ADMIN, $data);
        //Salida JSON
        //$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CATÁLOGO
//-----------------------------------------------------------------------------

    function catalog($product_family, $num_page = 1)
    {
        //Datos básicos de la exploración
            //$this->load->model('Noticia_model');
        
        //Variables
            $data['product_family'] = $product_family;
            $data['head_title'] = 'Libros';
            $data['view_a'] = $this->views_folder . 'catalog_v';
            
        //Cargar vista
            $this->App_model->view('templates/magnews/books_v', $data);
    }

    function get_catalog($product_family, $num_page = 1)
    {
        $data = $this->Product_model->get_catalog($product_family, $num_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// METADATA
//-----------------------------------------------------------------------------

    function delete_meta($product_id, $meta_id)
    {
        $data = $this->Product_model->delete_meta($product_id, $meta_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CATÁLOGO
//-----------------------------------------------------------------------------

    /*function details($product_id)
    {
        $data = $this->Product_model->basic($product_id);

        //Variables
        $data['view_a'] = 'products/details_v';
            
        //Cargar vista
            $this->App_model->view(TPL_FRONT, $data);
    }*/
}