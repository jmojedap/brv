<?php

class Sync_model extends CI_Model{
    
    /* Sync hace referencia a Sincronización,
     * Funciones para sincronizar base de datos local (back up y/o desarrollo) con 
     * base de datos en el servidor de la aplicación.
     */
    
    function __construct(){
        parent::__construct();
    }
    
//SINCRONIZACIÓN DE TABLAS - controllers/sync
//---------------------------------------------------------------------------------------------------------

    /**
     * Devuelve array con fields y rows, de una tabla, con un límite de registros desde un
     * ID determinado.
     */
    function get_rows($table, $limit = 50000, $since_id = 0)
    {
        $this->db->order_by('id', 'ASC');
        $this->db->where('id >', $since_id);
        $query = $this->db->get($table, $limit);
        
        $fields = $query->list_fields();
        $rows = array();
        
        foreach( $query->result() as $row )
        {    
            foreach ( $fields as $key => $field ) 
            {
                $arr_row[$key] = $row->$field;
            }
            
            $rows[] = $arr_row;
        }

        $data['fields'] = $fields;
        $data['rows'] = $rows;
        
        return $data;
    }
    
    /**
     * Para sincronización, primero eliminar todos los datos de la table local
     * 2021-05-24
     */
    function clean_table($table)
    {
        $data = array('status' => 0, 'message' => 'Proceso no ejecutado');

        //Debe ser desarrollador
        if ( $this->session->userdata('role') == 1 ) 
        {
            $sql = "TRUNCATE TABLE {$table}";
            $this->db->query($sql);

            $data = array('status' => 1, 'message' => "La tabla '{$table}' fue vaciada");
        }

        return $data;
    }
    
    /**
     * Llenar la table local con los rows descargados
     * 
     * @param type $table
     * @param type $descarga
     * @return type
     */
    function insert_rows($table, $download)
    {
        $fields = $download->fields;
        $arr_rows = $download->rows;
        $data['max_id'] = 0;
        $data['quan_rows'] = count($arr_rows);
        
        foreach( $arr_rows as $arr_row_liviano ) 
        {
            foreach ( $arr_row_liviano as $i => $valor ) 
            {
                $arr_row[$fields[$i]] = $valor;
            }
            
            $condition = "id = {$arr_row['id']}";
            $this->Db_model->save($table, $condition, $arr_row);
            
            $data['max_id'] = $arr_row['id'];   //Max ID, del que se parte en el siguiente ciclo
        }
        
        return $data;
    }
    
    /**
     * Condición SQL para filtrar las tables que se pueden sincronizar, filtro
     * aplicado en la table sis_table
     * 
     * @return string
     */
    function condition_sync()
    {
        $condition = 'id NOT IN (1140, 1100, 9998)';
        return $condition;
    }
    
    /**
     * Tablas de la base de datos
     * 
     * @param type $condition
     * @return type
     */
    function tables($condition = NULL)
    {
        
        $this->db->select('sis_table.*, (max_ids - max_ids) AS dif_rows');
        
        if ( ! is_null($condition) ) 
        {
            $this->db->where($condition);
        }
        
        //Orden
        $order_by = $this->input->get('ob');
        $order_type = $this->input->get('ot');
        if ( ! is_null($order_by) ) 
        {
            $this->db->order_by($order_by, $order_type);
        } else {
            $this->db->order_by('(max_ids - max_id)', 'DESC');
        }
        
        $tables = $this->db->get('sis_table');
        
        return $tables;
    }
    
    /**
     * Actualiza los fields sis_table.max_id y sis_table.quan_rows
     * ID máximo de la table en la versión local, para comparar con la versión
     * en servidor
     */
    function update_max_idl($arr_tables_status)
    {
        foreach ($arr_tables_status as $table_status)
        {
            //Construir row
                $arr_row['max_id'] = $table_status['max_id'];
                $arr_row['quan_rows'] = $table_status['quan_rows'];
                
            //Actualizar
                $this->db->where('id', $table_status['table_id']);
                $this->db->update('sis_table', $arr_row);
        }
    }
    
    /**
     * Actualiza los fields de la table sis_table: max_ids y quan_rows_servidor
     * Estado actual de datos de las tables en el servidor
     * 
     */
    function save_server_status($arr_tables_status)
    {   
        foreach ($arr_tables_status as $table_status)
        {
            //Construir row
                $arr_row['max_ids'] = $table_status['max_id'];
                $arr_row['quan_rows_server'] = $table_status['quan_rows'];
                
            //Actualizar
                $this->db->where('id', $table_status['table_id']);
                $this->db->update('sis_table', $arr_row);
        }
    }
    
    /**
     * Array con los valores de table de la BD, incluye el id de la table,
     * la cantidad de rows y el ID máximo
     * 
     * @param type $condition
     * @return type
     */
    function arr_tables_status($condition)
    {
        $arr_tables_status = array();
        
        //Seleccionar tables
            if ( ! is_null($condition) ) { $this->db->where($condition); }
            $tables = $this->db->get('sis_table');
        
        //Recorrer tables
        foreach ( $tables->result() as $row_table )
        {
            $table_status = $this->table_status($row_table->table_name);
            $arr_tables_status[] = $table_status;
        }
        
        return $arr_tables_status;
    }
    
    /**
     * Array con los datos del estado de una table
     * 
     * @param type $table_name
     * @return type
     */
    function table_status($table_name)
    {
        $row_table = $this->Db_model->row('sis_table', "table_name = '{$table_name}'");
        
        //Buscar máximo
            $this->db->select('MAX(id) AS max_id, COUNT(id) AS quan_rows');
            $query = $this->db->get($row_table->table_name);

            $table_status['max_id'] = 0;
            $table_status['quan_rows'] = 0;
            if ( $query->num_rows() ) 
            {
                $table_status['max_id'] = $query->row()->max_id;
                $table_status['quan_rows'] = $query->row()->quan_rows;   
            }

        //Elemento
            $table_status['table_id'] = $row_table->id;
            
        return $table_status;    
    }

    /**
     * Devuelve rol de un usuario logueado en una instalación local, que envía por post
     * $user_id y $username.
     */
    function sync_role()
    {
        $user_id = $this->input->post('user_id');
        $username = $this->input->post('username');
        $condition = "id = {$user_id} AND username = '{$username}'";

        $role = $this->Db_model->field('users', $condition, 'role');

        return $role;
        
    }
}