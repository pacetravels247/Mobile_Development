<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Model{
    
    function __construct() {
        //$column_order = array(null, 'refernce_no','trip_type','from_loc','to_loc','adults','children','infants','name');
        //$column_search = array('refernce_no','trip_type','from_loc','to_loc','adults','children','infants','name');
        $this->request_details();
    }

    public function request_details($request_details=""){
        $this->table = $request_details['table'];
        $this->column_order = $request_details['column_order'];
        $this->column_search = $request_details['column_search'];
        $this->order = $request_details['order'];
        $this->condition = $request_details['condition'];
    }
    
    /*
     * Fetch members data from the database
     * @param $_POST filter data based on the posted parameters
     */
    public function getRows($postData){
        $this->_get_datatables_query($postData);
        if($postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        //debug($query);die();
        return $query->result_array();
    }
    
    /*
     * Count all records
     */
    public function countAll(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    /*
     * Count records based on the filter params
     * @param $_POST filter data based on the posted parameters
     */
    public function countFiltered($postData){
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    /*
     * Perform the SQL queries needed for an server-side processing requested
     * @param $_POST filter data based on the posted parameters
     */
    private function _get_datatables_query($postData){
         //debug($postData);die('789');
        $this->db->from($this->table);

        $condition='';
        if(!empty($this->condition)){
            foreach ($this->condition as $k => $v) {
                $condition .= $v;
            }
            $this->db->where($condition);
        }

        
 
        $i = 0;
        // loop searchable columns 
        /*foreach($this->column_search as $item){
            //debug($item);
            //debug($postData);die();
            if($postData['search']['value']){
                // first loop
                if($i===0){
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                if(count($this->column_search) - 1 == $i){
                    $this->db->group_end();
                }
            }
            $i++;
        }*/
         
        if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }else if(isset($postData['order'])){
            $this->db->order_by($this->column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        }
    }

}
?>