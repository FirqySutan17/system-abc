<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReceiveLb_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_data_with_total($limit, $start, $role_id, $plants, $username, $search = '', $order = 'RECEIVE_DATE', $dir = 'DESC')
    {
        $role_id = (int)$role_id;

        $allowedOrder = ['RECEIVE_DATE','RECEIVE','DO','DRIVER','PLANT','CREATED_AT'];
        if (!in_array($order, $allowedOrder)) $order = 'RECEIVE_DATE';
        $dir = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        /* =====================================================
        BASE QUERY (DICACHE AGAR TOTAL & DATA SAMA FILTERNYA)
        ====================================================== */
        $this->db->start_cache();

        $this->db->from('mst_receive_lb r'); // ⬅ TANPA USE INDEX
        $this->db->join('cd_customer','r.SUPPLIER = cd_customer.CUST','left');
        $this->db->join("cd_code","cd_code.CODE = r.PLANT AND cd_code.HEAD_CODE = 'AJ'",'left');
        $this->db->where('r.DELETED IS NULL', null, false);

        if ($role_id !== 1) {
            if (empty($plants) || !is_array($plants)) {
                $this->db->flush_cache();
                return ['rows' => [], 'total' => 0];
            }
            $this->db->where_in('r.PLANT', $plants);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('r.RECEIVE', $search);
            $this->db->or_like('r.DO', $search);
            $this->db->or_like('r.DRIVER', $search);
            $this->db->or_like('cd_customer.FULL_NAME', $search);
            $this->db->or_like('r.REMARK', $search);
            $this->db->or_like('cd_code.CODE_NAME', $search);
            $this->db->group_end();
        }

        $this->db->stop_cache();

        /* =======================
        DATA QUERY
        ======================== */
        $this->db->select('
            r.RECEIVE,
            r.RECEIVE_DATE,
            r.SUPPLIER,
            r.DO,
            r.DRIVER,
            r.QTY,
            r.WEIGHT,
            r.AMOUNT,
            r.PLANT,
            r.RECEIVE_AMOUNT,
            r.CREATED_AT,
            cd_customer.FULL_NAME AS SUPPLIER_NAME,
            cd_code.CODE_NAME AS PLANT_NAME
        ');

        $this->db->order_by("r.$order", $dir);
        $this->db->limit((int)$limit, (int)$start);
        $rows = $this->db->get()->result();

        /* =======================
        TOTAL COUNT
        ======================== */
        $total = $this->db->count_all_results();

        $this->db->flush_cache();

        return ['rows' => $rows, 'total' => $total];
    }

    public function get_user_plants($username)
    {
        $cache_key = 'user_plants_'.$username;
        $plants = $this->cache->get($cache_key);

        if ($plants === FALSE) {
            $row = $this->db->select('plant')->where('username',$username)->get('users')->row();
            $plants = ($row && $row->plant) ? json_decode($row->plant, true) : [];
            $this->cache->save($cache_key, $plants, 600); // 10 menit
        }

        return is_array($plants) ? $plants : [];
    }

    public function get_plant_select2_by_user($username)
    {
        $plantCodes = $this->get_user_plants($username);

        if (empty($plantCodes)) {
            return [];
        }

        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->where('HEAD_CODE', 'AJ')
            ->where_in('CODE', $plantCodes)
            ->order_by('CODE_NAME', 'ASC')
            ->get('cd_code')
            ->result_array();
    }

    public function user_has_plant($username, $plant)
    {
        $plants = $this->get_user_plants($username);
        return in_array($plant, $plants);
    }

    public function upload_file($field_name = 'file', $plant = null, $receive_no = null, $receive_date = null)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return null;
        }

        if (!$plant || !$receive_no || !$receive_date) {
            return null; // wajib semua info untuk format nama file
        }

        // Buat folder uploads/{PLANT} jika belum ada
        $upload_path = './uploads/' . $plant . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Ambil ekstensi file
        $ext = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);

        // Format nama file: PLANT_RECEIVE_DATE.ext
        $date_str = date('Ymd', strtotime($receive_date));
        $filename = $plant . '_' . $receive_no . '_' . $date_str . '.' . $ext;

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|pdf|xlsx|docx';
        $config['max_size']      = 10240;
        $config['file_name']     = $filename;
        $config['overwrite']     = true; // jika file sama akan di-overwrite

        $this->load->library('upload', $config);

        if ($this->upload->do_upload($field_name)) {
            return [
                'filename' => $filename,
                'path'     => $upload_path . $filename
            ];
        }

        return null;
    }

    public function normalize_number($value)
    {
        if ($value === null || $value === '') return 0;

        $value = trim($value);

        // Format Indonesia: 1.234.567,89
        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        // Format ribuan pakai koma
        elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '', $value);
        }

        return (float)$value;
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */

    public function generate_receive_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today . 'RC'; // atau LB sesuai kebutuhan

        $this->db->select('RECEIVE');
        $this->db->from('mst_receive_lb');
        $this->db->where('PLANT', $plant);          // 🔑 KUNCI UTAMA
        $this->db->like('RECEIVE', $prefix, 'after');
        $this->db->order_by('RECEIVE', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        $seq = $row ? ((int) substr($row->RECEIVE, -4) + 1) : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* ---------------------------------------------------------
       CRUD OPERATIONS
    --------------------------------------------------------- */

    public function get_pdf_header($plant, $receive)
    {
        return $this->db
            ->select('
                r.RECEIVE,
                r.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                r.RECEIVE_DATE,
                r.PEMBAYARAN,
                r.JENIS_PAY,
                r.SLIP_NO,
                r.DO,
                r.SUPPLIER,
                s.FULL_NAME AS SUPPLIER_NAME,
                r.DRIVER,
                r.NO_CAR,
                r.ARRIVE_SCHEDULE,
                r.DEPART_SCHEDULE,
                r.QTY,
                r.WEIGHT,
                r.AVG_BW,
                r.PRICE,
                r.AMOUNT,
                r.DEAD,
                r.DEAD_WEIGHT,
                r.SHRINK,
                r.RECEIVE_AMOUNT,
                r.REMARK,
                r.ATTACHMENT_NAME,
                r.ATTACHMENT_PATH
            ')
            ->from('mst_receive_lb r')
            ->join('cd_code aj', "aj.CODE = r.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('cd_customer s', 's.CUST = r.SUPPLIER', 'left')
            ->where('r.RECEIVE', $receive)
            ->where('r.PLANT', $plant)
            ->limit(1)
            ->get()
            ->row();
    }

    public function insert($data)
    {
        return $this->db->insert('mst_receive_lb', $data);
    }

    public function get_for_edit($plant, $receive, $username, $role_id)
    {
        $this->db->select('
            mst_receive_lb.*,
            cd_customer.FULL_NAME AS SUPPLIER_NAME,
            cd_code.CODE_NAME AS AJ_NAME
        ');
        $this->db->from('mst_receive_lb');
        $this->db->join(
            'cd_customer',
            'mst_receive_lb.SUPPLIER = cd_customer.CUST',
            'left'
        );

        $this->db->join(
            'cd_code',
            "cd_code.CODE = mst_receive_lb.PLANT AND cd_code.HEAD_CODE = 'AJ'",
            'left'
        );

        $this->db->where('mst_receive_lb.PLANT', $plant);
        $this->db->where('mst_receive_lb.RECEIVE', $receive);

        // 🔐 non-admin hanya boleh data sesuai plant / ownership
        if ((int)$role_id !== 1) {
            $this->db->where('mst_receive_lb.CREATED_BY', $username);
        }

        return $this->db->get()->row_array();
    }

    public function update($oldPlant, $receive, $data)
    {
        $this->db
            ->where('PLANT', $oldPlant)
            ->where('RECEIVE', $receive)
            ->update('mst_receive_lb', $data);

        return $this->db->affected_rows();
    }

    // public function user_can_access($plant, $receive, $username, $role_id)
    // {
    //     if ($role_id === 1) return true; // admin bisa semua
    //     $this->db->from('mst_receive_lb');
    //     $this->db->where('PLANT', $this->session->userdata('plant'));
    //     $this->db->where('RECEIVE', $receive);
    //     return $this->db->count_all_results() > 0;
    // }


    public function hard_delete($plant, $receive)
    {
        $this->db->trans_start();

        // 🔥 Hapus header
        $this->db
            ->where('PLANT', $plant)
            ->where('RECEIVE', $receive)
            ->delete('mst_receive_lb');

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /* ---------------------------------------------------------
       SELECT2 HELPERS
    --------------------------------------------------------- */

    public function search_supplier($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('cd_customer');
        $this->db->where('CUST_KIND', 'SUPPLIER');
        $this->db->where('CUST_CLASS', 'SUPPLIER');
        $this->db->where('STATUS', 'Y');

        if ($q) {
            $this->db->group_start();
            $this->db->like('CUST', $q);
            $this->db->or_like('FULL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['name']
            ];
        }
        return $out;
    }

    /* ---------------------------------------------------------
       OTHERS
    --------------------------------------------------------- */

    public function get_all()
    {
        $this->db->where('DELETED IS NULL', null, false);
        return $this->db->get('mst_receive_lb')->result_array();
    }
}
