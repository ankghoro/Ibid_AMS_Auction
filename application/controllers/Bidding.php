<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

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
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json');
        $apiKey = 'AIzaSyC-ZoZ16SiFPoz76W0yJbqhlLOYpPrMU7I';
        $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        ->withDatabaseUri('https://ibid-ams-sample.firebaseio.com')
        ->create();

        $database = $firebase->getDatabase();
        $reference = $database->getReference('3/1/1/log');
        $last = $reference->orderByKey()->limitToLast(1)->getValue();

        if (count($last) > 0) {
            foreach ($last as $key => $value) {
                $bid = $value['bid'] + $interval;
            }
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