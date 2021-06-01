<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/messages/';
    public $url_controller = URL_ADMIN . 'messages/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('Message_model');
        date_default_timezone_set("America/Bogota");    //Para definir hora local
    }
    
    /**
     * Aplicación de mensajería
     */
    function conversation($conversation_id = 0)
    {
        $data['head_title'] = 'Mensajes';
        $data['view_a'] = $this->views_folder . 'conversation/conversation_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function template()
    {
        $data['head_title'] = 'Mensajes';
        $data['view_a'] = $this->views_folder . 'conversation/ejemplo_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

// Aplicación de mensajes
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Listado de conversaciones en las que participa el usuario en sesión
     */
    function conversations($num_page = 1)
    {
        $data['conversations'] = $this->Message_model->conversations($num_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Crea una conversación individual, entre el usuario en sesión y el usuario $user_id
     */
    function create_conversation($user_id)
    {
        $data['conversation_id'] = $this->Message_model->create_conversation($user_id);
        redirect("messages/conversation/{$data['conversation_id']}");
    }

    /**
     * Envía un mensaje en una conversación
     */
    function send_message($conversation_id)
    {
        $data = $this->Message_model->send_message($conversation_id);

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Obtiene el listado de mensajes de una conversación, y con ID mayor a $message_id
     */
    function get($conversation_id, $message_id = 0)
    {
        $messages = $this->Message_model->messages($conversation_id, $message_id);
        $data['messages'] = $messages->result();
        $data['qty_messages'] = $messages->num_rows();

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Elimina un mensaje de una conversación, solo para el usuario en sesión, no lo 
     * elimina de la base de datos, solo lo marca como eliminado.
     */
    function delete($message_id)
    {
        $data = $this->Message_model->set_deleted($message_id);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}