<?php
class Inbody_model extends CI_Model{

// EXPLORE FUNCTIONS - inbody/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 10)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'inbody';                      //Nombre del controlador
            $data['cf'] = 'inbody/explore/';                     //Nombre del controlador
            $data['views_folder'] = 'admin/inbody/explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'InBody mediciones';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de inbody, filtrados por búsqueda y num página, más datos adicionales sobre
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
        $arr_select['general'] = 'inbody.id, user_id, day_id, test_id, height, inbody.gender, age, test_date, weight, bmi_body_mass_index, lower_limit_bmi_normal_range, upper_limit_bmi_normal_range, inbody_score';
        $arr_select['general'] .= ', users.display_name, users.username, users.email, users.document_number, users.url_thumbnail';
        $arr_select['export'] = 'inbody.*';

        return $arr_select[$format];
    }
    
    /**
     * Query de inbody, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $this->db->select($this->select());
            $this->db->join('users', 'inbody.user_id = users.id');
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('test_date', 'DESC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('inbody', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar inbody
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        //$condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('display_name', 'test_id', 'day_id'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['gender'] != '' ) { $condition .= "gender = {$filters['gender']} AND "; }
        if ( $filters['u'] != '' ) { $condition .= "user_id = {$filters['u']} AND "; }
        
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
        $this->db->select('users.id, users.display_name');
        $this->db->join('users', 'inbody.user_id = users.id');
        $search_condition = $this->search_condition($filters);
        if ( $search_condition ) { $this->db->where($search_condition);}
        $query = $this->db->get('inbody'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de usuarios según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        //$condition = 'users.role  >= ' . $role;  //Valor por defecto, ningún user, se obtendrían cero users.
        
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
            'id' => 'ID',
            'test_date' => 'Fecha medida',
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
        $query = $this->db->get('inbody', 10000);  //Hasta 10.000 registros

        return $query;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Guardar un registro en la tabla inbody
     * 2021-09-30
     * @return Array data, resultado
     */
    function save($arr_row)
    {
        $condition = "user_id = {$arr_row['user_id']} AND day_id = {$arr_row['day_id']}";
        $data['saved_id'] = $this->Db_model->save('inbody', $condition, $arr_row);
        return $data;
    }

    /**
     * Eliminar registro de tabla inbody
     * 2021-10-17
     */
    function delete($inbody_id)
    {
        $this->db->where('id', $inbody_id);
        $this->db->delete('inbody');
        
        $qty_deleted = $this->db->affected_rows();

        return $qty_deleted;
    }

