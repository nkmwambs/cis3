<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
    
    public $write_db = null;
    public $read_db = null;

    function __construct(){
        parent::__construct();

        $this->read_db = $this->load->database('read_db',true);
        $this->write_db = $this->load->database('write_db',true);
    }
}