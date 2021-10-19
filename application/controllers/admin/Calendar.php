<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/calendar/';
    public $url_controller = URL_ADMIN . 'calendar/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Period_model');
        $this->load->model('Calendar_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($period_id = null)
    {
        if ( is_null($period_id) ) {
            redirect('admin/calendar/explore');
        } else {
            redirect("admin/calendar/details/{$period_id}");
        }
    }

// Calendario
//-----------------------------------------------------------------------------

    function calendar($day_id = NULL, $section = 'trainings')
    {
        if ( is_null($day_id) ) $day_id = date('Ymd');
        $day = $this->Db_model->row('periods', "type_id = 9 AND id = {$day_id}");
        $row_month = $this->Db_model->row('periods', "year = {$day->year} AND month = {$day->month}");

        $data['weeks'] = $this->Period_model->weeks($row_month->start, $row_month->end);

        //Opciones de filtros de búsqueda
            $data['rooms'] = $this->App_model->rooms();

        //Detalle periodos
            $data['day_start'] = $row_month->start;
            $data['day'] = $day;
            $data['section'] = $section;
            $data['options_year'] = range(date('Y') - 1, date('Y') + 2);

        //Vista
            $data['head_title'] = 'Calendario';
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['view_a'] = $this->views_folder . 'calendar/calendar_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

// Programación
//-----------------------------------------------------------------------------

    /**
     * Vista formulario para generar diferentes tipos de programación de eventos: 
     * sesiones de entrenamiento, citas de nutrición
     * 2021-10-05
     * 
     */
    function schedule_generator($schedule_type = 'trainings')
    {
        $data['head_title'] = 'Programar';
        $data['schedule_type'] = $schedule_type;
        $data['nav_2'] = $this->views_folder . 'menu_v';
        $data['nav_3'] = $this->views_folder . 'schedule_generator/menu_v';
        $data['view_a'] = $this->views_folder . "schedule_generator/{$schedule_type}_v";

        //Variables específicas
        if ( $schedule_type == 'trainings' ) {
            $data['rooms'] = $this->App_model->rooms();
            $sql_query = 'SELECT * FROM items WHERE category_id = 510 ORDER BY item_group ASC, cod ASC';
            $data['query_hours'] = $this->db->query($sql_query);
        }

        $this->App_model->view(TPL_ADMIN, $data);
    }

// Citas
//-----------------------------------------------------------------------------

    function get_appointments($day_id, $event_type_id = 0)
    {
        $appointments = $this->Calendar_model->get_appointments($day_id, $event_type_id);
        $data['list'] = $appointments;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Eliminar una cita
     * 2021-10-14
     */
    function delete_appointment($event_id, $type_id)
    {
        $data = array('qty_deleted' => 0);

        //Identificar evento
        $condition = "id = {$event_id} AND type_id = {$type_id} AND type_id IN (221)";
        $event = $this->Db_model->row('events', $condition);

        //Si existe eliminar
        if ( ! is_null($event) ) {
            $this->load->model('Event_model');
            $data['qty_deleted'] = $this->Event_model->delete($event->id);
        }

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }


// Citas de control nutricional
//-----------------------------------------------------------------------------

    /**
     * AJAX JSON
     * Ejecutar la creación de citas de control nutricional, tabla events, tipo 221
     * Recibe datos desde calendar/schedlule
     */
    function schedule_nutritional_control()
    {
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $hours = json_decode($this->input->post('str_hours'));
        
        $data = $this->Calendar_model->schedule_nutritional_control($date_start, $date_end, $hours);

        $data['hours'] = $hours;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    

// Testing Trainings
//-----------------------------------------------------------------------------

    function reset_trainings()
    {
        $this->db->query('DELETE FROM events WHERE type_id = 203'); //Eliminar trainings programadas

        $qty_deleted = $this->db->affected_rows();

        $data['messages'][] = 'Entrenamientos eliminados:' . $qty_deleted;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function simular_reservas()
    {
        $fecha_desde = date('Y-m-d H:i:s');

        $data['users'] = $this->db->get_where('users', 'role = 21');
        $data['trainings'] = $this->db->get_where('events', "type_id = 203 AND start >= '{$fecha_desde}'");

        $data['head_title'] = 'Simulación reservas';
        $data['view_a'] = $this->views_folder . 'simular_reservas_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

    function reset_reservas()
    {
        $this->db->query('DELETE FROM events WHERE type_id = 213'); //Eliminar reservas programadas

        $qty_deleted = $this->db->affected_rows();

        $this->db->query('UPDATE events SET integer_2 = integer_1 WHERE type_id = 203'); //Restaurar cupos

        $qty_restored_spots = $this->db->affected_rows();

        $data['messages'][] = 'Reservas eliminadas:' . $qty_deleted;
        $data['messages'][] = 'Cupos restaurados:' . $qty_restored_spots;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Simula fechas de expiración de suscripción
     */
    function simular_expiration_date()
    {
        //Limpiar
        $this->db->query('UPDATE users SET expiration_at = NULL WHERE role = 21');

        //Seleccionar
        $this->db->select('id');
        $this->db->where('role', 21);
        $users = $this->db->get('users');

        $updated = array();

        //Recorrer usuarios
        foreach ($users->result() as $user) {
            $random = rand(0,99);   //Para determinar que actualización se hace
            $random_days = rand(0, 56); //Número de días a sumar o restar

            if ( $random >= 10 ) {
               if ( $random <= 80 ) {
                //En el futuro
                    $time = strtotime(date('Y-m-d') . " +{$random_days} days");
               } else {
                $time = strtotime(date('Y-m-d') . " -{$random_days} days");
               }

               $arr_row['expiration_at'] = date('Y-m-d', $time);

               $this->db->where('id', $user->id);
               $this->db->update('users', $arr_row);

               $updated[] = $user->id;
            }
        }

        $data['updated'] = $updated;

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}