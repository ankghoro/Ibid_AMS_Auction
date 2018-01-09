<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Online_bid extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

    public function firebase(){
        $serviceAccount = ServiceAccount::fromJsonFile('/home/deploy/ibid-auction/ams-online-sample-firebase-adminsdk-v8jdt-667909a461.json'); // for staging uses
        // $serviceAccount = ServiceAccount::fromJsonFile($_SERVER['DOCUMENT_ROOT'].'/ibid-auction/ams-online-sample-firebase-adminsdk-v8jdt-667909a461.json'); //for local uses
        $apiKey = 'AIzaSyAE2B_NT9aHHAH1rIyclU-ELw-haAmOcU4';
        return $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        ->withDatabaseUri('https://ams-online-sample.firebaseio.com')
        ->create();
    }

}
