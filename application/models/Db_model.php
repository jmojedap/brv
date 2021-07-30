<?php
class Db_model extends CI_Model{
    
    /* Db, is abbreviation for Database
     * Functions that complement database operations with CodeIgniter
     * Actualizada 2021-07-26
     */
      
    /**
    * 
    * Devuelve el valor de un field ($nombre_field) del primer row de una $table
    * que cumpla una $condition con el formato where de sql.
    */
    function field($table, $condition, $nombre_field)
    {
        $field = NULL;  //Valor por defecto
        $query = $this->db->query("SELECT {$nombre_field} FROM {$table} WHERE {$condition} LIMIT 1");
        
        if ( $query->num_rows() > 0 ){ $field = $query->row()->$nombre_field; }
        
        return $field;
    }
    
    /**
    * 
    * Devuelve el valor de un field ($nombre_field) del primer row de una $table
    * que cumpla 
    */
    function field_id($table, $id, $nombre_field)
    {
        $field = NULL;
        
        if ( strlen($id) > 0 ) 
        {
            $query = $this->db->query("SELECT {$nombre_field} FROM {$table} WHERE id = {$id} LIMIT 1");
            if ( $query->num_rows() > 0 ){ $field = $query->row()->$nombre_field; }
        }
        
        return $field;
    }
    
    /* Devuelve el primer row de una $table
    * que cumpla una $condition con el formato where de sql.
    */
    function row($table, $condition)
    {
        //Valor por defecto
        $row = NULL;

        $query = $this->db->query("SELECT * FROM {$table} WHERE {$condition} LIMIT 1");
        if ( $query->num_rows() > 0 ){ $row = $query->row(); }
        
        return $row;
    }
    
    /* Devuelve el primer row de una $table
    *  teniendo un valor de table.id determinado
    */
    function row_id($table, $id)
    {
        $row = NULL;
        $row_id = 0;
        if ( strlen($id) > 0 ) { $row_id = $id; }
        
        $query = $this->db->query("SELECT * FROM {$table} WHERE id = {$row_id} LIMIT 1");
        if ( $query->num_rows() > 0 ){ $row = $query->row(); }
        
        return $row;   
    }
    
    /* Devuelve el número de rows de una table 
    * con una condición con el formato where de sql
    */
    function num_rows($table, $condition)
    {    
        $this->db->where($condition);
        $query = $this->db->get($table);
        return $query->num_rows();
    }
    
    /**
     * Determina si exists un row con una $condition sql en una $table
     * Si no exists devuelve 0, si exists devuelve el id del row
     * 
     * @param type $table
     * @param type $condition string
     * @return type
     */
    function exists($table, $condition)
    {
        $exists = 0;
        
        $query = $this->db->query("SELECT id FROM {$table} WHERE {$condition} LIMIT 1");
        if ( $query->num_rows() > 0 ){ $exists = $query->row()->id; }
        
        return $exists;
    }
    
    /**
     * Determina si un valor para un field es único en la table. Si se agrega
     * el ID de un row específico, lo descarta en la búsqueda, valor ya
     * existente.
     * 2021-07-26
     */
    function is_unique($table, $field, $value, $row_id = NULL)
    {
        $is_unique = 1;
        
        $this->db->select('id');
        $this->db->where("{$field} = '{$value}'");
        $this->db->where("LENGTH({$field}) > 0");
        if ( ! is_null($row_id) ) { $this->db->where("id <> {$row_id}"); }
        $query = $this->db->get($table);
        
        if ( $query->num_rows() > 0 && strlen($value) > 0 )
        {
            $is_unique = 0;
        }
        
        return $is_unique;
    }
    
