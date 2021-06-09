<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Start extends CI_Controller{
    
    function __construct() 
    {
        parent::__construct();
        
        //Para definir hora local
        date_default_timezone_set("America/Bogota");
    }

    function index()
    {
        redirect('app/accounts/login');
    }
}