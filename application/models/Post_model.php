<?php
class Post_model extends CI_Model{

    function basic($post_id)
    {
        $row = $this->Db_model->row_id('posts', $post_id);

        $data['row'] = $row;
        $data['type_folder'] = $this->type_folder($row->type_id);
        $data['head_title'] = $data['row']->post_name;
        $data['view_a'] = $this->views_folder . 'post_v';
        $data['nav_2'] = $data['type_folder'] . 'menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - posts/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'posts';                       //Nombre del controlador
            $data['cf'] = 'posts/explore/';                      //Nombre del controlador
            $data['views_folder'] = 'admin/posts/explore/';      //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                       //Número de la página
            
        //Vistas
            $data['head_title'] = 'Posts';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    function get($filters, $num_page, $per_page = 10)
    {
        //Load
            $this->load->model('Search_model');

        //Búsqueda y Resultados
            $data['filters'] = $filters;
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['list'] = $elements->result();
            $data['str_filters'] = $this->Search_model->str_filters($filters);
            $data['search_num_rows'] = $this->search_num_rows($filters);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    }
    
    /**
     * String con condición WHERE SQL para filtrar post
     */
    function search_condition_org($filters)
    {
        $condition = NULL;
        
        //Tipo de post
        if ( $filters['type'] != '' ) { $condition .= "type_id = {$filters['type']} AND "; }
        if ( $filters['condition'] != '' ) { $condition .= "{$filters['condition']} AND "; }
        
        if ( strlen($condition) > 0 )
        {
            $condition = substr($condition, 0, -5);
        }
        
        return $condition;
    }

    /**
     * Segmento Select SQL, con diferentes formatos, consulta de products
     * 2020-12-12
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'id, post_name, excerpt, type_id, url_thumbnail';
        $arr_select['export'] = '*';
        $arr_select['5_album'] = 'id, post_name AS name, status, slug, related_1 AS girl_id, related_2 AS cat_clothes, integer_1 AS album_type, integer_2 AS price, url_image, url_thumbnail';
        $arr_select['6_picture'] = 'id, content, posts.related_1 AS user_id, text_1 AS file_name, text_2 AS private_file_name, related_1 AS girl_id, status, parent_id AS album_id';

        return $arr_select[$format];
    }
    
    /**
     * Query con resultados de posts filtrados, por página y offset
     * 2020-07-15
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Segmento SELECT
            $select_format = 'general';
            if ( $filters['sf'] != '' ) { $select_format = $filters['sf']; }
            $this->db->select($this->select($select_format));
        
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('updated_at', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('posts', $per_page, $offset); //Resultados por página
        
        return $query;
        
    }

    /**
     * String con condición WHERE SQL para filtrar post
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('post_name', 'content', 'excerpt', 'keywords'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['type'] != '' ) { $condition .= "type_id = {$filters['type']} AND "; }
        if ( $filters['condition'] != '' ) { $condition .= "{$filters['condition']} AND "; }
        
        //Quitar cadena final de ' AND '
        if ( strlen($condition) > 0 ) { $condition = substr($condition, 0, -5);}
        
        return $condition;
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
        $query = $this->db->get('posts'); //Para calcular el total de resultados

        return $query->num_rows();
    }

    /**
     * Query para exportar
     * 2020-12-12
     */
    function export($filters)
    {
        $this->db->select($this->select('export'));
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('posts', 5000);  //Hasta 5000 productos

        return $query;
    }
    
    /**
     * Devuelve segmento SQL
     */
    function role_filter()
    {
        
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún post, se obtendrían cero posts.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los post
            $condition = 'id > 0';
        } else {
            $condition = 'type_id IN (6)';
        }
        
        return $condition;
    }
    
