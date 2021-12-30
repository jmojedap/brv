<?php
class User_model extends CI_Model{

    function basic($user_id)
    {
        $data['user_id'] = $user_id;
        $data['row'] = $this->Db_model->row_id('users', $user_id);
        $data['head_title'] = $data['row']->display_name;
        $data['view_a'] = 'admin/users/user_v';
        $data['nav_2'] = 'admin/users/menus/user_v';

        if ( $data['row']->role == 4 ) { $data['nav_2'] = 'admin/users/menus/role_4_v'; }
        if ( $data['row']->role == 13 ) { $data['nav_2'] = 'admin/users/menus/role_13_v'; }
        if ( $data['row']->role == 21 ) { $data['nav_2'] = 'admin/users/menus/role_21_v'; }

        return $data;
    }

// EXPLORE FUNCTIONS - users/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'users';                      //Nombre del controlador
            $data['cf'] = 'users/explore/';                     //Nombre del controlador
            $data['views_folder'] = 'admin/users/explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Usuarios';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de users, filtrados por búsqueda y num página, más datos adicionales sobre
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
     * 2021-08-14
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'users.id, username, document_number, document_type, display_name, first_name, last_name, email, role';
        $arr_select['general'] .= ', image_id, url_image, url_thumbnail, status, users.type_id, created_at, updated_at, last_login';
        $arr_select['general'] .= ', expiration_at, commercial_plan, admin_notes, birth_date';

        $arr_select['export'] = 'users.id, username, document_number AS no_documento, document_type AS tipo_documento, display_name AS nombre, email, role AS rol, status, created_at AS creado, updated_at AS actualizado, expiration_at AS suscripcion_hasta';
        $arr_select['follow'] = 'users.id, username, qty_followers, qty_following';

        return $arr_select[$format];
    }
    
    /**
     * Query de users, filtrados según búsqueda, limitados por página
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
            $query = $this->db->get('users', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar users
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('first_name', 'last_name', 'display_name', 'email', 'document_number'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['role'] != '' ) { $condition .= "role = {$filters['role']} AND "; }

        //Estado suscripción
        $now = date('Y-m-d H:i:s');
        if ( $filters['fe1'] == '0' ) { $condition .= "expiration_at IS NULL AND "; }
        if ( $filters['fe1'] == '1' ) { $condition .= "expiration_at >= '{$now}' AND "; }
        if ( $filters['fe1'] == '2' ) { $condition .= "expiration_at < '{$now}' AND "; }

        //Plan comercial
        if ( strlen($filters['fe2']) > 0 ) { $condition .= "commercial_plan = '{$filters['fe2']}' AND "; }
        
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
        $query = $this->db->get('users'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de usuarios según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'users.role  >= ' . $role;  //Valor por defecto, ningún user, se obtendrían cero users.
        
        /*if ( $role <= 2 ) 
        {   //Desarrollador, todos los user
            $condition = 'users.id > 0';
        }*/
        
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
            'id' => 'ID Usuario',
            'last_name' => 'Apellidos',
            'document_number' => 'No. documento',
        );
        
        return $order_options;
    }

    /**
     * Query para exportar
     * 2021-09-27
     */
    function query_export($filters)
    {
        $this->db->select($this->select('export'));
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('users', 10000);  //Hasta 10.000 registros

        return $query;
    }


    /**
     * Opciones de usuario en campos de autollenado, como agregar usuarios a una conversación
     * 2019-11-13
     */
    function autocomplete($filters, $limit = 15)
    {
        $role_filter = $this->role_filter();

        //Construir búsqueda
        //Crear array con términos de búsqueda
            if ( strlen($filters['q']) > 2 )
            {
                $words = $this->Search_model->words($filters['q']);

                foreach ($words as $word) {
                    $this->db->like('CONCAT(first_name, last_name, username, code)', $word);
                }
            }
        
        //Especificaciones de consulta
            //$this->db->select('id, CONCAT((display_name), " (",(username), ") Cod: ", IFNULL(code, 0)) AS value');
            $this->db->select('id, CONCAT((display_name), " (",(username), ")") AS value');
            $this->db->where($role_filter); //Filtro según el rol de usuario que se tenga
            $this->db->order_by('last_name', 'ASC');
            
        //Otros filtros
            if ( $filters['condition'] != '' ) { $this->db->where($filters['condition']); }    //Condición adicional
            
        $query = $this->db->get('users', $limit); //Resultados por página
        
        return $query;
    }

