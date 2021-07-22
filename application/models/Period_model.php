<?php
class Period_model extends CI_Model{

    function basic($period_id)
    {
        $data['period_id'] = $period_id;
        $data['row'] = $this->Db_model->row_id('periods', $period_id);
        $data['head_title'] = $data['row']->period_name;
        $data['view_a'] = $this->views_folder . 'info_v';
        $data['nav_2'] = $this->views_folder . 'menu_v';

        return $data;
    }

// EXPLORE FUNCTIONS - periods/explore
//-----------------------------------------------------------------------------
    
    /**
     * Array con los datos para la vista de exploración
     */
    function explore_data($filters, $num_page, $per_page = 15)
    {
        //Data inicial, de la tabla
            $data = $this->get($filters, $num_page, $per_page);
        
        //Elemento de exploración
            $data['controller'] = 'periods';                        //Nombre del controlador
            $data['cf'] = 'periods/explore/';                       //Nombre del controlador
            $data['views_folder'] = $this->views_folder . 'explore/';           //Carpeta donde están las vistas de exploración
            $data['num_page'] = $num_page;                      //Número de la página
            
        //Vistas
            $data['head_title'] = 'Periodos';
            $data['head_subtitle'] = $data['search_num_rows'];
            $data['view_a'] = $data['views_folder'] . 'explore_v';
            $data['nav_2'] = $data['views_folder'] . 'menu_v';
        
        return $data;
    }

    /**
     * Array con listado de periods, filtrados por búsqueda y num página, más datos adicionales sobre
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
     * Segmento Select SQL, con diferentes formatos, consulta de lugares
     * 2020-12-12
     */
    function select($format = 'general')
    {
        $arr_select['general'] = 'periods.*';

        //$arr_select['export'] = 'usuario.id, username, usuario.email, nombre, apellidos, sexo, rol_id, estado, no_documento, tipo_documento_id, institucion_id, grupo_id';

        return $arr_select[$format];
    }
    
    /**
     * Query de periods, filtrados según búsqueda, limitados por página
     * 2020-08-01
     */
    function search($filters, $per_page = NULL, $offset = NULL)
    {
        //Construir consulta
            $this->db->select($this->select());
            //$this->db->join('users', 'periods.user_id = users.id', 'left');
            
        //Orden
            if ( $filters['o'] != '' )
            {
                $order_type = $this->pml->if_strlen($filters['ot'], 'ASC');
                $this->db->order_by($filters['o'], $order_type);
            } else {
                $this->db->order_by('periods.id', 'ASC');
            }
            
        //Filtros
            $search_condition = $this->search_condition($filters);
            if ( $search_condition ) { $this->db->where($search_condition);}
            
        //Obtener resultados
            $query = $this->db->get('periods', $per_page, $offset); //Resultados por página
        
        return $query;
    }

    /**
     * String con condición WHERE SQL para filtrar periods
     * 2020-08-01
     */
    function search_condition($filters)
    {
        $condition = NULL;

        $condition .= $this->role_filter() . ' AND ';

        //q words condition
        $words_condition = $this->Search_model->words_condition($filters['q'], array('period_name', 'id'));
        if ( $words_condition )
        {
            $condition .= $words_condition . ' AND ';
        }
        
        //Otros filtros
        if ( $filters['type'] != '' ) { $condition .= "periods.type_id = {$filters['type']} AND "; }
        if ( $filters['y'] != '' ) { $condition .= "periods.year = {$filters['y']} AND "; }
        if ( $filters['d1'] != '' ) { $condition .= "periods.start >= '{$filters['d1']}' AND "; }
        if ( $filters['d2'] != '' ) { $condition .= "periods.end <= '{$filters['d2']}' AND "; }
        
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
        $query = $this->db->get('periods'); //Para calcular el total de resultados

        return $query->num_rows();
    }
    
    /**
     * Devuelve segmento SQL, para filtrar listado de lugares según el rol del usuario en sesión
     * 2020-08-01
     */
    function role_filter()
    {
        $role = $this->session->userdata('role');
        $condition = 'id = 0';  //Valor por defecto, ningún user, se obtendrían cero periods.
        
        if ( $role <= 2 ) 
        {   //Desarrollador, todos los user
            $condition = 'periods.id > 0';
        }
        
        return $condition;
    }

// CRUD
//-----------------------------------------------------------------------------

    /**
     * Determina si un user tiene el permiso para eliminar un registro de period
     * 2021-03-17
     */
    function deletable($period_id)
    {   
        $deletable = FALSE;
        
        //El user es aministrador
        if ( $this->session->userdata('role') == 0 ) { $deletable = TRUE; }
            
        return $deletable;
    }
    
    /**
     * Elimina un registro de periods y sus registros relacionados en otras tablas
     * 2021-03-17
     */
    function delete($period_id)
    {
        $qty_deleted = 0;
        $deletable = $this->deletable($period_id);
        
        if ( $deletable ) 
        {
            $this->db->where('id', $period_id)->delete('periods');
            $qty_deleted = $this->db->affected_rows();
        }
            
        return $qty_deleted;
    }
    
