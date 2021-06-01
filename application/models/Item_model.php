<?php

class Item_model extends CI_Model{
    
    function __construct(){
        parent::__construct();
        
    }
    
// CRUD ITEM
//---------------------------------------------------------------------------------------------------------
    
    function next_cod($category_id)
    {
        $cod = 1;
        
        $this->db->select('MAX(cod) AS max_cod');
        $this->db->where('category_id', $category_id);
        $query = $this->db->get('items');
        
        if ( $query->num_rows() > 0 ) 
        {
            $cod = $query->row()->max_cod + 1;
        }
        
        return $cod;
    }
    
    /**
     * Elimina un registro de la tabla item, requiere ID item, e ID category, para
     * asegurar y confirmar registro correcto.
     * 2020-04-03
     */
    function delete($item_id, $category_id)
    {
        $data = array('status' => 0, 'qty_deleted' => 0);   //Resultado inicial

        $this->db->where('id', $item_id);
        $this->db->where('category_id', $category_id);
        $this->db->delete('items');

        $data['qty_deleted'] = $this->db->affected_rows();

        if ( $data['qty_deleted'] > 0) { $data['status'] = 1; } //Verificar resultado

        return $data;
    }
    
    /**
     * Guardar un registro en la tabla items. Insertar o Editar.
     */
    function save($arr_row, $item_id)
    {
        //Set condition
            $condition = "id = {$item_id}";
            if ( $item_id == 0 ) { $condition = "category_id = {$arr_row['category_id']} AND cod = {$arr_row['cod']}"; }
        
        //Insert or Update
            $data['saved_id'] = $this->Db_model->save('items', $condition, $arr_row);
            
        //Result
            $data['status'] = 0;
            if ( $data['saved_id'] > 0 )
            {
                $data['status'] = 1;
                //Modificar campos dependientes
                $row_item = $this->Db_model->row_id('items', $data['saved_id']);
                $this->update_ancestry($row_item);
                $this->update_offspring($row_item);
            }
        
        return $data;
    }
    
    /**
     * Devuelve el value del field items.cod para una categoría
     * dado un value de un field
     */
    function cod($category_id, $value, $field = 'abbreviation')
    {   
        $condition = "category_id = {$category_id} AND {$field} = '{$value}'";
        $cod = $this->Pcrn->field('items', $condition, 'cod');
        
        return $cod;
    }
    
// DATOS
//-----------------------------------------------------------------------------
    
    function items($category_id)
    {
        $this->db->order_by('ancestry', 'ASC');
        $this->db->order_by('cod', 'ASC');
        $items = $this->db->get_where('items', "category_id = {$category_id}");
        
        return $items;
    }
    
    /**
     * Devuelve el name de un item con el formato correspondiente.
     * 
     */
    function name($category_id, $cod, $field = 'item_name')
    {
        $name = 'ND';
        
        $this->db->select("{$field} as field");
        $this->db->where('cod', $cod);
        $this->db->where('category_id', $category_id);
        $query = $this->db->get('items');
        
        if ( $query->num_rows() > 0 ) 
        {
            $name = $query->row()->field;
        }
        
        return $name;
    }
    
    /**
     * Devuelve el name de un item con el formato correspondiente, a partir
     * del items.id
     * 
     * @param type $item_id
     * @return type
     */
    function name_id($item_id, $field = 'item')
    {
        $name = 'ND';
        
        $this->db->select("{$field} as field");
        $this->db->where('id', $item_id);
        $query = $this->db->get('items');
        
        if ( $query->num_rows() > 0 ) 
        {
            $name = $query->row()->field;
        }
        
        return $name;
    }
    
    
// Arrays
//-----------------------------------------------------------------------------
    
    /**
     * Devuelve un array con índice y value para una categoría específica de items
     * Dadas unas características definidas en el array $config
     * 
     * @param type $condition
     * @return type
     */
    function arr_cod($condition)
    {   
        $this->db->select('cod, item_name');
        $this->db->where($condition);
        $this->db->order_by('position', 'ASC');
        $this->db->order_by('cod', 'ASC');
        $query = $this->db->get('items');
        
        $arr_item = $this->pml->query_to_array($query, 'item_name', 'cod');
        
        return $arr_item;
    }
    
