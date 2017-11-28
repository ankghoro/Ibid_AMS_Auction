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
        $AccessToken = isset($_COOKIE['AccessToken']) ? $_COOKIE['AccessToken'] : null;
        if (is_null($AccessToken) || ($this->check_token($AccessToken) == false)) {
            if(isset($AccessToken)){
                delete_cookie('AccessToken', base_domain(base_url()));
            }
            redirect($this->config->item('ibid_auth'), 'refresh');
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
        $lot_url = "http://ibid-lot.dev/api/getallLot";
        $lotdata = json_decode($this->get_curl($lot_url));
        // var_dump($lotdata); die();
        $stock_url = $this->config->item('ibid_stock')."/api/getallStock";
        $stockdata = json_decode($this->get_curl($stock_url));
        // var_dump($stockdata); die();
        $no = 0;
        $arr = array();
        foreach ($stockdata->data as $stock) {
                $datastatus = false;
                    foreach ($lotdata->data as $lot) {
                        if ($stock->AuctionItemId == (int)$lot->stock_id) {
                            $datastatus = true;
                            $lot_no = $lot->no_lot;
                            $no++;
                        }
                    }

                    if ($datastatus == true) {
                        if ($lot_no == $id) {
                            $arr['AuctionItemId'] = $stock->AuctionItemId; 
                            $arr['Merk'] = $stock->Merk;
                            $arr['Seri'] = $stock->Seri;
                            $arr['Silinder'] = $stock->Silinder;
                            $arr['Warna'] = $stock->Warna;
                            $arr['Transmisi'] = $stock->Transmisi;
                            $arr['Kilometer'] = $stock->Kilometer;
                            $arr['BahanBakar'] = $stock->BahanBakar;
                            $arr['Exterior'] = $stock->Exterior;
                            $arr['Interior'] = $stock->Interior;
                            $arr['Mechanical'] = $stock->Mechanical;
                            $arr['Frame'] = $stock->Frame;
                            $arr['ItemId'] = $stock->ItemId;
                            $arr['NoLot'] = (int)$lot_no;
                            $arr['StartPrice'] = $stock->StartPrice;
                        }
                    }
            }
            // var_dump($arr); die();
            
            count($arr) > 0 ? $status = true : $status = false;
            $id == $no ? $disable = true : $disable = false;
        $newData = [
            'status' => $status,
            'data' => $arr,
            'disable' => $disable
        ];
        echo json_encode($newData);
    }
}

?>