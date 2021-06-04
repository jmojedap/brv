<?php
class Comment_model extends CI_Model{

    function basic($comment_id)
    {
        $row = $this->Db_model->row_id('comments', $comment_id);

        $data['comment_id'] = $comment_id;
        $data['row'] = $row;
        $data['head_title'] = substr($data['row']->comment_text,0,50);
        $data['nav_2'] = 'comments/menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - comments/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'comments';                      //Nombre del controlador
            $data['cf'] = 'comments/explore/';                      //Nombre del controlador
            $data['views_folder'] = 'admin/comments/explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Comentarios';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de comments, filtrados por búsqueda y num página, más datos adicionales sobre
     * la búsqueda, filtros aplicados, total resultados, página máxima.
     * 2020-08-01
     */
    function get($filters, $num_page, $per_page)
    {
        //Referencia
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Búsqueda y Resultados
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['filters'] = $filters;
            $data['list'] = $this->list($filters, $per_page, $offset);    //Resultados para página
            $data['str_filters'] = $this->Search_model->str_filters();      //String con filtros en formato GET de URL
            $data['search_num_rows'] = $this->search_num_rows($data['filters']);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de usuarios
     * 2020-12-12
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'comments.*';

        //$arr_select['export'] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de comments, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $this->db->select($this->select());
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('id', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('comments', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar comments
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('comment_text'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['fe1'] != '' ) { $condition .= "table_id = {$filters['fe1']} AND "; }
        if ( $filters['fe2'] != '' ) { $condition .= "element_id = '{$filters['fe2']}' AND "; }
        if ( $filters['prnt'] != '' ) { $condition .= "parent_id = '{$filters['prnt']}' AND "; }
        
        //Quitar cadena final de ' AND '
        if ( strlen($condition) > 0 ) { $condition = substr($condition, 0, -5);}
        
        return $condition;
    }

    /**
     * Array Listado elemento resultado de la búsqueda (filtros).
     * 2020-06-19
     */
    function list($filters, $per_page = NULL, $offset = NULL)
    {
        $query = $this->search($filters, $per_page, $offset);
        $list = array();

        foreach ($query->result() as $row)
        {
            /*$row->qty_students = $this->Db_model->num_rows('group_user', "group_id = {$row->id}");  //Cantidad de estudiantes*/
            /*if ( $row->image_id == 0 )
            {
                $first_image = $this->first_image($row->id);
                $row->url_image = $first_image['url'];
                $row->url_thumbnail = $first_image['url_thumbnail'];
            }*/
            $list[] = $row;
        }

        return $list;
    }
    
    /**
     * Devuelve la cantidad de registros encontrados en la tabla con los filtros
     * establecidos en la búsqueda
     */
    function search_num_rows($filters)
    {
        $this->db->select('id');
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('comments'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de usuarios según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún user, se obtendrían cero comments.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los user
            $condition = 'comments.id > 0';
        }
        
        return $condition;
    }

// CRUD FUNCTIONS
//-----------------------------------------------------------------------------
    
    /**
     * Insertar un registro en la tabla comment.
     * 2021-04-27
     */
    function save($table_id, $element_id)
    {
        $data = array('saved_id' => 0);

        if ( $this->insertable($element_id) )
        {
            $arr_row = $this->input->post();
            $arr_row['table_id'] = $table_id;       //Tabla del elemento comentado
            $arr_row['element_id'] = $element_id;   //ID del elemento comentado
            $arr_row['creator_id'] = $this->session->userdata('user_id');
            $arr_row['created_at'] = date('Y-m-d H:i:s');

            //Insertar en la tabla
                $this->db->insert('comments', $arr_row);
                $data['saved_id'] = $this->db->insert_id();

                //Actualizar los contadores
                $this->update_qty_comments($table_id, $element_id, 1);

                //Si es una respuesta, tiene padre, actualizar contadores
                if ( $arr_row['parent_id'] > 0 ) { $this->update_qty_answers($arr_row['parent_id'], 1); }
        }
        
        return $data;
    }

    /**
     * Verificar si los datos enviados por POST cumplen las condiciones para insertar
     * un comentario (comment)
     */
    function insertable($element_id)
    {
        $insertable = FALSE;
        $conditions = 0;

        if ( strlen($this->input->post('comment_text')) > 0 ) { $conditions++; }
        if ( strlen($element_id) > 0 ) { $conditions++; }

        if ( $conditions == 2 ) { $insertable = TRUE; }

        return $insertable;
    }

    /**
     * Proceso alternado, like o unlike de un comentario
     * 2021-05-18
     */
    function alt_like($comment_id)
    {
        //Condición
        $condition = "user_id = {$this->session->userdata('user_id')} AND related_1 = {$comment_id} AND type_id = 1063";

        $row_meta = $this->Db_model->row('users_meta', $condition);

        $data = array();

        if ( is_null($row_meta) )
        {
            //No existe, crear like
            $arr_row['user_id'] = $this->session->userdata('user_id');
            $arr_row['related_1'] = $comment_id;
            $arr_row['type_id'] = 1063; //Like comment
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['creator_id'] = $this->session->userdata('user_id');

            $this->db->insert('users_meta', $arr_row);
            
            $data['saved_id'] = $this->db->insert_id();
            $data['qty_sum'] = 1;
            $data['like_status'] = 1;
        } else {
            //Existe, eliminar like
            $this->db->where('id', $row_meta->id)->delete('users_meta');
            
            $data['qty_sum'] = -1;
            $data['like_status'] = 0;
        }

        //Actualizar contador en registro tabla post
        $this->db->query("UPDATE comments SET score = (score + ({$data['qty_sum']})) WHERE id = {$comment_id}");

        return $data;
    }

// INFO
//-----------------------------------------------------------------------------

    /**
     * Array con listado de comentarios
     */
    function element_comments($table_id, $element_id, $parent_id, $num_page, $per_page = 10)
    {
        $query_comments = $this->element_comments_pre($table_id, $element_id, $parent_id, $num_page, $per_page);

        $comments = array();

        foreach ($query_comments->result() as $comment)
        {
            //Identificar si al usuario en sesión le gusta el comentario.
            $condition = "user_id = {$this->session->userdata('user_id')} AND type_id = 1063 AND related_1 = {$comment->id}";
            $comment->liked = $this->Db_model->num_rows('users_meta', $condition);

            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Query con listado de comentarios, si se agrega $parent_id se filtran los subcomentarios
     * hechos al comentario con ID = $parent_id.
     */
    function element_comments_pre($table_id, $element_id, $parent_id, $num_page, $per_page)
    {
        $offset = $per_page * ($num_page - 1);

        $this->db->select('comments.id, comment_text, parent_id, score, qty_comments, comments.created_at, comments.creator_id, users.username, users.display_name, users.url_thumbnail AS user_thumbnail');
        $this->db->where('element_id', $element_id);
        $this->db->where('table_id', $table_id);
        $this->db->where('parent_id', $parent_id);
        $this->db->order_by('comments.created_at', 'DESC');
        $this->db->join('users', 'users.id = comments.creator_id');
        $comments = $this->db->get('comments', $per_page, $offset);

        return $comments;
    }

    /**
     * Array, información adicional sobre listado de comentarios de un elemento:
     * Cantidad de comentarios y cantidad máxima de páginas en las que se podrían separar los comentarios 
     * de un elemento teniendo en cuenta una cantidad de comentarios por página ($per_page)
     * 2021-04-06
     */
    function element_comments_meta($table_id, $element_id, $parent_id, $per_page)
    {
        $data = array('total_comments' => 0, 'max_page' => 1);

        $this->db->select('comments.id');
        $this->db->where('element_id', $element_id);
        $this->db->where('table_id', $table_id);
        $this->db->where('parent_id', $parent_id);
        $comments = $this->db->get('comments');

        if ( $comments->num_rows() > 0 && $per_page > 0 )
        {
            $data['total_comments'] = $comments->num_rows();
            $data['max_page'] = ceil($comments->num_rows() / $per_page);
        }

        return $data;
    }

    /**
     * Cantidad máxima de páginas en las que se podrían separar los comentarios de un elemento
     * teniendo en cuenta una cantidad de comentarios por página ($per_page)
     * 2021-04-06
     */
    function max_page($table_id, $element_id, $parent_id, $per_page)
    {
        $max_page = 1;  //Valor por defecto

        $this->db->select('comments.id');
        $this->db->where('element_id', $element_id);
        $this->db->where('table_id', $table_id);
        $this->db->where('parent_id', $parent_id);
        $comments = $this->db->get('comments');

        if ( $comments->num_rows() > 0 && $per_page > 0 )
        {
            $max_page = ceil($comments->num_rows() / $per_page);
        }

        return $max_page;
    }

// ELIMINACIÓN DE COMENTARIOS
//-----------------------------------------------------------------------------

    //Establece si un comentario puede ser eliminado o no por el usuario en sesión.
    function deleteable($row)
    {
        $deleteable = FALSE;

        if ( ! is_null($row) )  //Existe
        {
            if ( $this->session->userdata('role') <= 2 ) { $deleteable = TRUE; }    //Tiene Rol Editor o superior
            if ( $this->session->userdata('user_id') == $row->creator_id ) { $deleteable = TRUE; }  //Es quien creó el comentario
        }

        return $deleteable;
    }

    /**
     * Delete a row in comment table
     */
    function delete($comment_id)
    {
        $row = $this->Db_model->row_id('comments', $comment_id);
        $qty_deleted = 0;

        if ( $this->deleteable($row) )
        {
            //Eliminar comentario y sus descendientes
            $this->db->where("id = {$comment_id} OR parent_id = {$comment_id}");
            $this->db->delete('comments');

            $qty_deleted = $this->db->affected_rows();

            //Actualizar contador de comentarios en tabla elemento
            if ( $qty_deleted > 0 )
            {
                $this->update_qty_comments($row->table_id, $row->element_id, -1 * $qty_deleted);
            }

            //Actualizar contador de comentarios en registro padre (sí tiene)
            if ( $row->parent_id > 0 )
            {
                $this->update_qty_answers($row->parent_id, -1 * $qty_deleted);
            }
        }
        
        return $qty_deleted;
    }

// CÁLCULO DE CANTIDAD DE COMENTARIOS
//-----------------------------------------------------------------------------

    /**
     * Después de agregar o eliminar un comentario, se actualiza el campo post.qty_comments.
     */
    function update_qty_comments($table_id, $element_id, $qty_sum = 1)
    {
        $table_name = $this->Item_model->name(30, $table_id);

        if ( ! is_null($qty_sum) ) {
            $sql = "UPDATE {$table_name} SET qty_comments = qty_comments + ({$qty_sum}) WHERE id = {$element_id}";
            $this->db->query($sql);
        } else {
            //Si $qty_sum es NULL, Se calcula el valor total desde la tabla comments
            $arr_row['qty_comments'] = $this->Db_model->num_rows('comments', "table_id = {$table_id} AND element_id = {$element_id}");
    
            $this->db->where('id', $element_id);
            $this->db->update($table_name, $arr_row);
        }
    }

    /**
     * Después de agregar o eliminar un comentario, se actualiza el campo comment.qty_comments.
     */
    function update_qty_answers($comment_id, $qty_sum = 1)
    {
        if ( ! is_null($qty_sum) ) {
            $sql = "UPDATE comments SET qty_comments = qty_comments + ({$qty_sum}) WHERE id = {$comment_id}";
            $this->db->query($sql);
        } else {
            //Si $qty_sum es NULL, Se calcula el valor total desde la tabla comments
            $arr_row['qty_comments'] = $this->Db_model->num_rows('comments', "parent_id = {$comment_id}");
    
            $this->db->where('id', $comment_id);
            $this->db->update('comments', $arr_row);
        }
    }
}