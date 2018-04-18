<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Online_bid extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

    public function firebase(){
        $firebasePath = FCPATH.'/application/third_party/firebase/';
        $serviceAccount = ServiceAccount::fromJsonFile($firebasePath.'ibid-firebase-service-account.json');
        // $serviceAccount = ServiceAccount::fromJsonFile(FCPATH.'ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json');
        $apiKey = 'AIzaSyBL_2GK0mHB2Vk9_zyRuCihoiTHg_r8WiU';
        return $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        // ->withDatabaseUri('https://ibid-ams-sample.firebaseio.com')
        ->withDatabaseUri('https://ibid-firebase.firebaseio.com')
        ->create();
    }

}
