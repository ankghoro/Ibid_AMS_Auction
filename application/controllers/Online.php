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
        $url = "http://ibid-ams-schedule.stagingapps.net/api/scheduleOnlineForTheDay/"; //for staging uses
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
                    $reference = $database->getReference("company/$company/schedule/$id");
                    $check = $reference->getValue();
                    // var_dump($check); die();
                    if (strtotime($currentDateTime) <= strtotime($dateTime)+$duration) {
                        if (is_null($check)) {
                            $lot_url = "http://ibid-ams-lot.stagingapps.net/api/getLotFilter/$id";
                            // $lot_url = "localhost/ibid-lot/api/getLotFilter/$id";
                            $lotData = json_decode($this->get_curl($lot_url));
                            if ($lotData->status) {
                                $postData = ["scheduleOn" => true];
                                $reference->set($postData); 
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
                                        "merk" => $value->stock_name,
                                        "tipe" => $value->stock_name,
                                        "silinder" => $value->stock_silinder,
                                        "tahun" => $value->stock_year,
                                        "nopol" => $value->stock_police_numb,
                                        "harga" => $value->stock_startprice,
                                        "VA" => " ",
                                    ];
                                    $lotReference->set($lotData);

                                    // if (!is_null(@$lotReady->data->proxyBS_PID)) {
                                    //     $this->kill($lotReady->data->proxyBS_PID);
                                    // } else {
                                    //     $commandForRunProxy = "php ".FCPATH."../ibid-autobid/index.php proxy bid ".$datauser['CompanyId']." ".$arr['ScheduleId']." ".$arr['NoLot']." ".$arr['Interval']." > /dev/null 2>&1 & echo $!";
                                    //     exec($commandForRunProxy ,$proxyPID);
                                    // }
                                    
                                    // if (!is_null(@$lotReady->data->queueBS_PID)) {
                                    //     $this->kill($lotReady->data->queueBS_PID);
                                    // } else {
                                    //     $commandForRunQueueing = "node ".FCPATH."que_worker.js ".$datauser['CompanyId']." ".$arr['ScheduleId']." ".$arr['NoLot']." ".$arr['Interval']." ".$arr['StartPrice']." > /dev/null 2>&1 & echo $!";
                                    //     exec($commandForRunQueueing ,$queuePID);
                                    // }

                                    // $updateBS_PID = $this->config->item('ibid_lot')."/api/updatelot/$lot_id?";
                                    // if (!is_null(@$proxyPID[0])) { 
                                    //     substr($updateBS_PID, -1) == '?' ? $updateBS_PID .= '' :  $updateBS_PID .= '&';
                                    //     $updateBS_PID .= "proxyPID=$proxyPID[0]";
                                    // }
                                    // if (!is_null(@$queuePID[0])) { 
                                    //     substr($updateBS_PID, -1) == '?' ? $updateBS_PID .= '' :  $updateBS_PID .= '&';
                                    //     $updateBS_PID .= "queuePID=$queuePID[0]";
                                    // }

                                    // if (!is_null($proxyPID) || !is_null($queuePID[0])) {
                                    //     $UpdateLotRes = json_decode($this->get_curl($updateBS_PID));
                                    // }
 
                                }
                            }
                        }                    
                    } else {
                        $postData = ["scheduleOn" => false];
                        $reference->update($postData);
                        $updateUrl = "http://ibid-ams-schedule.stagingapps.net/api/updateStatus/$id";
                        // $updateUrl = "localhost/ibid-ams-schedule/api/updateStatus/$id";
                        $this->get_curl($updateUrl);

                        $lot_url = "http://ibid-ams-lot.stagingapps.net/api/getLotFilter/$id";
                        // $lot_url = "localhost/ibid-lot/api/getLotFilter/$id";
                        $lotData = json_decode($this->get_curl($lot_url));
                        foreach ($lotData->data as $value) {
                            $lotStock = $value->no_lot;
                            $lotReference = $database->getReference("company/$company/schedule/$id/lot|stock/$lotStock");
                            $lotData = $lotReference->getValue();
                            $winnerReference = $database->getReference("company/$company/schedule/$id/lot|stock/$lotStock/log");
                            $lastBid = $winnerReference->orderByKey()->limitToLast(1)->getValue();
                            if (!is_null($lastBid)) {
                                $last = reset($lastBid);
                                $harga = $last['bid'];
                                $npl = $last['npl'];
                                $winnerData = array(
                                    "UnitName" => @$lotData['stockName'],
                                    "Npl" => $npl,
                                    "Lot" => @$lotData['lot'],
                                    "ScheduleId" => @$lotData['scheduleId'],
                                    "Schedule" => @$lotData['date'],
                                    "Type" => 1,
                                    "AuctionItemId" => @$lotData['stock_id'],
                                    "Price" => $harga,
                                    "Model" => @$lotData['model'],
                                    "Merk" => @$lotData['merk'],
                                    "Tipe" => @$lotData['tipe'],
                                    "Silinder" => @$lotData['silinder'],
                                    "Tahun" => @$lotData['tahun'],
                                    "NoPolisi" =>@$lotData['nopol'],
                                    "Va" => @$lotData['VA'],
                                );
                                // var_dump($winnerData);die();
                                $submitWinner = "http://ibid-ams-kpl.stagingapps.net/api/submitWinner";
                                // $submitWinner = "localhost/ibid-kpl/api/submitWinner";
                                $this->postCURL($submitWinner, $winnerData);
                            }
                        }
                        
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
        // var_dump($value['scheduleOn']);die();

        return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
    }

}

?>
