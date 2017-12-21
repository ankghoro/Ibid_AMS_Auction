<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * summary
 */
class Api extends CI_Controller
{
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

    public function currentlot(){
        if(isset($_POST['company_id']) && ($_POST['company_id'] != '')) {
            $schedule_url =  $this->config->item('ibid_schedule')."/api/scheduleForTheDay/".$_POST['company_id']; //Used for Staging
            $scheduledata = json_decode($this->get_curl($schedule_url));
            $check_schedule = count($scheduledata->data);
            $arr = array();
            if ($check_schedule != 0) {
                $schedule_id    = $scheduledata->data->id;
                $getLotUrl      = $this->config->item('ibid_lot')."/api/getLot/$schedule_id";
                $lotReady       = json_decode($this->get_curl($getLotUrl));
                $getLastLotUrl  = $this->config->item('ibid_lot')."/api/getLastLot/$schedule_id";
                $lastLot        = json_decode($this->get_curl($getLastLotUrl));
                $date = $scheduledata->data->date;
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
                    $arr['StartPrice'] = (int)$stockDatarow->StartPrice;
                    $arr['Interval'] = (int)$scheduledata->data->interval;

                    $jadwal = true; 
                    $status = true;
                    $desc = "Data berhasil diambil";
                } else {
                    $jadwal = true; 
                    $status = false;
                    $desc = "Lot sudah tidak tersedia";
                }
            } else {
                $jadwal = false;
                $status = false;
                $desc = "Schedule tidak tersedia";
            }
        } else {
            $jadwal = false;
            $status = false;
            $desc = "Company id harus diisi";
        }
        $newData = [
            'jadwal' => $jadwal,
            'status' => $status,
            'data' => isset($arr) ? $arr : null,
            'desc' => $desc
        ];
        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($newData));
    }

    public function multicurrentlot(){
        if(isset($_POST['schedules']) && (count($_POST['schedules']) > 4)){
            $jadwal = false;
            $status = false;
            $desc = "Tidak boleh memilih jadwal lebih dari 4";
        } else if(isset($_POST['schedules']) && ($_POST['schedules'] != '')) {
            $schedule_url =  $this->config->item('ibid_schedule')."/api/multiLiveSchedule"; //Used for Staging
            $schedules = json_encode($_POST['schedules']);
            $schedules = json_decode($this->postCURL($schedule_url, ["schedules" => $schedules]));
            $check_schedule = count($schedules->data);
            $arr = array();
            if ($check_schedule != 0) {
                foreach ($schedules->data as $key => $value) {
                    $schedule_id    = $value->id;
                    $company_id    = $value->company_id;
                    $getLotUrl      = $this->config->item('ibid_lot')."/api/getLot/$schedule_id";
                    $lotReady       = json_decode($this->get_curl($getLotUrl));
                    $getLastLotUrl  = $this->config->item('ibid_lot')."/api/getLastLot/$schedule_id";
                    $lastLot        = json_decode($this->get_curl($getLastLotUrl));
                    $date = $value->date;
                    $desc = "Data berhasil diambil";
                    if ($lotReady->status && $lastLot->status) {
                        $stock_id = $lotReady->data->stock_id;
                        $currentLot = $lotReady->data->no_lot;
                        $lastLot = $lastLot->data->no_lot;
                        $getStockUrl = $this->config->item('ibid_stock')."/api/stockData/".$stock_id;
                        $stockDatarow = json_decode($this->get_curl($getStockUrl));
                        $arr[$key]['Lot'] = $currentLot;
                        $arr[$key]['ScheduleId'] = $schedule_id;
                        $arr[$key]['CompanyId'] = $company_id;
                        $arr[$key]['Desc'] = "Lot tersedia";
                        $PrevLot = $lotReady->prevLot;
                        if (!is_null($PrevLot)){
                            $arr[$key]['PrevLot']['Desc'] = $PrevLot->stock_name.' '.$PrevLot->stock_seri.' '.$PrevLot->stock_year;
                            $arr[$key]['PrevLot']['Price'] = (int)$PrevLot->stock_startprice;
                        }else{
                            $arr[$key]['PrevLot'] = [];
                        }
                        $NextLot = $lotReady->nextLot;
                        if (!is_null($NextLot)){
                            $arr[$key]['NextLot']['Desc'] = $NextLot->stock_name.' '.$NextLot->stock_seri.' '.$NextLot->stock_year;
                            $arr[$key]['NextLot']['Price'] = (int)$NextLot->stock_startprice;
                        }else{
                            $arr[$key]['NextLot'] = [];
                        }
                        $arr[$key]['Stock']['AuctionItemId'] = $stockDatarow->AuctionItemId; 
                        $arr[$key]['Stock']['Merk'] = $stockDatarow->Merk;
                        $arr[$key]['Stock']['Tipe'] = $stockDatarow->Tipe;
                        $arr[$key]['Stock']['Silinder'] = $stockDatarow->Silinder;
                        $arr[$key]['Stock']['Model'] = $stockDatarow->Model;
                        $arr[$key]['Stock']['Tahun'] = $stockDatarow->Tahun;
                        $arr[$key]['Stock']['Warna'] = $stockDatarow->Warna;
                        $arr[$key]['Stock']['Transmisi'] = $stockDatarow->Transmisi;
                        $arr[$key]['Stock']['NoPolisi'] = $stockDatarow->NoPolisi;
                        $arr[$key]['Stock']['Kilometer'] = $stockDatarow->Kilometer;
                        $arr[$key]['Stock']['BahanBakar'] = $stockDatarow->BahanBakar;
                        $arr[$key]['Stock']['Exterior'] = $stockDatarow->Exterior;
                        $arr[$key]['Stock']['Interior'] = $stockDatarow->Interior;
                        $arr[$key]['Stock']['Mesin'] = $stockDatarow->Mesin;
                        $arr[$key]['Stock']['Rangka'] = $stockDatarow->Rangka;
                        $arr[$key]['Stock']['Grade'] = $stockDatarow->Grade;
                        $arr[$key]['Stock']['ItemId'] = $stockDatarow->ItemId;
                        $arr[$key]['Stock']['Date'] = $date;
                        $arr[$key]['Stock']['StartPrice'] = (int)$stockDatarow->StartPrice;
                        $arr[$key]['Stock']['Interval'] = (int)$value->interval;
    
                        $jadwal = true; 
                        $status = true;
                    } else {
                        $jadwal = true; 
                        $status = true;
                        $arr[$key]['Lot'] = null;
                        $arr[$key]['ScheduleId'] = $schedule_id;
                        $arr[$key]['CompanyId'] = $company_id;
                        $arr[$key]['Desc'] = "Lot sudah tidak tersedia";
                        $arr[$key]['Stock'] = []; 
                    }
                }
            } else {
                $jadwal = false;
                $status = false;
                $desc = "Jadwal tidak tersedia";
            }
        } else {
            $jadwal = false;
            $status = false;
            $desc = "Wajib memilih jadwal";
        }
        $newData = [
            'jadwal' => $jadwal,
            'status' => $status,
            'data' => isset($arr) ? $arr : null,
            'desc' => $desc
        ];
        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($newData));
    }
}