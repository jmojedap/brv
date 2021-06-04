<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comments extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/comments/';
    public $url_controller = URL_ADMIN . 'comments/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Comment_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($comment_id)
    {
        redirect("admin/comments/info/{$comment_id}");
    }
    
//EXPLORE FUNCTIONS
//---------------------------------------------------------------------------------------------------
    
    /**
     * Exploración y búsqueda de comentarios
     * 2021-03-15
     */
    function explore($num_page = 1)
    {        
        //Identificar filtros de búsqueda
            $this->load->model('Search_model');
            $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->Comment_model->explore_data($filters, $num_page);
        
        //Opciones de filtros de búsqueda
            $data['options_table'] = $this->Item_model->options('category_id = 30 AND cod IN (1020,2000,3100)', 'Todos');
            
        //Arrays con valores para contenido en lista
            $data['arr_tables'] = $this->Item_model->arr_cod('category_id = 30 AND cod IN (1020,2000,3100)');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * JSON
     * Listado de comentarios, según filtros de búsqueda
     * 2021-03-15
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();
        $data = $this->Comment_model->get($filters, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * Elimina un grupo de comentarios seleccionados
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) $data['qty_deleted'] += $this->Comment_model->delete($row_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CRUD
//-----------------------------------------------------------------------------
    
    //Elimina un comentario, tabla comment
    function delete($comment_id, $element_id)
    {
        $data['qty_deleted'] = $this->Comment_model->delete($comment_id, $element_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Formulario para agregar un nuevo comentario
     * 2021-03-12
     */
    function add()
    {
        //Variables específicas
            $data['options_table'] = $this->Item_model->options('category_id = 30 AND cod IN (1020,2000,3100)');

        //Variables generales
            $data['head_title'] = 'Crear comentario';
            $data['nav_2'] = $this->views_folder . 'explore/menu_v';
            $data['view_a'] = $this->views_folder . 'add_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }
    
    /**
     * POST JSON
     * Toma datos de POST e inserta un registro en la tabla Comment. Devuelve
     * result del proceso en JSON
     */ 
    function save($table_id, $element_id)
    {
        $data = $this->Comment_model->save($table_id, $element_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * Formulario para la edición de los datos de un Comment. Los datos que se
     * editan dependen de la $section elegida.
     */
    function edit($comment_id, $section = 'basic')
    {
        //Datos básicos
            $data = $this->Comment_model->basic($comment_id);
        
            $view_a = $this->views_folder . "edit_v/{$section}_v";
        
        //Array data espefícicas
            $data['nav_2'] = 'comments/menu_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
        
    }

    /**
     * POST JSON
     * 
     * @param type $comment_id
     */
    function update($comment_id)
    {
        $result = $this->Comment_model->update($comment_id);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    
    function info($comment_id)
    {
        //Datos básicos
        $data = $this->Comment_model->basic($comment_id);

        $data['subcomments'] = $this->Comment_model->element_comments($data['row']->table_id, $data['row']->element_id, $comment_id, 1); 
        
        //Variables específicas
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['view_a'] = $this->views_folder . 'info_v';
        $data['back_link'] = $this->url_controller . 'explore';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

// PROCESOS
//-----------------------------------------------------------------------------

    /**
     * Alternar like and unlike a un comment por parte del usuario en sesión
     * 2021-05-18
     */
    function alt_like($comment_id)
    {
        $data = $this->Comment_model->alt_like($comment_id);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// INFO 
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Listado de comentarios de un elemento
     * 2021-06-04
     */
    function element_comments($table_id, $element_id, $parent_id = 0, $num_page = 1)
    {
        $data['comments'] = $this->Comment_model->element_comments($table_id, $element_id, $parent_id, $num_page);

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}