// GUARDAR
//-----------------------------------------------------------------------------

    /**
     * Crear o actualizar registro de usuario
     * 2021-11-03
     */
    function save($arr_row = NULL)
    {
        if ( is_null($arr_row) ) $arr_row = $this->Db_model->arr_row();

        //Revisar fecha de expiración, si no está asignada, NULL
        if ( isset($arr_row['expiration_at']) ) {
            if ( $arr_row['expiration_at'] == '' ) {
                $arr_row['expiration_at'] = NULL;
            }
        }

        $data['saved_id'] = $this->Db_model->save_id('users', $arr_row);
        return $data;
    }
    
    /**
     * Construye array del registro para insertar o actualizar un usuario
     * 2021-02-17
     */
    function arr_row($user_id)
    {
        $arr_row = $this->input->post();
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        
        //Encriptar contraseña si está incluida
        if ( isset($arr_row['password']) )
        {
            $this->load->model('Account_model');
            $arr_row['password'] = $this->Account_model->crypt_pw($arr_row['password']);
        }

        //Nombre completo
        if ( ! isset($arr_row['display_name']) ) { $arr_row['display_name'] = $arr_row['first_name'] . ' ' . $arr_row['last_name']; }

        //Username
        if ( ! isset($arr_row['username']) ) { 
            $arr_row['username'] = $this->generate_username($arr_row['first_name'], $arr_row['last_name']);
        }
        
        //Es nuevo
        if ( is_null($user_id) ) $arr_row['creator_id'] = $this->session->userdata('user_id');
        
        return $arr_row;
    }

    /**
     * Valida datos de un user nuevo o existente, verificando validez respecto
     * a users ya existentes en la base de datos.
     */
    function validate($user_id = NULL)
    {
        $data = array('status' => 1, 'error' => '');
        $this->load->model('Validation_model');
        
        $username_validation = $this->Validation_model->username($user_id);
        $email_validation = $this->Validation_model->email($user_id);
        $document_number_validation = $this->Validation_model->document_number($user_id);

        $validation = array_merge($username_validation, $email_validation, $document_number_validation);
        $data['validation'] = $validation;

        if ( $email_validation['email_unique'] == 0 ) $data['error'] = "El e-mail escrito ya está registrado";
        if ( $username_validation['username_unique'] == 0 ) $data['error'] = "El username escrito lo ha tomado otro usuario";
        if ( $document_number_validation['document_number_unique'] == 0 ) $data['error'] = "El número de documento ya está registrado";

        //Si hay al menos un error, no se valida
        if ( strlen($data['error']) > 0 ) $data['status'] = 0;

        return $data;
    }

