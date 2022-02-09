<?php
class Wompi_model extends CI_Model{

    function form_destination()
    {
        //Formulario destino
        $form_destination = 'https://checkout.wompi.co/p/';

        return $form_destination;
    }

    /**
     * Array con todos los datos para construir el formulario que se envía a Wompi
     * para iniciar el proceso de pago.
     */
    function form_data($order)
    {
        //Construir array
            $data['public-key'] = K_WPPK;
            $data['reference'] = $order->order_code;
            $data['description'] = $order->description;
            $data['amount-in-cents'] = intval($order->amount * 100);
            $data['currency'] = 'COP';  //Pesos colombianos
            $data['redirect-url'] = URL_APP . ('suscripciones/resultado_pago');

        return $data;
    }

    /**
     * Tomar y procesar los datos POST que envía Wompi a la url de eventos
     * 2021-05-11
     * url de eventos >> 'orders/confirmation_wompi'
     */
    function confirmation()
    {   
        //Identificar Pedido
        $confirmation_id = 0;
        $wompi_response = $this->wompi_response();
        $order = $this->Order_model->row_by_code($wompi_response->data->transaction->reference);

        if ( ! is_null($order) )
        {
            //Guardar respuesta de wompi en la tabla "post"
                $confirmation_id = $this->save_confirmation($order, $wompi_response);

            //Actualizar estado registro en la tabla orders
                $order_status = $this->update_status($order->id, $wompi_response);

                if ( $order_status == 1 ) {
                    $this->load->model('Order_model');
                    $this->Order_model->payment_updated($order->id, 1);
                }

            //Enviar mensaje a administradores de tienda y al cliente
                //if ( ENV == 'production' ) { $this->Order_model->email_buyer($order->id); } 
        }

        return $confirmation_id;
    }

    /**
     * Crea un registro en la tabla post, con los datos recibidos tras en la 
     * ejecución de la URL de eventos por parte de Wompi
     */
    function save_confirmation($row, $wompi_response)
    {
        //Datos Wompi en formato JSON
            $json_confirmation_wompi = json_encode($wompi_response);
        
        //Construir registro para tabla Post
            $arr_row['type_id'] = 54;  //54: Respuesta resultado transacción de pago, Ver: items.category_id = 33
            $arr_row['code'] = $row->order_code;
            $arr_row['content'] = 'Se ejecutó un evento desde Wompi';
            $arr_row['post_name'] = 'Respuesta Wompi ' . $wompi_response->data->transaction->reference;
            $arr_row['excerpt'] = $wompi_response->data->transaction->status;
            $arr_row['content_json'] = $json_confirmation_wompi;
            $arr_row['status'] = ( $wompi_response->data->transaction->status == 'APPROVED' ) ? 1 : 0;
            $arr_row['parent_id'] = $row->id;
            $arr_row['date_1'] = date('Y-m-d H:i:s');
            $arr_row['text_1'] = $wompi_response->signature->checksum;
            $arr_row['text_2'] = $wompi_response->ip_address;
            $arr_row['creator_id'] = 105;    //Wompi internal user
            $arr_row['updater_id'] = 105;     //Wompi internal user
            $arr_row['updated_at'] = date('Y-m-d H:i:s');
            $arr_row['created_at'] = date('Y-m-d H:i:s');
        
        //Guardar, id = 0, condición imposible para siempre insertar
            $confirmation_id =$this->Db_model->save('posts', 'id = 0', $arr_row);
        
        return $confirmation_id;
    }

    /**
     * Objeto con respuesta JSON que Wompi envía a la URL de eventos
     * 2020-12-09
     */
    function wompi_response()
    {
        $input_wompi = file_get_contents('php://input');
        $wompi_response = json_decode($input_wompi);
        $wompi_response->ip_address = $this->input->ip_address();

        return $wompi_response;
    }

