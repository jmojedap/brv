<?php
class Subscription_model extends CI_Model{

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Guardar evento tipo user susbscription
     * 2021-12-23
     */
    function save($arr_row)
    {
        $arr_row['period_id'] = $this->pml->date_format($arr_row['end'], 'Ym');
        $arr_row['type_id'] = 121;  //Post tipo suscripción

        $condition_add = "period_id = {$arr_row['period_id']}";

        $this->load->model('Event_model');
        $subscription_id = $this->Event_model->save($arr_row, $condition_add, $arr_row);

        return $subscription_id;
    }

    /**
     * Eliminar un registro suscripción, tabla events, type 121
     * 2021-12-22
     */
    function delete($subscription_id, $user_id)
    {
        $this->db->where('id', $subscription_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('events');
        
        $qty_deleted = $this->db->affected_rows();

        return $qty_deleted;
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    /**
     * Query suscripciones del usuario, eventos typo 121
     * 2021-12-22
     */
    function user_susbscriptions($user_id)
    {
        $select = 'id, period_id, start, end, user_id, related_1 AS order_id,
                    related_3 AS subscription_type, content, created_at';

        $this->db->select($select);
        $this->db->where('type_id', 121);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('end', 'DESC');
        $subscriptions = $this->db->get('events');

        return $subscriptions;
    }

    /**
     * Categorías de productos que son suscripciones a los servicios de Brave
     * Filtro para campo products.cat_1
     * 2022-01-03
     */
    function subscription_product_categories($format = 'condition')
    {
        $categories = array(2110,2150);
        if ($format == 'array') {
            return $categories;
        } else if ( $format == 'string' ){
            return implode(',',$categories);
        } else {
            //Condición SQL para tabla products
            return 'cat_1 IN (' . implode(',',$categories) . ')';
        }
    }

    /**
     * Query con productos tipo suscripción a servicios Brave
     * 2022-01-04
     */
    function products()
    {
        $condition = $this->subscription_product_categories();

        $this->db->select('id, code, name, price, integer_1 AS for_partners');
        $this->db->where($condition);
        $this->db->order_by('code', 'ASC');
        $products = $this->db->get('products');

        return $products;
    }

    /**
     * Array con productos tipo subscripción servicios Brave
     * 2022-01-03
     */
    function subscription_products_array()
    {
        $condition = $this->subscription_product_categories();

        $this->db->select('id, code');
        $this->db->where($condition);
        $this->db->order_by('code', 'ASC');
        $query = $this->db->get('products');

        $products = $this->pml->query_to_array($query, 'code', 'id');

        return $products;
    }

// PROCESOS
//-----------------------------------------------------------------------------

    /**
     * Crea o actualiza suscripciones de usuario a partir de un pago (order_id)
     * realizado
     * 2021-12-23
     */
    function update_from_order($order_id)
    {
        $order = $this->Db_model->row_id('orders', $order_id);
        $this->load->model('Order_model');
        $products = $this->Order_model->products($order_id);

        // Array con categorías de productos que implican suscripción
        $subscription_product_categories = $this->subscription_product_categories('array');

        foreach ($products->result() as $product) {
            if ( in_array($product->cat_1, $subscription_product_categories) ) {
                $this->create($order->user_id, $order->id, 1);
            }
        }
    }

    /**
     * Revisa si entre los productos de una compra hay uno con beneficio de
     * partner (products.integer_1 = 1) , si el usuario comprador tiene 
     * beneficiario (users.partner_id > 0) se le crea suscripción también 
     * a este usuario beneficiario.
     * 2022-01-27
     */
    function update_for_partner($order_id)
    {
        $order = $this->Db_model->row_id('orders', $order_id);
        $user = $this->Db_model->row_id('users', $order->user_id);

        if ($user->partner_id > 0)
        {
            $this->load->model('Order_model');
            $products = $this->Order_model->products($order_id);
    
            foreach ($products->result() as $product) {
                //products.integer_1 indica que es producto de subscripción
                //products.quantity == 2, indica que hizo el pago por los dos usuarios
                if ( $product->integer_1 == 1 && $product->quantity == 2 ) {
                    $this->create($user->partner_id, $order->id, 1);
                }
            }
        }
    }

    /**
     * Crear una suscripción a un usuario por un número de meses, a partir de
     * su fecha de vencimiento actual: users.expiration_at
     * 2021-12-23
     */
    function create($user_id, $order_id = 0, $qty_months = 1)
    {
        $subscription_id = 0;
        $user = $this->Db_model->row_id('users', $user_id);
        
        if ( ! is_null($user) ) {
            
            $order = $this->Db_model->row_id('orders', $order_id);
            if ( ! is_null($order) ) $content = 'Cód. venta: ' . $order->order_code . '. ';
            
            $content = '';  //events.content, valor por defecto
            if ( strlen($user->expiration_at) > 0 ) {
                // Si ya tiene fecha
                $content .= "Fecha anterior de vencimiento: {$user->expiration_at}";
            }

            $arr_row['content'] = $content;
            $arr_row['user_id'] = $user_id;
            $arr_row['element_id'] = $user_id;
            $arr_row['start'] = $this->pml->date_add($user->expiration_at, '+1 days');
            $arr_row['end'] = $this->subscription_end_date($user->expiration_at, $qty_months);
            $arr_row['related_1'] = $order_id;

            $data['subscription_id'] = $this->save($arr_row);

            if ( $data['subscription_id'] > 0 ) {
                $data['expiration_update'] = $this->update_user_expiration_at($user_id);
            }
        }

        return $data;
    }

    /**
     * Devuelve fecha en formato Y-m-d, calculada a partir de una fecha actual
     * de vencimiento
     * 2022-01-26
     */
    function subscription_end_date($current_expiration_at, $qty_months = 1)
    {
        if ( strlen($current_expiration_at) == 0 ) { 
            $current_expiration_at = date('Y-m-d');
        }
            
        $start_date = $this->pml->date_add($current_expiration_at, '+1 days');
        $end_date = $this->pml->date_add_months($start_date, $qty_months);
        $end_date = $this->pml->date_add($end_date, '-1 seconds');

        return $end_date;
    }

    /**
     * Actualizar campo users.expiration_at según suscripciones del usuario
     * 2021-12-22
     */
    function update_user_expiration_at($user_id)
    {
        $data = array('status' => 0, 'expiration_at' => '');

        $subscriptions = $this->user_susbscriptions($user_id);

        if ( $subscriptions->num_rows() > 0 ) {
            $last_subscription = $subscriptions->row(0);

            $arr_row['expiration_at'] = $last_subscription->end;
            
            $this->db->where('id', $user_id);
            $this->db->update('users', $arr_row);

            if ( $this->db->affected_rows() > 0 ) {
                $data['status'] = 1;
                $data['expiration_at'] = $arr_row['expiration_at'];
            }
        }

        return $data;
    }

// Generación de órdenes de pago
//-----------------------------------------------------------------------------

    /**
     * Crear una orden de pago y dejarla lista para que sea pagada por el usuario
     * PENDIENTE ELIMINACIÓN
     * 2022-01-04
     */
    /*function create_order($creation_data)
    {
        $user = $this->Db_model->row_id('users', $creation_data['user_id']);
        $product = $this->Db_model->row_id('products', $creation_data['product_id']);

        //Si existe el usuario y el producto
        if ( ! is_null($user) && ! is_null($product) ) {
            //Crear order
            $this->load->model('Order_model');
            $data_order = $this->Order_model->create($user->id);

            // Si order fue creada
            if ( $data_order['order_id'] > 0 ) {
                //Agregar producto
                $product_id = $this->input->post('product_id');
                $quantity = $this->input->post('quantity');

                $data_product = $this->Order_model->add_product($product->id, 1, $data_order['order_id']);

                //Datos complementarios
                $arr_row['period_id'] = $creation_data['period_id'];
            }

        }
    }*/

}