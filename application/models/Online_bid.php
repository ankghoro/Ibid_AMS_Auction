<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Online_bid extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

    public function firebase(){
        $serviceAccount = ServiceAccount::fromJsonFile(FCPATH.'ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json');
        $apiKey = 'AIzaSyC-ZoZ16SiFPoz76W0yJbqhlLOYpPrMU7I';
        return $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        // ->withDatabaseUri('https://ibid-ams-sample.firebaseio.com')
        ->withDatabaseUri('https://ibid-firebase.firebaseio.com')
        ->create();
    }

}
