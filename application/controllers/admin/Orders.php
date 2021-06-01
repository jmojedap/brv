<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller{
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Order_model');
        $this->load->model('Product_model');
        
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
            
        //Arrays con valores para contenido en lista
            $data['arr_status'] = $this->Item_model->arr_cod('category_id = 7');
            
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
        $data['quan_deleted'] = 0;
        
        foreach ( $selected as $row_id ) 
        {
            $data['quan_deleted'] += $this->Order_model->delete($row_id);
        }

        //Establecer resultado
        if ( $data['qty_deleted'] > 0 ) { $data['status'] = 1; }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CRUD
//-----------------------------------------------------------------------------

    function info($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);

        $data['view_a'] = 'orders/info_v';
        $data['nav_2'] = 'orders/menu_v';
        $data['subtitle_head'] = 'Información';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Crear un nuevo pedido, tabla orders. Le agrega un producto inicial con cantidad 1.
     */
    function create($product_id)
    {
        $data = $this->Order_model->create();

        if ( $data['status'] )
        {
            $this->session->set_userdata('order_id', $data['order_id']);
            $this->Order_model->add_product($product_id);
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function add_product($product_id, $quantity = 1)
    { 
        if ( is_null($this->session->userdata('order_id')) ) 
        {
            $data_order = $this->Order_model->create();
            $this->session->set_userdata('order_id', $data_order['order_id']);
        }

        $data = $this->Order_model->add_product($product_id, $quantity);

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// DATOS Y DETALLES
//-----------------------------------------------------------------------------

    function details($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);

        $data['view_a'] = 'orders/details_v';
        $data['nav_2'] = 'orders/menu_v';
        $data['subtitle_head'] = 'Detalles';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Respuestas de PayU sobre este pedido
     * 2021-01-26
     */
    function payu($order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['confirmations'] = $this->Order_model->confirmations($order_id);
        if ( $data['confirmations']->num_rows() > 0 )
        {
            $data['last_confirmation'] = $data['confirmations']->row();
        }

        $data['view_a'] = 'orders/payu_v';
        $data['nav_2'] = 'orders/menu_v';
        $data['subtitle_head'] = 'Detalles';

        $this->App_model->view(TPL_ADMIN, $data);
    }

// PROCESO DE PAGO
//-----------------------------------------------------------------------------

    /**
     * Pasos en el proceso de compra:
     * Step 1: formulario para completar datos personales
     * Step 2: Verificación de datos y totales
     */
    function checkout($step = 1)
    {
        $order_id = $this->session->userdata('order_id');
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);
        $data['form_data'] = $this->Order_model->payu_form_data($order_id);
        $data['step'] = $step;

        $data['head_title'] = 'Completa tus datos';
        $data['view_a'] = "orders/checkout/step_{$step}_v";
        $this->App_model->view('templates/admin_pml/main', $data);
    }

    /**
     * Vista HTML, Página de respuesta, redireccionada desde PayU para mostrar el resultado
     * de una transacción de pago. Toma los datos de resultado de GET
     */
    function result()
    {
        $data = $this->Order_model->result_data();

        //Si el pago fue exitoso, se agrega el tipo de suscripción a las variables de sesión
        if ( $data['success'] )
        {
            $row_user = $this->Db_model->row('users', $this->session->userdata('user_id'));
        }

        $data['step'] = 3;  //Tercer y último paso, resultado
        $data['view_a'] = "orders/checkout/result_v";
        $this->App_model->view('templates/admin_pml/main', $data);
    }

    /**
     * AJAX JSON
     * Actualiza los datos de un pedido
     * 2019-06-17
     */
    function update($order_id)
    {
        $data = $this->Order_model->update($order_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Formulario para probar el resultado de ejecución de la página de confirmación
     * ejecutada por PayU remotamente
     */
    function test($type, $order_id)
    {
        $data = $this->Order_model->basic($order_id);

        $data['head_title'] = 'Test compras ' . $order_id;
        $data['head_subtitle'] = $type;
        $data['view_a'] = "orders/test/{$type}_v";
        $data['nav_2'] = "orders/menu_v";
        $data['nav_3'] = "orders/test/menu_v";
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Página de confirmación que ejecuta remotamente PagosOnLine (pol) al 
     * terminar una transacción. Recibe datos de POL vía post, actualiza 
     * datos del pago del pedido
     */
    function confirmation_payu()
    {
        $data['confirmation_id'] = $this->Order_model->confirmation_payu();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function test_email($order_id)
    {
        $row_order = $this->Db_model->row_id('orders', $order_id);
        $message = $this->Order_model->message_buyer($row_order);
        echo $message;
    }

// Compras y suscripciones de Usuarios
//-----------------------------------------------------------------------------

    /**
     * Compras de un usuario
     * 2020-05-20
     */
    function my_orders()
    {
        $user_id = $this->session->userdata('user_id');
        $this->load->model('User_model');
        $data = $this->User_model->basic($user_id);
        $data['orders'] = $this->Order_model->user_orders($user_id);
        
        //Variables específicas
        $data['nav_2'] = 'accounts/menu_v';
        $data['view_a'] = 'orders/my_orders_v';
        
        $this->App_model->view('templates/admin_pml/main', $data);
    }

    /**
     * Balance del saldo que tiene el usuario para consumo de contenidos digitales
     * 2020-08-11
     */
    function my_credit()
    {
        $user_id = $this->session->userdata('user_id');
        $this->load->model('User_model');
        $data = $this->User_model->basic($user_id);
        $data['credit_orders'] = $this->Order_model->user_credit_orders($user_id);
        $data['payed_posts'] = $this->Order_model->user_payed_posts($user_id);
        
        //Variables específicas
        $data['nav_2'] = 'accounts/menu_v';
        $data['view_a'] = 'orders/my_credit_v';
        
        $this->App_model->view('templates/admin_pml/main', $data);
    }

    /**
     * Estadod e una compra, visible para un cliente o comprador
     */
    function status($order_code)
    {
        $data = $this->Order_model->basic($order_id);

        $data['products'] = $this->Order_model->products($order_id);

        $data['view_a'] = 'orders/status_v';
        $data['nav_2'] = 'orders/menu_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }
}