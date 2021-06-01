<?php
class Follow_model extends CI_Model{

    /**
     * Proceso alternado, seguir o dejar de seguir un usuario de la plataforma
     * 2020-06-01
     */
    function toggle($user_id)
    {
        //Buscar registro de seguimiento
        $condition = "user_id = {$user_id} AND type_id = 1011 AND related_1 = {$this->session->userdata('user_id')}";
        $row_meta = $this->Db_model->row('users_meta', $condition);

        $data = array('status' => 0);

        if ( is_null($row_meta) )
        {
            //No existe, crear (Empezar a seguir)
            $data = $this->follow($user_id);
            $data['qty_sum'] = 1;
        } else {
            $data = $this->unfollow($row_meta->id);
            $data['qty_sum'] = -1;   //Restar a seguidores
        }

        //Actualizar cantidades de seguidores
        $this->update_qty($user_id, $data['qty_sum']);

        return $data;
    }

    /**
     * Crear registro de seguimiento, tabla users_meta
     * 2021-05-25
     */
    function follow($user_id)
    {
        $user = $this->Db_model->row_id('users', $user_id);

        //Construir registro
        $arr_row['user_id'] = $user_id;
        $arr_row['type_id'] = 1011; //Follower
        $arr_row['status'] = 10;     //Solicitud enviada
        $arr_row['related_1'] = $this->session->userdata('user_id');
        $arr_row['updater_id'] = $this->session->userdata('user_id');
        $arr_row['creator_id'] = $this->session->userdata('user_id');

        //Cuenta pública
        if ( $user->privacy == 1 ) $arr_row['status'] = 1;   //Seguimiento aprobado

        $this->db->insert('users_meta', $arr_row);

        $data['saved_id'] = $this->db->insert_id();
        $data['status'] = $arr_row['status'];

        return $data;
    }

    /**
     * Dejar de seguir a un usuario
     * 2021-05-25
     */
    function unfollow($meta_id)
    {
        //Existe, eliminar (Dejar de seguir)
        $this->db->where('id', $meta_id)->delete('users_meta');
        
        $data['qty_deleted'] = $this->db->affected_rows();
        $data['status'] = 2;

        return $data;
    }

    /**
     * Actualiza los campos qty_followers, y qty_following, ante un cambio en seguimiento
     * 2021-05*25
     */
    function update_qty($user_id, $qty_sum)
    {
        //Usuario seguido
        $this->db->query("UPDATE users SET qty_followers = qty_followers + ({$qty_sum}) WHERE id = {$user_id}");

        //Usuario en sesión
        $follower_id = $this->session->userdata('user_id');
        $this->db->query("UPDATE users SET qty_following = qty_following + ({$qty_sum}) WHERE id = {$follower_id}");
    }

    /**
     * Usuarios seguidos por user_id
     * 2020-07-15
     */
    function following($user_id, $num_page = 1, $per_page = 10)
    {
        $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        $this->db->select('users.id, username, display_name, url_thumbnail, users_meta.id AS meta_id');
        $this->db->join('users_meta', 'users.id = users_meta.user_id');
        $this->db->where('users_meta.related_1', $user_id);
        $this->db->where('users_meta.type_id', 1011);    //Follower
        $this->db->where('users_meta.status', 1);    //Aceptado
        $this->db->order_by('users_meta.created_at', 'DESC');
        $query = $this->db->get('users', $per_page, $offset);

        $users = array();
        foreach ( $query->result() as $user ) 
        {
            $user->follow_status = $this->follow_status($user->id);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Usuarios que siguen a user_id
     * 20211-05-27
     */
    function followers($user_id, $num_page = 1, $per_page = 10)
    {
        $offset = ($num_page - 1) * $per_page;      //Número de la página de datos que se está consultado

        $this->db->select('users.id, username, display_name, url_thumbnail, users_meta.id AS meta_id');
        $this->db->join('users_meta', 'users.id = users_meta.related_1');
        $this->db->where('users_meta.user_id', $user_id);
        $this->db->where('users_meta.type_id', 1011);    //Follower
        $this->db->where('users_meta.status', 1);    //Aceptado
        $this->db->order_by('users_meta.created_at', 'DESC');
        $query = $this->db->get('users', $per_page, $offset);

        $users = array();
        foreach ( $query->result() as $user ) 
        {
            $user->follow_status = $this->follow_status($user->id);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Establece si el usuario (user_id) es seguido o no por el usuario en sesión
     * 2020-06-16
     */
    function follow_status($user_id)
    {
        $follow_status = 2; //No seguido

        if ( $this->session->userdata('logged') )
        {
            $condition = "user_id = {$user_id} AND type_id = 1011 AND related_1 = {$this->session->userdata('user_id')}";
            $row_meta = $this->Db_model->row('users_meta', $condition);
    
            if ( ! is_null($row_meta) ) $follow_status = $row_meta->status;
        }

        return $follow_status;
    }

    /**
     * Int, cantidad calculada de seguidores que tiene un usuario
     * 2020-08-24
     */
    function qty_followers($user_id)
    {
        $qty_followers = $this->Db_model->num_rows('users_meta', "type_id = 1011 AND user_id = {$user_id}");
        return $qty_followers;
    }

// Procesos masivos
//-----------------------------------------------------------------------------

    /**
     * Calcular y actualizar el campo users.qty_followers de forma masiva
     * 2021-05-25
     */
    function calculate_qty_followers()
    {
        //Datos iniciales
        $data = array('status' => 0, 'message' => 'No se actualizaron los usuarios');
        $affected_rows = 0;

        //Consulta acumulada
        $this->db->select('user_id, COUNT(id) AS qty_followers');
        $this->db->group_by('user_id');
        $this->db->where('type_id', 1011);
        $users = $this->db->get('users_meta');

        foreach ( $users->result() as $user )
        {
            $arr_row['qty_followers'] = $user->qty_followers;
            $this->db->where('id', $user->user_id)->update('users', $arr_row);

            $affected_rows += $this->db->affected_rows();
        }

        //Actualizar resultado
        if ( $affected_rows > 0 ) {
            $data = array('status' => 1, 'message' => 'Usuarios actualizados: ' . $affected_rows);
        }

        return $data;
    }

    /**
     * Calcular y actualizar el campo users.qty_following de forma masiva
     * 2021-05-25
     */
    function calculate_qty_following()
    {
        //Datos iniciales
        $data = array('status' => 0, 'message' => 'No se actualizaron los usuarios');
        $affected_rows = 0;

        //Consulta acumulada
        $this->db->select('related_1 AS follower_id, COUNT(id) AS qty_following');
        $this->db->group_by('related_1');
        $this->db->where('type_id', 1011);
        $users = $this->db->get('users_meta');

        foreach ( $users->result() as $user )
        {
            $arr_row['qty_following'] = $user->qty_following;
            $this->db->where('id', $user->follower_id)->update('users', $arr_row);

            $affected_rows += $this->db->affected_rows();
        }

        //Actualizar resultado
        if ( $affected_rows > 0 ) {
            $data = array('status' => 1, 'message' => 'Usuarios actualizados: ' . $affected_rows);
        }

        return $data;
    }
}