<?php
class Message_model extends CI_Model{

    function basic($conversation_id)
    {
        $data['row'] = $this->Db_model->row_id('conversations', $conversation_id);
        $data['head_title'] = $data['row']->subject;

        return $data;
    }

// Conversation Application
//-----------------------------------------------------------------------------

    /**
     * Array con conversaciones en las que participa el usuario en sesión.
     * 2019-09-23
     */
    function conversations($num_page)
    {
        $this->db->select('conversations.*, users_meta.related_2 AS element_id');
        $this->db->join('users_meta', 'conversations.id = users_meta.related_1');
        $this->db->where('users_meta.user_id', $this->session->userdata('user_id'));
        $this->db->order_by('updated_at', 'DESC');
        $query = $this->db->get('conversations');

        foreach ($query->result_array() as $row_conversation)
        {
            $conversation_meta = $this->conversation_meta($row_conversation);
            $conversation = array_merge($row_conversation, $conversation_meta);

            $conversations[] = $conversation;
        }

        return $conversations;
    }

    /**
     * Metadatos para completar el registro de la conversación
     * 2019-09-23
     */
    function conversation_meta($row_conversation)
    {
        $conversation_meta['title'] = $this->Db_model->field_id('users', $row_conversation['element_id'], 'display_name');

        return $conversation_meta;
    }

    /**
     * Crea una conversación individual (tipo 1), agrega al usuario en sesión y al usuario
     * relacionado (tabla users_meta)
     * 2019-09-23
     */
    function create_conversation($user_id)
    {
        $arr_row['type_id'] = 1; //1: Conversación entre dos usuarios
        $arr_row['related_id'] = $user_id;  //Usuario al que se le envía el mensaje
        $arr_row['creator_id'] = $this->session->userdata('user_id');   //Usuario que inicia la conversación

        $condition = "type_id = 1 AND related_id = {$arr_row['related_id']} AND creator_id = {$arr_row['creator_id']}";
        $conversation_id = $this->Db_model->save('conversations', $condition, $arr_row);

        //Agregar usuario en sesión
        $meta_1 = $this->add_user($conversation_id, $this->session->userdata('user_id'), $user_id);

        //Agregar al usurio relacionado
        $meta_2 = $this->add_user($conversation_id, $user_id, $this->session->userdata('user_id'));

        return $conversation_id;
    }

    /**
     * Agrega un usuario a una conversación, y el elemento con el cual está relacionada la conversación
     * ya sea un otro usuario o un grupo.
     * 2019-09-23
     */
    function add_user($conversation_id, $user_id, $related_id)
    {
        $arr_row['user_id'] = $user_id;
        $arr_row['type_id'] = 10;   //Usuario en conversación individual
        $arr_row['related_1'] = $conversation_id;
        $arr_row['related_2'] = $related_id;    //Usuario con el cual está relacionado
        $arr_row['creator_id'] = $this->session->userdata('user_id');
        $arr_row['updater_id'] = $this->session->userdata('user_id');

        $condition = $this->Db_model->condition($arr_row, array('user_id', 'type_id', 'related_1'));
        $meta_id = $this->Db_model->save('users_meta', $condition, $arr_row);

        return $meta_id;
    }

    /**
     * Guarda el mensaje en la tabla message, y luego lo envía a los usuarios participantes
     * en la conversación.
     * 2019-09-24
     */
    function send_message($conversation_id)
    {
        //Crear registro
            $arr_row['conversation_id'] = $conversation_id;
            $arr_row['text'] = $this->input->post('text');
            $arr_row['user_id'] = $this->session->userdata('user_id');

            $this->db->insert('messages', $arr_row);
            $message_id = $this->db->insert_id();

        //Enviar mensaje a los participantes
            $qty_sent = $this->assign_users($conversation_id, $message_id);

        //Preparar resultado
            $data = array('status' => 1, 'message_id' => $message_id, 'qty_sent' => $qty_sent);

        return $data;
    }

    /**
     * Envía los mensajes a los usuarios participantes en una conversación.
     * Devuelve el número de registros creados en la tabla message_user
     * 2019-09-24
     */
    function assign_users($conversation_id, $message_id)
    {
        $qty_sent = 0;
        $users = $this->users($conversation_id);

        $arr_row['message_id'] = $message_id;
        foreach ($users->result() as $row_user)
        {
            $arr_row['user_id'] = $row_user->user_id;
            $arr_row['status'] = ( $row_user->user_id == $this->session->userdata('user_id') ) ? 1 : 0; //Leído si es el mismo que lo envía

            $condition = "user_id = {$arr_row['user_id']} AND message_id = {$arr_row['message_id']}";
            $sent_id = $this->Db_model->save('message_user', $condition, $arr_row);

            $qty_sent += ( $sent_id > 0 ) ? 1 : 0;
        }

        return $qty_sent;
    }

    /**
     * Usuarios que participan en una conversación. Tabla users_meta, type 10
     * 2019-09-24
     */
    function users($conversation_id)
    {
        $this->db->select('user_id');
        $this->db->where('type_id', 10);
        $this->db->where('related_1', $conversation_id);
        $users = $this->db->get('users_meta');

        return $users;
    }

    /**
     * 
     */
    function messages($conversation_id, $message_id)
    {
        $condition = 'messages.id = 0';
        if ( $message_id >= 0 ) { $condition = "messages.id > {$message_id}"; }

        $this->db->select('messages.id, text, messages.user_id, sent_at');
        $this->db->join('message_user', 'messages.id = message_users.message_id');
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where($condition);
        $this->db->where('message_user.status <= 1');   //Que no haya sido eliminado
        $this->db->where('message_user.user_id', $this->session->userdata('user_id'));
        $this->db->order_by('messages.id', 'ASC');
        $messages = $this->db->get('messages');

        return $messages;
    }

    /**
     * Marca un mensaje como eliminado (status = 2) en la tabla message_user
     * 2019-10-23
     */
    function set_deleted($message_id)
    {
        //Resultado inicial por defecto
            $data = array('status' => 0);

        //Actualizar registro en message_user
            $arr_row['status'] = 2; //Eliminado para el usuario en sesión
            $condition = "user_id = {$this->session->userdata('user_id')} AND message_id ={$message_id}";
            $mu_id = $this->Db_model->save('message_user', $condition, $arr_row);
    
        //Verificar resultado
            if ( $mu_id > 0 ){ $data = array('status' => 1); }
    
        return $data;
    }
}