<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Bidding_model extends CI_Model {

	function __construct()
    {
        parent::__construct();
    }

    public function firebase(){
        $serviceAccount = ServiceAccount::fromJsonFile($_SERVER['DOCUMENT_ROOT'].'/ibid-ams-sample-firebase-adminsdk-b6oyv-6befd6b9c5.json');
        $apiKey = 'AIzaSyC-ZoZ16SiFPoz76W0yJbqhlLOYpPrMU7I';
        return $firebase = (new Factory)
        ->withServiceAccountAndApiKey($serviceAccount,$apiKey)
        ->withDatabaseUri('https://ibid-ams-sample.firebaseio.com')
        ->create();
    }

}