<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auction extends CI_Controller {
	
 	public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->model('lot_model','lot');
        $this->load->helper(array('url','cookie'));
        $this->load->helper('domain_helper');
        
    }

    private function check_token($token)
    {
        $url = $this->config->item('adms_auth')['check'];
        $data = json_decode($this->postCURL($url, ["access_token" => $token]));
        if (isset($data->error)) {
            return false;
        } else {
            return $data->success;
        }
    }

    /**
     * function for parameter refresh token
     * @author akmal.m@smooets.com
     * @return array
    */  
    private function refresh_params($UserLogon){
        return [
            "grant_type" => 'refresh_token',
            "client_id" => 'ADMS web',
            "client_secret" => '1234567890',
            "refresh_token" => $UserLogon['refresh_token'],
            "username" => $UserLogon['username'],
            "ipAddress" => '127.0.0.1',
            "createdOn" => '1509330606'
        ];
    }
    
    /**
     * function for refresh token
     * @author akmal.m@smooets.com
     * @return object
    */
    private function refresh_token($UserLogon)
    {
        $url = $this->config->item('adms_auth')['login'];
        $data = json_decode($this->postCURL($url, $this->refresh_params($UserLogon)));
        return $data;
    }


    private function postCURL($_url, $_param){
        $postData = '';
            //create name value pairs seperated by &
            foreach($_param as $k => $v) 
            { 
               $postData .= $k . '='.$v.'&'; 
            }
            rtrim($postData, '&');
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, false); 
            curl_setopt($ch, CURLOPT_POST, count($postData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
            
            $output=curl_exec($ch);
            curl_close($ch);
            return $output;
    }

    private function get_curl($url)
    {
        //  Initiate curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL,$url);
        // Execute
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }

	public function index()
	{
        $UserLogon = isset($_COOKIE['UserLogon']) ? unserialize($_COOKIE['UserLogon']) : null;
        if (is_null($UserLogon) || ($this->check_token($UserLogon['access_token']) == false)) {
            if (!is_null($UserLogon) && $this->check_token($UserLogon['access_token']) == false) {
                $refresh_token = $this->refresh_token($UserLogon);
                if(isset($refresh_token->error)){
                    delete_cookie('UserLogon', base_domain(base_url()));
                    redirect($this->config->item('ibid_auth').'/user/login', 'refresh');
                }else {
                    $userlogon = [
                        "access_token" => $refresh_token->access_token,
                        "refresh_token" => $refresh_token->refresh_token,
                        "username" => $UserLogon['username'],
                        "CompanyId" => $UserLogon['CompanyId'],
                    ];
                    setcookie('UserLogon', serialize($userlogon), time() + (3600 * 4), "/", base_domain(base_url()));
                    redirect($this->config->item('ibid_kpl'), 'refresh');
                }
            }
            redirect($this->config->item('ibid_auth').'/user/login', 'refresh');
        }
		$this->load->helper('custom');
		$this->load->helper('url');
        $data['menu'] = load_menu()['menu'];
        $data['assets_url'] = load_header()['assets_url'];
        $data['content'] = 'content';
        $data['content_script'] = 'script';
        $data['content_modal'] = 'modal';
        $this->load->view('/templates/theadmin', $data);
	}

    public function datalot($id){
        $schedule_url =  $this->config->item('ibid_schedule')."/api/scheduleForTheDay"; // Used for Staging
        // $schedule_url = "http://ibid-ams-schedule.dev/api/scheduleForTheDay"; //Used on local
        $scheduledata = json_decode($this->get_curl($schedule_url));
        $schedule_id = $scheduledata->data[0]->id;
        // var_dump($scheduledata->data[0]); die();
        $lot_url =  $this->config->item('ibid_lot')."/api/getallLot";
        // $lot_url =  "http://ibid-lot.dev/api/getallLot";
        $lotdata = json_decode($this->get_curl($lot_url));
        // var_dump($lotdata); die();
        $lot_url2 = $this->config->item('ibid_lot')."/api/getLotReadyBySchedule/$schedule_id";
        // $lot_url2 = "http://ibid-lot.dev/api/getLotReadyBySchedule/$schedule_id";
        $lotReady = json_decode($this->get_curl($lot_url2));
        // var_dump($lotReady); die();
        $lot_url3 = $this->config->item('ibid_lot')."/api/getLotBySchedule/$schedule_id";
        // $lot_url3 = "http://ibid-lot.dev/api/getLotBySchedule/$schedule_id";
        $lotBySchedule = json_decode($this->get_curl($lot_url3));
        // var_dump($lotBySchedule); die();
        $stock_url = $this->config->item('ibid_stock')."/api/getallStock";
        // $stock_url = "http://ibid-stock.dev/api/getallStock";
        $stockdata = json_decode($this->get_curl($stock_url));
        // var_dump($stockdata); die();
        $no = 0;
        $arr = array();
        $countLotReady = count($lotReady->data);
        $countLotSchedule = count($lotBySchedule->data);
        if ($countLotReady != 0) {
            do {
                
                foreach ($lotdata->data as $check) {
                    if ($schedule_id == $check->schedule_id && $id == $check->no_lot) {
                        $reason = $check->reason;
                        $status = (int)$check->status;
                        $lot_no = $check->no_lot;
                        break;
                    }
                }
                $id++;
            } while ($reason != null || $status == 1);

            
            $no = (int)$lot_no;
            foreach ($stockdata->data as $stock) {
                $datastatus = false;
                    foreach ($lotdata->data as $lot) {
                        if ($stock->AuctionItemId == (int)$lot->stock_id && $lot->schedule_id == $scheduledata->data[0]->id) {
                            $datastatus = true;
                            $lot_no = $lot->no_lot;
                            $schedule_id = $lot->schedule_id;
                            $va = $lot->no_va;
                        }

                        if ($datastatus == true) {
                            if ($lot_no == $no) {
                                $arr['AuctionItemId'] = $stock->AuctionItemId; 
                                $arr['Merk'] = $stock->Merk;
                                $arr['Tipe'] = $stock->Tipe;
                                $arr['Silinder'] = $stock->Silinder;
                                $arr['Warna'] = $stock->Warna;
                                $arr['Transmisi'] = $stock->Transmisi;
                                $arr['Kilometer'] = $stock->Kilometer;
                                $arr['BahanBakar'] = $stock->BahanBakar;
                                $arr['Exterior'] = $stock->Exterior;
                                $arr['Interior'] = $stock->Interior;
                                $arr['Mesin'] = $stock->Mesin;
                                $arr['Rangka'] = $stock->Rangka;
                                $arr['Grade'] = $stock->Grade;
                                $arr['ItemId'] = $stock->ItemId;
                                $arr['NoLot'] = (int)$lot_no;
                                $arr['ScheduleId'] = $schedule_id;
                                $arr['VA'] = $va;
                                $arr['StartPrice'] = (int)$stock->StartPrice;
                                $arr['Interval'] = (int)$scheduledata->data[0]->interval;
                                break;
                            }
                        }
                    }
            }
            // var_dump($arr); die();
            
            count($arr) > 0 ? $status = true : $status = false;
            $no == $countLotSchedule ? $disable = true : $disable = false;   
        } else {
            $status = false;
            $disable = true;
        }
        $newData = [
            'status' => $status,
            'data' => $arr,
            'disable' => $disable
        ];
        echo json_encode($newData);
    }

    public function bidLogExample($price,$interval){
        $bidlog = array();
        $status = true;
        $nominal = $price + $interval;
        $bidlog['Nominal'] = $nominal;
        $bidlog['State'] = "Online Bid";
        $bidlog['No'] = mt_rand(1000, 9999);
        // var_dump($bidlog); die();

        $output = [
            'status' => $status,
            'data' => $bidlog
        ];
        echo json_encode($output);
    }

    public function floorBidExample($price,$interval){
        $bidlog = array();
        $status = true;
        $nominal = $price + $interval;
        $bidlog['Nominal'] = $nominal;
        $bidlog['State'] = "FloorBid";
        // var_dump($bidlog); die();

        $output = [
            'status' => $status,
            'data' => $bidlog
        ];
        echo json_encode($output);
    }
}

?>