// ELIMINAR
//-----------------------------------------------------------------------------
    
    /**
     * Determina si un usuario puede ser eliminado o no de la base de datos
     * 2021-06-02
     */
    function deleteable()
    {
        $deleteable = 0;
        if ( in_array($this->session->userdata('role'), array(1)) ) { $deleteable = 1; }

        return $deleteable;
    }

    /**
     * Eliminar un usuario de la base de datos, se elimina también de las tablas relacionadas
     * 2021-02-20
     */
    function delete($user_id)
    {
        $qty_deleted = 0;   //Valor inicial

        if ( $this->deleteable($user_id) ) 
        {
            //Tablas relacionadas
                $this->db->where('user_id', $user_id)->delete('users_meta');
            
            //Tabla principal
                $this->db->where('id', $user_id)->delete('users');

            //Resultado
            $qty_deleted = $this->db->affected_rows();

            //Eliminar archivos relacionados
            if ( $qty_deleted > 0 ) $this->delete_files($user_id);
        }

        return $qty_deleted;
    }

    /**
     * Eliminar los archivos relacionados con el usuario eliminado
     * 2021-02-20
     */
    function delete_files($user_id)
    {
        //Identificar archivos
        $this->db->select('id');
        $this->db->where("creator_id = {$user_id} OR (table_id = 1000 AND related_1 = {$user_id})");
        $files = $this->db->get('files');
        
        //Eliminar archivos
        $this->load->model('File_model');
        $session_data = $this->session->userdata();
        foreach ( $files->result() as $file ) $this->File_model->delete($file->id, $session_data);
    }

//IMAGEN DE PERFIL DE USUARIO
//---------------------------------------------------------------------------------------------------
    
    /**
     * Asigna una imagen registrada en la tabla archivo como imagen del usuario
     * 2020-12-14
     */
    function set_image($user_id, $file_id)
    {
        $data = array('status' => 0, 'message' => 'La imagen no fue asignada'); //Resultado inicial
        $row_file = $this->Db_model->row_id('files', $file_id);
        
        $arr_row['image_id'] = $row_file->id;
        $arr_row['url_image'] = $row_file->url;
        $arr_row['url_thumbnail'] = $row_file->url_thumbnail;
        
        $this->db->where('id', $user_id);
        $this->db->update('users', $arr_row);
        
        if ( $this->db->affected_rows() )
        {
            $data = array('status' => 1, 'message' => 'La imagen del usuario fue asignada');
            $data['image_id'] = $row_file->id;
            $data['url_image'] = $row_file->url;
        }

        return $data;
    }
    
    /**
     * Le quita la imagen de perfil asignada a un usuario, eliminado el archivo
     * correspondiente
     * 2021-09-22: sessión_data
     */
    function remove_image($user_id)
    {
        $data['status'] = 0;
        $row = $this->Db_model->row_id('users', $user_id);
        
        if ( ! is_null($row->image_id) )
        {
            $this->load->model('File_model');
            $session_data = $this->session->userdata();
            $this->File_model->delete($row->image_id, $session_data);
            $data['status'] = 1;
        }
        
        return $data;
    }