    /**
     * Array con respuestas de wompi a un order
     * 2021-03-29
     */
    function responses($order_id)
    {
        $this->db->select('id, excerpt AS wompi_status, created_at, content_json');
        $this->db->where('type_id', 54);    //Respuesta de Wompi
        $this->db->where('parent_id', $order_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('posts');

        $responses = array();
        foreach ($query->result() as $row)
        {
            $response = json_decode($row->content_json);
            $response->response_id = $row->id;
            $response->response_created_at = $row->created_at;

            $responses[] = $response;
        }
    
        return $responses;
    }

    /**
     * Actualiza el estado de una venta, dependiendo de la respuesta wompi
     * 2022-01-27
     */
    function update_status($order_id, $wompi_response)
    {
        $arr_row['status'] = ( $wompi_response->data->transaction->status == 'APPROVED' ) ? 1 : 5;
        $arr_row['payed'] = ( $wompi_response->data->transaction->status == 'APPROVED' ) ? 1 : 0;
        $arr_row['payment_channel'] = 12; //12 es wompi, Ver items.category_id = 106
        $arr_row['wompi_status'] = $wompi_response->data->transaction->status;
        $arr_row['wompi_id'] = $wompi_response->data->transaction->id;
        $arr_row['wompi_payment_method_type'] = $wompi_response->data->transaction->payment_method_type;
        $arr_row['confirmed_at'] = date('Y-m-d H:i:s', $wompi_response->timestamp);
        $arr_row['updated_at'] = date('Y-m-d H:i:s', $wompi_response->timestamp);
        $arr_row['updater_id'] = 1001;  //Wompi Automático

        $this->db->where('id', $order_id);   //Parent ID = Order ID
        $this->db->update('orders', $arr_row);

        return $arr_row['status'];
    }

    /**
     * Datos resultado del pago
     * 2020-12-05
     */
    function result_data()
    {
        $wompi_id = $this->input->get('id');
        $data = array('status' => 0, 'message' => 'Compra no identificada', 'success' => 0, 'order_code' => null);
        $result = NULL;

        //Requerir datos de API Wompi
        $url_wompi_transaction = "https://sandbox.wompi.co/v1/transactions/{$wompi_id}";
        $json_wompi = $this->pml->get_url_content($url_wompi_transaction);

        if ( $json_wompi )
        {
            $wompi = json_decode($json_wompi);
            $result = $wompi->data;    
            //Idenficar registro de Order
            $row = $this->Order_model->row_by_code($result->reference);
    
            $data['success'] = 0;
            $data['order_id'] = 0;
            $data['head_title'] = 'Pago no realizado';
            
            if ( ! is_null($row) )
            {
                $data['status'] = 1;
                $data['message'] = 'Resultado recibido';
                $data['order_id'] = $row->id;
                $data['order_code'] = $row->order_code;
    
                if ( $result->status == 'APPROVED' )
                {
                    $data['success'] = 1;
                    $data['head_title'] = 'Pago aprobado';
                }
            }
            $data['result'] = $result;

            //Actualizar registro tabla orders
            $data['order_updating'] = $this->update_wompi_status($row->id, $result);
        }

        return $data;
    }

    /**
     * Actualiza los campos de la tabla orders, relacionados con la información de wompi
     * 2020-12-05
     */
    function update_wompi_status($order_id, $result)
    {
        $data = array('status' => 0, 'qty_affected' => 0);

        $arr_row['wompi_status'] = $result->status;
        $arr_row['wompi_payment_method_type'] = $result->payment_method->type;
        $arr_row['wompi_id'] = $result->id;
        $arr_row['confirmed_at'] = $result->created_at;
        $arr_row['status'] = 5;

        if ( $result->status == 'APPROVED' ) { $arr_row['status'] = 1; }    //Si es APPROVED, se marca como Pago confirmado

        $this->db->where('id', $order_id);
        $this->db->update('orders', $arr_row);
        
        $data['qty_affected'] = $this->db->affected_rows();

        if ( $data['qty_affected'] > 0 ) { $data['status'] = 1;}

        return $data;
    }
}