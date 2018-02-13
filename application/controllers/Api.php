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
        $this->load->helper(array('url','cookie','global'));
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
                $jadwal = true; 
                $status = true;
                $desc = "Jadwal tersedia";
                foreach ($schedules->data as $key => $value) {
                    $arr[$key]['ScheduleId'] = $value->id;
                    $arr[$key]['CompanyId'] = $value->company_id;
                    $arr[$key]['companyName'] = $value->CompanyName;
                    $arr[$key]['Desc'] = "Jadwal tersedia";
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