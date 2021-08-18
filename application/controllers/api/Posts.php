<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        
        //Local time set
        date_default_timezone_set("America/Bogota");
    }

    /**
     * Listado de Posts, filtrados por búsqueda, JSON
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data = $this->Post_model->get($filters, $num_page, $per_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Posts de información de la administración de Brave para informar a sus usuarios.
     * 2021-08-15
     */
    function get_admin_info_posts()
    {
        /*$post = [
            'title' => 'Información',
            'content' => 'Te invitamos a seguir entrenando con Brave, no olvides renovar su suscripción',
            'url_image' => 'https://www.bravebackend.com/content/uploads/2021/06/202024_20210618162717_67.png'
        ];

        $posts = array($post);*/
        
        $posts = $this->Post_model->admin_info_posts(1);

        $data['posts'] = array();
        if ( $posts->num_rows() > 0 ) {
            $data['posts'] = $posts->result();
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}