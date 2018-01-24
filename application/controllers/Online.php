<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Online extends CI_Controller {
	
 	public function __construct()
    {
        parent::__construct();
        $this->load->model('bidding_model','bid');
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


    /**
    * Check if the Application running !
    * @author akmal.m@smooets.com
    * @param     unknown_type $PID
    * @return     boolen
    */
    private function is_running($PID){
       exec("ps $PID", $ProcessState);
       return(count($ProcessState) >= 2);
    }

    /**
    * Kill Application by PID
    * @author akmal.m@smooets.com
    * @param  unknown_type $PID
    * @return boolen
    */
    private function kill($PID){
      if($this->is_running($PID)){
        exec("kill -KILL $PID");
      }
      return true;
    }

	public function scheduler()
	{
        $database = $this->bid->firebase()->getDatabase();
        date_default_timezone_set("Asia/Jakarta");
        $currentDateTime = date("d-m-y H:i:s");
        $url = $this->config->item('ibid_schedule')."/api/scheduleOnlineForTheDay"; 
        // $url = "localhost/ibid-ams-schedule/api/scheduleOnlineForTheDay"; //local uses
        $scheduleOnline = json_decode($this->get_curl($url));
        if ($scheduleOnline) {
            $PID = 0;
            foreach ($scheduleOnline->data as $schedule) {
                $date = date_create($schedule->date);
                $date = date_format($date, "d-m-y");
                $dateTime = $date." ".$schedule->waktu;
                $duration = (int)$schedule->duration;
                if (strtotime($currentDateTime) >= strtotime($dateTime)) {
                    $company = $schedule->company_id;
                    $id = $schedule->id;
                    $reference = $database->getReference("company/$company/schedule/$id");
                    $check = $reference->getValue();
                    if (strtotime($currentDateTime) <= strtotime($dateTime)+$duration) {
                        if (is_null($check)) {
                            $lot_url = $this->config->item('ibid_lot')."/api/getLotFilter/$id";
                            // $lot_url = "localhost/ibid-lot/api/getLotFilter/$id";
                            $lotData = json_decode($this->get_curl($lot_url));
                            if ($lotData->status) {
                                foreach ($lotData->data as $value) {
                                    $lotStock = $value->no_lot;
                                    $lotReference = $database->getReference("company/$company/schedule/$id/lot|stock/$lotStock");

                                    $lotData = [
                                        "lot" => $value->no_lot,
                                        "scheduleId" => $id,
                                        "date" => $schedule->date,
                                        "stockName" => $value->stock_name,
                                        "type_id" => 1,
                                        "stock_id" => $value->stock_id,
                                        "model" => $value->stock_model,
                                        "merk" => $value->stock_merk,
                                        "tipe" => $value->stock_tipe,
                                        "seri" => $value->stock_seri,
                                        "silinder" => $value->stock_silinder,
                                        "tahun" => $value->stock_year,
                                        "nopol" => $value->stock_police_numb,
                                        "harga" => $value->stock_startprice,
                                        "duration" => $value->duration,
                                        "VA" => $value->no_va,
                                        "LotStatus" => $value->status
                                    ];

                                    $lotDataReference = $lotReference->getChild("lotData");
                                    $lotDataReference->set($lotData);
                                    $allowBidReference = $lotReference->getChild("allowBid");
                                    $allowBidReference->set(true);

                                    if (!is_null(@$value->proxyBS_PID)) {
                                        $this->kill($value->proxyBS_PID);
                                    }

                                    $backgroundProcess = " > /dev/null 2>&1 & echo $!";

                                    $commandForRunProxy = "node ".FCPATH."proxy_runner.js ";
                                    $commandForRunProxy .= $value->company_id." ";
                                    $commandForRunProxy .= $value->schedule_id." ";
                                    $commandForRunProxy .= $value->no_lot." ";
                                    $commandForRunProxy .= (int)$schedule->interval+0;
                                    $commandForRunProxy .= " ";
                                    $commandForRunProxy .= ((int)str_replace(array('.'), '',$value->stock_startprice));
                                    $commandForRunProxy .= $backgroundProcess;

                                    exec($commandForRunProxy ,$proxyPID);
                                    
                                    if (!is_null(@$value->queueBS_PID)) {
                                        $this->kill($value->queueBS_PID);
                                    }

                                    $commandForRunQueueing = "node ".FCPATH."online_worker.js ";
                                    $commandForRunQueueing .= " ".$value->company_id;
                                    $commandForRunQueueing .= " ".$value->schedule_id;
                                    $commandForRunQueueing .= " ".$value->no_lot." ";
                                    $commandForRunQueueing .= (int)$schedule->interval+0;
                                    $commandForRunQueueing .= " ";
                                    $commandForRunQueueing .= ((int)str_replace(array('.'), '',$value->stock_startprice));
                                    $commandForRunQueueing .= " ".$backgroundProcess; 

                                    exec($commandForRunQueueing ,$queuePID);

                                    $updateBS_PID = $this->config->item('ibid_lot')."/api/updatelot/$value->id?";
                                    if (!is_null(@$proxyPID[$PID])) { 
                                        substr($updateBS_PID, -1) == '?' ? $updateBS_PID .= '' :  $updateBS_PID .= '&';
                                        $updateBS_PID .= "proxyPID=$proxyPID[$PID]";
                                    }
                                    if (!is_null(@$queuePID[$PID])) { 
                                        substr($updateBS_PID, -1) == '?' ? $updateBS_PID .= '' :  $updateBS_PID .= '&';
                                        $updateBS_PID .= "queuePID=$queuePID[$PID]";
                                    }

                                    if (!is_null($proxyPID[$PID]) || !is_null($queuePID[$PID])) {
                                        $UpdateLotRes = json_decode($this->get_curl($updateBS_PID));
                                    }
                                    $PID++;
                                }
                            }
                        }                    
                    }
                }
            $PID++;
            }
        }
	}

	public function bid()
	{
        $startprice = (int)$this->input->post('startprice');
        $interval = (int)$this->input->post('interval');
        $company = $this->input->post('company');
        $schedule = $this->input->post('schedule');
        $lotStock = $this->input->post('lot');
        $npl = $this->input->post('npl');
        $database = $this->bid->firebase()->getDatabase();
        $checkreference = $database->getReference("company/$company/schedule/$schedule");
        $reference = $database->getReference("company/$company/schedule/$schedule/lot|stock/$lotStock/log");
        $value_check = $checkreference->getValue();
        $lastBid = $reference->orderByKey()->limitToLast(1)->getValue();
        if ($value_check['scheduleOn']) {
            if (is_null($lastBid)) {
                $bid = $startprice + $interval;
                $postData = ["bid" => $bid, "npl" => $npl];
                $reference->push($postData); 
            } else {
                $last = reset($lastBid);
                $bid = $last['bid'] + $interval;
                $postData = ["bid" => $bid, "npl" => $npl];
                $reference->push($postData); 
            }
            $status = true;
            $description = "Berhasil menambahkan data";
        } else {
           $status = false;
           $description = "Gagal, sesi lelang telah berakhir"; 
        }

        $response = [
            'status' => $status,
            'description' => $description
        ];

        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
    }

}

?>
