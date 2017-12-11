<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bidding extends CI_Controller {
	
 	public function __construct()
    {
        parent::__construct();
        $this->load->model('bidding_model','bid');
    }

	public function index()
	{
        $data['menu'] = load_menu()['menu'];
        $data['content'] = 'bidding/index';
        $data['content_script'] = 'bidding/script';
        // $data['content_modal'] = 'modal';
        $this->load->view('/templates/current-bid', $data);
	}

	public function bid()
	{
        $interval = (int)$this->input->post('interval');
        $biddertype = $this->input->post('biddertype');
        $startprice = 1000000;
        $database = $this->bid->firebase()->getDatabase();
        $reference = $database->getReference('3/1/1/log');
        $last = $reference->orderByKey()->limitToLast(1)->getValue();

        if (count($last) > 0) {
            $last = reset($last);
            $bid = $last['bid'] + $interval;
        }else{
            $bid = $startprice + $interval;
        }

        $postData = [
            "bid" => $bid,
            "type" => $biddertype
        ];
        $reference->push($postData);

        echo json_encode(["status" => true]);
	}
}

?>