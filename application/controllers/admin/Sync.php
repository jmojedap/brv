<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends CI_Controller {

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/sync/';
    public $url_controller = URL_ADMIN . 'sync/';

// Constructor
//-----------------------------------------------------------------------------

    function __construct()
    {
        parent::__construct();
        
        //Específico
        $this->load->model('Sync_model');
        
        //Para formato de horas
        date_default_timezone_set("America/Bogota");
        
        //Para permitir acceso externo a funciones ajax desde local
        header('Access-Control-Allow-Origin: *'); 
        header('Access-Control-Allow-Methods: GET');
        //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    }

// FUNCIONES PARA EJECUTAR EN INSTALACIÓN LOCAL
//-----------------------------------------------------------------------------
    
    /**
     * Vista para el proceso de sincronización de la base de data local, con la
     * base de data en el server. Listado de tables.
     */
    function panel($method_id = 0)
    {
        //Procesos iniciales
            $condition = $this->Sync_model->condition_sync();
            $arr_tables_status = $this->Sync_model->arr_tables_status($condition);
            $this->Sync_model->update_max_idl($arr_tables_status);
            
        //Variables específicas
            $data['method_id'] = $method_id;    //Método de sincronización.
            $data['tables'] = $this->Sync_model->tables($condition);
            $data['limit'] = 5000;             //Número máximo de rows a transferir por ciclo
            
        //Se puede sincronizar solo si es versión local, backup
            $view_a = $this->views_folder . 'panel_v'; 
            if ( strlen(URL_SYNC) == 0 ) 
            {
                $view_a = 'app/message_v';
                $data['message'] = '<i class="fa fa-info-circle"></i> La sincronización está activa solo en versiones locales de desarrollo';
            }
            
        //Variables vista
            $data['head_title'] = 'Sincronización DB';
            $data['view_a'] = $view_a;
        
        //load vista
            $this->App_model->view(TPL_ADMIN, $data);   
    }
    
    /**
     * AJAX JSON
     * Actualiza el campo sis_table. Se marca el momento de inicio de sincronización.
     *
     * @param type $table
     */
    function start_sync($table)
    {        
        //Marcar momento de inicio
            $arr_row['start_date'] = date('Y-m-d H:i:s');
            $condition = "table_name = '{$table}'";
            
            $this->Db_model->save('sis_table', $condition, $arr_row);

        $data = array('status' => 1, 'message' => 'Sincro iniciada: ' . $table);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * AJAX
     * Elimina todos los data de una table
     *
     * @param type $table
     */
    function clean_table($table)
    {
        //Marcar momento de inicio
            $arr_row['start_date'] = date('Y-m-d H:i:s');
            $condition = "table_name = '{$table}'";
            
            $this->Db_model->save('sis_table', $condition, $arr_row);
        
        //Eliminar los data de table
            $data = $this->Sync_model->clean_table($table);

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * AJAX
     * Inserta rows descargados del server en la table local
     * 
     * @param type $table
     */
    function insert_rows($table)
    {
        //2015-12-04 solucionar problema memory limit y tiempo de ejecución
            ini_set('memory_limit', '2048M');   
            set_time_limit(120);    //120 segundos, dos minutos por ciclo
        
        $download = json_decode($this->input->post('json_download'));
        $data = $this->Sync_model->insert_rows($table, $download);
        
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    
    /**
     * AJAX
     * Actualiza status tables server, en la table sis_table
     */
    function save_server_status()
    {
        $arr_tables_status = $this->input->post('json_tables_status');
        $this->Sync_model->save_server_status($arr_tables_status);
        
        $this->output->set_content_type('application/json')->set_output(count($arr_tables_status));
    }
    
    /**
     * AJAX
     * Establece la fecha actual, como fecha de sincronización reciente
     * @param type $table
     */
    function update_sync_data($table)
    {
        //Calcular status de la table
        $table_status = $this->Sync_model->table_status($table);
        
        //Construir registro
        $arr_row['sincro_date'] = date('Y-m-d H:i:s');
        $arr_row['quan_rows'] = $table_status['quan_rows'];
        $arr_row['max_id'] = $table_status['max_id'];
        
        //Actualizar en sis_table
        $this->db->where('table_name', $table);
        $this->db->update('sis_table', $arr_row);
    }
    
// FUNCIONES EN SERVIDOR
//-----------------------------------------------------------------------------

    /**
     * AJAX, rows de una table en formato json, con el ID mayor al valor de
     * la variable $since_id
     * 
     * @param type $table
     * @param type $since_id
     */
    function get_rows($table, $limit = 50000, $since_id = 0)
    {
        $role = $this->Sync_model->sync_role();
        $data = array();   //Valor por defecto

        if ( $role <= 1 )   //Si es administrador o desarrollador
        {
            $data = $this->Sync_model->get_rows($table, $limit, $since_id);
        }
        
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
    
    /**
     * AJAX
     * devuelve el número de rows que tiene una table, sirve para calcular el número
     * de ciclos que se deben realizar para sincronizar los datos de la tabla 
     * 
     * @param type $table
     * @return type
     */
    function quan_rows($table, $since_id = 0)
    {
        $sql = "SELECT COUNT(id) AS quan_rows FROM {$table} WHERE id > {$since_id}";
        $query = $this->db->query($sql);
        
        $quan_rows = 0;
        if ( $query->num_rows() > 0 ) { $quan_rows = $query->row()->quan_rows; }
        
        $this->output
        ->set_content_type('application/json')
        ->set_output($quan_rows);
    }
    
    /**
     * AJAX JSON
     * Objeto json con data de status de tables, cantidad de rows y
     * ID máximo
     */
    function tables_status()
    {
        $role = $this->Sync_model->sync_role();

        $arr_tables_status = array();   //Valor por defecto

        if ( $role <= 1 )   //Si es administrador o desarrollador
        {
            $condition = $this->Sync_model->condition_sync();
            $arr_tables_status = $this->Sync_model->arr_tables_status($condition);
        }
        
        $this->output->set_content_type('application/json')->set_output(json_encode($arr_tables_status));
    }
    
}

/* Fin del archivo Sync.php */