    /**
     * Array con options de item, para elementos select de formularios.
     * La variable $condition es una condición WHERE de SQL para filtrar los items.
     * En el array el índice corresponde al cod y el value del array al
     * field items. La variable $empty_value se pone al principio del array
     * cuando el field select está vacío, sin ninguna opción seleccionada.
     * 
     * @param type $condition
     * @param type $empty_value
     * @return type
     */
    function options($condition, $empty_value = NULL)
    {
        
        $select = 'CONCAT("0", (cod)) AS str_cod, item_name AS field_value';
        
        $this->db->select($select);
        $this->db->where($condition);
        $this->db->order_by('cod', 'ASC');
        $this->db->order_by('position', 'ASC');
        $query = $this->db->get('items');
        
        $options_pre = $this->pml->query_to_array($query, 'field_value', 'str_cod');
        
        if ( ! is_null($empty_value) ) 
        {
            $options = array_merge(array('' => '[ ' . $empty_value . ' ]'), $options_pre);
        } else {
            $options = $options_pre;
        }
        
        return $options;
    }
    
    /**
     * Array con options de item, para elementos select de formularios.
     * La variable $condition es una condición WHERE de SQL para filtrar los items.
     * En el array el índice corresponde al id y el value del array al
     * field items. La variable $empty_value se pone al principio del array
     * cuando el field select está vacío, sin ninguna opción seleccionada.
     * 
     * @param type $condition
     * @param type $empty_value
     * @return type
     */
    function options_id($condition, $empty_value = NULL)
    {
        $select = 'CONCAT("0", (id)) AS field_index_str, item_name AS field_value';
        
        $this->db->select($select);
        $this->db->where($condition);
        $this->db->order_by('position', 'ASC');
        $this->db->order_by('cod', 'ASC');
        $query = $this->db->get('items');
        
        $options_pre = $this->pml->query_to_array($query, 'field_value', 'field_index_str');
        
        if ( ! is_null($empty_value) ) {
            $options = array_merge(array('' => '[ ' . $empty_value . ' ]'), $options_pre);
        } else {
            $options = $options_pre;
        }
        
        return $options;
    }
    
    /**
     * Devuelve array con valuees predeterminados para utilizar en la función
     * Item_model->arr_item
     * 
     * @param type $format
     * @return string
     */
    function arr_config_item($format = 'cod')
    {
        $arr_config['order_type'] = 'ASC';
        $arr_config['field_value'] = 'item_name';
        
        switch ($format) 
        {
            case 'id':
                //id, ordenado alfabéticamente
                $arr_config['field_index'] = 'id';
                $arr_config['order_by'] = 'item_name';
                $arr_config['str'] = TRUE;
                break;
            case 'cod':
                //cod, ordenado por cod
                $arr_config['field_index'] = 'cod';
                $arr_config['order_by'] = 'cod';
                $arr_config['str'] = TRUE;
                break;
            case 'cod_num':
                //cod, ordenado por cod, numérico
                $arr_config['field_index'] = 'cod';
                $arr_config['order_by'] = 'cod';
                $arr_config['str'] = FALSE;
                break;
            case 'cod_abr':
                //cod, abreviatura, string
                $arr_config['field_index'] = 'cod';
                $arr_config['field_value'] = 'abbreviation';
                $arr_config['order_by'] = 'abbreviation';
                $arr_config['str'] = TRUE;
                break;
        }
        
        return $arr_config;
    }
    
    /**
     * Devuelve un array con índice y value para una categoría específica de items
     * Dadas unas características definidas en el array $config
     * 
     * @param type $format
     * @return type
     */
    function arr_item($condition, $format = 'cod')
    {
        
        $config = $this->arr_config_item($format);
        
        $select = $config['field_index'] . ' AS field_index, CONCAT("0", (' . $config['field_index'] . ')) AS field_index_str, ' . $config['field_value'] .' AS field_value';
        
        $indice = 'field_index_str';
        if ( ! $config['str'] ) { $indice = 'field_index'; }
        
        $this->db->select($select);
        $this->db->where($condition);
        $this->db->order_by($config['order_by'], $config['order_type']);
        $query = $this->db->get('items');
        
        $arr_item = $this->pml->query_to_array($query, 'field_value', $indice);
        
        return $arr_item;
    }
    
    function arr_field($category_id, $field)
    {
        $config = $this->arr_config_item($format);
        
        $select = $config['field_index'] . ' AS field_index, CONCAT("0", (cod)) AS field_index_str, ' . $field .' AS field_value';
        
        $indice = 'field_index_str';
        if ( ! $config['str'] ) { $indice = 'field_index'; }
        
        $this->db->select($select);
        if ( $category_id > 0 ) { $this->db->where('category_id', $category_id); }
        $this->db->where($config['condition']);
        $this->db->order_by($config['order_by'], $config['order_type']);
        $query = $this->db->get('items');
        
        $arr_item = $this->pml->query_to_array($query, 'field_value', $indice);
        
        return $arr_item;
    }

// IMPORTAR
//-----------------------------------------------------------------------------}

