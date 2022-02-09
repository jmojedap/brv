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
     * 2022-01-25
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page = 10);
        
        //Elemento de exploración
            $data['controller'] = 'orders';                      //Nombre del controlador
            $data['cf'] = 'orders/explore/';                      //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'explore/';           //Carpeta donde están las vistas de exploración
            
        //Vistas
            $data['head_title'] = 'Ventas y pagos';
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Obtener listado de orders, según filtros de búsqueda, paginado, con 
     * datos adicionales para representación en interfaz
     * 2022-01-25
     */
    function get($filters, $num_page, $per_page)
    {
        //Referencia
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Búsqueda y Resultados
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['filters'] = $filters;
            $data['list'] = $elements->result();
            $data['str_filters'] = $this->Search_model->str_filters();
            $data['search_num_rows'] = $this->search_num_rows($data['filters']);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de usuarios
     * 2021-12-28
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'orders.*';
        $arr_select['personal'] = 'orders.id, order_code, bill, amount, confirmed_at';
        $arr_select['export'] = 'orders.id AS ID_venta, orders.status, order_code AS ref_venta, buyer_name AS nombre_comprador, 
            email, document_number AS numero_documento, phone_number AS telefono, address AS direccion, 
            notes_admin AS notas_internas, bill AS numero_factura, total_tax AS impuestos, 
            total_extras AS otros_cobros, amount AS total_venta, payed AS pagado, 
            payment_channel AS canal_pago, wompi_id, wompi_status, 
            wompi_payment_method_type AS wompi_tipo_metodo_pago, confirmed_at AS fecha_confirmado, 
            user_id AS ID_usuario, orders.created_at AS fecha_creado, orders.updater_id AS ID_actualizado_por, 
            orders.updated_at AS fecha_actualizado,
            order_product.product_id AS ID_producto, products.name AS nombre_producto, products.code AS referencia_producto, order_product.quantity AS cantidad_producto';

        return $arr_select[$format];
    }
    
    /**
     * String con condición WHERE SQL para filtrar post
     */
    function search_condition($filters)
    {
        $condition = NULL;
        
        //Tipo de post
        if ( $filters['status'] != '' ) { $condition .= "status = {$filters['status']} AND "; }
        if ( $filters['fe3'] != '' ) { $condition .= "payment_channel = {$filters['fe3']} AND "; }
        if ( $filters['u'] != '' ) { $condition .= "user_id = {$filters['u']} AND "; }
        if ( $filters['d1'] != '' ) { $condition .= "orders.confirmed_at >= '{$filters['d1']} 00:00:00' AND "; }
        if ( $filters['d2'] != '' ) { $condition .= "orders.confirmed_at <= '{$filters['d2']} 23:59:59' AND "; }
        
        if ( strlen($condition) > 0 )
        {
            $condition = substr($condition, 0, -5);
        }
        
        return $condition;
    }
    
    /**
     * Query con la búsqueda de orders, página específica
     * 2022-01-25
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {   
        $role_filter = $this->role_filter();

        //Construir consulta
            $select_format = 'general';
            if ( $filters['sf'] ) $select_format = $filters['sf'];
            $this->db->select($this->select($select_format));
        
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
                $this->db->order_by('id', 'DESC');
                $this->db->order_by('updated_at', 'DESC');
                $this->db->order_by('confirmed_at', 'DESC');
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

    /**
     * Query para exportar
     * 2021-09-27
     */
    function query_export($filters)
    {
        $this->db->select($this->select('export'));
        $this->db->join('order_product', 'orders.id = order_product.order_id', 'left');
        $this->db->join('products', 'order_product.product_id = products.id', 'left');
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('orders', 10000);  //Hasta 10.000 registros

        return $query;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Eliminación de una venta
     * 2021-12-09
     */
    function delete($order_id)
    {
        $this->db->where('id', $order_id);
        $this->db->delete('orders');
        
        $qty_deleted = $this->db->affected_rows();

        /*
        if ( $qty_deleted > 0 ) {
            //Procesos relacionados
        }*/
    
        return $qty_deleted;
    }

    /**
     * Actualiza los datos de una compra desde la edición de la administración
     * 2021-12-10
     */
    function admin_update($arr_row = null)
    {
        if ( is_null($arr_row) ) { $arr_row = $this->input->post(); }
        
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        //Actualizar registro orders
        $saved_id = $this->Db_model->save_id('orders', $arr_row);
    
        return $saved_id;
    }

    /**
     * Crear una compra, la tabla orders
     */
    function create($user_id)
    {
        $data = array('order_id' => 0, 'order_code' => NULL);

        //Datos por defecto
            $arr_row['city_id'] = 0;        //Cali, ver tabla places
            $arr_row['country_id'] = 51;    //Colombia, ver tabla places
            $arr_row['address'] = '';
            $arr_row['created_at'] = date('Y-m-d H:i:s');
            $arr_row['updated_at'] = date('Y-m-d H:i:s');

        //Datos de usuario
            $row_user = $this->Db_model->row_id('users', $user_id);
            if ( ! is_null($row_user) )
            {
                $arr_row['buyer_name'] = $row_user->first_name . ' ' . $row_user->last_name;
                $arr_row['email'] = $row_user->email;
                $arr_row['phone_number'] = $row_user->phone_number;
                $arr_row['address'] = $row_user->address;
                $arr_row['user_id'] = $row_user->id;
                $arr_row['document_number'] = $row_user->document_number;
                $arr_row['document_type'] = $row_user->document_type;
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
     * Genera y establece un código único para un pedido. Campo orders.order_code
     * 2019-06-17
     */
    function set_order_code($order_id)
    {
        $this->load->helper('string');
        
        $order_code = 'B' . strtoupper(random_string('alpha', 3)) . '-' . $order_id;

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
        $data = array('status' => 0, 'op_id' => 0, 'qty_items' => NULL, 'order_id' => $order_id);

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
     * 2021-12-17
     */
    function substract_stock($order_id)
    {
        $products = $this->products($order_id);
        
        foreach ( $products->result() as $row_product ) {
            $sql = "UPDATE products";
            $sql .= " SET stock = stock - {$row_product->quantity}";
            $sql .= " WHERE id = {$row_product->product_id}";   //El producto correspondiente
            $sql .= " AND weight > 0";                          //Es un producto físico
            $this->db->query($sql);
        }
    }

    /**
     * Reestablecer campo products.stock cuando se reversa el pago de una venta
     * Se devuelven cantidades al inventario.
     * 2021-12-17
     */
    function reset_stock($order_id)
    {
        $products = $this->products($order_id);
        
        foreach ( $products->result() as $row_product ) {
            $sql = "UPDATE products";
            $sql .= " SET stock = stock + {$row_product->quantity}";
            $sql .= " WHERE id = {$row_product->product_id}";   //El producto correspondiente
            $sql .= " AND weight > 0";                          //Es un producto físico
            $this->db->query($sql);
        }
    }

// COSTOS DE ENVÍO
//-----------------------------------------------------------------------------

    /**
     * Actualiza el valor del costo de envío de una compra
     * Agrega o edita el registro en la tabla order_product
     */
    function update_shipping_cost($order_id)
    {
        $data = array('status' => 0, 'saved_id' => 0, 'shipping_info' => array());

        $row_order = $this->Db_model->row_id('orders', $order_id);
        
        //Se actualiza solo si la ciudad destino ya está definida
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

    /**
     * Productos incluidos en una compra
     * 2022-01-05
     */
    function products($order_id)
    {
        $this->db->select(
                'order_product.*, products.name, products.url_thumbnail, 
                products.slug, products.stock, products.code, products.cat_1,
                products.integer_1'
            );
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
     * Query con posts confirmaciones de un pedido hechas por Wompi
     * 2021-05-03
     */
    function confirmations($order_id)
    {
        $this->db->select('id, status, content_json,
            code AS reference_sale, slug AS value, excerpt AS response_message_pol, related_1 AS response_code_pol, 
            related_2 AS payment_method_id, cat_1 AS merchant_id, cat_2 AS state_pol, text_1 AS sign,
            text_2 AS currency, created_at, updated_at');
        $this->db->where('type_id', 54);    //54: Post confirmación de pago
        $this->db->where('parent_id', $order_id);
        $this->db->order_by('id', 'DESC');
        $confirmations = $this->db->get('posts');    

        return $confirmations;
    }

    /**
     * Datos faltantes para poder ir a pagar
     * 2021-11-18
     */
    function missing_data($order)
    {
        $missing_data = array();
        if ( strlen($order->document_number) == 0 ) { $missing_data[] = 'Número de documento'; }
        if ( strlen($order->email) == 0 ) { $missing_data[] = 'Correo electrónico'; }
        if ( strlen($order->address) == 0 && $order->total_weight > 0 ) { $missing_data[] = 'Dirección de entrega'; }
        if ( strlen($order->phone_number) == 0 ) { $missing_data[] = 'Número de celular'; }

        return $missing_data;
    }

// REGISTRO DE PAGOS NO AUTOMÁTICOS
//-----------------------------------------------------------------------------

    /**
     * Establecer una venta como pagada.
     * 2021-11-18
     */
    function set_payed($order_id, $arr_row)
    {
        //Estado inicial de la venta, registro order
        $order_pre = $this->Db_model->row_id('orders', $order_id);

        // Resultado por defecto
        $data = array('saved_id' => 0, 'message' => 'Venta no actualizada', 'payed_updated' => 0);

        // Actualizar
        $arr_row['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $order_id);
        $this->db->update('orders', $arr_row);
        
        //Verificar resultado
        if ( $this->db->affected_rows() > 0 ) {
            $data['saved_id'] = $order_id;
            $data['message'] = 'Pago actualizado';

            // Si el estado de pago se modificó respecto al inicial
            if ( $order_pre->payed != $arr_row['payed'] ) {
                $this->payment_updated($order_id, $arr_row['payed']);

                $data['payed_updated'] = 1;
                $data['message'] .= '. Se modificó como Pagada.';
            }
        }

        return $data;
    }

    /**
     * Actualizar los pago de una venta, como No Pagado
     * 2021-11-22
     */
    function remove_payment()
    {
        $data = array('saved_id' => 0, 'message' => 'La información de pago de la venta no se actualizó');

        $arr_row = $this->input->post();
        $arr_row['payment_channel'] = 0;
        $arr_row['payed'] = 0;
        $arr_row['status'] = 10;  //Iniciado
        $arr_row['bill'] = '';
        $arr_row['confirmed_at'] = NULL;

        $saved_id = $this->admin_update($arr_row);

        if ( $saved_id > 0 ) {
            $this->payment_updated($arr_row['id'], $arr_row['payed']);
            $data = array('saved_id' => $saved_id, 'message' => 'El pedido se actualizó como NO PAGADO');
        }

        return $data;
    }

    /**
     * Procesos automatizados tras la confirmación de un pago.
     * 2021-12-16
     */
    function payment_updated($order_id, $payed)
    {
        //Descontar cantidades de producto.cant_disponibles, si el pedido fue pagado = 1: Pagado
        if ( $payed == 1 )
        {
            $this->substract_stock($order_id);     //Restar vendidos de cantidades disponibles

            //Actualizar susbcripción de usuario, si tiene productos de este tipo
            $this->load->model('Subscription_model');
            $this->Subscription_model->update_from_order($order_id);
            $this->Subscription_model->update_for_partner($order_id);   //También a beneficiarios

            //Enviar e-mails a administradores de tienda y al cliente
            if ( ENV == 'production' )
            {
                $this->Order_model->email_buyer($order_id);
            }
        } else {
            //Reestablecer inventario, sumar cantidades disponibles
            $this->reset_stock($order_id);
        }
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
            $subject = "Pago {$row_order->order_code}: " . $this->Item_model->name(7, $row_order->status);
        
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

        $this->load->model('Notification_model');
        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = $this->views_folder . 'emails/message_buyer_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }

// TEMPORAL CARGUE PAGOS HISTORICOS
//-----------------------------------------------------------------------------

    function cph_arr_row($user, $post)
    {
        $arr_row['status'] = 1; //Pagado
        $arr_row['buyer_name'] = $user->first_name . ' ' . $user->last_name;
        $arr_row['email'] = $user->email;
        $arr_row['document_number'] = $user->document_number;
        $arr_row['document_type'] = $user->document_type;
        $arr_row['address'] = $user->address;
        $arr_row['phone_number'] = $user->phone_number;
        $arr_row['description'] = 'CargueMasivo20220208';
        $arr_row['notes_admin'] = $post->content;
        $arr_row['amount'] = $post->integer_1;
        $arr_row['payed'] = 1;
        $arr_row['payment_channel'] = $post->related_2;
        $arr_row['confirmed_at'] = $post->published_at;
        $arr_row['created_at'] = $post->published_at;
        $arr_row['updated_at'] = $post->published_at;
        $arr_row['updater_id'] = 202024;
        $arr_row['creator_id'] = 202024;

        $arr_row['user_id'] = $user->id;

        return $arr_row;
    }

    function cph_add_product($product_id, $price = 0, $order_id)
    {
        $data = array('status' => 0, 'op_id' => 0, 'qty_items' => NULL, 'order_id' => $order_id);

        //Construir registro order_product
        $arr_row['order_id'] = $order_id;
        $arr_row['product_id'] = $product_id;
        $arr_row['original_price'] = $price;
        $arr_row['price'] = $price;
        $arr_row['quantity'] = 1;

        //Guardar registro en order_product
        $condition = "order_id = {$arr_row['order_id']} AND product_id = {$arr_row['product_id']} AND type_id = 1";
        $data['op_id'] = $this->Db_model->save('order_product', $condition, $arr_row);

        //Actualizar totales de la compra
        $this->update_totals($order_id);
        
        //Actualizar resultado respuesta
        $data['status'] = ($data['op_id'] > 0 ) ? 1 : 0 ;

        return $data;
    }

}