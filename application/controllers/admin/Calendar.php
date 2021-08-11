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

    function calendar($day_id = NULL, $room_id = 0)
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
            $data['room_id'] = $room_id;
            $data['options_year'] = range(date('Y') - 1, date('Y') + 2);

        //Vista
            $data['head_title'] = 'Calendario';
            $data['nav_2'] = $this->views_folder . 'menu_v';
            $data['view_a'] = $this->views_folder . 'calendar/calendar_v';

        $this->App_model->view(TPL_ADMIN, $data);
    }

// Reservaciones
//-----------------------------------------------------------------------------

    

// Testing
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