<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('base_account')) {
            show_404();
        }
        $this->load->model('Account_model', 'm');
        $this->load->library('session', 'form_validation');
        $this->load->helper(['url','download','file']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Account']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/account/list'); // konten dinamis

        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        try {
            // Ambil parameter GET
            $page   = (int)($this->input->get('page') ?? 1);
            $limit  = (int)($this->input->get('limit') ?? 10);
            $search = $this->input->get('search') ?? '';
            $order  = $this->input->get('order') ?? 'ACCOUNT';
            $dir    = strtoupper($this->input->get('dir') ?? 'ASC');

            // Validasi order & direction
            $allowed_order = ['ACCOUNT','ACCOUNT_NAME','ACCOUNT_TYPE'];
            if (!in_array($order, $allowed_order)) $order = 'ACCOUNT';
            if (!in_array($dir, ['ASC','DESC'])) $dir = 'ASC';

            $offset = ($page - 1) * $limit;

            // Ambil data
            $rows  = $this->m->get_data($limit, $offset, $search, $order, $dir);
            $total = $this->m->count_all($search);

            // Pagination (pastikan pagination_lib tersedia)
            $pages = ceil($total / $limit);
            $pagination = $this->build_pagination($pages, $page);

            echo json_encode([
                'rows'       => $rows,
                'total'      => $total,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            echo json_encode([
                'rows'       => [],
                'total'      => 0,
                'pagination' => '',
                'error'      => 'Terjadi kesalahan server'
            ]);
        }
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

    public function create() {
        $data = $this->_collect_data();
        $save = $this->m->insert($data);

        echo json_encode(["status" => $save ? "success" : "failed"]);
    }

    public function detail($account) {
        echo json_encode($this->m->get_by_pk($account));
    }

    public function edit() {
        $account = $this->input->get('account', TRUE);
        if (!$account) {
            echo json_encode(['status'=>false,'message'=>'Key invalid']);
            return;
        }

        $row = $this->m->get_by_pk($account);
        echo json_encode(['status'=>true,'data'=>$row]);
    }

    public function update() {
        $account = $this->input->post('orig_account', true); // pk lama
        if (!$account) {
            echo json_encode(['status'=>false,'message'=>'Missing account key']);
            return;
        }

        $data = $this->_collect_data(); // ACCOUNT diisi dari ACCOUNT_EDIT
        $save = $this->m->update($account, $data);

        echo json_encode([
            'status' => $save ? true : false,
            'message' => $save ? 'Updated' : 'Failed'
        ]);
    }

    public function delete($account) {
        $del = $this->m->delete($account);
        echo json_encode(["status" => $del ? "success" : "failed"]);
    }

    private function _collect_data() {
        $fields = [
            'ACCOUNT', 'ACCOUNT_NAME', 'REMARK',
            'CHECK_IN','CHECK_DC','CHECK_DEPT','CHECK_VENDOR','CHECK_BANK','CHECK_GOODS',
            'CHECK_REMAIN','CHECK_BUDGET','CHECK_CURRENCY','CHECK_BANK_ACCOUNT','CHECK_EMP',
            'CHECK_DATE1','CHECK_DATE2','CHECK_QTY','CHECK_QTY_UNIT','CHECK_RATE','CHECK_AMT1',
            'CHECK_AMT2','CHECK_OTHNO','CHECK_INVEST',
            'LEVEL_NO','BS_GROUP1','BS_GROUP2','ACCOUNT_TYPE',
            'MOC_GROUP1','MOC_GROUP2'
        ];

        $data = [];
        foreach($fields as $f) {
            $val = $this->input->post($f);
            if (strlen($f) >= 6 && substr($f,0,6) == "CHECK_") {
                $val = ($val == "Y") ? "Y" : "N";
            }
            $data[$f] = $val;
        }
        return $data;
    }
}
