<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Current extends CI_Controller {
	
 	public function __construct()
    {
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


    /**
     * function for post with curl
     * @author akmal.m@smooets.com
     * @return object
    */  
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

	public function index()
	{
        echo "current works";
	}

	public function bidding()
	{
        $this->check_auth();
        $data['menu'] = load_menu()['menu'];
        $data['content'] = 'current-bidding/index';
        $data['content_script'] = 'current-bidding/script';
        $data['content_modal'] = 'modal';
        $this->load->view('/templates/current-bid', $data);
    }
    

    /**
     * function for check authentication
     * @author akmal.m@smooets.com
    */ 
    private function check_auth(){
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
    }
}

?>