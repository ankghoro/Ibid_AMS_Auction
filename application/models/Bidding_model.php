<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Bidding_model extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

    public function firebase(){
        $firebasePath = FCPATH.'/application/third_party/firebase/';
        $serviceAccount = ServiceAccount::fromJsonFile($firebasePath.'ibid-firebase-service-account.json');
        $apiKey = 'AIzaSyC-ZoZ16SiFPoz76W0yJbqhlLOYpPrMU7I';
        return $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        // ->withDatabaseUri('https://ibid-ams-sample.firebaseio.com')
        ->withDatabaseUri('https://ibid-firebase.firebaseio.com')
        ->create();
    }

}