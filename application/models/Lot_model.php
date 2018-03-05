<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lot_model extends CI_Model {

	var $table = 'lots';
    var $column_order = array('id', 'stock_id', 'no_lot'); //set column field database for datatable orderable
    var $column_search = array('id', 'stock_id'); //set column field database for datatable searchable 
    var $order = array('id' => 'desc');

	function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
         
        $this->db->from($this->table);
 
        $i = 0;
     
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if( isset($_POST['columns'][2]['search']['value']) ){
            if (strlen($_POST['columns'][2]['search']['value']) > 0) {
                $this->db->where('schedule_id', $_POST['columns'][2]['search']['value']); 
            }
        }

        if( isset($_POST['columns'][1]['search']['value']) ){
            if (strlen($_POST['columns'][1]['search']['value']) > 0) {
                $this->db->where('type', $_POST['columns'][1]['search']['value']); 
            }
        }

        if( isset($_POST['columns'][0]['search']['value']) ){
            if (strlen($_POST['columns'][0]['search']['value']) > 0) {
                $this->db->where('place_id', $_POST['columns'][0]['search']['value']); 
            }
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function count_data(){
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getalldata(){
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query;
    }

    public function getitem($id){
        $this->db->from($this->table);
        $this->db->where('schedule_id',$id);
        $query = $this->db->get();
        return $query->result();
    }

    public function getLotById($id){
        $this->db->from($this->table);
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function update($where,$data){
        $this->db->update($this->table,$data,$where);
    }

    public function insertData($post){
        $query = $this->db->insert($this->table, $post);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function insertSchedule($post) {
        $this->db->insert("schedules", $post);
        $insertId = $this->db->insert_id();
        return $insertId;
    }

    function updateSchedule($post) {
        $id = $post['id'];
        unset($post['id']);
        $this->db->update("schedules", $post,"id = ".$id);
        return true;
    }

    function getScheduleById($postId)
    {
        return $this->db->get_where($this->table, array('id'=>$postId))->row();
    }

    public function scheduleOnly()
    {
        $this->db->select('date');
        $this->db->group_by('date');
        $this->db->from($this->table);
        $query = $this->db->get();
        return $query->result();
    }

    public function delete($where){
        $this->db->where($where);
        $this->db->delete($this->table);
    }

}