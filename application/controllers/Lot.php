<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lot extends CI_Controller {

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
        $AccessToken = isset($_COOKIE['AccessToken']) ? $_COOKIE['AccessToken'] : null;
        if (is_null($AccessToken) || ($this->check_token($AccessToken) == false)) {
            if(isset($AccessToken)){
                delete_cookie('AccessToken', base_domain(base_url()));
            }
            redirect($this->config->item('ibid_auth'), 'refresh');
        }
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
		$this->load->helper('custom');
		$this->load->helper('url');
        $data['menu'] = load_menu()['menu'];
        $data['assets_url'] = load_header()['assets_url'];
        $data['content'] = 'content';
        $data['content_script'] = 'script';
        $data['content_modal'] = 'modal';
        $this->load->view('/templates/theadmin', $data);
	}

    public function add(){
        $this->load->helper('custom');
        $this->load->helper('url');
        $data['title'] = "Tambah Lot";
        $data['menu'] = load_menu()['menu'];
        $data['assets_url'] = load_header()['assets_url'];
        $data['content'] = 'add';
        $data['content_script'] = 'add_script';
        $data['content_modal'] = 'modal';
        $this->load->view('/templates/theadmin', $data);
    }

    public function submit(){
        // var_dump($this->input->post('multiselect_data'));
        // die();
        $multidata = $this->input->post('multiselect_data');
        $schedule_id = $this->input->post('schedule_id');
        $date = $this->input->post('date');
        $date = explode("-", $date);
        $no = 0;
            foreach ($multidata as $item) {
                $dataitem = explode(".", $item);
                $count = $this->lot->count_data();
                $no++;
                $va = $date[0].$date[1].$date[2].mt_rand(100, 999);
                // var_dump($va); die();
                $data = [
                        'no_lot' => $no,
                        'no_va' => $va,
                        'status' => 0,
                        'schedule_id' => $schedule_id,
                        'stock_id' => $dataitem[0],
                        'stock_name' => $dataitem[1]
                ];
                // var_dump($data);
                // die();
            $this->lot->insertData($data);    
            }

        $this->session->set_flashdata('message',"Berhasil menambahkan $no data.");
        redirect('lot','refresh');
    }

    public function getStockData($data){
        $schedule_data = explode(".", $data);
        // var_dump($schedule_data); die();
        $id = $schedule_data[0];
        $item_id = $schedule_data[1];
        $date = $schedule_data[2];
        $no = 0;
        $arr = array();
        $lotdata = $this->lot->getalldata()->result();
        // var_dump($lotdata); die();
        $url = $this->config->item('ibid_stock')."/api/getallStock";
        $data = json_decode($this->get_curl($url));
        // var_dump($data); die();
            foreach ($data->data as $value) {
                $datastatus = true;
                if ($value->ItemId == $item_id) {
                    foreach ($lotdata as $lot) {
                        if ($value->AuctionItemId == (int)$lot->stock_id) {
                            $datastatus = false;
                        }
                    }

                    if ($datastatus == true) {
                        $arr[$no]['AuctionItemId'] = $value->AuctionItemId; 
                        $arr[$no]['Merk'] = $value->Merk;
                        $arr[$no]['Seri'] = $value->Seri;
                        $arr[$no]['Jenis'] = $value->ItemName;
                        $arr[$no]['ItemId'] = $value->ItemId;
                        $no++;
                    }
                }
            }
            if (count($arr) > 0) {
                $status = true;
            } else {
                $status = false;
            }
        $newData = [
            'status' => $status,
            'schedule_id' => $id,
            'date' => $date,
            'data' => $arr
        ];
        echo json_encode($newData);
    }

    public function getStockLotData($data){
        $schedule_data = explode(".", $data);
        // var_dump($schedule_data); die();
        $id = $schedule_data[0];
        $item_id = $schedule_data[1];
        $date = $schedule_data[2];
        $no = 0;
        $item = 0;
        $status = true;
        $selected = array();
        $lotdata = $this->lot->getalldata()->result();
        $count = count($lotdata);
        $url = $this->config->item('ibid_stock')."/api/getallStock";
        $data = json_decode($this->get_curl($url));
        // var_dump($data); die();
            foreach ($data->data as $value) {
                //for lot multiselect
                foreach ($lotdata as $lot) {
                    if ($lot->stock_id == $value->AuctionItemId) {
                        $lot_arr[$item]['AuctionItemId'] = $value->AuctionItemId;
                        $lot_arr[$item]['Merk'] = $value->Merk;
                        $lot_arr[$item]['Seri'] = $value->Seri;
                        $lot_arr[$item]['Jenis'] = $value->ItemName;
                        $lot_arr[$item]['ItemId'] = $value->ItemId;
                        $item++;
                        $selected[] = $value->AuctionItemId; 
                    }
                }
            }
            // var_dump($selected);die();
            foreach ($data->data as $stock) {
                $datastatus = true;
                if ($stock->ItemId == $item_id) {
                    for ($i = 0; $i < $count; $i++) {
                        if ($stock->AuctionItemId == $selected[$i]) {
                            $datastatus = false;
                        }
                    }

                    if ($datastatus == true) {
                        $arr[$no]['AuctionItemId'] = $stock->AuctionItemId; 
                        $arr[$no]['Merk'] = $stock->Merk;
                        $arr[$no]['Seri'] = $stock->Seri;
                        $arr[$no]['Jenis'] = $stock->ItemName;
                        $arr[$no]['ItemId'] = $stock->ItemId;
                        $no++;
                    }      
                }
            }
            // var_dump($arr);die();
        $newData = [
            'status' => $status,
            'schedule_id' => $id,
            'date' => $date,
            'data' => $arr,
            'lot' => $lot_arr
        ];
        echo json_encode($newData);
        // var_dump($newData); die();
    }

    public function edit(){
        $this->load->helper('custom');
        $this->load->helper('url');
        $data['title'] = "Edit Lot";
        $data['menu'] = load_menu()['menu'];
        $data['assets_url'] = load_header()['assets_url'];
        $data['content'] = 'add';
        $data['content_script'] = 'edit_script';
        $data['content_modal'] = 'modal';
        $this->load->view('/templates/theadmin', $data);
    }

	public function datatable()
	{
		$list = $this->lot->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $lot) {
        	$url = '/lot/'.$lot->id.'/update';
            $no++;
            $row = array();
            $row[] = $no;
            $row['no_lot'] = $lot->no_lot;
            $row['unit'] = $lot->stock_name;
            $row['action'] = '<a href="javascript:void(0);" onclick="detail_item('.$lot->id.')" class="btn btn-info btn-xs" title="View"><i class="fa fa-search fa-fw"></i></a>&nbsp;';
 
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->lot->count_all(),
                        "recordsFiltered" => $this->lot->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}

    public function update($id)
    {
        $multidata = $this->input->post('multiselect_data');
        $schedule_id = $id;
        $date = $this->input->post('date');
        $date = explode("-", $date);
        $no = 0;
        $where = array('schedule_id' => $id);
        $this->lot->delete($where);
            foreach ($multidata as $item) {
                $dataitem = explode(".", $item);
                $count = $this->lot->count_data();
                $no++;
                $va = $date[0].$date[1].$date[2].mt_rand(100, 999);
                // var_dump($va); die();
                $data = [
                        'no_lot' => $no,
                        'no_va' => $va,
                        'status' => 0,
                        'schedule_id' => $schedule_id,
                        'stock_id' => $dataitem[0],
                        'stock_name' => $dataitem[1]
                ];
                // var_dump($data);
                // die();
            $this->lot->insertData($data);    
            }

        $this->session->set_flashdata('message',"Berhasil update data Lot.");
        redirect('lot','refresh');

        // var_dump($id); die();
    }

    public function getdata($id)
    {
        $data = $this->lot->getLotById($id);
        $url = $this->config->item('ibid_schedule')."/api/scheduleList";
        $schedule_data = json_decode($this->get_curl($url));

        if (!is_null($data)) {
            foreach ($schedule_data->data as $value) {
                    if ($data->schedule_id == $value->id) {
                        $data->date = $value->date;
                        $data->CompanyName = $value->CompanyName;
                        $data->type = $value->type;
                        break;
                    }
            }
        
            $data->date = date_create($data->date);
            $data->date = date_format($data->date,"m/d/Y");
            $status = true;
            $description = 'Data berhasil diambil';
        } else {
            $status = false;
            $description = 'Data tidak tersedia';
        }

        // var_dump($data); die();

        $output = array(
                "status" => $status,
                "description" => $description,
                "data" => $data,
        );

        echo json_encode($output);
    }

    public function getLotSchedule(){
        $data = $this->lot->getalldata()->result();
        $url = $this->config->item('ibid_schedule')."/api/scheduleList";
        $schedule_data = json_decode($this->get_curl($url));
        $no = 0;
        $arr = array();
        foreach ($schedule_data->data as $value) {
            $datastatus = true;
            foreach ($data as $lot) {
                if ($value->id == $lot->schedule_id) {
                    $datastatus = false;
                }
            }
                if ($datastatus == true) {
                    $arr[$no]['id'] = $value->id; 
                    $arr[$no]['date'] = $value->date;
                    $arr[$no]['CompanyName'] = $value->CompanyName;
                    $arr[$no]['item_id'] = $value->item_id;
                    $no++;
                }
        }
        // var_dump($arr); die();
        if (count($arr) > 0) {
            $status = true;
        } else {
            $status = false;
        }
        $newData = [
            'status' => $status,
            'data' => $arr
            ];
        echo json_encode($newData);
    }

    public function getSchedule(){
        $data = $this->lot->getalldata()->result_array();
        $data = $this->super_unique($data,'schedule_id');
        // var_dump($data);die();
        $url = $this->config->item('ibid_schedule')."/api/scheduleList";
        $schedule_data = json_decode($this->get_curl($url));
        // var_dump($schedule_data); die();
        $no = 0;
        $arr = array();
        foreach ($data as $alldata) {
            foreach ($schedule_data->data as $value) {
                if ($alldata['schedule_id'] == $value->id) {
                    $arr[$no]['id'] = $value->id; 
                    $arr[$no]['date'] = $value->date;
                    $arr[$no]['CompanyName'] = $value->CompanyName;
                    $arr[$no]['item_id'] = $value->item_id;
                    $no++;
                }
            }
        }
        // var_dump($arr); die();
        if (count($arr) > 0) {
            $status = true;
        } else {
            $status = false;
        }
        $newData = [
            'status' => $status,
            'data' => $arr
            ];
        echo json_encode($newData);
    }

    public function lot()
    {
        $data = $this->lot->lotOnly();
        echo json_encode($data);
    }

    public function delete($id)
    {
        $result = new StdClass;
        if($this->lot->deletelot($id)) {
            $result->success = true;
        } else {
            $result->success = false;    
        }
        print_r(json_encode($result));die();
    }

    private function super_unique($array,$key){
            $temp_array = array();
            foreach ($array as &$v) {
                if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] =& $v;
                }
                $array = array_values($temp_array);
            return $array;
            }

    // private function _validate()
    // {
    //     $data = array();
    //     $data['error_string'] = array();
    //     $data['inputerror'] = array();
    //     $data['status'] = TRUE;
 
    //     if($this->input->post('date') == '')
    //     {
    //         $data['inputerror'][] = 'date';
    //         $data['error_string'][] = 'Tanggal wajib diisi';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($this->input->post('place_id') == '')
    //     {
    //         $data['inputerror'][] = 'place_id';
    //         $data['error_string'][] = 'Tolong pilih salah satu tempat';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($this->input->post('waktu') == '')
    //     {
    //         $data['inputerror'][] = 'waktu';
    //         $data['error_string'][] = 'Waktu dimulai wajib diisi';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($this->input->post('category_id') == '')
    //     {
    //         $data['inputerror'][] = 'category_id';
    //         $data['error_string'][] = 'Tolong pilih salah satu jenis lelang';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($this->input->post('type') == '')
    //     {
    //         $data['inputerror'][] = 'type';
    //         $data['error_string'][] = 'Tolong pilih salah satu tipe lelang';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($this->input->post('type'))
    //     {
    //         if( $this->input->post('duration') == '' )
    //         {
    //             $data['inputerror'][] = 'duration';
    //             $data['error_string'][] = 'Durasi wajib diisi';
    //             $data['status'] = FALSE;
    //         } elseif ($this->input->post('duration') < 1800) {
    //             $data['inputerror'][] = 'duration';
    //             $data['error_string'][] = 'Durasi tidak boleh kurang dari 30 menit';
    //             $data['status'] = FALSE;
    //         }
    //     }
 
    //     if ( (!$this->input->post('category_id') == '') && $this->input->post('category_id') == 0) {
    //         if($this->input->post('limit') == '')
    //         {
    //             $data['inputerror'][] = 'limit';
    //             $data['error_string'][] = 'Limit waktu untuk jenis lelang motor wajib diisi';
    //             $data['status'] = FALSE;
    //         }
    //     }
 
    //     if($this->input->post('interval') == '')
    //     {
    //         $data['inputerror'][] = 'interval';
    //         $data['error_string'][] = 'Kelipatan wajib diisi';
    //         $data['status'] = FALSE;
    //     }
 
    //     if($data['status'] === FALSE)
    //     {
    //         echo json_encode($data);
    //         exit();
    //     }
    // }
}
