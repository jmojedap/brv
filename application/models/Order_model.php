<?php
class Order_model extends CI_Model{

    function basic($order_id)
    {
        $data['row'] = $this->Db_model->row_id('orders', $order_id);
        $data['head_title'] = $data['row']->order_code;
        $data['nav_2'] = $this->views_folder . 'menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - orders/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($num_page)
    {
        //Data inicial, de la tabla
            $data = $this->get($num_page);
        
        //Elemento de exploración
            $data['controller'] = 'orders';                      //Nombre del controlador
            $data['cf'] = 'orders/explore/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'explore/';           //Carpeta donde están las vistas de exploración
            
        //Vistas
            $data['head_title'] = 'Compras';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    function get($num_page)
    {
        //Referencia
            $per_page = 10;                             //Cantidad de registros por página
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Búsqueda y Resultados
            $this->load->model('Search_model');
            $data['filters'] = $this->Search_model->filters();
            $elements = $this->search($data['filters'], $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['list'] = $elements->result();
            $data['str_filters'] = $this->Search_model->str_filters();
            $data['search_num_rows'] = $this->search_num_rows($data['filters']);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }
    
    /**
     * String con condición WHERE SQL para filtrar post
     */
    function search_condition($filters)
    {
        $condition = NULL;
        
        //Tipo de post
        if ( $filters['status'] != '' ) { $condition .= "status = {$filters['status']} AND "; }
        
        if ( strlen($condition) > 0 )
        {
            $condition = substr($condition, 0, -5);
        }
        
        return $condition;
    }
    
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        
        $role_filter = $this->role_filter($this->session->userdata('post_id'));

        //Construir consulta
            //$this->db->select('id, post_name, except, ');
        
        //Crear array con términos de búsqueda
            $words_condition = $this->Search_model->words_condition($filters['q'], array('order_code', 'buyer_name', 'city', 'email', 'phone_number'));
            if ( $words_condition )
            {
                $this->db->where($words_condition);
            }
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('updated_at', 'DESC');
            }
            
        //Filtros
            $this->db->where($role_filter); //Filtro según el rol de post en sesión
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
        if ( is_null($per_page) )
        {
            $query = $this->db->get('orders'); //Resultados totales
        } else {
            $query = $this->db->get('orders', $per_page, $offset); //Resultados por página
        }
        
        return $query;
        
    }
    
    /**
     * Devuelve la cantidad de registros encontrados en la tabla con los filtros
     * establecidos en la búsqueda
     */
    function search_num_rows($filters)
    {
        $query = $this->search($filters); //Para calcular el total de resultados
        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = "user_id = {$this->session->userdata('user_id')}";  //Valor por defecto, ninguna orden, se obtendrían cero orders.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos las compras
            $condition = 'id > 0';
        }
        
        return $condition;
    }
    
