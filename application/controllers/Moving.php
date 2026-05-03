<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Moving extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('productions_moving')) {
            show_404();
        }

        $this->load->model('Moving_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Moving']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/moving/list');
        $this->load->view('templates/footer');
    }

    /* =========================
        LOAD DATA (LIST)
    ========================= */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);

        $start = ($page - 1) * $limit;

        $rows  = $this->Moving_model->get_data($limit, $start, $search);
        $total = $this->Moving_model->count_data($search);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page);

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    private function build_pagination($pages, $current)
    {
        if ($pages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';
        $range = 2;
        $start = max(1, $current - $range);
        $end   = min($pages, $current + $range);

        if ($current > 1) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="loadPage('.($current-1).')">«</a>
                    </li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                    </li>';
        }

        if ($current < $pages) {
            $html .= '<li class="page-item">
                        <a class="page-link" onclick="loadPage('.($current+1).')">»</a>
                    </li>';
        }

        $html .= '</ul>';
        return $html;
    }

    /* =========================
        SELECT2 ITEM
    ========================= */
    public function get_item()
    {
        $term = $this->input->get('q');
        $data = $this->Moving_model->search_item($term);
        echo json_encode($data);
    }

    /* =========================
        SELECT2 PLANT
    ========================= */
    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');
        $data = $this->Moving_model->get_plant_select2_by_user($username);
        echo json_encode($data);
    }

    /* =========================
        CREATE (SAVE)
    ========================= */
    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        $username = $this->session->userdata('username');

        if (empty($data['PLANT']) || empty($data['TO_PLANT']) || empty($data['MOVING_DATE'])) {
            echo json_encode(['status'=>false,'message'=>'Plant, Tujuan dan Tanggal wajib diisi']);
            return;
        }

        if ($data['PLANT'] === $data['TO_PLANT']) {
            echo json_encode(['status'=>false,'message'=>'Plant asal dan tujuan tidak boleh sama']);
            return;
        }

        $detailRows = json_decode($data['DETAIL'] ?? '[]', true);

        if (empty($detailRows)) {
            echo json_encode(['status'=>false,'message'=>'Detail tidak boleh kosong']);
            return;
        }

        $this->db->trans_start();

        $movingNo = $this->Moving_model->generate_moving_no($data['PLANT']);

        $rows = [];
        $seq  = 1;

        foreach ($detailRows as $row) {

            if (empty($row['ITEM'])) continue;

            $qty   = $this->parseDecimal($row['QTY'] ?? 0);
            $berat = $this->parseDecimal($row['BERAT'] ?? 0);

            if ($qty <= 0 && $berat <= 0) continue;

            $rows[] = [
                'moving_no' => $movingNo,
                'seq_no'    => $seq++,
                'item'      => $row['ITEM'],
                'qty'       => $qty,
                'berat'     => $berat,
                'remark'    => $row['REMARK'] ?? null
            ];
        }

        if (empty($rows)) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Detail tidak valid']);
            return;
        }

        /* HEADER */
        $this->Moving_model->insert_header([
            'moving_no'   => $movingNo,
            'plant'       => $data['PLANT'],
            'moving_date' => $data['MOVING_DATE'],
            'to_plant'    => $data['TO_PLANT'],
            'remark'      => $data['REMARK'] ?? null,
            'created_date'=> date('Y-m-d'),
            'created_by'  => $username
        ]);

        /* DETAIL */
        $this->Moving_model->insert_detail_batch($rows);

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Moving berhasil dibuat'
                : 'Gagal membuat moving'
        ]);
    }

    /* =========================
        EDIT
    ========================= */
    public function edit()
    {
        $moving = $this->input->get('moving');

        $header = $this->Moving_model->get_header($moving);
        $detail = $this->Moving_model->get_detail($moving);

        if (!$header) {
            echo json_encode([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    /* =========================
        UPDATE
    ========================= */
    public function update()
    {
        $data = $this->input->post(NULL, TRUE);
        $moving = $data['MOVING_NO'];

        if (!$moving) {
            echo json_encode(['status'=>false,'message'=>'Moving No tidak ada']);
            return;
        }

        $detailRows = json_decode($data['DETAIL'] ?? '[]', true);

        if (empty($detailRows)) {
            echo json_encode(['status'=>false,'message'=>'Detail kosong']);
            return;
        }

        $this->db->trans_start();

        // UPDATE HEADER
        $this->db->where('moving_no', $moving)->update('mst_moving_master', [
            'moving_date' => $data['MOVING_DATE'],
            'to_plant'    => $data['TO_PLANT'],
            'remark'      => $data['REMARK'],
            'update_at'   => date('Y-m-d'),
            'update_by'   => $this->session->userdata('username')
        ]);

        // DELETE DETAIL
        $this->db->delete('mst_moving_detail', ['moving_no' => $moving]);

        // INSERT ULANG
        $rows = [];
        $seq = 1;

        foreach ($detailRows as $r) {
            $rows[] = [
                'moving_no' => $moving,
                'seq_no'    => $seq++,
                'item'      => $r['ITEM'],
                'qty'       => $r['QTY'],
                'berat'     => $r['BERAT'],
                'remark'    => $r['REMARK']
            ];
        }

        $this->db->insert_batch('mst_moving_detail', $rows);

        $this->db->trans_complete();

        echo json_encode([
            'status'=>$this->db->trans_status(),
            'message'=>$this->db->trans_status() ? 'Update berhasil' : 'Gagal update'
        ]);
    }

    /* =========================
        DELETE
    ========================= */
    public function remove()
    {
        $moving = $this->input->post('moving', TRUE);

        if (!$moving) {
            echo json_encode([
                'status'=>false,
                'message'=>'Parameter tidak lengkap'
            ]);
            return;
        }

        // 🔒 CEK DATA ADA
        $exist = $this->db
            ->where('moving_no', $moving)
            ->get('mst_moving_master')
            ->row();

        if (!$exist) {
            echo json_encode([
                'status'=>false,
                'message'=>'Data tidak ditemukan'
            ]);
            return;
        }

        $this->db->trans_begin();

        // 🔥 HAPUS DETAIL DULU (WAJIB)
        $this->Moving_model->delete_detail($moving);

        // 🔥 HAPUS HEADER
        $this->Moving_model->delete_header($moving);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            echo json_encode([
                'status'=>false,
                'message'=>'Gagal menghapus moving'
            ]);
        } else {
            $this->db->trans_commit();

            echo json_encode([
                'status'=>true,
                'message'=>'Moving berhasil dihapus'
            ]);
        }
    }

    private function parseDecimal($value)
    {
        if ($value === null || $value === '') return 0;

        if (is_numeric($value)) return (float) $value;

        return (float) str_replace(',', '.', str_replace('.', '', $value));
    }
}