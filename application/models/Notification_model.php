<?php
class Notification_model extends CI_Model{

    /**
     * Array con estilos CSS para mensajes de correo electrónico
     * 2021-07-26
     */
    function email_styles()
    {
        $email_styles = file_get_contents(URL_RESOURCES . 'css/email.json');
        $styles = json_decode($email_styles, true);

        return $styles;
    }

    function select($format = 'general')
    {
        $arr_select['general'] = 'id, title, content, status, created_at, related_3 AS alert_type, element_id, related_1, related_2';

        return $arr_select[$format];
    }

    function row($event_id)
    {
        $row = NULL;

        $this->db->select($this->select());
        $this->db->where('id', $event_id);
        $notifications = $this->db->get('events');

        if ( $notifications->num_rows() ) $row = $notifications->row();

        return $row;
    }

// Account activation
//-----------------------------------------------------------------------------

    /**
     * Envía e-mail notificando a un usuario activó su cuenta
     * 2021-11-18
     */
    function email_password_updated($user_id)
    {
        if ( ENV == 'production' )
        {
            
        //Variables
            $user = $this->Db_model->row_id('users', $user_id);
        
        //Enviar Email
            $this->load->library('email');
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            $this->email->subject('Your password was successfully updated');
            $this->email->from('info@' . APP_DOMAIN, APP_NAME);
            $this->email->to($user->email);
            $this->email->message($this->password_updated_message($user_id));
            
            $this->email->send();   //Enviar
        }
    }