// INFORMACIÓN
//-----------------------------------------------------------------------------

    function info($inbody_id, $user_id)
    {
        $data = array();
        $row_inbody = $this->Db_model->row('inbody', "id = {$inbody_id} AND user_id = {$user_id}");

        if ( ! is_null($row_inbody) )
        {
            $data = $this->arr_info_data($row_inbody);
        }

        return $data;
    }

    /**
     * A partir de objeto row de tabla inbody, construir array estructurado
     * 2021-10-01
     * @return Array data
     */
    function arr_info_data($row_inbody)
    {
        $data['variables'] = $this->info_data_variables();
        
        $data['general'] = array(
            'id' => $row_inbody->id,
            'height' => $row_inbody->height,
            'gender' => $row_inbody->gender,
            'age' => $row_inbody->age,
            'test_date' => $row_inbody->test_date,
        );

        $data['body'] = array(
            'weight' => array(
                'short_name' => 'Weight',
                'title' => 'Peso',
                'value' => $row_inbody->weight,
                'lower_limit' => $row_inbody->lower_limit_weight_normal_range,
                'upper_limit' => $row_inbody->upper_limit_weight_normal_range,
            ),
            'tbw' => array(
                'short_name' => 'TBW',
                'title' => 'Agua corporal total',
                'value' => $row_inbody->tbw,
                'lower_limit' => $row_inbody->lower_limit_tbw_normal_range,
                'upper_limit' => $row_inbody->upper_limit_tbw_normal_range,
            ),
            'bfm' => array(
                'short_name' => 'BFM',
                'title' => 'Masa de grasa corporal',
                'value' => $row_inbody->bfm_body_fat_mass,
                'lower_limit' => $row_inbody->lower_limit_bfm_normal_range,
                'upper_limit' => $row_inbody->upper_limit_bfm_normal_range,
            ),
            'smm' => array(
                'short_name' => 'SMM',
                'title' => 'Masa del músculo esquelético',
                'value' => $row_inbody->smm_skeletal_muscle_mass,
                'lower_limit' => $row_inbody->lower_limit_smm_normal_range,
                'upper_limit' => $row_inbody->upper_limit_smm_normal_range,
            ),
            'bmi' => array(
                'short_name' => 'BMI',
                'title' => 'Índice de masa corporal',
                'value' => $row_inbody->bmi_body_mass_index,
                'lower_limit' => $row_inbody->lower_limit_bmi_normal_range,
                'upper_limit' => $row_inbody->upper_limit_bmi_normal_range,
            ),
            'pbf' => array(
                'short_name' => 'PBF',
                'title' => '% grasa corporal',
                'value' => $row_inbody->pbf_percent_body_fat,
                'lower_limit' => $row_inbody->lower_limit_pbf_normal_range,
                'upper_limit' => $row_inbody->upper_limit_pbf_normal_range,
            ),
            'inbody_score' => array('short_name' => 'Puntaje InBody', 'title' => 'Puntaje InBody', 'value' => $row_inbody->inbody_score),
            'target_weight' => array('short_name' => 'Peso objetivo', 'title' => 'Peso objetivo', 'value' => $row_inbody->target_weight),
            'bfm_control' => array('short_name' => 'Control BFM', 'title' => 'Control BFM', 'value' => $row_inbody->bfm_control),
            'bmr' => array('short_name' => 'BMR', 'title' => 'Tasa metabólica basal', 'value' => $row_inbody->bmr_basal_metabolic_rate),
            'vfl' => array('short_name' => 'VFL', 'title' => 'Nivel de grasa visceral', 'value' => $row_inbody->vfl_visceral_fat_level),
            'vfa' => array('short_name' => 'VFA', 'title' => 'Área de grasa visceral', 'value' => $row_inbody->vfa_visceral_fat_area),
            'obesity_degree' => array('short_name' => 'Grado de obesidad', 'title' => 'Grado de obesidad', 'value' => $row_inbody->obesity_degree),
            'systolic' => array('short_name' => 'Sistólico', 'title' => 'Sistólico', 'value' => $row_inbody->systolic),
            'diastolic' => array('short_name' => 'Diastólico', 'title' => 'Diastólico', 'value' => $row_inbody->diastolic),
            'pulse' => array('short_name' => 'Pulso', 'title' => 'Pulso', 'value' => $row_inbody->pulse),
            'mean_artery_pressure' => array('short_name' => 'Presión arterial media', 'title' => 'Presión arterial media', 'value' => $row_inbody->mean_artery_pressure),
        );

        //Info partes del cuerpo
        $data['right_arm'] = $this->info_data_right_arm($row_inbody);
        $data['left_arm'] = $this->info_data_left_arm($row_inbody);
        $data['right_leg'] = $this->info_data_right_leg($row_inbody);
        $data['left_leg'] = $this->info_data_left_leg($row_inbody);
        $data['trunk'] = $this->info_data_trunk($row_inbody);

        return $data;
    }

    /**
     * Array datos brazo derecho
     */
    function info_data_right_arm($row_inbody){
        $info_data_right_arm = array(
            'tbw' => array(
                'short_name' => 'tbw',
                'title' => 'Agua corporal total',
                'value' => $row_inbody->tbw_right_arm,
                'lower_limit' => $row_inbody->lower_limit_tbw_right_arm_normal_range,
                'upper_limit' => $row_inbody->upper_limit_tbw_right_arm_normal_range
            ),
            'bfm' => array('short_name' => 'bfm', 'title' => 'Masa de grasa corporal', 'value' => $row_inbody->bfm_right_arm),
            'bfm_percent' => array('short_name' => 'bfm_percent', 'title' => '% Masa de grasa corporal', 'value' => $row_inbody->bfm_pct_right_arm),
        );

        return $info_data_right_arm;
    }

    /**
     * Array datos brazo izquierdo
     */
    function info_data_left_arm($row_inbody){
        $info_data_left_arm = array(
            'tbw' => array('short_name' => 'TBW', 'title' => 'Agua corporal total', 'value' => $row_inbody->tbw_left_arm, 'lower_limit' => $row_inbody->lower_limit_tbw_left_arm_normal_range, 'upper_limit' => $row_inbody->upper_limit_tbw_left_arm_normal_range),
            'bfm' => array('short_name' => 'BFM', 'title' => 'Masa de grasa corporal', 'value' => $row_inbody->bfm_left_arm),
            'bfm_percent' => array('short_name' => '% BFM', 'title' => '% Masa de grasa corporal', 'value' => $row_inbody->bfm_pct_left_arm),
        );

        return $info_data_left_arm;
    }

    /**
     * Array datos pierna derecha
     */
    function info_data_right_leg($row_inbody){
        $info_data_right_leg = array(
            'tbw' => array('short_name' => 'TBW', 'title' => 'Agua corporal total', 'value' => $row_inbody->tbw_right_leg, 'lower_limit' => $row_inbody->lower_limit_tbw_right_leg_normal_range, 'upper_limit' => $row_inbody->upper_limit_tbw_right_leg_normal_range),
            'bfm' => array('short_name' => 'BFM', 'title' => 'Masa de grasa corporal', 'value' => $row_inbody->bfm_right_leg),
            'bfm_percent' => array('short_name' => '% BFM', 'title' => '% Masa de grasa corporal', 'value' => $row_inbody->bfm_pct_right_leg),
        );

        return $info_data_right_leg;
    }

    /**
     * Array datos pierna izquierda
     */
    function info_data_left_leg($row_inbody){
        $info_data_left_leg = array(
            'tbw' => array('short_name' => 'TBW', 'title' => 'Agua corporal total', 'value' => $row_inbody->tbw_left_leg, 'lower_limit' => $row_inbody->lower_limit_tbw_left_leg_normal_range, 'upper_limit' => $row_inbody->upper_limit_tbw_left_leg_normal_range),
            'bfm' => array('short_name' => 'BFM', 'title' => 'Masa de grasa corporal', 'value' => $row_inbody->bfm_left_leg),
            'bfm_percent' => array('short_name' => '% BFM', 'title' => '% Masa de grasa corporal', 'value' => $row_inbody->bfm_pct_left_leg),
        );

        return $info_data_left_leg;
    }

    /**
     * Array datos pierna izquierda
     */
    function info_data_trunk($row_inbody){
        $info_data_trunk = array(
            'tbw' => array('short_name' => 'tbw', 'title' => 'Agua corporal total', 'value' => $row_inbody->tbw_trunk, 'lower_limit' => $row_inbody->lower_limit_tbw_trunk_normal_range, 'upper_limit' => $row_inbody->upper_limit_tbw_trunk_normal_range),
            'bfm' => array('short_name' => 'BFM', 'title' => 'Masa de grasa corporal', 'value' => $row_inbody->bfm_trunk),
            'bfm_percent' => array('short_name' => '% BFM', 'title' => '% Masa de grasa corporal', 'value' => $row_inbody->bfm_pct_trunk),
        );

        return $info_data_trunk;
    }

    function info_data_variables()
    {
        $str_inbody_variables = file_get_contents(PATH_RESOURCES . "config/brave/inbody_variables.json");
        $inbody_variables = json_decode($str_inbody_variables, true);

        return $inbody_variables;
    }

