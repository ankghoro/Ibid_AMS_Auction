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
}