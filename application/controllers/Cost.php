<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cost extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('base_cost')) {
            show_404();
        }
        $this->load->model('Cost_model');
        $this->load->library('session');
        $this->load->helper(['url','download','file']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Cost']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/cost/list'); // konten dinamis

        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'COST';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $rows = $this->Cost_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->Cost_model->count_data($search);

        // build pagination html (bootstrap)
        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        // return JSON
        echo json_encode([
            'rows' => $rows,
            'total' => $total,
            'pagination' => $pagination,
            'page' => $page
        ]);
    }

    private function build_pagination($pages, $current)
    {
        $html = '<ul class="pagination pagination-sm">';
        for ($i=1;$i<=$pages;$i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'"><a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * AJAX: get detail (for modal)
     */
    public function detail()
    {
        $cost = $this->input->get('cost', TRUE);
        if (!$cost) {
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }
        $row = $this->Cost_model->get_by_pk($cost);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    /**
     * AJAX: create new record
     */
    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        // Validasi wajib diisi
        if (empty($data['COST']) || empty($data['COST_NAME'])) {
            echo json_encode(['status'=>false,'message'=>'COST dan COST NAME wajib diisi']);
            return;
        }

        // Validasi UNIQUE: HEAD_CODE + CODE
        $exists = $this->db->where('cost', $data['COST'])
                        ->get('abc_cd_cost')
                        ->row();

        if($exists){
            echo json_encode([
                'status' => false,
                'message' => 'Kombinasi COST sudah ada'
            ]);
            return;
        }

        // Insert ke DB
        $ok = $this->db->insert('abc_cd_cost', [
            'cost'          => $data['COST'],
            'cost_name'     => $data['COST_NAME'],
            'account_code'  => $data['ACCOUNT_CODE'],
            'class'         => $data['CLASS'],
            'remark'        => $data['REMARK'] ?? ' '
        ]);

        echo json_encode([
            'status' => $ok,
            'message' => $ok ? 'Data berhasil ditambahkan' : 'Gagal menambahkan data'
        ]);
    }

    public function get_account(){
         $term = $this->input->get('q');

        $this->db->select('ACCOUNT, ACCOUNT_NAME')
                ->from('cd_account');

        if (!empty($term)) {
            $this->db->like('ACCOUNT', $term);
            $this->db->or_like('ACCOUNT_NAME', $term);
        }

        $this->db->order_by('ACCOUNT', 'ASC');
        $query = $this->db->get()->result();

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'id'   => $row->ACCOUNT,
                'text' => $row->ACCOUNT . ' - ' . $row->ACCOUNT_NAME
            ];
        }

        echo json_encode($data);
    }

    /**
     * AJAX: edit (fetch satu)
     */
    public function edit()
    {
        $cost = $this->input->get('cost', TRUE);

        $this->db->select('
            abc_cd_cost.COST,
            abc_cd_cost.COST_NAME,
            abc_cd_cost.ACCOUNT_CODE,
            abc_cd_cost.CLASS,
            abc_cd_cost.REMARK,
            cd_account.ACCOUNT_NAME AS ACCOUNT_NAME
        ');
        $this->db->from('abc_cd_cost');
        $this->db->join('cd_account', 'cd_account.ACCOUNT = abc_cd_cost.ACCOUNT_CODE', 'left');
        $this->db->where('abc_cd_cost.COST', $cost);

        $row = $this->db->get()->row_array();

        echo json_encode([
            'status' => true,
            'data'   => $row
        ]);
    }

    /**
     * AJAX: update
     */
    public function update()
    {
        // Primary key asli, untuk where
        $orig_cost = $this->input->post('orig_cost', TRUE);

        if (!$orig_cost) {
            echo json_encode(['status' => false, 'message' => 'Primary key missing']);
            return;
        }

        // Ambil seluruh input POST
        $data = $this->input->post(NULL, TRUE);

        // Jangan ikut simpan PK lama
        unset($data['orig_cost']);

        // Lakukan update
        $ok = $this->db->where('COST', $orig_cost)->update('abc_cd_cost', $data);

        echo json_encode([
            'status'  => $ok,
            'message' => $ok ? 'Data berhasil diupdate' : 'Gagal update data'
        ]);
    }

    public function remove()
    {
        $cost = $this->input->post('cost', TRUE);
        if(!$cost){
            echo json_encode(['status'=>false,'message'=>'Invalid key']);
            return;
        }

        $ok = $this->Cost_model->delete($cost);

        echo json_encode([
            'status' => $ok,
            'message' => $ok ? 'Data dihapus' : 'Gagal menghapus'
        ]);
    }

}