    /**
     * Si un row con una $condition sql exists en una $table, se edita
     * Si no exists se inserta nuevo row. Devuelve el id del row editado o insertado
     * 
     * @param type $table
     * @param type $condition
     * @param type $arr_row
     * @return type
     */
    function save($table, $condition, $arr_row)
    {
        $row_id = $this->exists($table, $condition);
        
        if ( $row_id == 0 ) 
        {
            //Do not exists, insert
            $this->db->insert($table, $arr_row);
            $row_id = $this->db->insert_id();
        } else {
            //Already exists, update
            $this->db->where('id', $row_id);
            $this->db->update($table, $arr_row);
        }
        
        return $row_id;
    }

    /**
     * Si $arr_row['id'] no existe, inserta nuevo registro
     * Si un el array contiene $arr_row['id'], se edita
     * 2021-03-18
     */
    function save_id($table, $arr_row = NULL)
    {
        //Verificar si hay array con registro
        if ( is_null($arr_row) ) $arr_row = $this->arr_row();

        //Verificar si tiene id definido, insertar o actualizar
        if ( ! isset($arr_row['id']) ) 
        {
            //No existe, insertar
            $this->db->insert($table, $arr_row);
            $row_id = $this->db->insert_id();
        } else {
            //Ya existe, editar
            $row_id = $arr_row['id'];
            unset($arr_row['id']);

            $this->db->where('id', $row_id)->update($table, $arr_row);
        }
        
        return $row_id;
    }
    
    /**
     * Si un row con una $condition sql no exists en una $table, se inserta
     * Diferente a Db_model->save(), si exists, NO se edita.
     * Devuelve el id del row editado o insertado
     *
     */
    function insert_if($table, $condition, $row)
    {
        $row_id = $this->exists($table, $condition);
        
        if ( $row_id == 0 ) 
        {
            //No exists, insert
            $this->db->insert($table, $row);
            $row_id = $this->db->insert_id();
        }
        
        return $row_id;           
    }

    /**
     * String con condición Where SQL, a partir de un registro $arr_row, filtrando
     * los que coinciden con los campos en $fields;
     * 2019-09-23
     */
    function condition($arr_row, $fields)
    {
        $condition = '';

        foreach ($fields as $field)
        {
            $condition .= "{$field} = {$arr_row[$field]} AND ";
        }

        $condition = substr($condition,0,-5);

        return $condition;
    }

// HELPERS
//-----------------------------------------------------------------------------

    /**
     * Array from Post, adding edition data
     * 2021-06-02
     */
    function arr_row($data_from_post = TRUE)
    {
        $arr_row = array();

        if ( $data_from_post ) { $arr_row = $this->input->post(); }
        
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');
        $arr_row['creator_id'] = $this->session->userdata('user_id');
        $arr_row['created_at'] = date('Y-m-d H:i:s');
        
        if ( isset($arr_row['id']) )
        {
            unset($arr_row['creator_id']);
            unset($arr_row['created_at']);
        }

        return $arr_row;
    }

    /**
     * Array predeterminado para edición de un registro
     * 2019-06-17
     */
    function arr_row_edit($data_from_post = TRUE)
    {
        $arr_row = array();
        if ( $data_from_post ) { $arr_row = $this->input->post(); } //Se toman los datos del POST
        
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['updated_at'] = date('Y-m-d H:i:s');

        return $arr_row;
    }
    
// TEXT AND STRING FUNCTIONS
//-----------------------------------------------------------------------------
    
    function slug($text)
    {
        $this->load->helper('text');
        $slug = convert_accented_characters($text);     //Without accents
        $slug = url_title($slug, '-', TRUE);            //Without spaces Sin espaciosy sin caracteres
        $slug = substr($slug, 0, 140);
        
        return $slug;
    }
    
    function unique_slug($text, $table, $field = 'slug')
    {
        $base_slug = $this->slug($text);
        
        //Count equal slug
            $condition = "{$field} = '{$base_slug}'";
            $num_rows = $this->num_rows($table, $condition);
        
        $sufix = '';
        if ( $num_rows > 0 )
        {
            $this->load->helper('string');
            $sufix = '-' . random_string('numeric', 2);
        }
        
        $slug = $base_slug . $sufix;
        
        return $slug;
    }
}