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
        $data['content'] = 'content';
        $data['content_script'] = 'script';
        $data['content_modal'] = 'modal';
        $data['CompanyId'] = $UserLogon['CompanyId'];
        $this->load->view('/templates/theadmin', $data);
	}

    public function datalot($id){
        if (isset($_COOKIE['UserLogon'])) {
            $datauser = isset($_COOKIE['UserLogon']) ? unserialize($_COOKIE['UserLogon']) : null;
            $schedule_url =  $this->config->item('ibid_schedule')."/api/scheduleForTheDay/".$datauser['CompanyId']; //Used for Staging
            // $schedule_url = "localhost/ibid-ams-schedule/api/scheduleForTheDay/".$datauser['CompanyId']; //Used on local
            $scheduledata = json_decode($this->get_curl($schedule_url));
            $check_schedule = count($scheduledata->data);
            // var_dump($scheduledata); die();
            $arr = array();
            if ($check_schedule != 0) {
                $schedule_id = $scheduledata->data->id;
                $lot_url2 = $this->config->item('ibid_lot')."/api/getLotReadyBySchedule/$schedule_id";
                // $lot_url2 = "localhost/ibid-lot/api/getLotReadyBySchedule/$schedule_id";
                $lotReady = json_decode($this->get_curl($lot_url2));
                $lot_url3 = $this->config->item('ibid_lot')."/api/getLotBySchedule/$schedule_id";
                // $lot_url3 = "localhost/ibid-lot/api/getLotBySchedule/$schedule_id";
                $lotBySchedule = json_decode($this->get_curl($lot_url3));
                // var_dump($stockdata); die();
                $no = 0;
                $date = $scheduledata->data->date;
                $countLotReady = count($lotReady->data); // Mengecek jumlah lot dari schedule yang ready
                $countLotSchedule = count($lotBySchedule->data); //Mengecek jumlah lot dari schedule

                // var_dump($lotReady->data); die();
                if ($countLotReady != 0) {
                    do {
                        
                        foreach ($lotBySchedule->data as $check) {
                            if ((int)$id == (int)$check->no_lot) {
                                $reason = $check->reason;
                                $status = $check->status;
                                $lot_no = $check->no_lot;
                                break;
                            }
                        }
                        $id++;
                    } while ($reason != null || $status == "terjual" || $status == "tidak terjual");

                    
                    $no = (int)$lot_no;
                    $lot_url4 = $this->config->item('ibid_lot')."/api/lotData/".$no."/".$schedule_id; //lot data row
                    // $lot_url4 = "localhost/ibid-lot/api/lotData/".$no."/".$schedule_id; //lot data row
                    $lotdatarow = json_decode($this->get_curl($lot_url4));
                    $stock_id = $lotdatarow->stock_id;
                    $stock_url2 = $this->config->item('ibid_stock')."/api/stockData/".$stock_id;
                    // $stock_url2 = "localhost/ibid-stock/api/stockData/".$stock_id;
                    $stockDatarow = json_decode($this->get_curl($stock_url2));
                        $arr['AuctionItemId'] = $stockDatarow->AuctionItemId; 
                        $arr['Merk'] = $stockDatarow->Merk;
                        $arr['Tipe'] = $stockDatarow->Tipe;
                        $arr['Silinder'] = $stockDatarow->Silinder;
                        $arr['Warna'] = $stockDatarow->Warna;
                        $arr['Transmisi'] = $stockDatarow->Transmisi;
                        $arr['Kilometer'] = $stockDatarow->Kilometer;
                        $arr['BahanBakar'] = $stockDatarow->BahanBakar;
                        $arr['Exterior'] = $stockDatarow->Exterior;
                        $arr['Interior'] = $stockDatarow->Interior;
                        $arr['Mesin'] = $stockDatarow->Mesin;
                        $arr['Rangka'] = $stockDatarow->Rangka;
                        $arr['Grade'] = $stockDatarow->Grade;
                        $arr['ItemId'] = $stockDatarow->ItemId;
                        $arr['NoLot'] = (int)$lot_no;
                        $arr['ScheduleId'] = $schedule_id;
                        $arr['Date'] = $date;
                        $arr['StartPrice'] = (int)$stockDatarow->StartPrice;
                        $arr['Interval'] = (int)$scheduledata->data->interval;
                    // var_dump($stockDatarow);die();
                    $jadwal = true; 
                    count($arr) > 0 ? $status = true : $status = false;
                    $no == $countLotSchedule ? $disable = true : $disable = false; 

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