    /**
     * Array con options para ordenar el listado de post en la vista de
     * exploración
     * 
     * @return string
     */
    function order_options()
    {
        $order_options = array(
            '' => '[ Ordenar por ]',
            'id' => 'ID Compra',
            'order_code' => 'Cód. venta'
        );
        
        return $order_options;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Actualiza los datos de una compra desde la edición de la administración
     * 2021-05-04
     */
    function admin_update()
    {
        $arr_row = $this->input->post();
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        //Actualizar registro orders
        $saved_id = $this->Db_model->save_id('orders', $arr_row);
    
        return $saved_id;
    }

    /**
     * Crear una compra, la tabla orders
     */
    function create()
    {
        $data = array('order_id' => 0, 'order_code' => NULL);

        //Datos por defecto
            $arr_row['city'] = '';
            $arr_row['country_id'] = 51;    //Colombia, ver tabla places
            $arr_row['address'] = '';
            $arr_row['created_at'] = date('Y-m-d H:i:s');
            $arr_row['updated_at'] = date('Y-m-d H:i:s');

        //Si hay usuario en sesión
            $row_user = $this->Db_model->row_id('users', $this->session->userdata('user_id'));
            if ( ! is_null($row_user) )
            {
                $arr_row['buyer_name'] = $row_user->display_name;
                $arr_row['email'] = $row_user->email;
                $arr_row['phone_number'] = $row_user->phone_number;
                $arr_row['user_id'] = $row_user->id;
            }

        //Crear registro
            $this->db->insert('orders', $arr_row);
            $order_id = $this->db->insert_id();
    
        //Establecer resultado
        if ( $order_id > 0 )
        {
            $data['order_id'] = $order_id;
            $data['order_code'] = $this->set_order_code($order_id);
        }
    
        return $data;
    }

    /**
     * Genera y establece un código único para un pedido. Campo order.order_code
     * 2019-06-17
     */
    function set_order_code($order_id)
    {
        $this->load->helper('string');
        
        $order_code = 'CP' . strtoupper(random_string('alpha', 2)) . '-' . $order_id;

        $arr_row['order_code'] = $order_code;
        $arr_row['description'] = 'Compra ' . $order_code . ' en ' . APP_NAME;
        
        $this->db->where('id', $order_id)->update('orders', $arr_row);

        return $arr_row['order_code'];
    }

    /**
     * Determina sin una compra puede ser modificada o no, según su estado o rol de usuario
     * 2021-05-06
     */
    function editable($row_order)
    {
        $editable = false;   //Valor por defecto

        //La compra tiene estado iniciado
        if ( $row_order->status == 10 ) $editable = true;

        //Es administrador
        if ( $this->session->userdata('logged') && $this->session->userdata('role') <= 1 ) $editable = true;

        return $editable;
    }

    /**
     * Actualizar los datos de una compra, enviados por POST y actualiza totales
     * 2021-04-13
     */
    function update($order_id)
    {
        $data['status'] = 0;

        $arr_row = $this->input->post();
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        //Establecer ciudad y datos
        if ( isset($arr_row['city_id']) )
        {
            $row_city = $this->Db_model->row_id('places', $arr_row['city_id']);
            $arr_row['city'] = $row_city->place_name . ' - ' . $row_city->region . ' - ' . $row_city->country;
            $arr_row['country_id'] = $row_city->country_id;
            $arr_row['region_id'] = $row_city->region_id;
        }

        //Actualizar registro orders
        $this->db->where('id', $order_id)->update('orders', $arr_row);

        //Verificar y actualizar totales
        if ( $this->db->affected_rows() >= 0)
        {
            $data['status'] = 1;

            
            $this->update_totals($order_id);           //Actualizar totales
        }

        return $data;
    }

    /**
     * Agrega un producto en una cantidad definida a una orden, guarda el registro
     * en la tabla order_producto (op), devuelve ID del registro guardado.
     * 2019-06-17
     */
    function add_product($product_id, $quantity = 1, $order_id)
    {
        $data = array('status' => 0, 'op_id' => 0, 'qty_items' => NULL);

        //Identificar producto
        $this->load->model('Product_model');
        $row_product = $this->Db_model->row_id('products', $product_id);

        //Construir registro order_product
        $arr_row['order_id'] = $order_id;
        $arr_row['product_id'] = $product_id;
        $arr_row['original_price'] = $row_product->price;
        $arr_row['price'] = $row_product->price;
        $arr_row['quantity'] = $quantity;

        //Guardar registro en order_product
        $condition = "order_id = {$arr_row['order_id']} AND product_id = {$arr_row['product_id']} AND type_id = 1";
        $data['op_id'] = $this->Db_model->save('order_product', $condition, $arr_row);

        //Actualizar totales de la compra
        $this->update_totals($order_id);

        //Calcular número de productos (tipo 1) en la compra, y ponerla en variables de sesión
        $data['qty_items'] = $this->Db_model->num_rows('order_product', "order_id = {$arr_row['order_id']} AND type_id = 1");
        $this->session->set_userdata('order_qty_items', $data['qty_items']);
        
        //Actualizar resultado respuesta
        $data['status'] = ($data['op_id'] > 0 ) ? 1 : 0 ;

        return $data;
    }

    /**
     * Quita un producto de la orden y recalcula totales, elimina de la tabla order_product
     * y recalcula totales.
     * 2021-02-09 (No modificar pagos ya procesados)
     */
    function remove_product($product_id, $row_order)
    {
        $data = array('status' => 0, 'message' => 'El producto no fue retirado de la compra');
        
        $this->db->where('product_id', $product_id);
        $this->db->where('order_id', $row_order->id);
        $this->db->where('type_id', 1);     //Es un producto
        $this->db->delete('order_product');
        
        $data['qty_deleted'] = $this->db->affected_rows();

        //Actualizar totales de la compra
        $this->update_totals($row_order->id);
        
        //Verificar resultado
        if ( $data['qty_deleted'] > 0 ) 
        {
            $data['status'] = ( $data['qty_deleted'] > 0 ) ? 1 : 0 ;
            $data['message'] = 'Producto retirado de la compra';
        }

        return $data;
    }

    /**
     * Elimina las variables de sesión asociadas a una compra
     * 2021-05-06
     */
    function unset_session()
    {
        $this->session->unset_userdata('order_code');
        $this->session->unset_userdata('order_qty_items');

        $data = array('status' => 1, 'message' => 'Compra desactivada');
    
        return $data;
    }

    /**
     * Actualiza el campo products.stock después de que se confirma el pago de una compra
     * 2021-05-03
     */
    function substract_stock($order_id)
    {
        $products = $this->products($order_id);
        
        foreach ( $products->result() as $row_product ) {
            $sql = "UPDATE products SET stock = stock - {$row_product->quantity} WHERE id = {$row_product->product_id}";
            $this->db->query($sql);
        }
    }

// COSTOS DE ENVÍO
//-----------------------------------------------------------------------------

    /**
     * Actualiza el valor del costo de una compra
     * Agrega o edita el registro en la tabla order_product
     */
    function update_shipping_cost($order_id)
    {
        $data = array('status' => 0, 'saved_id' => 0, 'shipping_info' => array());

        $row_order = $this->Db_model->row_id('orders', $order_id);
        
        //Se actualiza si la ciudad destino ya está definida
        if ( $row_order->city_id > 0 ) 
        {
            //Guardar costo de envío, tabla order_product
                $this->load->model('Shipping_model');
                $shipping_info = $this->Shipping_model->shipping_info(909, $row_order->city_id, $row_order->total_weight);

                $saved_id = $this->save_shipping_cost($row_order->id, $shipping_info);

            //Actualizar método de envío, tabla orders
                $arr_row['shipping_method_id'] = $shipping_info['method_id'];
                $this->db->where('id', $row_order->id)->update('orders', $arr_row);

            //Actualizar resultado
            if ( $saved_id > 0 )
            {
                $data = array('status' => 1, 'saved_id' => $saved_id, 'shipping_info' => $shipping_info);
            }
        }

        return $data;
    }

    /**
     * Guardar registro de costos de envío en tabla order_product
     * 2021-04-21
     */
    function save_shipping_cost($order_id, $shipping_info)
    {
        //Construir registro
        $arr_row['order_id'] = $order_id;
        $arr_row['product_id'] = 1;   //COD 1, corresponde a flete, ver Ajustes > Parámetros > Extras pedidos
        $arr_row['type_id'] = 2;       //No es un producto (1), es un elemento extra (2)
        $arr_row['price'] = $shipping_info['cost'];
        $arr_row['quantity'] = 1;      //Un envío
        $arr_row['cost'] = 0;          //No aplica
        $arr_row['tax'] = 0;           //No aplica

        //Guardar
        $condition = "order_id = {$order_id} AND product_id = {$arr_row['product_id']} AND type_id = {$arr_row['type_id']}";
        $saved_id = $this->Db_model->save('order_product', $condition, $arr_row);

        return $saved_id;
    }

// CÁLCULO Y ACTUALIZACIÓN DE TOTALES
//-----------------------------------------------------------------------------

    function update_totals($order_id)
    {
        $this->update_products_totals($order_id);
        $this->update_shipping_cost($order_id);
        $this->update_extras_totals($order_id);
    }

    /**
     * Actualiza los valores numéricos totales del pedido, a partir de los datos detallados en la tabla
     * order_product.
     * 2021-04-23
     */
    function update_products_totals($order_id)
    {
        
        $arr_row = $this->order_totals_1($order_id);    //Productos, impuestos y peso

        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $order_id)->update('orders', $arr_row);
    }

