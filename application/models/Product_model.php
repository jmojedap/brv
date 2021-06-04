<?php
class Product_model extends CI_Model{

    function basic($product_id)
    {
        $data['product_id'] = $product_id;
        $data['row'] = $this->Db_model->row_id('products', $product_id);
        $data['head_title'] = $data['row']->name;
        $data['nav_2'] = 'admin/products/menu_v';
        $data['back_link'] = $this->url_controller . 'explore';

        return $data;
    }

// EXPLORE FUNCTIONS - products/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'products';                       //Nombre del controlador
            $data['cf'] = 'products/explore/';                      //Nombre del controlador
            $data['views_folder'] = 'admin/products/explore/'; //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                          //Número de la página
            
        //Vistas
            $data['head_title'] = 'Productos';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de products, filtrados por búsqueda y num página, más datos adicionales sobre
     * la búsqueda, filtros aplicados, total resultados, página máxima.
     * 2020-08-01
     */
    function get($filters, $num_page, $per_page = 8)
    {
        //Load
            $this->load->model('Search_model');

        //Búsqueda y Resultados
            $data['filters'] = $filters;
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado
            $elements = $this->search($filters, $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['filters'] = $filters;
            $data['list'] = $this->list($filters, $per_page, $offset);      //Resultados para página
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
        $arr_select['general'] = 'products.id, name, slug, description, keywords, price, stock, image_id, url_image, url_thumbnail, status, products.type_id, created_at, updated_at';

        //$arr_select['export'] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de products, filtrados según búsqueda, limitados por página
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
                $this->db->order_by('updated_at', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('products', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar products
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('code', 'name', 'description', 'keywords'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        //if ( $filters['cat_1'] != '' ) { $condition .= "role = {$filters['cat_1']} AND "; }
        
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
        $query = $this->db->get('products'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de usuarios según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'products.id = 0';  //Valor por defecto, ningún user, se obtendrían cero registros.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los registros
            $condition = 'products.id > 0';
        }
        
        return $condition;
    }
    
    /**
     * Array con options para ordenar el listado de user en la vista de
     * exploración
     * 
     */
    function order_options()
    {
        $order_options = array(
            '' => '[ Ordenar por ]',
            'id' => 'ID Producto',
            'name' => 'Nombre',
            'price' => 'Precio',
        );
        
        return $order_options;
    }
    
    /**
     * Establece si un usuario en sesión puede o no editar los datos de un producto
     */
    function editable($product_id)
    {
        $editable = FALSE;
        if ( $this->session->userdata('role') <= 2 ) { $editable = TRUE; }
        if ( $this->session->userdata('product_id') == $product_id ) { $editable = TRUE; }

        return $editable;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Guardar registro de producto
     * 2021-03-19
     */
    function save()
    {
        $data['saved_id'] = $this->Db_model->save_id('products');
        return $data;
    }

// ELINMINACIÓN
//-----------------------------------------------------------------------------
       
    /**
     * Establece permiso para eliminar un producto
     */
    function deletable($product_id)
    {
        $deletable = 0;
        $row = $this->Db_model->row_id('products', $product_id);

        if ( $this->session->userdata('role') <= 1 ) { $deletable = 1; }
        
        return $deletable;
    }

    /**
     * Eliminar un usuario de la base de datos, se elimina también de
     * las tablas relacionadas
     */
    function delete($product_id)
    {
        $qty_deleted = 0;

        if ( $this->deletable($product_id) ) 
        {
            //Tablas relacionadas

                //meta
                /*$this->db->where('table_id', 1000); //Tabla usuario
                $this->db->where('product_id', $product_id);
                $this->db->delete('meta');*/
            
            //Tabla principal
                $this->db->where('id', $product_id);
                $this->db->delete('products');

            $qty_deleted = $this->db->affected_rows();
        }

        return $qty_deleted;
    }

// GESTIÓN DE CAMPOS DEPENDIENTES
//-----------------------------------------------------------------------------

    /**
     * Actualiza los campos adicionales de la tabla producto
     * 
     */
    function update_dependent($product_id) 
    {
        //Datos iniciales
            $row = $this->Db_model->row_id('products', $product_id);
        
        //Construir registro
            $arr_row['name'] = $this->generate_name($row->id);
            $arr_row['letter'] = strtoupper($row->letter);
            
        //Si está vacío el título
            if ( strlen($row->title) == 0 ) { $arr_row['title'] = $arr_row['title']; }
        
        //Actualizar
            $this->db->where('id', $product_id);
            $this->db->update('products', $arr_row);
    }

// IMAGES
//-----------------------------------------------------------------------------

    /**
     * Imágenes asociadas al producto
     * 2021-02-24
     */
    function images($product_id)
    {
        $this->db->select('files.id, files.title, url, url_thumbnail, files.integer_1 AS main');
        $this->db->where('is_image', 1);
        $this->db->where('table_id', 3100);           //Tabla products
        $this->db->where('related_1', $product_id);   //Relacionado con el product
        $images = $this->db->get('files');

        return $images;
    }

    /**
     * Establecer una imagen asociada a un product como la imagen principal (tabla file)
     * 2021-02-24
     */
    function set_main_image($product_id, $file_id)
    {
        $data = array('status' => 0);

        $row_file = $this->Db_model->row_id('files', $file_id);
        if ( ! is_null($row_file) )
        {
            //Quitar otro principal
            $this->db->query("UPDATE files SET integer_1 = 0 WHERE table_id = 3100 AND related_1 = {$product_id} AND integer_1 = 1");

            //Poner nuevo principal
            $this->db->query("UPDATE files SET integer_1 = 1 WHERE id = {$file_id} AND related_1 = {$product_id}");

            //Actualizar registro en tabla products
            $arr_row['image_id'] = $row_file->id;
            $arr_row['url_image'] = $row_file->url;
            $arr_row['url_thumbnail'] = $row_file->url_thumbnail;

            $this->db->where('id', $product_id);
            $this->db->update('products', $arr_row);

            $data['status'] = 1;
        }

        return $data;
    }

// IMPORTAR
//-----------------------------------------------------------------------------}

    /**
     * Array con configuración de la vista de importación según el tipo de usuario
     * que se va a importar.
     * 2020-02-27
     */
    function import_config($type)
    {
        $data = array();

        if ( $type == 'general' )
        {
            $data['help_note'] = 'Se importarán productos a la base de datos.';
            $data['help_tips'] = array();
            $data['template_file_name'] = 'f60_productos.xlsx';
            $data['sheet_name'] = 'productos';
            $data['head_subtitle'] = 'Importar';
            $data['destination_form'] = "products/import_e/{$type}";
        }

        return $data;
    }

    /**
     * Importa posts a la base de datos
     * 2020-02-27
     */
    function import($arr_sheet)
    {
        $data = array('qty_imported' => 0, 'results' => array());
        
        foreach ( $arr_sheet as $key => $row_data )
        {
            $data_import = $this->import_product($row_data);
            $data['qty_imported'] += $data_import['status'];
            $data['results'][$key + 2] = $data_import;
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel. Valida los campos, crea registro
     * en la tabla product
     * 2020-02-27
     */
    function import_product($row_data)
    {
        //Validar
            $error_text = '';
                            
            if ( strlen($row_data[1]) == 0 ) { $error_text = 'La casilla Nombre está vacía. '; }
            if ( strlen($row_data[3]) == 0 ) { $error_text = 'La casilla Descripción está vacía. '; }
            if ( ! (floatval($row_data[6]) > 0) ) { $error_text .= 'Debe tener costo (' . $row_data[6] .  ') mayor a 0. '; }
            if ( ! (floatval($row_data[8]) > floatval($row_data[6])) ) { $error_text .= 'El precio debe ser mayor al costo. '; }

        //Si no hay error
            if ( $error_text == '' )
            {
                $arr_row['name'] = $row_data[1];
                $arr_row['type_id'] = 1;    //Producto
                $arr_row['code'] = $row_data[2];
                $arr_row['cat_1'] = $row_data[4];
                $arr_row['status'] = $this->pml->if_strlen($row_data[4], 2);
                $arr_row['description'] = $row_data[3];
                $arr_row['keywords'] = $this->pml->if_null($row_data[5]);
                $arr_row['cost'] = $row_data[6];
                $arr_row['tax_percent'] = $this->pml->if_null($row_data[7]);
                $arr_row['price'] = $row_data[8];
                $arr_row['weight'] = $this->pml->if_null($row_data[9]);
                $arr_row['width'] = $this->pml->if_null($row_data[10]);
                $arr_row['height'] = $this->pml->if_null($row_data[11]);
                $arr_row['depth'] = $this->pml->if_null($row_data[12]);
                $arr_row['stock'] = $this->pml->if_null($row_data[13]);
                
                $arr_row['slug'] = $this->Db_model->unique_slug($row_data[1], 'products');
                
                $arr_row['creator_id'] = $this->session->userdata('user_id');
                $arr_row['updater_id'] = $this->session->userdata('user_id');

                //Guardar en tabla user
                //if ( strlen($row_data[0]) ) { $arr_row['id'] = $row_data[0]; }
                $condition = "code = '{$row_data[2]}'";
                $saved_id = $this->Db_model->save('products', $condition, $arr_row);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $saved_id);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

// CATÁLOGO
//-----------------------------------------------------------------------------

    function get_catalog($product_family, $num_page)
    {
        //Referencia
            $per_page = 6;                             //Cantidad de registros por página
            $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        //Product family
            $family_condition = 'id > 0';
            if ( $product_family == 'books' ) { $family_condition = 'cat_1 IN (1111,2115)'; }

        //Búsqueda y Resultados
            $this->load->model('Search_model');
            $data['filters'] = $this->Search_model->filters();
            $data['filters']['condition'] = $family_condition;
            $data['list'] = $this->list($data['filters'], $per_page, $offset);    //Resultados para página
        
        //Cargar datos
            $data['str_filters'] = $this->Search_model->str_filters();
            $data['search_num_rows'] = $this->search_num_rows($data['filters']);
            $data['max_page'] = ceil($this->pml->if_zero($data['search_num_rows'],1) / $per_page);   //Cantidad de páginas

        return $data;
    
    }

// METADATOS
//-----------------------------------------------------------------------------
    
    /**
     * Elimina un registro de la tabla products_meta
     * 2020-06-17
     */
    function delete_meta($product_id, $meta_id)
    {
        $data = array('status' => 0, 'qty_deleted' => 0);

        $this->db->where('id', $meta_id);
        $this->db->where('product_id', $product_id);
        $this->db->delete('products_meta');
        
        $data['qty_deleted'] = $this->db->affected_rows();
    
        if ( $data['qty_deleted'] > 0 ) { $data['status'] = 1; }
    
        return $data;
    }

// GESTIÓN DE POSTS ASOCIADOS
//-----------------------------------------------------------------------------

    /**
     * Asignar un contenido de la tabla post a un producto, lo agrega como metadato
     * en la tabla meta, con el tipo 310012
     * 2020-04-15
     */
    function add_post($product_id, $post_id)
    {
        //Construir registro
        $arr_row['product_id'] = $product_id; //Producto ID, al que se asigna
        $arr_row['type_id'] = 310012;   //Asignación de post a un producto
        $arr_row['related_1'] = $post_id;  //ID contenido
        $arr_row['updater_id'] = $this->session->userdata('user_id');  //Usuario que asigna
        $arr_row['creator_id'] = $this->session->userdata('user_id');  //Usuario que asigna

        $condition = "product_id = {$arr_row['product_id']} AND related_1 = {$arr_row['related_1']}";
        $meta_id = $this->Db_model->save('products_meta', $condition, $arr_row);

        //Establecer resultado
        $data = array('status' => 0, 'saved_id' => '0');
        if ( $meta_id > 0) { $data = array('status' => 1, 'saved_id' => $meta_id); }

        return $data;
    }

    /**
     * Contenidos digitales asignados a un producto
     */
    function assigned_posts($product_id)
    {
        $this->db->select('posts.id, post_name AS title, code, slug, excerpt, posts.status, published_at, products_meta.id AS meta_id, posts.related_1');
        $this->db->join('products_meta', 'posts.id = products_meta.related_1');
        $this->db->where('products_meta.type_id', 310012);   //Asignación de contenido
        $this->db->where('products_meta.product_id', $product_id);

        $posts = $this->db->get('posts');
        
        return $posts;
    }
}