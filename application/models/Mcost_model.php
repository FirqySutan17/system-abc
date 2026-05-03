<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcost_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->db->save_queries = false;
    }

    /* =========================================================
       LIST / COUNT  (PO PATTERN)
    ========================================================= */

    public function get_data($limit,$start,$role_id,$plants,$search='',$order='COST_DATE',$dir='DESC')
    {
        $allowedOrder = ['COST','COST_DATE','PEMBAYARAN','SLIP_NO','PLANT','CREATED_AT'];
        if (!in_array($order,$allowedOrder)) $order='COST_DATE';
        $dir = strtoupper($dir)==='ASC'?'ASC':'DESC';

        $this->db->select('mst_cost.*, cd_code.CODE_NAME AS PLANT_NAME')
                 ->from('mst_cost')
                 ->join('cd_code',"cd_code.CODE = mst_cost.PLANT AND cd_code.HEAD_CODE='AJ'",'left')
                 ->where('mst_cost.DELETED IS NULL',null,false);

        if ($role_id!=1){
            if (empty($plants)) return [];
            $this->db->where_in('mst_cost.PLANT',$plants);
        }

        if ($search!=''){
            $this->db->group_start()
                ->like('mst_cost.COST',$search)
                ->or_like('mst_cost.PEMBAYARAN',$search)
                ->or_like('mst_cost.SLIP_NO',$search)
                ->or_like('mst_cost.REMARK',$search)
                ->or_like('cd_code.CODE_NAME',$search)
                ->group_end();
        }

        return $this->db->order_by("mst_cost.$order",$dir)
                        ->limit((int)$limit,(int)$start)
                        ->get()->result_array();
    }

    public function count_data($role_id,$plants,$search='')
    {
        $this->db->from('mst_cost')
                 ->join('cd_code',"cd_code.CODE = mst_cost.PLANT AND cd_code.HEAD_CODE='AJ'",'left')
                 ->where('mst_cost.DELETED IS NULL',null,false);

        if ($role_id!=1){
            if (empty($plants)) return 0;
            $this->db->where_in('mst_cost.PLANT',$plants);
        }

        if ($search!=''){
            $this->db->group_start()
                ->like('mst_cost.COST',$search)
                ->or_like('mst_cost.PEMBAYARAN',$search)
                ->or_like('mst_cost.SLIP_NO',$search)
                ->or_like('mst_cost.REMARK',$search)
                ->or_like('cd_code.CODE_NAME',$search)
                ->group_end();
        }

        return $this->db->count_all_results();
    }

    /* =========================================================
       USER / PLANT ACCESS
    ========================================================= */

    public function get_user_plants($username)
    {
        $row=$this->db->select('plant')->from('users')->where('username',$username)->get()->row();
        if(!$row||empty($row->plant)) return [];
        return array_map('strval',json_decode($row->plant,true)??[]);
    }

    public function get_plant_select2_by_user($username)
    {
        $plants=$this->get_user_plants($username);
        if(empty($plants)) return [];
        return $this->db->select('CODE as id, CODE_NAME as text')
                        ->where('HEAD_CODE','AJ')
                        ->where_in('CODE',$plants)
                        ->order_by('CODE_NAME','ASC')
                        ->get('cd_code')->result_array();
    }

    public function user_has_plant($username,$plant)
    {
        return in_array((string)$plant,$this->get_user_plants($username),true);
    }

    /* =========================================================
       COST HEADER
    ========================================================= */

    public function insert_cost_header($data){ return $this->db->insert('mst_cost',$data); }

    public function get_cost_header($plant, $cost)
    {
        return $this->db
            ->from('mst_cost')
            ->where('PLANT', $plant)
            ->where('COST', $cost)
            ->where('DELETED IS NULL', null, false)
            ->get()
            ->row_array();
    }

    public function check_cost_exist($plant, $cost)
    {
        return $this->db
            ->from('mst_cost')
            ->where('PLANT', $plant)
            ->where('COST', $cost)
            ->where('DELETED IS NULL', null, false)
            ->count_all_results() > 0;
    }

    public function update_cost_header($plant,$cost,$data){
        return $this->db->where(['PLANT'=>$plant,'COST'=>$cost])->update('mst_cost',$data);
    }

    /* =========================================================
       COST DETAIL
    ========================================================= */

    public function insert_cost_detail_batch($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('mst_cost_detail', $rows);
    }

    public function get_cost_detail($plant,$cost){
        return $this->db->select('d.*,c.COST_NAME AS TIPE_COST_TEXT')
                        ->from('mst_cost_detail d')
                        ->join('cd_cost c','c.COST=d.TIPE_COST','left')
                        ->where(['d.PLANT'=>$plant,'d.COST'=>$cost])
                        ->where('d.DELETED IS NULL',null,false)
                        ->order_by('d.SEQ_NO','ASC')
                        ->get()->result_array();
    }

    public function delete_cost_detail($plant,$cost,$username){
        return $this->db->where(['PLANT'=>$plant,'COST'=>$cost])
                        ->where('DELETED IS NULL',null,false)
                        ->update('mst_cost_detail',[
                            'DELETED'=>date('Y-m-d H:i:s'),
                            'UPDATED_AT'=>date('Y-m-d H:i:s'),
                            'UPDATED_BY'=>$username
                        ]);
    }

    public function delete_cost_attachments_by_cost($plant,$cost)
    {
        $rows=$this->db->where(['PLANT'=>$plant,'COST'=>$cost])
                       ->where('DELETED IS NULL',null,false)
                       ->get('mst_cost_attachment')->result_array();

        foreach($rows as $r){
            $file=rtrim($r['FILE_PATH'],'/').'/'.$r['FILE_NAME'];
            if(is_file($file)) unlink($file);
        }

        return $this->db->where(['PLANT'=>$plant,'COST'=>$cost])
                        ->update('mst_cost_attachment',['DELETED'=>date('Y-m-d H:i:s')]);
    }

    public function delete_cost_folder($plant,$cost)
    {
        $path="./uploads/{$plant}/cost/{$cost}";
        if(!is_dir($path)) return;
        $files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,RecursiveDirectoryIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $fileinfo){ ($fileinfo->isDir()?'rmdir':'unlink')($fileinfo->getRealPath()); }
        rmdir($path);
    }

    public function delete_cost_header($plant,$cost,$username){
        return $this->db->where(['PLANT'=>$plant,'COST'=>$cost,'DELETED'=>null])
                        ->update('mst_cost',[
                            'DELETED'=>date('Y-m-d H:i:s'),
                            'UPDATED_AT'=>date('Y-m-d H:i:s'),
                            'UPDATED_BY'=>$username
                        ]);
    }

    public function upload_cost_file($field,$plant,$cost,$seq,$username)
    {
        $path = "./uploads/{$plant}/cost/{$cost}/{$seq}/";
        if(!is_dir($path)) mkdir($path,0755,true);

        $config=['upload_path'=>$path,'allowed_types'=>'jpg|jpeg|png|pdf','max_size'=>5120,'encrypt_name'=>true];

        $this->load->library('upload',$config);
        if(!$this->upload->do_upload($field)){
            log_message('error','UPLOAD ERROR: '.$this->upload->display_errors('',''));
            return null;
        }

        $file=$this->upload->data();
        return [
            'file'     => $file['file_name'],
            'original' => $file['orig_name'],
            'path'     => "uploads/{$plant}/cost/{$cost}/{$seq}/", // 🔥 TANPA "./"
            'type'     => $file['file_ext']
        ];
    }

    public function get_cost_attachments_by_seq($plant,$cost,$seq){
        return $this->db->where(['PLANT'=>$plant,'COST'=>$cost,'SEQ_NO'=>$seq])
                        ->where('DELETED IS NULL',null,false)
                        ->get('mst_cost_attachment')->result_array();
    }

    public function search_cost($q = null, $limit = 20)
    {
        // 🔒 pastikan query bersih
        $this->db->reset_query();

        $this->db->select('COST, COST_NAME, CLASS');
        $this->db->from('cd_cost');

        if (!empty($q)) {
            $this->db->group_start();
            $this->db->like('COST', $q);
            $this->db->or_like('COST_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('COST', 'ASC');
        $this->db->limit((int)$limit);

        $rows = $this->db->get()->result_array();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id'   => $row['COST'],
                'text' => $row['COST'] . ' - ' . $row['COST_NAME'] . ' (' . $row['CLASS']. ') ' 
            ];
        }

        return $result;
    }

    public function get_cost_attachments($plant, $cost)
    {
        return $this->db
            ->select('
                ID,
                COST,
                PLANT,
                SEQ_NO,
                FILE_NAME,
                FILE_ORIGINAL,
                FILE_PATH
            ')
            ->from('mst_cost_attachment')
            ->where('PLANT', $plant)
            ->where('COST', $cost)
            ->where('DELETED IS NULL', null, false)
            ->order_by('SEQ_NO', 'ASC')
            ->get()
            ->result_array();
    }

    public function delete_attachment_by_seq($plant, $cost, $seq)
    {
        $rows = $this->db
            ->where('PLANT', $plant)
            ->where('COST', $cost)
            ->where('SEQ_NO', $seq)
            ->where('DELETED IS NULL', null, false)
            ->get('mst_cost_attachment')
            ->result_array();

        foreach ($rows as $r) {
            $file = rtrim($r['FILE_PATH'], '/') . '/' . $r['FILE_NAME'];
            if (is_file($file)) {
                unlink($file);
            }
        }

        return $this->db
            ->where('PLANT', $plant)
            ->where('COST', $cost)
            ->where('SEQ_NO', $seq)
            ->update('mst_cost_attachment', [
                'DELETED'    => date('Y-m-d H:i:s')
            ]);
    }

    public function delete_unused_attachments($plant, $cost, $keepIds = [])
    {
        if (!empty($keepIds)) {
            $this->db->where_not_in('ID', $keepIds);
        }

        $this->db->where('PLANT', $plant)
                ->where('COST', $cost)
                ->update('mst_cost_attachment', [
                    'DELETED' => date('Y-m-d H:i:s')
                ]);
    }

    public function insert_cost_attachment($data){ return $this->db->insert('mst_cost_attachment',$data); }

    public function generate_cost_no($plant)
    {
        $prefix=date('Ymd').'CS';
        $row=$this->db->select('COST')->from('mst_cost')
                      ->where('PLANT',$plant)->like('COST',$prefix,'after')
                      ->order_by('COST','DESC')->limit(1)->get()->row();
        $seq=$row?((int)substr($row->COST,-4)+1):1;
        return $prefix.str_pad($seq,4,'0',STR_PAD_LEFT);
    }

    public function generate_slip_no($pembayaran,$plant)
    {
        $code=strtoupper($pembayaran)==='CASH'?'AC':'AB';
        $prefix=date('Ymd').$code;

        $row=$this->db->select('SLIP_NO')->from('mst_cost')
                      ->where('PLANT',$plant)->like('SLIP_NO',$prefix,'after')
                      ->order_by('SLIP_NO','DESC')->limit(1)->get()->row();
        $seq=$row?((int)substr($row->SLIP_NO,-4)+1):1;
        return $prefix.str_pad($seq,4,'0',STR_PAD_LEFT);
    }
}