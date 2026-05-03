<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reprocess extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!has_permission('productions_process')) {
            show_404();
        }

        $this->load->model('Reprocess_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Reprocess']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/reprocess/list');
        $this->load->view('templates/footer');
    }

    /* =========================
        LOAD DATA
    ========================= */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);

        $start = ($page - 1) * $limit;

        $rows  = $this->Reprocess_model->get_data($limit, $start, $search);
        $total = $this->Reprocess_model->count_data($search);

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
                        <a class="page-link" onclick="loadPage('.($current-1).')">«</a>
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
        $data = $this->Reprocess_model->search_item($term);
        echo json_encode($data);
    }

    /* =========================
        SELECT2 PROCESS CLASS
    ========================= */
    public function get_process_class()
    {
        $term = $this->input->get('q');
        $data = $this->Reprocess_model->get_process_class($term);
        echo json_encode($data);
    }

    /* =========================
        SELECT2 PLANT
    ========================= */
    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');
        $data = $this->Reprocess_model->get_plant_select2_by_user($username);
        echo json_encode($data);
    }

    /* =========================
        CREATE
    ========================= */
    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        $username = $this->session->userdata('username');

        if (empty($data['PLANT']) || empty($data['PROCESS_DATE']) || empty($data['PROCESS_CLASS'])) {
            echo json_encode(['status'=>false,'message'=>'Plant, Tanggal dan Process Class wajib diisi']);
            return;
        }

        $detailRows = json_decode($data['DETAIL'] ?? '[]', true);

        if (empty($detailRows)) {
            echo json_encode(['status'=>false,'message'=>'Detail tidak boleh kosong']);
            return;
        }

        $this->db->trans_start();

        $processNo = $this->Reprocess_model->generate_process_no($data['PLANT']);

        $rows = [];
        $seq  = 1;

        foreach ($detailRows as $row) {

            if (empty($row['ITEM']) || empty($row['TO_ITEM'])) continue;

            if ($row['ITEM'] === $row['TO_ITEM']) continue;

            $qty   = $this->parseDecimal($row['QTY'] ?? 0);
            $berat = $this->parseDecimal($row['BERAT'] ?? 0);

            if ($qty <= 0 && $berat <= 0) continue;

            $rows[] = [
                'process_no' => $processNo,
                'seq_no'     => $seq++,
                'item'       => $row['ITEM'],
                'to_item'    => $row['TO_ITEM'],
                'qty'        => $qty,
                'berat'      => $berat,
                'remark'     => $row['REMARK'] ?? null,
                'created_date'=> date('Y-m-d H:i:s'),
                'created_by' => $username
            ];
        }

        if (empty($rows)) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Detail tidak valid']);
            return;
        }

        /* HEADER */
        $this->Reprocess_model->insert_header([
            'process_no'   => $processNo,
            'plant'        => $data['PLANT'],
            'process_date' => $data['PROCESS_DATE'],
            'process_class'=> $data['PROCESS_CLASS'],
            'remark'       => $data['REMARK'] ?? null,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by'   => $username
        ]);

        /* DETAIL */
        $this->Reprocess_model->insert_detail_batch($rows);

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Process berhasil dibuat'
                : 'Gagal membuat process'
        ]);
    }

    /* =========================
        EDIT
    ========================= */
    public function edit()
    {
        $process = $this->input->get('process');

        $header = $this->Reprocess_model->get_header($process);
        $detail = $this->Reprocess_model->get_detail($process);

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
        $process = $data['PROCESS_NO'];

        if (!$process) {
            echo json_encode(['status'=>false,'message'=>'Process No tidak ada']);
            return;
        }

        $detailRows = json_decode($data['DETAIL'] ?? '[]', true);

        if (empty($detailRows)) {
            echo json_encode(['status'=>false,'message'=>'Detail kosong']);
            return;
        }

        $this->db->trans_start();

        // UPDATE HEADER
        $this->db->where('process_no', $process)->update('mst_process_master', [
            'process_date' => $data['PROCESS_DATE'],
            'process_class'=> $data['PROCESS_CLASS'],
            'remark'       => $data['REMARK'],
            'updated_at'   => date('Y-m-d H:i:s'),
            'updated_by'   => $this->session->userdata('username')
        ]);

        // DELETE DETAIL
        $this->db->delete('mst_process_detail', ['process_no' => $process]);

        // INSERT ULANG
        $rows = [];
        $seq = 1;

        foreach ($detailRows as $r) {

            if (empty($r['ITEM']) || empty($r['TO_ITEM'])) continue;
            if ($r['ITEM'] === $r['TO_ITEM']) continue;

            $rows[] = [
                'process_no' => $process,
                'seq_no'     => $seq++,
                'item'       => $r['ITEM'],
                'to_item'    => $r['TO_ITEM'],
                'qty'        => $r['QTY'],
                'berat'      => $r['BERAT'],
                'remark'     => $r['REMARK']
            ];
        }

        if (empty($rows)) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Detail tidak valid']);
            return;
        }

        $this->db->insert_batch('mst_process_detail', $rows);

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
        $process = $this->input->post('process', TRUE);

        if (!$process) {
            echo json_encode([
                'status'=>false,
                'message'=>'Parameter tidak lengkap'
            ]);
            return;
        }

        $exist = $this->db
            ->where('process_no', $process)
            ->get('mst_process_master')
            ->row();

        if (!$exist) {
            echo json_encode([
                'status'=>false,
                'message'=>'Data tidak ditemukan'
            ]);
            return;
        }

        $this->db->trans_start();

        $this->Reprocess_model->delete_detail($process);
        $this->Reprocess_model->delete_header($process);

        $this->db->trans_complete();

        echo json_encode([
            'status'=>$this->db->trans_status(),
            'message'=>$this->db->trans_status()
                ? 'Process berhasil dihapus'
                : 'Gagal menghapus process'
        ]);
    }

    /* =========================
        PARSE DECIMAL
    ========================= */
    private function parseDecimal($value)
    {
        if ($value === null || $value === '') return 0;

        if (is_numeric($value)) return (float) $value;

        return (float) str_replace(',', '.', str_replace('.', '', $value));
    }
}