    /**
     * Array con configuración de la vista de importación 
     * 2020-04-01
     */
    function import_config()
    {
        $data['help_note'] = 'Se importarán items a la BD';
        $data['help_tips'] = array();
        $data['template_file_name'] = 'f60_items.xlsx';
        $data['sheet_name'] = 'items';
        $data['head_subtitle'] = 'Importar items';
        $data['destination_form'] = "items/import_e/";

        return $data;
    }

    /**
     * Importa items a la base de datos
     * 2020-04-01
     */
    function import($arr_sheet)
    {
        $data = array('qty_imported' => 0, 'results' => array());
        
        foreach ( $arr_sheet as $key => $row_data )
        {
            $data_import = $this->import_row($row_data);
            $data['qty_imported'] += $data_import['status'];
            $data['results'][$key + 2] = $data_import;
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel. Valida los campos, crea registro
     * en la tabla item
     * 2020-04-01
     */
    function import_row($row_data)
    {
        //Validar
            $error_text = '';
            $row_category = $this->Db_model->row('items', "category_id = 0 AND cod = '$row_data[0]'");
                            
            if ( strlen($row_data[1]) == 0 ) { $error_text = 'La casilla `cod` está vacía. '; }
            if ( strlen($row_data[2]) == 0 ) { $error_text = 'La casilla `item name` está vacía. '; }
            if ( is_null($row_category) ) { $error_text = "El ID de category '{$row_data[0]}' no existe. "; }

        //Si no hay error
            if ( $error_text == '' )
            {                
                $arr_row['category_id'] = $row_data[0];
                $arr_row['cod'] = $row_data[1];
                $arr_row['item_name'] = $row_data[2];
                $arr_row['abbreviation'] = ( is_null($row_data[3]) ) ? strtolower(substr($row_data[2],0,4)) : $row_data[3];
                $arr_row['parent_id'] = ( is_null($row_data[6]) ) ? 0 : $row_data[6];
                $arr_row['description'] = ( is_null($row_data[8]) ) ? $row_category->item_name . ' - ' . $row_data[2] : $row_data[8];
                $arr_row['long_name'] = ( is_null($row_data[10]) ) ? $row_data[2] : $row_data[10];
                $arr_row['short_name'] = ( is_null($row_data[11]) ) ? $row_data[2] : $row_data[11];
                $arr_row['slug'] = $row_category->slug . '-' . $this->Db_model->unique_slug($row_data[2], 'items');

                //Guardar en tabla item
                $data_insert = $this->save($arr_row, 0);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $data_insert['saved_id']);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

// GESTIÓN DE JERARQUÍA DE ÍTEMS
//-----------------------------------------------------------------------------

    /**
     * Actualiza el campo items.ancestry para un item específico
     * 2020-05-05
     */
    function update_ancestry($row)
    {
        //Valores por iniciales defecto
        $prefix = '-';
        $level = 0;
        
        //Si tiene padre, cambiar valores
        if ( $row->parent_id > 0 )
        {
            $row_parent = $this->Db_model->row('items', "category_id = {$row->category_id} AND cod = {$row->parent_id}");
            $prefix = $row_parent->ancestry;
            $level = $row_parent->level + 1;
        }
        
        //Construir registro
            $arr_row['ancestry'] = $prefix . $row->cod . '-';
            $arr_row['level'] = $level;
        
        //Actualizar
            $this->db->where('id', $row->id);
            $this->db->update('items', $arr_row);   
    }
    
    /**
     * Actualiza el campo items.ancestry para todos los items correspondientes a la descendencia
     * de un item ($row), necesaria cuando un item cambia de padre inmediado en la jerarqía
     * 2020-05-05
     */
    function update_offspring($row)
    {
        $items = $this->offspring($row->id);

        foreach ( $items->result() as $row_child )
        {
            $this->update_ancestry($row_child);
        }
    }
    
    /**
     * Descendencia de un ítem, en un formato específico
     * 2020-04-02
     */
    function offspring($item_id, $format = 'query')
    {
        $offspring = NULL;
        $row = $this->Db_model->row_id('items', $item_id);
        
        $this->db->like("CONCAT('-', (ancestry), '-')", "-{$row->cod}-");
        $this->db->where('category_id', $row->category_id);
        $this->db->order_by('ancestry', 'ASC');
        $query = $this->db->get('items');
        
        if ( $format == 'query' ) {
            $offspring = $query;
        } elseif ( $format == 'array' ) {
            $offspring = $this->pml->query_to_array($query, 'id');
        } elseif ( $format == 'string' ) {
            $offspring = '0';
            $arr_offspring = $this->pml->query_to_array($query, 'id');
            if ( $query->num_rows() > 0 ) {
                $offspring = implode(',', $arr_offspring);
            }
        }
        
        return $offspring;
    }
}