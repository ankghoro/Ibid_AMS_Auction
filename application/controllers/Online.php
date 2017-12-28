<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online extends CI_Controller {
	
 	public function __construct()
    {
        parent::__construct();
        $this->load->model('online_bid','bid');
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

	public function scheduler()
	{
        $database = $this->bid->firebase()->getDatabase();
        date_default_timezone_set("Asia/Jakarta");
        $currentDateTime = date("d-m-y H:i:s");
        $url = $this->config->item('ibid_schedule')."/api/scheduleOnlineForTheDay/"; //for staging uses
        // $url = "localhost/ibid-ams-schedule/api/scheduleOnlineForTheDay"; //local uses
        $scheduleOnline = json_decode($this->get_curl($url));
        if ($scheduleOnline) {
            foreach ($scheduleOnline->data as $schedule) {
                $date = date_create($schedule->date);
                $date = date_format($date, "d-m-y");
                $dateTime = $date." ".$schedule->waktu;
                $duration = (int)$schedule->duration;
                if (strtotime($currentDateTime) >= strtotime($dateTime)) {
                    $company = $schedule->company_id;
                    $id = $schedule->id;
                    $reference = $database->getReference("company/$company/schedule|online/$id");
                    $check = $reference->getValue();
                    // var_dump($check); die();
                    if (strtotime($currentDateTime) <= strtotime($dateTime)+$duration) {
                        if (is_null($check)) {
                            $postData = ["scheduleOn" => true];
                            $reference->set($postData);  
                        }                    
                    } else {
                        $updateUrl = $this->config->item('ibid_schedule')."/api/updateStatus/$id";
                        // $updateUrl = "localhost/ibid-ams-schedule/api/updateStatus/$id";
                        $this->get_curl($updateUrl);
                        $postData = ["scheduleOn" => false];
                        $reference->update($postData);
                    }
                }
            }
        }

        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(["status" => true]));
        // var_dump($scheduleOnline); die();
        
	}

	public function bid()
	{
        $interval = (int)$this->input->post('interval');
        $company = $this->input->post('company');
        $schedule = $this->input->post('schedule');
        $lotStock = $this->input->post('stock');
        $database = $this->bid->firebase()->getDatabase();
        $checkreference = $database->getReference("company/$company/schedule|online/$schedule");
        $reference = $database->getReference("company/$company/schedule|online/$schedule/lot|stock/$lotStock/log");
        $value = $checkreference->getValue();
        if ($value['scheduleOn']) {
            $status = true;
            $description = "Berhasil menambahkan data";
            $postData = ["bid" => $interval];
            $reference->push($postData);
        } else {
           $status = false;
           $description = "Gagal, sesi lelang telah berakhir"; 
        }

        $response = [
            'status' => $status,
            'description' => $description
        ];
        // var_dump($value['scheduleOn']);die();

        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
    }

}

?>