<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Files extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/files/';
    public $url_controller = URL_ADMIN . 'files/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('File_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

//EXPLORE
//---------------------------------------------------------------------------------------------------
                
    /** Exploración de Files */
    function explore($num_page = 1)
    {
        //Identificar filtros de búsqueda
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        //Datos básicos de la exploración
            $data = $this->File_model->explore_data($filters, $num_page, 10);
        
        //Opciones de filtros de búsqueda
            //$data['options_type'] = $this->Item_model->options('category_id = 33', 'Todos');
            
        //Arrays con valores para contenido en lista
            //$data['arr_types'] = $this->Item_model->arr_cod('category_id = 33');
            
        //Cargar vista
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Listado de Posts, filtrados por búsqueda, JSON
     */
    function get($num_page = 1, $per_page = 10)
    {
        $this->load->model('Search_model');
        $filters = $this->Search_model->filters();

        $data = $this->File_model->get($filters, $num_page, $per_page);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * AJAX JSON
     * Eliminar un conjunto de posts seleccionados
     */
    function delete_selected()
    {
        $selected = explode(',', $this->input->post('selected'));
        $data['qty_deleted'] = 0;
        
        foreach ( $selected as $row_id ) 
        {
            $data['qty_deleted'] += $this->File_model->delete($row_id);
        }

        //Establecer resultado
        if ( $data['qty_deleted'] > 0 ) { $data['status'] = 1; }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Elimina un registro de la tabla file, y los archivos asociados en el servidor
     * 2020-07-24
     */
    function delete($file_id)
    {
        $data = $this->File_model->delete($file_id);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CRUD
//-----------------------------------------------------------------------------

    function info($file_id)
    {
        $data = $this->File_model->basic($file_id);
        $data['view_a'] = 'common/row_details_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = URL_ADMIN . 'files/explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Formulario para cargar un archivo al servidor de la aplicación.
     */
    function add()
    {
        $data['head_title'] = 'Archivos';
        $data['view_a'] = $this->views_folder . 'add_v';
        $data['nav_2'] = $this->views_folder . 'explore/menu_v';
        $data['head_subtitle'] = 'Cargar';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Carga un archivo en la ruta "content/uploads/{year}/}{month}/"
     * Crea registro de ese arhivo en la tabla file
     */
    function upload()
    {
        $data = $this->File_model->upload();
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Formulario de edición de datos del archivo
     * 2021-06-05
     */
    function edit($file_id)
    {
        $data = $this->File_model->basic($file_id);
        
        //Variables
            $data['destino_form'] = "files/editar_e/{$file_id}";
            $data['att_img'] = $this->File_model->att_img($file_id, '500px_');
        
        //Variables generales
            $data['file_id'] = $file_id;
            $data['back_link'] = URL_ADMIN . 'files/explore';
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['view_a'] = $this->views_folder . 'edit_v';
        
        $this->App_model->view(TPL_ADMIN, $data);
    }

    function update($file_id)
    {
        $arr_row = $this->input->post();
        $data = $this->File_model->update($file_id, $arr_row);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// RECORTE DE IMAGEN
//-----------------------------------------------------------------------------

    /**
     * Formulario para recorte de archivo de imagen.
     */
    function cropping($file_id)
    {
        $data = $this->File_model->basic($file_id);

        $data['image_id'] = $data['row']->id;
        $data['url_image'] = $data['row']->url;
        $data['back_destination'] = "files/edit/{$file_id}";

        $data['view_a'] = $this->views_folder . 'cropping_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['back_link'] = URL_ADMIN . 'files/explore';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX
     * Recorta una imagen según unos parámetros geométricos enviados por POST
     * 2019-05-21
     */
    function crop($file_id)
    {
        //Valor inicial por defecto
        $data = array('status' => 0, 'message' => 'No tiene permiso para modificar esta imagen');
        
        $editable = $this->File_model->editable($file_id);
        if ( $editable ) { $data = $this->File_model->crop($file_id);}
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// CAMBIAR ARCHIVO
//-----------------------------------------------------------------------------

    function change($file_id)
    {
        $data = $this->File_model->basic($file_id);
        
        //Variables
            $data['destino_form'] = "files/cambiar_e/{$file_id}";
            $data['att_img'] = $this->File_model->att_img($file_id, '500px_');
        
        //Variables generales
            $data['file_id'] = $file_id;
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['view_a'] = $this->views_folder . 'change_v';
            $data['back_link'] = URL_ADMIN . 'files/explore';
            
        //Variables generales
            $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * AJAX JSON
     * Cambia un archivo, conservando su registro y sus asignaciones en la DB.
     * 2019-09-19
     */
    function change_e($file_id)
    {
        $row_ant = $this->Db_model->row_id('files', $file_id);   //Registro antes del cambio

        $data = $this->File_model->upload($file_id);
        
        if ( $data['status'] )
        {
            //Eliminar archivo anterior
                $this->File_model->unlink($row_ant->folder, $row_ant->file_name);
            
            //Actualizar archivo, datos del nuevo archivo
                $data['row'] = $this->File_model->change($file_id, $data['upload_data']);
                $this->File_model->create_thumbnails($file_id);     //Crear miniaturas de la nueve imagen
                $this->File_model->mod_original($data['row']->folder, $data['row']->file_name);          //Mofificar imagen nueva después de crear miniaturas
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// PROCESOS MASIVOS
//-----------------------------------------------------------------------------

    /**
     * Actualiza los campos url y url_thumbnail, según los parámetros base de la aplicación actual
     * 2021-05-31
     */
    function update_url()
    {
        $data = $this->File_model->update_url();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }   
}