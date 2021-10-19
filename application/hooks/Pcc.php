<?php

class Pcc {
    
    //Pcc, hace referencia al punto del hook, Post Controller Constructor
    
    /**
     * 2021-06-12
     */
    function index()
    {
        //Crea instancia para obtener acceso a las librerías de codeigniter, basado en el id
            $this->CI = &get_instance();
        
        //Identificar controlador/función, y allow
            $cf = $this->CI->uri->segment(2) . '/' . $this->CI->uri->segment(3);
            $allow_cf = $this->allow_cf($cf);    //Permisos de acceso al recurso controlador/función
            
            //Verificar allow
            if ( $allow_cf )
            {
                //$this->no_leidos();     //Actualizar variable de sesión, cant mensajes no leídos
            } else {
                //No tiene autorización
                if ( $this->CI->uri->segment(1) == 'api' ) {
                    redirect("api/app/denied/{$cf}");
                } else {
                    redirect("app/app/denied/{$cf}");
                }
            }
    }
    
    /**
     * Control de acceso de usuarios basado en el archivo config/acl.php
     * CF > Ruta Controller/Function
     * 2021-10-16
     */
    function allow_cf($cf)
    {
        //Cargando lista de control de acceso, application/config/acl.php
        $this->CI->config->load('acl', TRUE);
        $acl = $this->CI->config->item('acl');

        //Variables
        $role = $this->CI->session->userdata('role');
        $allow_cf = FALSE;
        
        //Verificar en funciones públicas
        if ( in_array($cf, $acl['public_functions']) ) $allow_cf = TRUE;
        
        //Si inició sesión
        if ( $this->CI->session->userdata('logged') == TRUE )
        {
            //Es administrador, todos los permisos
            if ( in_array($role, array(1,2)) ) $allow_cf = TRUE;
            //Funciones para todos los usuarios con sesión iniciada
            if ( in_array($cf, $acl['logged_functions']) ) $allow_cf = TRUE;
        }

        //Funciones para el rol actual
        if ( array_key_exists($cf, $acl['function_roles']) )
        {
            $roles = $acl['function_roles'][$cf];
            if ( in_array($role, $roles) ) $allow_cf = TRUE;
        }

        //Funciones de API
        if ( $this->CI->uri->segment(1) == 'api' ) {
            $allow_cf = FALSE;
            //Está en las funciones públicas
            if ( in_array($cf, $acl['api_public_functions']) ) $allow_cf = TRUE;

            //Autorizado por userkey
            $user_request = $this->user_request();
            if ( ! is_null($user_request) ) $allow_cf = TRUE;
        }

        return $allow_cf;
    }
    
    /**
     * Antes de cada acceso, actualiza la variable de sesión de cantidad de mensajes sin leer
     */
    function qty_unread()
    {
        $this->CI = &get_instance();
        
        //Consulta
            $this->CI->db->where('status', 0);  //No leído
            $this->CI->db->where('user_id', $this->CI->session->userdata('user_id'));  //No leído
            $messages = $this->CI->db->get('message_user');
            
        //Establecer valor
            $qty_unread = 0;
            if ( $messages->num_rows() > 0 ) { $qty_unread = $messages->num_rows(); }
        
        //Actualizar variable de sesión
            $this->CI->session->set_userdata('qty_unread', $qty_unread);
    }

    /**
     * Row usuario que hace el request por la API
     * 2021-10-16
     */
    function user_request()
    {
        $this->CI = &get_instance();

        $user = null;   //Valor por defecto
        
        $arr_ik = explode('-', $this->CI->input->get('ik'));
        if ( count($arr_ik) == 2 ) {
            $user_id = $arr_ik[0];
            $userkey = $arr_ik[1];

            $condition = "id = '{$user_id}' AND userkey = '{$userkey}'";
            $this->CI->db->where($condition);
            $users = $this->CI->db->get('users', 1);
            
            if ( $users->num_rows() > 0 ) $user = $users->row();            
        }

        return $user;
    }
    
}