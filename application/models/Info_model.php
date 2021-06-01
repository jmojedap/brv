<?php
class Info_model extends CI_Model{

    function save_contact()
    {
        $data = array('status' => 0, 'message' => 'El mensaje no fue guardado');

        $arr_row = $this->Db_model->arr_row();
        unset($arr_row['g-recaptcha-response']);    //Desactivar el campo de recaptcha

        $arr_row['type_id'] = 14;   //Mensaje de contacto
        $arr_row['updater_id'] = 0;
        $arr_row['creator_id'] = 0;

        $post_id = $this->Db_model->save('posts', 'id = 0', $arr_row);

        if ( $post_id > 0 )
        {
            $this->send_contact_email($post_id);
            $data = array('status' => 1, 'message' => 'Mensaje guardado: ' . $post_id);
        }

        return $data;
    }

    /**
     * Enviar por correo electrónico mensaje de contacto recibido en la sección info/contacto
     * a los emails establecidos en la tabla sis_option.id = 27
     */
    function send_contact_email($post_id)
    {
        $row_message = $this->Db_model->row_id('posts', $post_id);
        $email_to = $this->Db_model->field_id('sis_option', 27, 'option_value');    //27: Emails para mensajes de contacto web

        $this->load->library('email');

        $this->email->from('noresponder@cursilloscolombia.com', 'MCC Colombia');
        $this->email->to($email_to);
        $this->email->subject('Contacto: ' . $row_message->excerpt);
        $this->email->set_mailtype('html');

        //Construir mensaje con datos adicionales
            $message = 'De: <b>' . $row_message->post_name . '</b><br>';
            $message .= 'Correo electrónico: ' . $row_message->text_1 . '<br>';
            $message .= 'Celular: ' . $row_message->text_2 . '<hr>';
            $message .= $row_message->content . '<hr>';

        $this->email->message($message);

        $this->email->send();
    }
}