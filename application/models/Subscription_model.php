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
                    related_3 AS subscription_type, created_at';

        $this->db->select($select);
        $this->db->where('type_id', 121);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('end', 'DESC');
        $subscriptions = $this->db->get('events');

        return $subscriptions;
    }

// PROCESOS
//-----------------------------------------------------------------------------

    /**
     * Crea o actualiza suscripciones de usuario a partir de una compra o pago
     * realizado
     * 2021-12-23
     */
    function update_from_order($order_id)
    {
        $order = $this->Db_model->row_id('orders', $order_id);
        $this->load->model('Order_model');
        $products = $this->Order_model->products($order_id);

        // Array con categorías de productos que implican suscripción
        $subscription_product_categories = array(2110);

        foreach ($products->result() as $product) {
            if ( in_array($product->cat_1, $subscription_product_categories) ) {
                $this->create($order->user_id, $order->id, 1);
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

        // Tiene fecha
        if ( strlen($user->expiration_at) > 0 ) {
            $start_date = $this->pml->date_add($user->expiration_at, '+1 days');
            $end_date = $this->pml->date_add_months($start_date, $qty_months);
            $end_date = $this->pml->date_add($end_date, '-1 seconds');

            $arr_row['user_id'] = $user_id;
            $arr_row['element_id'] = $user_id;
            $arr_row['start'] = $start_date;
            $arr_row['end'] = $end_date;
            $arr_row['related_1'] = $order_id;
            $arr_row['content'] = "Fecha anterior de vencimiento: {$user->expiration_at}";

            $data['subscription_id'] = $this->save($arr_row);

            if ( $data['subscription_id'] > 0 ) {
                $data['expiration_update'] = $this->update_user_expiration_at($user_id);
            }
        }

        return $data;
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
}