    /**
     * Array con options para ordenar el listado de post en la vista de
     * exploración
     */
    function order_options()
    {
        $order_options = array(
            '' => '[ Ordenar por ]',
            'id' => 'ID Post',
            'post_name' => 'Nombre'
        );
        
        return $order_options;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Objeto registro de un post ID, con un formato específico
     * 2021-01-04
     */
    function row($post_id, $format = 'general')
    {
        $row = NULL;    //Valor por defecto

        $this->db->select($this->select($format));
        $this->db->where('id', $post_id);
        $query = $this->db->get('posts', 1);

        if ( $query->num_rows() > 0 ) $row = $query->row();

        return $row;
    }

    function save()
    {
        $data['saved_id'] = $this->Db_model->save_id('posts');
        return $data;
    }

    /**
     * Nombre de la vista con el formulario para la edición del post. Puede cambiar dependiendo
     * del tipo (type_id).
     * 2021-03-26
     */
    function type_folder($type_id)
    {
        $type_folder = $this->views_folder;
        $special_types = array(6,7);

        if ( in_array($type_id, $special_types) ) { $type_folder = "{$this->views_folder}/types/{$type_id}/"; }

        return $type_folder;
    }

// ELIMINACIÓN DE UN POST
//-----------------------------------------------------------------------------
    
    /**
     * Verifica si el usuario en sesión tiene permiso para eliminar un registro tabla post
     * 2020-08-18
     */
    function deleteable($row_id)
    {
        $row = $this->Db_model->row_id('posts', $row_id);

        $deleteable = 0;
        if ( in_array($this->session->userdata('role'), array(1,2,3)) ) $deleteable = 1;                   //Es Administrador
        if ( $row->creator_id = $this->session->userdata('user_id') ) $deleteable = 1;  //Es el creador

        return $deleteable;
    }

    /**
     * Eliminar un post de la base de datos, se eliminan registros de tablas relacionadas
     * 2020-08-18
     */
    function delete($post_id)
    {
        $qty_deleted = 0;

        if ( $this->deleteable($post_id) ) 
        {
            //Tablas relacionadas
                $this->db->where('parent_id', $post_id)->delete('posts');
                //$this->db->where('post_id', $post_id)->delete('post_meta');
            
            //Tabla principal
                $this->db->where('id', $post_id)->delete('posts');

            $qty_deleted = $this->db->affected_rows();  //De la última consulta, tabla principal
        }

        return $qty_deleted;
    }

// VALIDATION
//-----------------------------------------------------------------------------

    function arr_row($process = 'update')
    {
        $arr_row = $this->input->post();
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        
        if ( $process == 'insert' )
        {
            $arr_row['slug'] = $this->Db_model->unique_slug($arr_row['post_name'], 'posts');
            $arr_row['creator_id'] = $this->session->userdata('user_id');
        }
        
        return $arr_row;
    }

// IMAGES
//-----------------------------------------------------------------------------

    /**
     * Imágenes asociadas al post
     * 2020-09-05
     */
    function images($post_id)
    {
        $this->db->select('files.id, files.title, url, url_thumbnail, files.integer_1 AS main');
        $this->db->where('is_image', 1);
        $this->db->where('table_id', '2000');      //Tabla post
        $this->db->where('related_1', $post_id);   //Relacionado con el post
        $images = $this->db->get('files');

        return $images;
    }

    /**
     * Establecer una imagen asociada a un post como la imagen principal (tabla file)
     * 2020-09-05
     */
    function set_main_image($post_id, $file_id)
    {
        $data = array('status' => 0);

        $row_file = $this->Db_model->row_id('files', $file_id);
        if ( ! is_null($row_file) )
        {
            //Quitar otro principal
            $this->db->query("UPDATE files SET integer_1 = 0 WHERE table_id = 2000 AND related_1 = {$post_id} AND integer_1 = 1");

            //Poner nuevo principal
            $this->db->query("UPDATE files SET integer_1 = 1 WHERE id = {$file_id} AND related_1 = {$post_id}");

            //Actualizar registro en tabla post
            $arr_row['image_id'] = $row_file->id;
            $arr_row['url_image'] = $row_file->url;
            $arr_row['url_thumbnail'] = $row_file->url_thumbnail;

            $this->db->where('id', $post_id);
            $this->db->update('posts', $arr_row);

            $data['status'] = 1;
        }

        return $data;
    }

// IMPORTAR
//-----------------------------------------------------------------------------}

    /**
     * Array con configuración de la vista de importación según el tipo de usuario
     * que se va a importar.
     * 2019-11-20
     */
    function import_config($type)
    {
        $data = array();

        if ( $type == 'general' )
        {
            $data['help_note'] = 'Se importarán posts a la base de datos.';
            $data['help_tips'] = array();
            $data['template_file_name'] = 'f50_posts.xlsx';
            $data['sheet_name'] = 'posts';
            $data['head_subtitle'] = 'Importar';
            $data['destination_form'] = "posts/import_e/{$type}";
        }

        return $data;
    }

    /**
     * Importa posts a la base de datos
     * 2020-02-22
     */
    function import($arr_sheet)
    {
        $data = array('qty_imported' => 0, 'results' => array());
        
        foreach ( $arr_sheet as $key => $row_data )
        {
            $data_import = $this->import_post($row_data);
            $data['qty_imported'] += $data_import['status'];
            $data['results'][$key + 2] = $data_import;
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel. Valida los campos, crea registro
     * en la tabla post, y agrega al grupo asignado.
     * 2020-02-22
     */
    function import_post($row_data)
    {
        //Validar
            $error_text = '';
                            
            if ( strlen($row_data[0]) == 0 ) { $error_text = 'La casilla Nombre está vacía. '; }
            if ( strlen($row_data[1]) == 0 ) { $error_text .= 'La casilla Cod Tipo está vacía. '; }
            if ( strlen($row_data[2]) == 0 ) { $error_text .= 'La casilla Resumen está vacía. '; }
            if ( strlen($row_data[14]) == 0 ) { $error_text .= 'La casilla Fecha Publicación está vacía. '; }

        //Si no hay error
            if ( $error_text == '' )
            {
                $arr_row['post_name'] = $row_data[0];
                $arr_row['type_id'] = $row_data[1];
                $arr_row['excerpt'] = $row_data[2];
                $arr_row['content'] = $row_data[3];
                $arr_row['content_json'] = $row_data[4];
                $arr_row['keywords'] = $row_data[5];
                $arr_row['code'] = $row_data[6];
                $arr_row['place_id'] = $this->pml->if_strlen($row_data[7], 0);
                $arr_row['related_1'] = $this->pml->if_strlen($row_data[8], 0);
                $arr_row['related_2'] = $this->pml->if_strlen($row_data[9], 0);
                $arr_row['image_id'] = $this->pml->if_strlen($row_data[10], 0);
                $arr_row['text_1'] = $this->pml->if_strlen($row_data[11], '');
                $arr_row['text_2'] = $this->pml->if_strlen($row_data[12], '');
                $arr_row['status'] = $this->pml->if_strlen($row_data[13], 2);
                $arr_row['published_at'] = $this->pml->dexcel_dmysql($row_data[14]);
                $arr_row['slug'] = $this->Db_model->unique_slug($row_data[0], 'posts');
                
                $arr_row['creator_id'] = $this->session->userdata('user_id');
                $arr_row['updater_id'] = $this->session->userdata('user_id');

                //Guardar en tabla user
                $data_insert = $this->insert($arr_row);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $data_insert['saved_id']);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

// INTERACCIÓN DE USUARIOS
//-----------------------------------------------------------------------------

    /**
     * Proceso alternado, like or unlike un post, registro type 10 en la tabla users_meta
     * 2020-12-22
     */
    function alt_like($post_id)
    {
        //Condición
        $condition = "related_1 = {$post_id} AND type_id = 10 AND user_id = {$this->session->userdata('user_id')}";

        $row_meta = $this->Db_model->row('users_meta', $condition);

        $data = array('status' => 0);

        if ( is_null($row_meta) )
        {
            //No existe: like
            $arr_row['user_id'] = $this->session->userdata('user_id');
            $arr_row['related_1'] = $post_id;
            $arr_row['type_id'] = 10; //Like de un post
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['creator_id'] = $this->session->userdata('user_id');

            $this->db->insert('users_meta', $arr_row);
            
            $data['saved_id'] = $this->db->insert_id();
            $data['status'] = 1;

            //$this->db->query("UPDATE post SET ");
        } else {
            //Existe, eliminar (Unlike)
            $this->db->where('id', $row_meta->id);
            $this->db->delete('users_meta');
            
            $data['qty_deleted'] = $this->db->affected_rows();
            $data['status'] = 2;
        }

        return $data;
    }

// Asignación a usuario
//-----------------------------------------------------------------------------

    /**
     * Asignar un contenido de la tabla post a un usuario, lo agrega como metadato
     * en la tabla users_meta, con el tipo 100012
     * 2020-04-15
     */
    function add_to_user($post_id, $user_id)
    {
        //Construir registro
        $arr_row['user_id'] = $user_id;     //Usuario ID, al que se asigna
        $arr_row['type_id'] = 100012;       //Asignación de post
        $arr_row['related_1'] = $post_id;   //ID contenido
        $arr_row['updater_id'] = 100001;    //Usuario que asigna
        $arr_row['creator_id'] = 100001;    //Usuario que asigna

        //Establecer usuario que ejecuta
        if ( $this->session->userdata('logged') ) {
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['creator_id'] = $this->session->userdata('user_id');
        }

        $condition = "type_id = {$arr_row['type_id']} AND user_id = {$arr_row['user_id']} AND related_1 = {$arr_row['related_1']}";
        $meta_id = $this->Db_model->save('users_meta', $condition, $arr_row);

        //Establecer resultado
        $data = array('status' => 0, 'saved_id' => '0');
        if ( $meta_id > 0) { $data = array('status' => 1, 'saved_id' => $meta_id); }

        return $data;
    }

    /**
     * Quita la asignación de un post a un usuario
     * 2020-04-30
     */
    function remove_to_user($post_id, $meta_id)
    {
        $data = array('status' => 0, 'qty_deleted' => 0);

        $this->db->where('id', $meta_id);
        $this->db->where('related_1', $post_id);
        $this->db->delete('users_meta');

        $data['qty_deleted'] = $this->db->affected_rows();

        if ( $data['qty_deleted'] > 0) { $data['status'] = 1; }

        return $data;
    }

// ESPECIAL POSTS PAGADOS
//-----------------------------------------------------------------------------

    /**
     * ESPECIAL
     * Asignar un contenido de la tabla post a un usuario, lo agrega como metadato
     * en la tabla users_meta, con el tipo 100012, asignado un valor monetario pagado o descontado
     * 2020-08-20
     */
    function add_to_user_payed($post_id, $user_id, $price)
    {
        //Construir registro
            $arr_row['user_id'] = $user_id;     //Usuario ID, al que se asigna
            $arr_row['type_id'] = 100012;       //Asignación de post
            $arr_row['related_1'] = $post_id;   //ID contenido
            $arr_row['integer_2'] = $price;     //Precio del post
            $arr_row['updater_id'] = $this->session->userdata('user_id');    //Usuario que asigna
            $arr_row['creator_id'] = $this->session->userdata('user_id');    //Usuario que asigna

        //Establecer usuario que ejecuta
        if ( $this->session->userdata('logged') )
        {
            $arr_row['updater_id'] = $this->session->userdata('user_id');
            $arr_row['creator_id'] = $this->session->userdata('user_id');
        }

        $condition = "type_id = {$arr_row['type_id']} AND user_id = {$arr_row['user_id']} AND related_1 = {$arr_row['related_1']}";
        $meta_id = $this->Db_model->insert_if('users_meta', $condition, $arr_row);

        //Establecer resultado
        $data = array('status' => 0, 'saved_id' => '0');
        if ( $meta_id > 0) { $data = array('status' => 1, 'saved_id' => $meta_id); }

        return $data;
    }

// Seguimiento
//-----------------------------------------------------------------------------
    /**
     * Guardar evento de apertura de post
     * 2020-04-26
     */
    function save_open_event($post_id)
    {
        $arr_row['type_id'] = 51;   //Apertura de post
        $arr_row['start'] = date('Y-m-d H:i:s');
        $arr_row['end'] = date('Y-m-d H:i:s');
        $arr_row['created_at'] = date('Y-m-d H:i:s');
        $arr_row['ip_address'] = $this->input->ip_address();
        $arr_row['element_id'] = $post_id;

        if( ! is_null($this->session->userdata('user_id')) )
        {
            $arr_row['user_id'] = $this->session->userdata('user_id');
            $arr_row['creator_id'] = $this->session->userdata('user_id');
        }

        $event_id = $this->Db_model->save('events', 'id = 0', $arr_row);

        if ( $event_id > 0 ) $this->update_qty_read($post_id);

        return $event_id;
    }

    function update_qty_read($post_id)
    {
        $arr_row['qty_read'] = $this->Db_model->num_rows('events', "type_id = 51 AND element_id = {$post_id}");

        $this->db->where('id', $post_id);
        $this->db->update('posts', $arr_row);
        
        return $this->db->affected_rows();
    }
}