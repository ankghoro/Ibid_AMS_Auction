<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedules extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('schedule','schedule');
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

	public function datatable()
	{
		$list = $this->schedule->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $schedule) {
        	$url = '/schedule/'.$schedule->id.'/update';
            $no++;
            $row = array();
            $row[] = $no;
            $date = date_create($schedule->date);
            $date = date_format($date,"d F Y");
            $row['date'] = $date;
            $row['place_id'] = $schedule->place_id ? 'Jakarta' : 'Bandung';
            $row['interval'] = $schedule->interval;
            $row['type'] = $schedule->type ? 'Online' : 'Live';
            $row['waktu'] = date("H:i", strtotime($schedule->waktu));
            $row['category_id'] = $schedule->category_id ? 'Mobil' : 'Motor';
            $row['action'] = '<a href="javascript:void(0);" onclick="detail_item('.$schedule->id.')" class="btn btn-info btn-xs" title="View">
                                <i class="fa fa-eye fa-fw"></i>
                            </a>&nbsp;
                            <a href="#" data-id="'.$schedule->id.'" class="btn btn-edit btn-success btn-xs" title="Edit">
                                <i class="fa fa-pencil-square-o fa-fw"></i>
                            </a>&nbsp;
                            <a href="#" class="btn btn-danger btn-xs actDelete" title="Delete" data-id="'.$schedule->id.'" data-name="'.$schedule->date.'" data-button="delete">
                                <i class="fa fa-trash-o fa-fw"></i>
                            </a>';
 
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->schedule->count_all(),
                        "recordsFiltered" => $this->schedule->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}

    public function submit()
    {
        $this->_validate();
        $date = $this->input->post('date');
        $date = date_create($date);
        $date = date_format($date,"Y-m-d");
        $arr['date'] = $date;
        $arr['place_id'] = $this->input->post('place_id');
        $arr['interval'] = $this->input->post('interval');
        $arr['waktu'] = $this->input->post('waktu');
        $arr['type'] = $this->input->post('type');
        $arr['category_id'] = $this->input->post('category_id');
        $arr['duration'] = $this->input->post('duration');
        if ($arr['category_id'] == '0') {
            $arr['limit'] = $this->input->post('limit');
        }
        $result = new StdClass;
        if($this->schedule->insertSchedule($arr)) {
            $result->success = true;
        } else {
            $result->success = false;    
        }
        print_r(json_encode($result));die();
    }

    public function update($id)
    {
        $this->_validate();
        $date = $this->input->post('date');
        $date = date_create($date);
        $date = date_format($date,"Y-m-d");
        $arr['date'] = $date;
        $arr['id'] = $this->input->post('id');
        $arr['place_id'] = $this->input->post('place_id');
        $arr['interval'] = $this->input->post('interval');
        $arr['type'] = $this->input->post('type');
        $arr['category_id'] = $this->input->post('category_id');
        $arr['limit'] = $this->input->post('limit');
        $arr['waktu'] = $this->input->post('waktu');
        $arr['duration'] = $this->input->post('duration');
        $result = new StdClass;
        if($this->schedule->updateSchedule($arr)) {
            $result->success = true;
        } else {
            $result->success = false;    
        }
        print_r(json_encode($result));die();
    }

    public function getdata($id)
    {
        $data = $this->schedule->getScheduleById($id);
        $data->date = date_create($data->date);
        $data->date = date_format($data->date,"d/m/Y");
        $data->waktu = date("H:i", strtotime($data->waktu));
        $data->hour = gmdate("H", $data->duration);
        $data->minute = gmdate("i", $data->duration);
        $data->interval = $data->interval + 0;
        print_r(json_encode($data));die();
    }

    public function schedule()
    {
        $data = $this->schedule->scheduleOnly();
        echo json_encode($data);
    }

    public function delete($id)
    {
        $result = new StdClass;
        if($this->schedule->deleteSchedule($id)) {
            $result->success = true;
        } else {
            $result->success = false;    
        }
        print_r(json_encode($result));die();
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
 
        if($this->input->post('date') == '')
        {
            $data['inputerror'][] = 'date';
            $data['error_string'][] = 'Tanggal wajib diisi';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('place_id') == '')
        {
            $data['inputerror'][] = 'place_id';
            $data['error_string'][] = 'Tolong pilih salah satu tempat';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('waktu') == '')
        {
            $data['inputerror'][] = 'waktu';
            $data['error_string'][] = 'Waktu dimulai wajib diisi';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('category_id') == '')
        {
            $data['inputerror'][] = 'category_id';
            $data['error_string'][] = 'Tolong pilih salah satu jenis lelang';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('type') == '')
        {
            $data['inputerror'][] = 'type';
            $data['error_string'][] = 'Tolong pilih salah satu tipe lelang';
            $data['status'] = FALSE;
        }
 
        if($this->input->post('type'))
        {
            if( $this->input->post('duration') == '' )
            {
                $data['inputerror'][] = 'duration';
                $data['error_string'][] = 'Durasi wajib diisi';
                $data['status'] = FALSE;
            } elseif ($this->input->post('duration') < 1800) {
                $data['inputerror'][] = 'duration';
                $data['error_string'][] = 'Durasi tidak boleh kurang dari 30 menit';
                $data['status'] = FALSE;
            }
        }
 
        if ( (!$this->input->post('category_id') == '') && $this->input->post('category_id') == 0) {
            if($this->input->post('limit') == '')
            {
                $data['inputerror'][] = 'limit';
                $data['error_string'][] = 'Limit waktu untuk jenis lelang motor wajib diisi';
                $data['status'] = FALSE;
            }
        }
 
        if($this->input->post('interval') == '')
        {
            $data['inputerror'][] = 'interval';
            $data['error_string'][] = 'Kelipatan wajib diisi';
            $data['status'] = FALSE;
        }
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }
}
