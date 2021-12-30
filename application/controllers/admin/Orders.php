<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller{
    
// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/orders/';
    public $url_controller = URL_ADMIN . 'orders/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Order_model');
        $this->load->model('Product_model');
        $this->load->model('Wompi_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

//EXPLORE FUNCTIONS
//---------------------------------------------------------------------------------------------------

    /** Exploración de Posts */
    function explore()
    {        
        //Datos básicos de la exploración
            $data = $this->Order_model->explore_data(1);
        
        //Opciones de filtros de búsqueda
            $data['options_status'] = $this->Item_model->options('category_id = 7', 'Todos');
            $data['options_payment_channel'] = $this->Item_model->options('category_id = 106 AND item_group < 30', 'Todos');
            
        //Arrays con valores para contenido en lista
            $data['arr_status'] = $this->Item_model->arr_cod('category_id = 7');
            $data['arr_payment_channel'] = $this->Item_model->arr_cod('category_id = 106');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Listado de Posts, filtrados por búsqueda, JSON
     */
    function get($num_page = 1)
    {
        $data = $this->Order_model->get($num_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * AJAX JSON
     * Eliminar un conjunto de posts seleccionados
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) 
        {
            $data['qty_deleted'] += $this->Order_model->delete($row_id);
        }

        //Establecer resultado
        if ( $data['qty_deleted'] > 0 ) { $data['status'] = 1; }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Exportar resultados de búsqueda
     * 2021-09-27
     */
    function export()
    {
        set_time_limit(120);    //120 segundos, 2 minutos para el proceso

        //Identificar filtros y búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data['query'] = $this->Order_model->query_export($filters);

        if ( $data['query']->num_rows() > 0 ) {
            //Preparar datos
                $data['sheet_name'] = 'ventas';

            //Objeto para generar archivo excel
                $this->load->library('Excel');
                $file_data['obj_writer'] = $this->excel->file_query($data);

            //Nombre de archivo
                $file_data['file_name'] = date('Ymd_His') . '_' . $data['sheet_name'];

            $this->load->view('common/download_excel_file_v', $file_data);
            //Salida JSON
            //$this->output->set_content_type('application/json')->set_output(json_encode($file_data['obj_writer']));
        } else {
            $data = array('message' => 'No se encontraron registros para exportar');
            //Salida JSON
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

// CREAR UNA VENTA
//-----------------------------------------------------------------------------

    /**
     * Formulario para la creación de una nueva order
     * 2021-12-09
     */
    function add()
    {   
        //Productos     
        $this->load->model('Product_model');
        $data['options_product'] = $this->Product_model->options('status = 1');
        $data['options_product_category'] = $this->Item_model->options('category_id = 25 AND level = 0');
        $data['options_payment_channel'] = $this->Item_model->options('category_id = 106 AND item_group = 2');

        //Referencia
        $data['arr_document_types'] = $this->Item_model->arr_item('category_id = 53', 'cod_abr');

        //Variables básicas
        $data['head_title'] = 'Nueva venta';
        $data['view_a'] = $this->views_folder . 'add/add_v';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Información de una compra, vista para administración
     * 2021-05-06
     */
    function info($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);
        $data['extras'] = $this->Order_model->extras($order_id);

        $data['arr_order_status'] = $this->Item_model->arr_cod('category_id = 7');
        $data['arr_shipping_methods'] = $this->Item_model->arr_cod('category_id = 183');
        $data['arr_shipping_status'] = $this->Item_model->arr_cod('category_id = 187');
        $data['arr_document_types'] = $this->Item_model->arr_cod('category_id = 53');

        $data['view_a'] = $this->views_folder . 'info/info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Información de una compra identificada por su código
     * 2021-04-16
     */
    function get_info($order_code = '')
    {
        $data['order'] = array();
        $data['products'] = array();
        $data['extras'] = array();
        
        $order = $this->Order_model->row_by_code($order_code);
        if ( ! is_null($order) )
        {
            $products = $this->Order_model->products($order->id);
            $extras = $this->Order_model->extras($order->id);

            $data['order'] = $order;
            $data['products'] = $products->result();
            $data['extras'] = $extras->result();
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Crear order, agregar producto, marcar estado de pago.
     * 2021-12-23
     */
    function create()
    {
        //Resultado por defecto
        $data = array('saved_id' => 0);

        //Crear order
            $user_id = $this->input->post('user_id');   
            $data_order = $this->Order_model->create($user_id);
            $order_code = $data_order['order_code'];

        //Agregar producto
            $product_id = $this->input->post('product_id');
            $quantity = $this->input->post('quantity');

            $data_product = $this->Order_model->add_product($product_id, $quantity, $data_order['order_id']);

        //Establecer estado de pago
            $arr_row['payment_channel'] = $this->input->post('payment_channel');
            $arr_row['payed'] = 1;
            $arr_row['status'] = 1;  //Pago confirmado
            $arr_row['confirmed_at'] = date('Y-m-d H:i:s');
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            if ( $this->input->post('confirmed_at') != null ) {
                $arr_row['confirmed_at'] = $this->input->post('confirmed_at');
            }
            $arr_row['notes_admin'] = $this->input->post('notes_admin');
            $data_payment = $this->Order_model->set_payed($data_order['order_id'], $arr_row);

        //Respuesta
            $data['saved_id'] = $data_order['order_id'];
            $data['order'] = $data_order;
            $data['product'] = $data_product;
            $data['payment'] = $data_payment;
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Agrega un producto a una compra, si la compra no está definida crea una y la agrega 
     * a variables de sesión
     * 2021-05-06
     */
    function add_product()
    { 
        //Resultado por defecto
        $data = array('status' => 0, 'message' => 'Compra no identificada');

        //Variables básicas
        $product_id = $this->input->post('product_id');
        $quantity = $this->input->post('quantity');
        $order_code = $this->input->post('order_code');
        // Identificar usuario
        $user_id = $this->session->userdata('user_id');
        if ( $this->input->post('user_id') ) $user_id = $this->input->post('user_id');

        //No hay order_code definida
        if ( is_null($order_code) )
        {
            //Crear nueva order, y ponerla en variables de sesión
            $data_order = $this->Order_model->create($user_id);
            $this->session->set_userdata('order_code', $data_order['order_code']);
            $order_code = $data_order['order_code'];
        }

        //Registro de compra
        $row_order = $this->Order_model->row_by_code($order_code);
        $editable = $this->Order_model->editable($row_order);

        //Código de compra existe
        if ( $editable )
        {
            $data = $this->Order_model->add_product($product_id, $quantity, $row_order->id);
        } else {
            $this->Order_model->unset_session();
            $data = array('status' => 2, 'message' => 'La compra no puede modificarse, ya fue procesada');
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Quita un producto de la orden y recalcula totales
     * 2021-04-23
     */
    function remove_product($product_id, $order_code = '')
    {
        //Resultado por defecto
        $data = array('status' => 0, 'message' => 'Compra no identificada');

        $row_order = $this->Order_model->row_by_code($order_code);
        $editable = $this->Order_model->editable($row_order);

        if ( $editable )
        {
            $data = $this->Order_model->remove_product($product_id, $row_order);
        } else {
            $this->Order_model->unset_session();
            $data = array('status' => 2, 'message' => 'La compra no puede modificarse, ya fue procesada');
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// EDICIÓN DE UNA COMPRA DESDE LA ADMINISTRACIÓN
//-----------------------------------------------------------------------------

    /**
     * Vista formulario edición de pedido
     * 2021-05-04
     */
    function edit($order_id)
    {
        $data = $this->Order_model->basic($order_id);
        $data['options_shipping_status'] = $this->Item_model->options('category_id = 187');
        $data['options_shipping_method_id'] = $this->Item_model->options('category_id = 183');
        
        $data['view_a'] = $this->views_folder . 'edit_v';
        $data['back_link'] = $this->url_controller . 'explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Actualiza datos de gestión administrativa de la compra
     * 2021-05-04
     */
    function admin_update($send_email_buyer = 'false')
    {
        $data['saved_id'] = $this->Order_model->admin_update();

        //Si se solicitó, enviar actualización por correo al cliente.
        /*if ( $data['saved_id'] && $send_email_buyer == 'true' ) {
            if ( ENV == 'production') $this->Order_model->email_buyer($data['saved_id']);
            $data['email_sent'] = 1;
        }*/

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }


// DATOS Y DETALLES
//-----------------------------------------------------------------------------

    function details($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);
        $data['extras'] = $this->Order_model->extras($order_id);

        $data['view_a'] = 'common/row_details_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Respuestas de Wompi sobre una compra
     * 2021-01-26
     */
    function responses($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['responses'] = $this->Wompi_model->responses($order_id);
        $data['response_signature'] = '';

        if ( count($data['responses']) > 0 )
        {
            $data['last_response'] = $data['responses'][0];
        }

        $data['view_a'] = $this->views_folder . 'responses_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = $this->url_controller . 'explore';

        $this->App_model->view(TPL_ADMIN, $data);
    }

// PAGO MANUAL
//-----------------------------------------------------------------------------

    /**
     * Formulario de datos de pago del pedido
     * 2021-11-22
     */
    function payment($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['options_payment_channel'] = $this->Item_model->options('category_id = 106 AND item_group = 2');
        $data['missing_data'] = $this->Order_model->missing_data($data['row']);

        $data['active_form'] = TRUE;
        if ( $data['row']->payment_channel > 0 AND $data['row']->payment_channel < 20 ) $data['active_form'] = FALSE;

        $data['view_a'] = $this->views_folder . 'payment/payment_v';
        $data['back_link'] = $this->url_controller . 'explore/';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Actualizar datos de pago de una venta.
     * 2021-12-17
     */
    function update_payment()
    {
        $payed = $this->input->post('payed');
        $order_id = $this->input->post('id');

        if ( $payed == '01' ) {
            $arr_row = $this->input->post();
            $arr_row['payed'] = 1;
            $arr_row['status'] = 1;  //Pago confirmado
            $arr_row['confirmed_at'] = date('Y-m-d H:i:s');
            if ( $this->input->post('confirmed_at') != null ) {
                $arr_row['confirmed_at'] = $this->input->post('confirmed_at');
            }
            $data = $this->Order_model->set_payed($order_id, $arr_row);
        } else {
            $data = $this->Order_model->remove_payment();
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// GESTIÓN DE LA COMPRA
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Actualiza los datos de una compra en su fase de construción por el comprador
     * 2021-04-23
     */
    function update($order_code)
    {
        $data = array('status' => 0, 'message' => 'Compra no identificada');

        //Identificar compra
        $row_order = $this->Order_model->row_by_code($order_code);
        
        $editable = $this->Order_model->editable($row_order);
        if ( $editable ) {
            $data = $this->Order_model->update($row_order->id);
        } else {
            //$this->Order_model->unset_session();
            $data = array('status' => 2, 'message' => 'La compra no puede modificarse, ya fue procesada');
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Cancelar una compra, se eliminan variables de sesión asociadas a la compra
     * 2021-05-06
     */
    function cancel()
    {
        $data = $this->Order_model->unset_session();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Formulario para probar el resultado de ejecución de la página de confirmación
     * ejecutada por Wompi remotamente
     */
    function test($order_id, $type = 'result')
    {
        $data = $this->Order_model->basic($order_id);

        $data['head_title'] = 'Test pagos ' . $order_id;
        $data['view_a'] = "ecommerce/orders/test_wompi/{$type}_v";
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['nav_3'] = $this->views_folder . 'test_wompi/menu_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Página de eventos que ejecuta automáticamente Wompi al
     * terminar una transacción por parte del comprador.
     * Recibe datos de Wompi JSON, actualiza datos del pago de la compra
     * 2021-05-11
     */
    function confirmation_wompi()
    {
        $data['confirmation_id'] = $this->Wompi_model->confirmation();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function test_email($order_id)
    {
        $row_order = $this->Db_model->row_id('orders', $order_id);
        echo $this->Order_model->message_buyer($row_order);
    }
}