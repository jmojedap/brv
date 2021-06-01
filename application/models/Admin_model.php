<?php

class Admin_model extends CI_Model{
    
    /* Admin hace referencia a Administración,
     * Colección de funciones especiales para utilizarse específicamente
     * con CodeIgniter en la aplicación para tareas de administración
     * 
     */
    
    function __construct(){
        parent::__construct();
        
    }

// OPCIONES DE LA APLICACION 2019-06-15
//-----------------------------------------------------------------------------

    /** Guarda registro de una opción en la tabla sis_option */
    function save_option($option_id)
    {
        $arr_row = $this->input->post();
        $option_id = $this->Db_model->save('sis_option', "id = {$option_id}", $arr_row);

        return $option_id;
    }

    /**
     * Elimina opción, de la tabla posts.
     */
    function delete_option($option_id)
    {
        $data = array('status' => 0, 'message' => 'La opción no fue eliminada');

        //Tabla post
            $this->db->where('id', $option_id);
            $this->db->delete('sis_option');

        if ( $this->db->affected_rows() > 0 ) {
            $data = array('status' => 1, 'message' => 'Opción eliminada');
        }

        return $data;
    }    
}