// IMPORTAR
//-----------------------------------------------------------------------------}

    /**
     * Array con configuración de la vista de importación según el tipo de inbody data
     * que se va a importar.
     * 2021-09-30
     */
    function import_config($type)
    {
        $data = array();

        if ( $type == 'general' )
        {
            $data['help_note'] = 'Se importarán datos de InBody a la base de datos.';
            $data['help_tips'] = array();
            $data['template_file_name'] = 'f51_inbody.xlsx';
            $data['sheet_name'] = 'inbody770_data';
            $data['destination_form'] = "admin/inbody/import_e/{$type}";
        }

        return $data;
    }

    /**
     * Importa datos inbody a la base de datos
     * 2021-09-30
     */
    function import($arr_sheet)
    {
        $data = array('qty_imported' => 0, 'results' => array());
        
        foreach ( $arr_sheet as $key => $row_data )
        {
            $data_import = $this->import_inbody($row_data);
            $data['qty_imported'] += $data_import['status'];
            $data['results'][$key + 2] = $data_import;
        }
        
        return $data;
    }

    /**
     * Realiza la importación de una fila del archivo excel. Valida los campos, crea registro
     * en la tabla inbody
     * 2021-09-30
     */
    function import_inbody($row_data)
    {
        //Validar
            $error_text = '';
            
            if ( strlen($row_data[0]) == 0 ) { $error_text = 'La casilla ID está vacía. '; }
            if ( strlen($row_data[4]) == 0 ) { $error_text .= 'La casilla Test Date/Time está vacía. '; }
            
            //Identificar usuario
            $document_number = str_replace(array('<', '>'),'',$row_data[0]);
            $user = $this->Db_model->row('users', "document_number = '{$document_number}'");
            if ( is_null($user) ) { $error_text = "No existe un usuario identificado con el documento '{$document_number}'."; }

            //Identificar fecha
            $test_date = New DateTime($row_data[4]);
            $day = $this->Db_model->row('periods', "id = {$test_date->format('Ymd')}");
            if ( is_null($day) ) { $error_text = "La fecha del test no pudo ser identificada correctamente: '{$row_data[4]}'"; }

            $inbody_variables = $this->variables();

        //Si no hay error
            if ( $error_text == '' )
            {
                $arr_row['user_id'] = $user->id;
                $arr_row['day_id'] = $day->id;
                $arr_row['test_id'] = $document_number;
                $arr_row['height'] = $row_data[1];
                $arr_row['gender'] = ($row_data[2] == 'F') ? 1 : 2;
                $arr_row['age'] = $row_data[3];
                $arr_row['test_date'] = $test_date->format('Y-m-d H:i:s');

                //Cargando variables en campos
                for ($i=5; $i <= 215; $i++) {
                    $field = $inbody_variables[$i]['name'];
                    $arr_row[$field] = $row_data[$i];
                }
                
                //$arr_row['creator_id'] = $this->session->userdata('user_id');
                //$arr_row['updater_id'] = $this->session->userdata('user_id');

                //Guardar en tabla user
                $data_insert = $this->save($arr_row);

                $data = array('status' => 1, 'text' => '', 'imported_id' => $data_insert['saved_id']);
            } else {
                $data = array('status' => 0, 'text' => $error_text, 'imported_id' => 0);
            }

        return $data;
    }

    function variables()
    {
        //Índice
        $str_inbody_variables = file_get_contents(PATH_RESOURCES . "config/brave/inbody_fields.json");
        $inbody_variables = json_decode($str_inbody_variables, true);

        return $inbody_variables;
    }
}