    /**
     * Guarda un registro en la tabla periods
     * 2021-03-17
     */
    function save($arr_row, $period_id = 0)
    {
        //Complementar registro

        //Condición para identificar el registro del period
        $saved_id = $this->Db_model->save('periods', "id = {$period_id}", $arr_row);
        
        return $saved_id;
    }

    function toggle_business_day($period_id)
    {
        $data = array('saved_id' => 0, 'qty_business_days' => NULL);

        //Debe ser tipo día (9)
        $period = $this->Db_model->row('periods', "id = {$period_id} AND type_id = 9");

        if ( ! is_null($period) )
        {
            $arr_row['qty_business_days'] = ( $period->qty_business_days == 0) ? 1 : 0;

            //Actualizar
            $this->db->where('id', $period_id);
            $this->db->update('periods', $arr_row);
            
            //Verificar resultado
            if (  $this->db->affected_rows() > 0 ) {
                $data['saved_id'] = $period_id;
                $data['qty_business_days'] = $arr_row['qty_business_days'];
            }
        }
        return $data;
    }

    function update_business_days($day)
    {
        //Debe ser tipo día (9)
        $month_id = substr($day->id, 0, 6);
        //$condition = "type_id = 7 AND "
        //$month = $hola;


        //$condition = "type_id = 9 AND month = {}";
    }

    function weeks($date_1, $date_2)
    {
        $this->db->select('year, week_number, MIN(start) AS first_day');
        $this->db->where('type_id', 9); //Tipo día
        $this->db->where('start >= ', $date_1);
        $this->db->where('start <= ', $date_2);
        $this->db->group_by('year, week_number');
        $query_weeks = $this->db->get('periods');

        foreach ($query_weeks->result() as $row_week)
        {
            $week['year'] = $row_week->year;
            $week['week_number'] = $row_week->week_number;
            $week['first_day'] = $row_week->first_day;
            $week['days'] = $this->days_week($row_week->week_number);

            $weeks[] = $week;
        }

        //$data['weeks'] = $weeks;

        return $weeks;
    }

    function days_week($week_number)
    {
        $this->db->where('type_id', 9); //Tipo día
        $this->db->where('week_number', $week_number);
        $days = $this->db->get('periods');

        return $days->result();
    }

// Tools
//-----------------------------------------------------------------------------

    /**
     * Query con días en un rango de fechas y que cumplen una condición.
     * 2021-07-21
     */
    function days($start, $end, $condition = NULL)
    {
        //Seleccionar días laborales
        $this->db->where('type_id', 9); //Periodo tipo día
        $this->db->where('start >=', $start);
        $this->db->where('start <=', $end);
        if ( ! is_null($condition) ) $this->db->where($condition);
        $days = $this->db->get('periods');

        return $days;
    }

// Calendario
//-----------------------------------------------------------------------------

    function calendar_prefs()
    {
        $prefs = array(
            'start_day'    => 'sunday',
            'month_type'   => 'long',
            'day_type'     => 'short',
            'show_next_prev'  => TRUE,
        );

        return $prefs;
    }

    function calendar_template()
    {
        $calendar_template = '
            {table_open}<table class="table bg-white text-center" border="0" cellpadding="0" cellspacing="0">{/table_open}

            {heading_row_start}<tr>{/heading_row_start}

            {heading_previous_cell}<th><a class="btn btn-light" href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
            {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
            {heading_next_cell}<th><a class="btn btn-light" href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

            {heading_row_end}</tr>{/heading_row_end}

            {week_row_start}<tr>{/week_row_start}
            {week_day_cell}<td>{week_day}</td>{/week_day_cell}
            {week_row_end}</tr>{/week_row_end}

            {cal_row_start}<tr>{/cal_row_start}
            {cal_cell_start}<td v-on:click="set_day">{/cal_cell_start}
            {cal_cell_start_today}<td class="table-warning">{/cal_cell_start_today}
            {cal_cell_start_other}<td class="other-month">{/cal_cell_start_other}

            {cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
            {cal_cell_content_today}<div><a href="{content}">{day}</a></div>{/cal_cell_content_today}

            {cal_cell_no_content}{day}{/cal_cell_no_content}
            {cal_cell_no_content_today}<div>{day}</div>{/cal_cell_no_content_today}

            {cal_cell_blank}&nbsp;{/cal_cell_blank}

            {cal_cell_other}{day}{/cal_cel_other}

            {cal_cell_end}</td>{/cal_cell_end}
            {cal_cell_end_today}</td>{/cal_cell_end_today}
            {cal_cell_end_other}</td>{/cal_cell_end_other}
            {cal_row_end}</tr>{/cal_row_end}

            {table_close}</table>{/table_close}
        ';

        return $calendar_template;
    }
}