    /**
     * Actualiza los valores numéricos totales del pedido, a partir de los datos detallados en la tabla
     * order_product.
     * 2021-04-23
     */
    function update_extras_totals($order_id)
    {
        $row_order = $this->Db_model->row_id('orders', $order_id);

        $arr_row['total_extras'] = $this->total_extras($order_id);
        $arr_row['amount'] = $row_order->total_products + $arr_row['total_extras'];

        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $order_id)->update('orders', $arr_row);
    }

    /**
     * Array, totales para tabla orders: total_products, total_tax y total_weight, a partir de datos en order_product
     * 2021-04-21
     */
    function order_totals_1($order_id)
    {
        //Valor inicial por defecto
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['total_products'] = 0;
        $arr_row['total_tax'] = 0;
        $arr_row['total_weight'] = 0;

        //Consulta para calcular totales
        $this->db->select('SUM(order_product.price * quantity) AS total_products, SUM(order_product.tax * quantity) AS total_tax, SUM(order_product.quantity * weight) as total_weight');
        $this->db->where('order_id', $order_id);
        $this->db->where('order_product.type_id', 1);  //Productos
        $this->db->join('products', 'products.id = order_product.product_id');
        $query = $this->db->get('order_product');

        if ( $query->num_rows() > 0 ) 
        {
            $arr_row['total_products'] = $query->row()->total_products;
            $arr_row['total_tax'] = $query->row()->total_tax;
            $arr_row['total_weight'] = ceil($query->row()->total_weight / 1000); //Convierte de gramos a Kg, y redondea hacia arriba;
        }

        return $arr_row;
    }

    /**
     * Calcula y devuelve sumatoria de extras (order_product, type 2), para actualizar orders.total_extras
     * 2021-04-13
     */
    function total_extras($order_id)
    {
        $total_extras = 0;
        
        $this->db->select('SUM(order_product.price * quantity) AS total_extras');
        $this->db->where('order_id', $order_id);
        $this->db->where('order_product.type_id', 2);  //Extras
        $query = $this->db->get('order_product');
        
        if ($query->num_rows() > 0 ) {
            $total_extras = $query->row()->total_extras;
        }
        
        return $total_extras;
    }