    /**
     * Devuelve la vista con el mensaje de email para notificar que usuario
     * activó la cuenta
     * 2021-11-17
     */
    function password_updated_message($user_id)
    {
        $user = $this->Db_model->row_id('users', $user_id);

        //Usuarios relacionados
        $data['user'] = $user;

        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = 'admin/notifications/email_password_updated_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }
    
// Notificación following
//-----------------------------------------------------------------------------

    /**
     * Envía e-mail notificando a un usuario que tiene un nuevo seguidor
     * 2021-07-27
     */
    function email_new_follower($user_id, $meta_id)
    {
        if ( ENV == 'production' )
        {
            
        //Variables
            $user = $this->Db_model->row_id('users', $user_id);
        
        //Enviar Email
            $this->load->library('email');
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            $this->email->subject('You have a new follower');
            $this->email->from('info@' . APP_DOMAIN, APP_NAME);
            $this->email->to($user->email);
            $this->email->message($this->new_follower_message($user_id, $meta_id));
            
            $this->email->send();   //Enviar
        }
    }

    /**
     * Devuelve la vista con el mensaje de email para notificar nuevo seguidor
     * 2021-11-17
     */
    function new_follower_message($user_id, $meta_id)
    {
        $user = $this->Db_model->row_id('users', $user_id);
        $following = $this->Db_model->row_id('users_meta', $meta_id);

        //Usuarios relacionados
        $data['user'] = $user;
        $data['follower'] = $this->Db_model->row_id('users', $following->related_1);

        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = 'admin/notifications/email_new_follower_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }

// Notificación new message reciente
//-----------------------------------------------------------------------------

    /**
     * Contar número de mensajes recibidos por el usuario en cuestion, por parte de otro usuario
     * en las últimas 2 horas y diferentes al mensaje en cuestión.
     * 2021-07-29
     */
    function qty_recent_messages($user_id, $row_message)
    {   
        $mktime = strtotime(date('Y-m-d H:i:s') . ' -2 hours');    //Mensajes enviados en las últimas 2 horas
        $min_date = date('Y-m-d H:i:s', $mktime);                  //Fecha y hora hace 2 horas

        $condition = "user_id <> {$user_id}";    //Quien recibe el mensaje
        $condition .= " AND conversation_id = {$row_message->conversation_id}";
        $condition .= " AND sent_at >= '{$min_date}'";
        $condition .= " AND id <> {$row_message->id}";   //Diferente al mensaje en cuestión.

        $qty_recent_messages = $this->Db_model->num_rows('messages', $condition);

        return $qty_recent_messages;
    }

    /**
     * Envía e-mail notificando a un usuario que tiene un nuevo mensaje
     * 2021-07-27
     */
    function email_new_message($user_id, $message_id)
    {
        if ( ENV == 'production' )
        {   
            //Variables
            $row_user = $this->Db_model->row_id('users', $user_id);
            $row_message = $this->Db_model->row_id('messages', $message_id);

            //Verificar que no haya mensajes recientes
            $qty_recent_messages = $this->qty_recent_messages($user_id, $row_message);

            //Si no hay mensajes recientes se envía notificación
            if ( $qty_recent_messages == 0 ) {
                //Enviar Email
                $this->load->library('email');
                $config['mailtype'] = 'html';
    
                $this->email->initialize($config);
                $this->email->subject('You have a new message in ' . APP_NAME);
                $this->email->from('info@' . APP_DOMAIN, APP_NAME);
                $this->email->to($row_user->email);
                $this->email->message($this->new_message_message($row_user, $row_message));
                
                $this->email->send();   //Enviar
            }
        }
    }

    /**
     * Devuelve la vista con el mensaje de email para notificar nuevo mensaje interno
     * 2021-11-17
     */
    function new_message_message($row_user, $row_message)
    {
        //Variables para vista
        $data['user'] = $row_user;
        $data['sender'] = $this->Db_model->row_id('users', $row_message->user_id);
        $data['row_message'] = $row_message;

        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = 'admin/notifications/email_new_message_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }

// Notificación new comment
//-----------------------------------------------------------------------------

    /**
     * Envía e-mail notificando a un usuario que tiene un nuevo comentario
     * 2021-07-30
     */
    function email_new_comment($comment_id)
    {
        if ( ENV == 'production' )
        {   
            //Variables
            $row_comment = $this->Db_model->row_id('comments', $comment_id);
            $table_name = $this->Db_model->field_id('sis_table', $row_comment->table_id, 'table_name');
            $row_element = $this->Db_model->row_id($table_name, $row_comment->element_id);
            $row_user = $this->Db_model->row_id('users', $row_element->creator_id);

            if ( ! is_null($row_user) )
            {
                //Enviar Email
                $this->load->library('email');
                $config['mailtype'] = 'html';
    
                $this->email->initialize($config);
                $this->email->subject('You have a new comment in ' . APP_NAME);
                $this->email->from('info@' . APP_DOMAIN, APP_NAME);
                $this->email->to($row_user->email);
                $this->email->message($this->new_comment_message($row_comment));
                
                $this->email->send();   //Enviar
            }
        }
    }

    /**
     * Devuelve la vista con el mensaje de email para notificar nuevo comentario
     * 2021-07-30
     */
    function new_comment_message($row_comment)
    {
        //Variables para vista
        $data['comment'] = $row_comment;
        $data['creator'] = $this->Db_model->row_id('users', $row_comment->creator_id);
        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = 'admin/notifications/email_new_comment_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }

// Alerta de notificaciones
//-----------------------------------------------------------------------------

    /**
     * Formato base de row, para crear alerta de notificación en tabla events
     * 2021-08-19
     */
    function arr_row_alert($user_id, $alert_type)
    {
        $arr_row['type_id'] = 111;  //Alerta de notificación
        $arr_row['start'] = date('Y-m-d H:i:s');
        $arr_row['status'] = 2;     //No leída
        $arr_row['user_id'] = $user_id;
        $arr_row['related_3'] = $alert_type;  //Tipo de alerta de notificación, new_follower

        return $arr_row;
    }

    /**
     * Guarda registro de alerta de notificación (tipo 111) en la tabla events asociada
     * al recibir un nuevo seguidor (alerta tipo 10)
     * 2021-08-18
     */
    function save_new_follower_alert($user_id, $meta_id)
    {
        $user = $this->Db_model->row_id('users', $user_id);
        $following = $this->Db_model->row_id('users_meta', $meta_id);
        $follower = $this->Db_model->row_id('users', $following->related_1);    //related_1 => follower_id

        //Preparar registro para events
        $arr_row = $this->arr_row_alert($user_id, 10);
        $arr_row['content'] = "{$follower->display_name} has started to follow you";
        $arr_row['element_id'] = $follower->id;

        $this->load->model('Event_model');
        $alert_id = $this->Event_model->save($arr_row, "start = '{$arr_row['start']}'");

        return $alert_id;
    }

    /**
     * Guarda registro de alerta de notificación (tipo 111) en la tabla events asociada
     * al recibir mensaje reciente (alert_type 20)
     * 2021-08-18
     */
    function save_recent_message_alert($user_id, $message_id)
    {
        //Resultado por defecto
        $alert_id = 0; 

        //Varialbes
        $row_user = $this->Db_model->row_id('users', $user_id);
        $row_message = $this->Db_model->row_id('messages', $message_id);
        $qty_recent_messages = $this->qty_recent_messages($user_id, $row_message);
        
        //Si no hay mensajes recientes, crear alerta de notificación
        if ( $qty_recent_messages == 0 ) {

            $sender = $this->Db_model->row_id('users', $row_message->user_id);

            //Preparar registro para events
            $arr_row = $this->arr_row_alert($user_id, 20);
            $arr_row['content'] = "{$sender->display_name} has sent you a message";
            $arr_row['element_id'] = $row_message->id;
    
            $this->load->model('Event_model');
            $alert_id = $this->Event_model->save($arr_row, "start = '{$arr_row['start']}'");
        }

        return $alert_id;
    }

    /**
     * Guarda registro de alerta de notificación (tipo 111) en la tabla events asociada
     * al recibir un nuevo comentario (alerta tipo 30)
     * 2021-08-19
     */
    function save_new_comment_alert($comment_id)
    {
        $comment = $this->Db_model->row_id('comments', $comment_id);
        $commenter = $this->Db_model->row_id('users', $comment->creator_id);
        $table_name = $this->Db_model->field_id('sis_table', $comment->table_id, 'table_name');
        $row_element = $this->Db_model->row_id($table_name, $comment->element_id);

        //Preparar registro para events
        $arr_row = $this->arr_row_alert($row_element->creator_id, 30);
        $arr_row['content'] = "You have a new comment from {$commenter->display_name}";
        $arr_row['element_id'] = $comment->id;          //ID Comentario
        $arr_row['related_1'] = $comment->table_id;     //Id Tabla donde está el elemento comentado
        $arr_row['related_2'] = $row_element->id;       //Id Elemento Comentado

        $this->load->model('Event_model');
        $alert_id = $this->Event_model->save($arr_row, "start = '{$arr_row['start']}'");

        return $alert_id;
    }

    /**
     * Cantidad de notificaciones que el usuario en sesión no ha leído
     * 2021-08-12
     */
    function qty_unread_notifications(){

        //Notificaciones (111) no leídas (2) por el usuario
        $condition = "type_id = 111 AND status = 2 AND user_id = {$this->session->userdata('user_id')}";
        $qty_unread_notifications = $this->Db_model->num_rows('events', $condition);
        return $qty_unread_notifications;
    }

    /**
     * Notificaciones para informar actividad reslacionada con el usuario
     * 2021-08-12
     */
    function notifications()
    {
        $this->db->select('id, title, content, status, created_at, related_3 AS alert_type, element_id, related_1, related_2');
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('type_id', 111);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(12);
        $events = $this->db->get('events');

        return $events;
    }

    /**
     * Marca una alerta de notificación como leída y retorna url para redirigir al usuario
     * 2021-08-21
     */
    function open($event_id)
    {
        $data['url_destination'] = URL_FRONT;   //Valor por defecto
        $notification = $this->row($event_id);

        //Marcar como leída
        $this->db->query("UPDATE events SET status = 1 WHERE id = {$event_id}");

        if ( $notification->alert_type == 10 ) {
            //Nuevo seguidor
            $data['url_destination'] = URL_FRONT . "professionals/profile/{$notification->element_id}";
        } elseif( $notification->alert_type == 20 ){
            //Mensaje reciente
            $data['url_destination'] = URL_FRONT . "messages/conversation/";
        } elseif( $notification->alert_type == 30 ){
            //Elemento comentado es una magen
            $data['url_destination'] = URL_FRONT . "pictures/details/{$notification->related_2}";

            //Elemento comentado es un project, (2000 tabla posts)
            if ( $notification->related_1 == 2000 ) {
                $data['url_destination'] = URL_FRONT . "projects/info/{$notification->related_2}";
            }
        }

        return $data;
    }

// Invitations
//-----------------------------------------------------------------------------

    function email_invitation($user_id)
    {
        $data = array(
            'status' => 0,
            'message' => 'Mensaje no enviado desde instalación local',
            'event_id' => 0
        );

        if ( ENV == 'production' )
        {
            //Establecer clave de activación
            $this->load->model('Account_model');
            //$user_id = $this->input->post('user_id');
            $this->Account_model->activation_key($user_id);

            //Identificar usuario
            $user = $this->Db_model->row_id('users', $user_id);
            
            if ( ! is_null($user) )
            {
                $short_name = $user->display_name;
                $name_parts = explode(' ', $user->display_name);
                if ( count($name_parts) > 0 ) $short_name = $name_parts[0];

                //Enviar Email
                $this->load->library('email');
                $config['mailtype'] = 'html';
    
                $this->email->initialize($config);
                $this->email->subject($short_name . ', activate your Account in ' . APP_NAME);
                $this->email->from('info@' . APP_DOMAIN, APP_NAME);
                //$this->email->to($user->email);
                $this->email->to('pacarinamedialab@gmail.com');
                if ( strlen($this->input->post('bcc')) > 0 ) { $this->email->bcc($this->input->post('bcc')); }
                $this->email->message($this->invitation_message($user));
                
                $this->email->send();   //Enviar

                //Guardar evento
                $data['event_id'] = $this->save_event_invitation($user);
                $data['status'] = 1;
                $data['message'] = 'E-mail enviado';
            }

        }

        return $data;
    }

    /**
     * HTML del mensaje para invitar a usuario a activar su cuenta
     * 2021-11-17
     */
    function invitation_message($user)
    {
        $data['user'] = $user;

        $this->load->model('Professional_model');
        $data['images'] = $this->Professional_model->images($user->id);
        $data['text_message'] = $this->input->post('text_message');

        $data['styles'] = $this->Notification_model->email_styles();
        $data['view_a'] = 'admin/notifications/email_invitation_v';
        
        $message = $this->load->view('templates/email/main', $data, TRUE);
        
        return $message;
    }

    function save_event_invitation($user)
    {
        //Content
        $content['email_to'] = $user->email;
        $content['text_message'] = $this->input->post('text_message');

        //Evento
        $this->load->model('Event_model');
        $arr_row['type_id'] = 121;
        $arr_row['content'] = json_encode($content);
        $arr_row['start'] = date('Y-m-d H:i:s');
        $arr_row['end'] = date('Y-m-d H:i:s');
        $arr_row['element_id'] = date('Ymd');
        $arr_row['user_id'] = $user->id;

        $event_id = $this->Event_model->save($arr_row, "user_id = {$arr_row['user_id']}");

        return $event_id;
    }

}