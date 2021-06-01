<?php
class Tag_model extends CI_Model{

// GET TAGS
//-----------------------------------------------------------------------------

    /**
     * Query con listado de tags
     */
    function get($segment_id, $parent_id = 0, $num_page = 1)
    {
        $per_page = 25;
        $offset = $per_page * ($num_page - 1);

        $this->db->select('tags.id, tag_name, slug');
        $this->db->order_by('tag_name', 'DESC');
        $query = $this->db->get('tags', $per_page, $offset);

        return $query;
    }

// CRUD FUNCTIONS
//-----------------------------------------------------------------------------
    
    /**
     * Insertar un registro en la tabla tags.
     * 
     * @param type $arr_row
     * @return type
     */
    function insert($arr_row)
    {
        $data = array('status' => 0, 'message' => 'Tag no agregado');

        if ( $this->insertable() )
        {
            $arr_row['slug'] = $this->Db_model->unique_slug($arr_row['tag_name'], 'tag');

            //Insert in table
                $condition = "tag_name = '{$arr_row['tag_name']}'";
                $tag_id = $this->Db_model->save('tags', $condition, $arr_row);

            //Set result
                if ( $tag_id > 0 ) {
                    $data = array('status' => 1, 'message' => 'Tag creado', 'tag_id' => $tag_id);
                }
        }
        
        return $data;
    }

    /**
     * Verificar si los datos enviados cumplen las condiciones para insertar
     * un tag (tag)
     */
    function insertable()
    {
        $insertable = TRUE;

        return $insertable;
    }

// ELIMINACIÓN DE TAGS
//-----------------------------------------------------------------------------

    //Establece si un tag puede ser eliminado o no por el usuario en sesión.
    function deleteable($tag_id)
    {
        $deleteable = FALSE;
        $row = $this->Db_model->row_id('tags', $tag_id);

        if ( in_array($this->session->userdata('role'), array(1,2,3)) ) { $deleteable = TRUE; }    //Tiene Rol Editor o superior
        if ( $this->session->userdata('user_id') == $row->creator_id ) { $deleteable = TRUE; }  //Es quien creó el tag

        return $deleteable;
    }

    /**
     * Delete a row in tag table
     */
    function delete($tag_id, $segment_id)
    {
        $data = array('status' => 0, 'message' => 'Tag no eliminado');

        if ( $this->deleteable($tag_id) )
        {
            $this->db->where('id', $tag_id);
            $this->db->where('segment_id', $segment_id);  //Requerido para confirmar validez de origen en la eliminación
            $this->db->delete('tags');

            if ( $this->db->affected_rows() > 0 ) {
                $data = array('status' => 1, 'message' => 'Tag eliminado');

                $this->update_count_tags($segment_id, -1);
            }
        }
        
        return $data;
    }
    
// EXPLORACIÓN Y BÚSQUEDA DE TAGS
//-----------------------------------------------------------------------------

    /**
     * String con condición WHERE SQL para filtrar tag
     * 
     * @param type $filters
     * @return type
     */
    function search_condition($filters)
    {
        $condition = NULL;
        
        //Rol de tag
        if ( $filters['type'] != '' ) { $condition .= "type_id = {$filters['type']} AND "; }
        
        if ( strlen($condition) > 0 )
        {
            $condition = substr($condition, 0, -5); //Remove final ' AND '
        }
        
        return $condition;
    }
    
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        
        $role_filter = $this->role_filter($this->session->userdata('tag_id'));

        //Construir consulta
            $this->db->select('id, tag_text, document_id');
        
        //Crear array con términos de búsqueda
            $words_condition = $this->Search_model->words_condition($filters['q'], array('tag_name'));
            if ( $words_condition )
            {
                $this->db->where($words_condition);
            }
            
        //Order
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('created_at', 'DESC');
            }
            
        //Filtros
            $this->db->where($role_filter); //Filtro según el rol de tag en sesión
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
        if ( is_null($per_page) )
        {
            $query = $this->db->get('tags'); //Resultados totales
        } else {
            $query = $this->db->get('tags', $per_page, $offset); //Resultados por página
        }
        
        return $query;
        
    }
    
    /**
     * Devuelve la cantidad de registros encontrados en la tabla con los filtros
     * establecidos en la búsqueda
     * 
     * @param type $filters
     * @return type
     */
    function search_num_rows($filters)
    {
        $query = $this->search($filters); //Para calcular el total de resultados
        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL
     * 
     * @param type $tag_id
     * @return type 
     */
    function role_filter()
    {
        
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún tag, se obtendrían cero tags.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los tag
            $condition = 'id > 0';
        }
        
        return $condition;
    }
}