// DATOS DEL PEDIDO
//-----------------------------------------------------------------------------

    //Productos incluidos en un pedido
    function products($order_id)
    {
        $this->db->select('order_product.*, products.name, products.url_thumbnail, products.slug, products.stock');
        $this->db->join('products', 'products.id = order_product.product_id');
        $this->db->where('order_id', $order_id);
        $this->db->where('order_product.type_id', 1);  //Producto
        $products = $this->db->get('order_product');

        return $products;
    }

    /**
     * Elementos extras (no productos) en una compra, items categoría 6
     * 2021-04-22
     */
    function extras($order_id)
    {
        $this->db->select('order_product.*, items.item_name AS extra_name');
        $this->db->join('items', 'items.cod = order_product.product_id AND items.category_id = 6');
        $this->db->where('order_id', $order_id);
        $this->db->where('order_product.type_id', 2);  //Elementos extra
        $extras = $this->db->get('order_product');

        return $extras;
    }

    /**
     * Devuelve un elemento row, de una compra dado el código de la compra
     */
    function row_by_code($order_code) 
    {
        $row = null;

        $code_parts = explode('-', $order_code);
        if ( count($code_parts) == 2 )
        {
            $row = $this->Db_model->row('orders', "id = '{$code_parts[1]}' AND order_code = '{$order_code}'");
        }

        return $row;
    }

    /**
     * Query con posts confirmaciones de un pedido hechas por PayU
     * 2021-05-03
     */
    function confirmations($order_id)
    {
        $this->db->select('id, status, content_json,
            code AS reference_sale, slug AS value, excerpt AS response_message_pol, related_1 AS response_code_pol, 
            related_2 AS payment_method_id, cat_1 AS merchant_id, cat_2 AS state_pol, text_1 AS sign,
            text_2 AS currency, created_at, updated_at');
        $this->db->where('type_id', 54);
        $this->db->where('parent_id', $order_id);
        $this->db->order_by('id', 'DESC');
        $confirmations = $this->db->get('posts');    

        return $confirmations;
    }

// CHECKOUT PayU
//-----------------------------------------------------------------------------

    /**
     * Array con todos los datos para construir el formulario que se envía a PayU
     * para iniciar el proceso de pago.
     */
    function z_payu_form_data($order_id)
    {
        //Registro del pedido
        $row = $this->Db_model->row_id('orders', $order_id);

        //Construir array
            $data['merchantId'] = K_PUMI;
            $data['referenceCode'] = $row->order_code;
            $data['description'] = $row->description;
            $data['amount'] = $row->amount;
            $data['tax'] = $row->total_tax;
            $data['taxReturnBase'] = 0; //No tiene IVA
            $data['signature'] = $this->payu_signature($row);
            $data['accountId'] = K_PUAI;
            $data['currency'] = 'COP';  //Pesos colombianos
            $data['test'] = ( $this->input->get('test') == 1 ) ? 1 : 0;
            $data['buyerFullName'] = $row->buyer_name;
            $data['buyerEmail'] = $row->email;
            $data['shippingAddress'] = $row->address;
            $data['shippingCity'] = $row->city;
            $data['shippingCountry'] = 'CO';
            $data['telephone'] = $row->phone_number;
            $data['responseUrl'] = base_url('orders/result');
            $data['confirmationUrl'] = base_url('orders/confirmation_payu');

        return $data;
    }

    /**
     * Genera la firma que se envía en el Formulario para ir al pago en PayU
     */
    function z_payu_signature($row_order)
    {
        $signature_pre = K_PUAK;
        $signature_pre .= '~' . K_PUMI;
        $signature_pre .= '~' . $row_order->order_code;
        $signature_pre .= '~' . $row_order->amount;
        $signature_pre .= '~' . 'COP';
        
        return md5($signature_pre);
    }

    function payu_confirmation_signature($order_id, $row_confirmation)
    {
        $new_value = number_format($row_confirmation->value, 1, '.', '');   //
        $arr_response_pol = json_decode($row_confirmation->content_json, TRUE);

        $signature_pre = K_PUAK;
        $signature_pre .= '~' . $row_confirmation->merchant_id;
        $signature_pre .= '~' . $row_confirmation->reference_sale;
        $signature_pre .= '~' . $new_value;
        $signature_pre .= '~' . $row_confirmation->currency;
        $signature_pre .= '~' . $row_confirmation->state_pol;

        $confirmation_signature = $signature_pre;

        return md5($signature_pre);
    }

    /**
     * Tomar y procesar los datos POST que envía PayU a la página de confirmación
     * url_confirmacion >> 'orders/confirmation_payu'
     * 2021-05-03
     */
    function confirmation_payu()
    {   
        //Identificar Pedido
        $confirmation_id = 0;
        $order = $this->row_by_code($this->input->post('reference_sale'));

        if ( ! is_null($order) )
        {
            //Guardar array completo de confirmación en la tabla "meta"
                $row_confirmation = $this->save_confirmation($order);

            //Restar existencias si hay confirmación de pago y la compra no sido marcada como pagada aún (1)
                if ( $row_confirmation->status == 1 && $order->status != 1 )
                {
                    $this->substract_stock($order->id);     //Restar vendidos de cantidades disponibles
                }

            //Actualizar registro de la compra
                if ( ! is_null($row_confirmation) )
                {
                    $confirmation_id = $row_confirmation->id;
                    $this->update_status($order, $row_confirmation);
                }

            //Enviar mensaje a administradores de tienda y al comprador
                if ( ENV == 'production' ) { $this->email_buyer($order->id); } 
        }

        return $confirmation_id;
    }

    /**
     * Actualiza el estado de un pedido, dependiendo del código de respuesta en la 
     * confirmación
     */
    function update_status($order, $row_confirmation)
    {
        $arr_row['status'] = ( $row_confirmation->related_1 == 1 ) ? 1 : 5;
        $arr_row['response_code_pol'] = $row_confirmation->related_1;
        $arr_row['confirmed_at'] = date('Y-m-d H:i:s');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');
        $arr_row['updater_id'] = 100001;  //PayU Automático

        $this->db->where('id', $order->id);   //Parent ID = Order ID
        $this->db->update('orders', $arr_row);
    }

    function result_data()
    {
        $order_code = $this->input->get('referenceCode');
        $row = $this->row_by_code($order_code);

        $data = array('status' => 0, 'message' => 'Compra no identificada', 'success' => 0);
        $data['success'] = 0;
        $data['order_id'] = 0;
        $data['head_title'] = 'Pago no realizado';

        if ( ! is_null($row) )
        {
            $data['status'] = 1;
            $data['message'] = 'Resultado recibido';
            $data['order_id'] = $row->id;

            if ( $this->input->get('polResponseCode') == 1 )
            {
                $data['success'] = 1;
                $data['head_title'] = 'Pago exitoso';
            }
        }

        return $data;
    }

// Datos de compras asociados a usuarios
//-----------------------------------------------------------------------------

    /**
     * Pedidos realizados por el usuario, tabla orders.
     */
    function user_orders($user_id)
    {
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $orders = $this->db->get('orders');

        return $orders;
    }

// MENSAJES DE CORREO ELECTRÓNICO
//-----------------------------------------------------------------------------

    /**
     * Tras la confirmación PayU, se envía un mensaje de estado del pedido
     * al cliente
     */
    function email_buyer($order_id)
    {
        $row_order = $this->Db_model->row_id('orders', $order_id);
        $admin_email = $this->Db_model->field_id('sis_option', 25, 'option_value'); //Opción 25
            
        //Asunto de mensaje
            $subject = "Estado compra {$row_order->order_code}: " . $this->Item_model->name(7, $row_order->status);
        
        //Enviar Email
            $this->load->library('email');
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            $this->email->from('info@' . APP_DOMAIN, APP_NAME);
            $this->email->to($row_order->email);
            $this->email->bcc($admin_email);
            $this->email->subject($subject);
            $this->email->message($this->message_buyer($row_order));
            
            $this->email->send();   //Enviar
    }

    /**
     * String con contenido del mensaje del correo electrónico enviado al comprador
     * después de recibir la confirmación de pago
     */
    function message_buyer($row_order)
    {
        $data['row_order'] = $row_order;
        $data['products'] = $this->products($row_order->id);
        $data['extras'] = $this->extras($row_order->id);

        $str_style = file_get_contents(URL_RESOURCES . 'css/email.json');
        $data['style'] = json_decode($str_style);
        
        $message = $this->load->view($this->views_folder . 'emails/message_buyer_v', $data, TRUE);
        
        return $message;
    }
}