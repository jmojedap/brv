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

    
}