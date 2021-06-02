<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Follow extends CI_Controller{

// Variables generales
//-----------------------------------------------------------------------------
    public $views_folder = 'admin/follow/';
    public $url_controller = URL_ADMIN . 'follow/';

// Constructor
//-----------------------------------------------------------------------------
    
    function __construct() 
    {        
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Follow_model');
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }
    
    function index($user_id)
    {
        redirect("admin/users/profile/{$user_id}");
    }

// SEGUIDORES
//-----------------------------------------------------------------------------

    function following($user_id)
    {
        $data = $this->User_model->basic($user_id);

        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->following($user_id);

        $data['per_page'] = 10;
        $data['max_page'] = ceil($data['row']->qty_following / $data['per_page']);
        $data['back_link'] = URL_ADMIN . 'users/explore';
        $data['view_a'] = $this->views_folder . 'following_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /**
     * Vista listado de seguidores que tiene el usuario
     * 2021-05-27
     */
    function followers($user_id)
    {
        $data = $this->User_model->basic($user_id);

        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->followers($user_id);

        $data['per_page'] = 10;
        $data['max_page'] = ceil($data['row']->qty_followers / $data['per_page']);
        $data['back_link'] = URL_ADMIN . 'users/explore';
        $data['view_a'] = $this->views_folder . 'followers_v';
        $this->App_model->view(TPL_ADMIN, $data);
    }

    /** Alternar seguir o dejar de seguir un usuario por parte del usuario en sesión */
    function toggle($user_id)
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->toggle($user_id);
        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Listado de usuarios seguidos por un usuario
     * 2021-05-24
     */
    function get_following($user_id, $num_page = 1, $per_page = 10)
    {
        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->following($user_id, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * AJAX JSON
     * Listado de usuarios seguidos por un usuario
     * 2021-05-24
     */
    function get_followers($user_id, $num_page = 1, $per_page = 10)
    {
        $this->load->model('Follow_model');
        $data['users'] = $this->Follow_model->followers($user_id, $num_page, $per_page);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Proceso del sistema, masivo
     * Calcula y actualiza el número de seguidores de un usuario
     * 2021-05-25
     */
    function calculate_qty_followers()
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->calculate_qty_followers();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    /**
     * Proceso del sistema, masivo
     * Calcula y actualiza el número de usuarios a los que se está siguiendo
     * 2021-05-25
     */
    function calculate_qty_following()
    {
        $this->load->model('Follow_model');
        $data = $this->Follow_model->calculate_qty_following();

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

// Procesos masivos
//-----------------------------------------------------------------------------

    function simulate_follow()
    {
        //Eliminar actuales
        $this->db->query('DELETE FROM users_meta WHERE type_id = 1011');

        $qty_created = 0;
        $this->db->where('role >= 10');
        $users = $this->db->get('users');

        foreach ($users->result() as $user)
        {
            $arr_row['type_id'] = 1011; //Follower
            $arr_row['status'] = 1;     //Solicitud aceptada
            $arr_row['related_1'] = $user->id;
            $arr_row['updater_id'] = $user->id;
            $arr_row['creator_id'] = $user->id;
            $arr_row['created_at'] = date('Y-m-d H:i:s');
            $arr_row['updated_at'] = date('Y-m-d H:i:s');

            $qty_generated = rand(15,25);

            for ($i=0; $i <$qty_generated; $i++) 
            { 
                $key = rand(0, $users->num_rows() - 1);
                $arr_row['user_id'] = $users->row($key)->id;
                $this->Db_model->save_id('users_meta', $arr_row);

                $qty_created += 1;
            }
        }

        $this->Follow_model->calculate_qty_followers();
        $this->Follow_model->calculate_qty_following();

        $data = array('status' => 1, 'message' => 'Seguimientos creados: ' . $qty_created);

        //Salida JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
}