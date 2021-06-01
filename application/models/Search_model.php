<?php
class Search_model extends CI_Model{
    
    function words($search_text)
    {
        
        $words = array();
        
        if ( strlen($search_text) > 2 ){
            
            $no_buscar = array('la', 'el', 'los', 'las', 'del', 'de','y');

            $words = explode(' ', $search_text);

            foreach ($words as $key => $palabra)
            {
                if ( in_array($palabra, $no_buscar ) )
                {
                    unset($words[$key]);
                }
            }
        }
        
        return $words;
    }
    
    /**
     * Array de índices
     * 
     * @return string
     */
    function search_indexes()
    {
        $search_indexes = array(
            'q',        //Query or search text
            'cat',      //Category
            'type',     //Type ID
            'status',   //Status
            'u',        //User ID
            'gender',   //Gender ID
            'plc',      //places.id
            'role',     //User role
            'e',        //edited at
            'num_min',  //Valor numérico mínimo
            'num_max',  //Valor numérico máximo
            'prnt',     //Padre o superior
            'org',      //Organization or company
            'y',        //Year, año generación
            'd1',       //Initial date
            'd2',       //Final date
            'condition',//SQL Where additional condition
            'fe1',      //Filtro especial 1
            'fe2',      //Filtro especial 2
            'fe3',       //Filtro especial 3
            'o',        //Order by
            'ot',       //Order type
            'sf',       //Select format
        );
        
        return $search_indexes;
    }
    
    /**
     * Array de búsqueda con valor NULL para todos los índices
     * Valor inicial antes de evaluar contenido de POST y GET
     */
    function default_filters()
    {
        $search_indexes = $this->search_indexes();
        foreach ($search_indexes as $index) { $search[$index] = NULL; }
        return $search;
    }
    
    /**
     * Array con los parámetros de una búsqueda, respuesta para los dos métodos
     * de solicitud POST y GET.
     * 2020-07-28
     */
    function filters()
    {
        $search = $this->default_filters();
        $search_indexes = $this->search_indexes();
        
        if ( $this->input->post() )
        {
            //POST form search
            foreach ($search_indexes as $index) 
            {
                $search[$index] = $this->input->post($index);
            }
        } else {            
            //Search by GET in URL
            foreach ($search_indexes as $index) 
            {
                $search[$index] = $this->input->get($index);
            }
        }
            
        return $search;
    }
    
    /**
     * String con la cadena para URL tipo GET, con los valores de filtros de la búsqueda
     * 2020-07-15
     */
    function str_filters($filters = NULL)
    {

        if ( is_null($filters) ) { $filters = $this->filters(); }   //Si no están definidos

        $search_indexes = $this->search_indexes();
        $str_filters = '';
        
        foreach ( $search_indexes as $index ) 
        {
            $value = $filters[$index];
            if ( $filters[$index] != '' ) { $str_filters .= "{$index}={$value}&"; }
        }

        //Preparar
        $str_filters = str_replace(' ','+',$str_filters);               //Reemplazar espacios por signo +
        $str_filters = str_replace(array('<','>','?'),'',$str_filters); //Quitar caractéres especiales*/
        
        return $str_filters;
    }
    
    /**
     * String con segmento SQL con campos concatenados para realizar una búsqueda conjunta
     * 2020-07-28
     */
    function concat_fields($fields)
    {
        $concat_fields = '';
        
        foreach ( $fields as $field ) 
        {
            $concat_fields .= "IFNULL({$field}, ''), ";
        }
        
        return substr($concat_fields, 0, -2);
    }
    
    /**
     * String SQL WHERE buscando cada palabra de un texto, en una concatenación de campos de una tabla
     * 2020-07-28 
     */
    function words_condition($text_search, $fields)
    {
        $condition = NULL;
        
        if ( strlen($text_search) > 2 )
        {
            $concat_fields = $this->concat_fields($fields); //Campos concatenados
            $words = $this->words($text_search);            //Array con palabras buscadas (q)

            foreach ($words as $word) 
            {
                $condition .= "CONCAT({$concat_fields}) LIKE '%{$word}%' AND ";
            }
            
            $condition = substr($condition, 0, -5); //Quitar el último segmento ' AND ', no utilizado
        }
        
        return $condition;
    }
}