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

    private function jsonPost($url,$data_json){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
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
        $data['menu'] = load_menu()['menu'];
        $data['assets_url'] = load_header()['assets_url'];
        // $data['content'] = 'content';
        $data['content_script'] = 'script';
        $data['content_modal'] = 'modal';
        $data['CompanyId'] = $UserLogon['CompanyId'];
        $this->load->view('/templates/auction', $data);
	}

    public function datalot(){
        if (isset($_COOKIE['UserLogon'])) {
            $datauser = isset($_COOKIE['UserLogon']) ? unserialize($_COOKIE['UserLogon']) : null;
            $schedule_url =  $this->config->item('ibid_schedule')."/api/scheduleForTheDay/".$datauser['CompanyId']; //Used for Staging
            // $schedule_url = "localhost/ibid-ams-schedule/api/scheduleForTheDay/".$datauser['CompanyId']; //Used on local
            $scheduledata = json_decode($this->get_curl($schedule_url));
            $check_schedule = count($scheduledata->data);
            $arr = array();
            if ($check_schedule != 0) {
                $schedule_id    = $scheduledata->data->id;
                $getLotUrl      = $this->config->item('ibid_lot')."/api/getLot/$schedule_id";
                $lotReady       = json_decode($this->get_curl($getLotUrl));
                $getLastLotUrl  = $this->config->item('ibid_lot')."/api/getLastLot/$schedule_id";
                $lastLot        = json_decode($this->get_curl($getLastLotUrl));
                $getLotBySchedule  = $this->config->item('ibid_lot')."/api/getLotBySchedule/$schedule_id";
                $lotBySchedule     = json_decode($this->get_curl($getLotBySchedule));
                $lotBySchedule     = count($lotBySchedule->data);
                $date = $scheduledata->data->date;
                $schedule_date = $scheduledata->data->date;
                $schedule_date = date_create($schedule_date);
                $schedule_date = date_format($schedule_date, "j F Y");
                $company = $scheduledata->data->CompanyName;
                $waktu = $scheduledata->data->waktu;
                $waktu = date_create($waktu);
                $waktu = date_format($waktu, "H:i");
                $jenis = $scheduledata->data->ItemName;
                if ($lotReady->status && $lastLot->status) {
                    $stock_id = $lotReady->data->stock_id;
                    $currentLot = $lotReady->data->no_lot;
                    $lastLot = $lastLot->data->no_lot;
                    $getStockUrl = $this->config->item('ibid_stock')."/api/stockData/".$stock_id;
                    $stockDatarow = json_decode($this->get_curl($getStockUrl));
                    $arr['AuctionItemId'] = $stockDatarow->AuctionItemId; 
                    $arr['Merk'] = $stockDatarow->Merk;
                    $arr['Tipe'] = $stockDatarow->Tipe;
                    $arr['Silinder'] = $stockDatarow->Silinder;
                    $arr['Model'] = $stockDatarow->Model;
                    $arr['Tahun'] = $stockDatarow->Tahun;
                    $arr['Warna'] = $stockDatarow->Warna;
                    $arr['Transmisi'] = $stockDatarow->Transmisi;
                    $arr['NoPolisi'] = $stockDatarow->NoPolisi;
                    $arr['Kilometer'] = $stockDatarow->Kilometer;
                    $arr['BahanBakar'] = $stockDatarow->BahanBakar;
                    $arr['Exterior'] = $stockDatarow->Exterior;
                    $arr['Interior'] = $stockDatarow->Interior;
                    $arr['Mesin'] = $stockDatarow->Mesin;
                    $arr['Rangka'] = $stockDatarow->Rangka;
                    $arr['Grade'] = $stockDatarow->Grade;
                    $arr['ItemId'] = $stockDatarow->ItemId;
                    $arr['NoLot'] = (int)$currentLot;
                    $arr['ScheduleId'] = $schedule_id;
                    $arr['Date'] = $date;
                    $arr['ScheduleDate'] = $schedule_date;
                    $arr['Company'] = $company;
                    $arr['Waktu'] = $waktu;
                    $arr['Jenis'] = $jenis;
                    $arr['LotTotal'] = $lotBySchedule;
                    $arr['StartPrice'] = (int)$stockDatarow->StartPrice;
                    $arr['Interval'] = (int)$scheduledata->data->interval;

                    $jadwal = true; 
                    $status = true;
                    $currentLot == $lastLot ? $disable = true : $disable = false; 
                } else {
                    $jadwal = true; 
                    $status = false;
                    $disable = true;
                }
            } else {
                $jadwal = false;
                $status = false;
                $disable = true;
            }
        }
        $newData = [
            'jadwal' => $jadwal,
            'schedule_id' => isset($schedule_id) ? $schedule_id : null,
            'status' => $status,
            'data' => $arr,
            'disable' => $disable
        ];
        echo json_encode($newData);
    }

    public function skip(){
        $reason = $this->input->post('Reason');
        $schedule_id = $this->input->post('ScheduleId');
        $lot = (int)$this->input->post('Lot');
        $data_json = array();
        $status = true;
            $data_json['schedule_id'] = $schedule_id;
            $data_json['reason'] = $reason;
            $data_json['lot'] = $lot;
            $data_json = json_encode($data_json);
            // var_dump($data_json); die();
            $url = $this->config->item('ibid_lot')."/api/skipLot";
            // $url = "localhost/ibid-lot/api/skipLot";
            $proceed = $this->jsonPost($url,$data_json);

            $output = [
                'status' => $status
            ];

        echo json_encode($output);
    }

    public function checkLot(){
        $schedule_id = $this->input->post('ScheduleId');
        $skiprange = (int)$this->input->post('SkipRange');
        $lot = (int)$this->input->post('Lot');
        $lot_url = $this->config->item('ibid_lot')."/api/getLotBySchedule/$schedule_id";
        // $lot_url = "localhost/ibid-lot/api/getLotBySchedule/$schedule_id";
        $lotBySchedule = json_decode($this->get_curl($lot_url));
        $count = count($lotBySchedule->data);
        $check = $count - $skiprange;
        $arr = array();
        if ($check < 0) {
            $status = false;
        } else {
            $status = true;
        }

        $arr['total'] = $count;
        $output = [
            'status' => $status,
            'data' => $arr,
        ];

        echo json_encode($output);
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
        $bidlog['State'] = "Floor Bid";
        // var_dump($bidlog); die();

        $output = [
            'status' => $status,
            'data' => $bidlog
        ];
        echo json_encode($output);
    }

    public function proxyBidExample($price,$interval){
        $bidlog = array();
        $status = true;
        $nominal = $price + $interval;
        $bidlog['Nominal'] = $nominal;
        $bidlog['State'] = "Proxy Bid";
        $bidlog['No'] = mt_rand(1000, 9999);
        // var_dump($bidlog); die();

        $output = [
            'status' => $status,
            'data' => $bidlog
        ];
        echo json_encode($output);
    }
}

?>
