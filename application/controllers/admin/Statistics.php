<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends CI_Controller{
    
    function __construct() 
    {
        parent::__construct();

        $this->load->model('Statistic_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

    function girls()
    {
        $data['head_title'] = 'Girls';
        $data['head_subtitle'] = 'Visitas por perfil';
        $data['girls'] = $this->Statistic_model->girls();
        $data['view_a'] = 'statistics/girls/girls_v';
        $data['girls_result'] = $data['girls']->result();
        $this->App_model->view(TPL_ADMIN, $data);
        //Salida JSON
        //$this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    function albums()
    {
        $this->load->model('File_model');

        $data['head_title'] = 'Álbums';
        $data['head_subtitle'] = 'Visitas por álbum';
        $data['albums'] = $this->Statistic_model->albums();
        $data['view_a'] = 'statistics/albums/albums_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Actividad de usuarios
     * 2019-08-22
     */
    function users()
    {
        $this->load->model('Event_model');

        $data['d1'] = NULL;
        if ( ! is_null($this->input->get('days_ago')) )
        {
            $date = date('Y-m-d H:i:s');
            $days_ago = $this->input->get('days_ago');
            $data['d1'] = date('Y-m-d H:i:s', strtotime("{$date} .  - {$days_ago} days"));
        }

        $this->db->select('id, display_name');
        $this->db->where('role', 21);
        $this->db->where('status', 1);
        $data['users'] = $this->db->get('users');

        $data['head_title'] = 'Usuarios';
        $data['head_subtitle'] = 'Actividad';
        $data['view_a'] = 'statistics/users/activity_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }
}