<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online extends CI_Controller {
	
 	public function __construct()
    {
        parent::__construct();
        $this->load->model('online_bid','bid');
    }

	public function index()
	{

	}

	public function bid()
	{
        $interval = (int)$this->input->post('interval');
        $company = $this->input->post('company');
        $schedule = $this->input->post('schedule');
        $lotStock = $this->input->post('stock');
        $database = $this->bid->firebase()->getDatabase();
        $reference = $database->getReference("company/$company/schedule|online/$schedule/mode|ON/lot|stock/$lotStock/log");
        // var_dump($reference);die();
        $postData = [
            "bid" => $interval
        ];
        $reference->push($postData);



        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(["status" => true]));
    }

}

?>