// IMPORTAR USUARIOS
//-----------------------------------------------------------------------------

    /**
     * Importa usuarios a la base de datos
     * 2021-06-01
     */
    function import($arr_sheet)
    {
        $data = array('qty_imported' => 0, 'results' => array());

        $this->load->model('Account_model');
        
        foreach ( $arr_sheet as $key => $row_data )
        {
            $data_import = $this->import_user($row_data);
            $data['qty_imported'] += $data_import['status'];
            $data['results'][$key + 2] = $data_import;
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel. Valida los campos, crea registro
     * en la tabla users.
     * 2021-06-01
     */
    function import_user($row_data)
    {
        //Validar
            $error_text = '';

            if ( strlen($row_data[0]) == 0 ) { $error_text .= 'El nombre está vacío. '; }          //Debe tener nombre escrito
            if ( strlen($row_data[1]) == 0 ) { $error_text .= 'El apellido está vacío. '; }        //Debe tener apellido
            if ( strlen($row_data[2]) == 0 ) { $error_text .= 'El e-mail está vacío. '; }          //Debe tener email
            if ( strlen($row_data[3]) < 8 ) { $error_text .= 'La contraseña debe tener al menos 8 caracteres. '; }       //Debe tener contraseña de 8 caracteres
            if ( strlen($row_data[4]) <= 1 ) { $error_text .= 'El código del rol no es válido.'; } //No rol de administrador o desarrollador
            if ( ! $this->Db_model->is_unique('users', 'email', $row_data[2]) ) { $error_text .= 'El e-mail ya está registrado. '; } //El email debe ser único
            if ( ! $this->Db_model->is_unique('users', 'document_number', $row_data[6]) ) { $error_text .= 'El No. documento ya está registrado para otro usuario. '; } //El documento debe ser único

        //Si no hay error
            if ( $error_text == '' )
            {
                $arr_row['first_name'] = $row_data[0];
                $arr_row['last_name'] = $row_data[1];
                $arr_row['display_name'] = $row_data[0] . ' ' . $row_data[1];
                $arr_row['email'] = $row_data[2];
                $arr_row['username'] = explode('@', $row_data[2])[0];
                $arr_row['password'] = $this->Account_model->crypt_pw($row_data[3]);
                $arr_row['role'] = $row_data[4];
                $arr_row['document_type'] = ( strlen($row_data[5]) > 0 ) ? $row_data[5] : 0;
                $arr_row['document_number'] = ( strlen($row_data[6]) > 0 ) ? $row_data[6] : '';
                $arr_row['document_number'] = ( strlen($row_data[6]) > 0 ) ? $row_data[6] : '';
                //$arr_row['birth_date'] = date('Y-m-d H:i:s', $this->pml->date_excel_unix($row_data[7]));
                $arr_row['birth_date'] = ( strlen($row_data[7]) ) ? date('Y-m-d H:i:s', $this->pml->dexcel_unix($row_data[7])) : '';
                $arr_row['gender'] = ( $row_data[8] >= 1 && $row_data[8] <= 2 ) ? $row_data[8] : 0;
                $arr_row['creator_id'] = $this->session->userdata('user_id');
                $arr_row['updater_id'] = $this->session->userdata('user_id');

                //Guardar en tabla user
                $saved_id = $this->Db_model->save_id('users', $arr_row);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $saved_id);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

// GENERAL
//-----------------------------------------------------------------------------

    function generate_username($first_name, $last_name)
    {
        //Sin espacios iniciales o finales
        $first_name = trim($first_name);
        $last_name = trim($last_name);
        
        //Sin acentos
        $this->load->helper('text');
        $first_name = convert_accented_characters($first_name);
        $last_name = convert_accented_characters($last_name);
        
        //Arrays con partes
        $arr_last_name = explode(" ", $last_name);
        $arr_first_name = explode(" ", $first_name);
        
        //Construyendo por partes
            $username = $arr_first_name[0];
            //if ( isset($arr_first_name[1]) ){ $username .= substr($arr_first_name[1], 0, 2);}
            
            //Apellidos
            $username .= '_' . $arr_last_name[0];
            //if ( isset($arr_last_name[1]) ){ $username .= substr($arr_last_name[1], 0, 2); }    
        
        //Reemplazando caracteres
            $username = str_replace (' ', '', $username); //Quitando espacios en blanco
            $username = strtolower($username); //Se convierte a minúsculas    
        
        //Verificar, si el username requiere un suffix numérico para hacerlo único
            $suffix = $this->username_suffix($username);
            $username .= $suffix;
        
        return $username;
    }

    /**
     * Devuelve un entero aleatorio de tres cifras cuando el username generado inicialmente (generate_username)
     * ya exista dentro de la plataforma.
     * 2019-11-05
     */
    function username_suffix($username)
    {
        $suffix = '';
        
        $condition = "username = '{$username}'";
        $qty_users = $this->Db_model->num_rows('users', $condition);

        if ( $qty_users > 0 )
        {
            $this->load->helper('string');
            $suffix = random_string('numeric', 4);
        }
        
        return $suffix;
    }

// LISTAS DE USUARIO
//-----------------------------------------------------------------------------

    /**
     * Listas de usuario, marcando a cuales pertenece un usuario específico
     * 2021-12-03
     */
    function lists($user_id, $cat_list = 10)
    {
        $this->db->select('posts.id, post_name AS name, excerpt as description, IF(users_meta.user_id IS NULL, (0), (1)) AS in_list');
        $this->db->where('posts.type_id', 22);
        $this->db->where('posts.cat_1', $cat_list);
        $this->db->join('users_meta', "posts.id = users_meta.related_1 AND users_meta.user_id = {$user_id}", 'left');
        
        $user_lists = $this->db->get('posts');
    
        return $user_lists;
    }

    /**
     * Agregar o eliminar a un usuario de una lista, tabla users_meta
     * 2021-12-06
     */
    function update_list($user_id, $list_id, $add = TRUE)
    {
        $data = array('saved_id' => 0, 'qty_deleted' => 0);

        $condition = "type_id = 22 AND user_id = $user_id AND related_1 = $list_id";
    
        if ( $add )
        {
            //Crear registro
            $arr_row = $this->Db_model->arr_row(false);
    
            $arr_row['type_id'] = 22;   //Usuario en lista
            $arr_row['user_id'] = $user_id;
            $arr_row['related_1'] = $list_id;

            $data['saved_id'] = $this->Db_model->save('users_meta', $condition, $arr_row);
        } else {
            //Eliminar existente
            $this->db->where($condition);
            $this->db->delete('users_meta');
            
            $data['qty_deleted'] = $this->db->affected_rows();
        }
    
        return $data;
    }

// CONTENIDOS VIRUTALES ASIGNADOS
//-----------------------------------------------------------------------------

    /**
     * Contenidos digitales asignados a un usuario
     */
    function assigned_posts($user_id)
    {
        $this->db->select('posts.id, post_name AS title, code, slug, excerpt, posts.status, published_at, url_image, url_thumbnail, users_meta.id AS meta_id');
        $this->db->join('users_meta', 'posts.id = users_meta.related_1');
        $this->db->where('users_meta.type_id', 100012);   //Asignación de contenido
        $this->db->where('users_meta.user_id', $user_id);
        $this->db->order_by('posts.status', 'ASC');
        $this->db->order_by('posts.published_at', 'ASC');

        $posts = $this->db->get('posts');
        
        return $posts;
    }

// GESTIÓN DE PRODUCTOS ASOCIADOS
//-----------------------------------------------------------------------------

    /**
     * Asignar un producto a un usuario, lo agrega como metadato
     * en la tabla meta, con el tipo 100016
     * 2020-06-17
     */
    function add_product($user_id, $product_id)
    {
        //Construir registro
        $arr_row['user_id'] = $user_id; //User ID, al que se asigna
        $arr_row['type_id'] = 100016;   //Asignación de producto a un usuario
        $arr_row['related_1'] = $product_id;  //ID contenido
        $arr_row['updater_id'] = $this->session->userdata('user_id');  //Usuario que asigna
        $arr_row['creator_id'] = $this->session->userdata('user_id');  //Usuario que asigna

        $condition = "user_id = {$arr_row['user_id']} AND related_1 = {$arr_row['related_1']}";
        $meta_id = $this->Db_model->save('users_meta', $condition, $arr_row);

        //Establecer resultado
        $data = array('status' => 0, 'saved_id' => '0');
        if ( $meta_id > 0) { $data = array('status' => 1, 'saved_id' => $meta_id); }

        return $data;
    }

    /**
     * Productos asociados a un usuario
     */
    function assigned_products($user_id)
    {
        $this->db->select('products.id, name, price, slug, description, users_meta.id AS meta_id, url_image, url_thumbnail');
        $this->db->join('users_meta', 'products.id = users_meta.related_1');
        $this->db->where('users_meta.type_id', 100016);   //Asignación de producto
        $this->db->where('users_meta.user_id', $user_id);

        $products = $this->db->get('products');
        
        return $products;
    }
}