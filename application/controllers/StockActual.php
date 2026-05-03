<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockActual extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('productions_stock_actual')) {
            show_404();
        }
        $this->load->model('StockActual_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Stock Actual']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/stock_actual/list'); // view list
        $this->load->view('templates/footer');
    }

    /**
     * Load data for table (ajax)
     */
   public function load_data()
    {
        $allowedOrder = ['STOCK_ACTUAL','SA_DATE','SA_TIME','REMARK'];
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $orderInput = $this->input->get('order', TRUE);
        $order = in_array($orderInput, $allowedOrder) ? $orderInput : 'SA_DATE';
        $dir = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');
        $plant    = $this->session->userdata('plant'); // JSON

        $rows  = $this->StockActual_model
            ->get_data($limit, $start, $role_id, $plant, $username, $search, $order, $dir);

        $total = $this->StockActual_model
            ->count_data($role_id, $plant, $username, $search);

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
        $html = '<ul class="pagination pagination-sm">';
        for ($i=1; $i <= $pages; $i++) {
            $active = ($i == $current) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                     </li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function get_all_item()
    {
        $plant = $this->input->get('plant', TRUE);
        $username = $this->session->userdata('username');

        if (!$plant) {
            echo json_encode([]);
            return;
        }

        // 🔐 validasi plant milik user
        if (!$this->StockActual_model->user_has_plant($username, $plant)) {
            echo json_encode([]);
            return;
        }

        $items = $this->StockActual_model->get_stock_actual_by_plant($plant);
        echo json_encode($items);
    }

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');
        $data = $this->StockActual_model->get_plant_select2_by_user($username);
        echo json_encode($data);
    }

    /**
     * Create new Stock Actual
     */
    public function create()
    {
        $data = $this->input->post(NULL, FALSE);
        $username = $this->session->userdata('username');

        if (empty($data['PLANT'])) {
            echo json_encode(['status'=>false,'message'=>'Plant wajib dipilih']);
            return;
        }

        $plant = $data['PLANT'];

        // 🔐 VALIDASI PLANT MILIK USER
        if (!$this->StockActual_model->user_has_plant($username, $plant)) {
            echo json_encode(['status'=>false,'message'=>'Plant tidak diizinkan']);
            return;
        }

        if (empty($data['DETAIL']) || !is_array($data['DETAIL'])) {
            echo json_encode(['status'=>false,'message'=>'Detail item wajib diisi']);
            return;
        }

        $this->db->trans_start();

        $stockActualNo = $this->StockActual_model
            ->generate_stock_actual_no($plant, $data['SA_DATE']);

        $header = [
            'PLANT'        => $plant,
            'STOCK_ACTUAL' => $stockActualNo,
            'SA_DATE'      => $data['SA_DATE'],
            'SA_TIME'      => $data['SA_TIME'],
            'PIC'          => $data['PIC'],
            'REMARK'       => $data['REMARK'] ?? null,
            'CREATED_AT'   => date('Y-m-d H:i:s'),
            'CREATED_BY'   => $username
        ];

        $this->StockActual_model->insert_header($header);

        // =========================
        // INSERT DETAIL + SEQ_NO
        // =========================
        $lastSeq = 0; // karena ini CREATE BARU

        $rows = [];
        foreach ($data['DETAIL'] as $row) {
            $lastSeq++;

            $rows[] = [
                'PLANT'         => $plant,
                'STOCK_ACTUAL'  => $stockActualNo,
                'SEQ_NO'        => $lastSeq, // 🔥 INI KUNCI
                'ITEM'          => $row['ITEM'],
                'ITEM_NAME'     => $row['ITEM_NAME'],
                'ACTUAL_QTY'   => $this->normalize_number($row['ACTUAL_QTY']),
                'ACTUAL_BERAT' => $this->normalize_number($row['ACTUAL_BERAT']),
                'REMARK'        => $row['REMARK'] ?? null,
                'CREATED_AT'    => date('Y-m-d H:i:s'),
                'CREATED_BY'    => $this->session->userdata('username')
            ];
        }

        if (!empty($rows)) {
            $this->StockActual_model->insert_detail_batch($rows);
        }

        // =========================
        // TRANSACTION END
        // =========================
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal menyimpan Stock Actual'
            ]);
        } else {
            $this->db->trans_commit();
            echo json_encode([
                'status'        => true,
                'stock_actual'  => $stockActualNo,
                'message'       => 'Stock Actual berhasil disimpan'
            ]);
        }
    }

    /**
     * Edit: return header + detail by STOCK_ACTUAL
     */
    /**
 * Ambil header + detail Stock Actual berdasarkan STOCK_ACTUAL
 */

    public function get_stock_for_edit()
    {
        $stockActual = $this->input->get('stock_actual', TRUE);
        $plant       = $this->input->get('plant', TRUE);
        $username    = $this->session->userdata('username');
        $role_id     = (int)$this->session->userdata('role_id');

        if (!$stockActual || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        // 🔐 validasi plant milik user (non-admin)
        if ($role_id !== 1) {
            if (!$this->StockActual_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Plant tidak diizinkan'
                ]);
                return;
            }
        }

        // 🔑 HEADER PAKAI COMPOSITE KEY
        $header = $this->StockActual_model->get_header($plant, $stockActual);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
            return;
        }

        // ================= DETAIL =================
        $detailRaw = $this->StockActual_model->get_detail($plant, $stockActual);

        $detail = [];
        foreach ($detailRaw as $d) {

            $stock = $this->StockActual_model->get_stock_by_item($d['ITEM'], $plant);

            $detail[] = [
                'SEQ_NO'        => (int)$d['SEQ_NO'],
                'ITEM'          => $d['ITEM'],
                'FULL_NAME'     => $d['ITEM_NAME'],
                'ACTUAL_QTY'    => (float)$d['ACTUAL_QTY'],
                'ACTUAL_BERAT'  => (float)$d['ACTUAL_BERAT'],
                'REMARK'        => $d['REMARK'],
                'STOCK_QTY'     => (float)$stock['stock_qty'],
                'STOCK_BERAT'   => (float)$stock['stock_bw'],
            ];
        }

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function edit()
    {
        $stockActual = $this->input->get('stock_actual', TRUE);
        $plant       = $this->input->get('plant', TRUE);
        $username    = $this->session->userdata('username');
        $role_id     = (int)$this->session->userdata('role_id');

        if (!$stockActual || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        // 🔐 validasi plant milik user (non-admin)
        if ($role_id !== 1) {
            if (!$this->StockActual_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Plant tidak diizinkan'
                ]);
                return;
            }
        }

        // 🔑 AMBIL HEADER PAKAI COMPOSITE KEY
        $header = $this->StockActual_model->get_header($plant, $stockActual);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
            return;
        }

        // ================= DETAIL =================
        $detail = $this->StockActual_model->get_detail($plant, $stockActual);

        foreach ($detail as &$row) {
            $row['ACTUAL_QTY']   = number_format((float)$row['ACTUAL_QTY'], 2, '.', '');
            $row['ACTUAL_BERAT'] = number_format((float)$row['ACTUAL_BERAT'], 2, '.', '');
        }

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    /**
     * Update Stock Actual
     */
    public function update()
    {
        $data = $this->input->post(NULL, FALSE);

        $stockActual = $data['STOCK_ACTUAL'] ?? null;
        $plant       = $data['PLANT'] ?? null;
        $username    = $this->session->userdata('username');
        $role_id     = (int)$this->session->userdata('role_id');

        if (!$stockActual || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'PLANT dan STOCK_ACTUAL wajib diisi'
            ]);
            return;
        }

        // 🔐 validasi plant milik user (non-admin)
        if ($role_id !== 1) {
            if (!$this->StockActual_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Plant tidak diizinkan'
                ]);
                return;
            }
        }

        // 🔑 VALIDASI HEADER (ANTI SALAH UPDATE)
        $header = $this->StockActual_model->get_header($plant, $stockActual);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan atau tidak berhak'
            ]);
            return;
        }

        if (empty($data['DETAIL']) || !is_array($data['DETAIL'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'Detail item wajib diisi'
            ]);
            return;
        }

        $this->db->trans_begin();

        // ================= UPDATE HEADER =================
        $this->StockActual_model->update_header($plant, $stockActual, [
            'SA_DATE'    => $data['SA_DATE'],
            'PIC'        => $data['PIC'],
            'REMARK'     => $data['REMARK'] ?? null,
            'UPDATED_AT' => date('Y-m-d H:i:s'),
            'UPDATED_BY' => $username
        ]);

        // ================= DETAIL PROCESS =================
        $lastSeq = $this->StockActual_model->get_last_seq_no($plant, $stockActual);
        $existSeq = [];

        foreach ($data['DETAIL'] as $row) {

            // ===== UPDATE EXISTING =====
            if (!empty($row['SEQ_NO'])) {

                $existSeq[] = $row['SEQ_NO'];

                $this->db->where([
                    'PLANT'        => $plant,
                    'STOCK_ACTUAL' => $stockActual,
                    'SEQ_NO'       => $row['SEQ_NO']
                ])->update('mst_stock_actual_detail', [
                    'ITEM'         => $row['ITEM'],
                    'ITEM_NAME'    => $row['ITEM_NAME'],
                    'ACTUAL_QTY'   => $this->normalize_number($row['ACTUAL_QTY']),
                    'ACTUAL_BERAT' => $this->normalize_number($row['ACTUAL_BERAT']),
                    'REMARK'       => $row['REMARK'] ?? null,
                    'UPDATED_AT'   => date('Y-m-d H:i:s'),
                    'UPDATED_BY'   => $username
                ]);

            }
            // ===== INSERT NEW =====
            else {

                $lastSeq++;

                $this->db->insert('mst_stock_actual_detail', [
                    'PLANT'        => $plant,
                    'STOCK_ACTUAL' => $stockActual,
                    'SEQ_NO'       => $lastSeq,
                    'ITEM'         => $row['ITEM'],
                    'ITEM_NAME'    => $row['ITEM_NAME'],
                    'ACTUAL_QTY'   => $this->normalize_number($row['ACTUAL_QTY']),
                    'ACTUAL_BERAT' => $this->normalize_number($row['ACTUAL_BERAT']),
                    'REMARK'       => $row['REMARK'] ?? null,
                    'CREATED_AT'   => date('Y-m-d H:i:s'),
                    'CREATED_BY'   => $username
                ]);
            }
        }

        // ================= DELETE REMOVED ROW =================
        if (!empty($existSeq)) {
            $this->db->where('PLANT', $plant)
                ->where('STOCK_ACTUAL', $stockActual)
                ->where_not_in('SEQ_NO', $existSeq)
                ->delete('mst_stock_actual_detail');
        }

        // ================= TRANSACTION END =================
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal update Stock Actual'
            ]);
        } else {
            $this->db->trans_commit();
            echo json_encode([
                'status'  => true,
                'message' => 'Stock Actual berhasil diupdate'
            ]);
        }
    }

    /**
     * Remove Stock Actual by STOCK_ACTUAL
     */
    public function remove()
    {
        $stockActual = $this->input->post('stock_actual', TRUE);
        $plant       = $this->input->post('plant', TRUE);

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$stockActual || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        // 🔐 VALIDASI PLANT MILIK USER (NON ADMIN)
        if ($role_id !== 1) {
            if (!$this->StockActual_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Plant tidak diizinkan'
                ]);
                return;
            }
        }

        // 🔑 HEADER VALIDATION (COMPOSITE KEY)
        $header = $this->StockActual_model->get_header($plant, $stockActual);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data tidak ditemukan atau sudah dihapus'
            ]);
            return;
        }

        // ================= TRANSACTION =================
        $this->db->trans_begin();

        // 🔥 DELETE DETAIL DULU (WAJIB)
        $this->StockActual_model->delete_detail($plant, $stockActual);

        // 🔥 DELETE HEADER
        $this->StockActual_model->delete_header($plant, $stockActual);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus Stock Actual'
            ]);
            return;
        }

        $this->db->trans_commit();

        echo json_encode([
            'status'  => true,
            'message' => 'Stock Actual berhasil dihapus'
        ]);
    }

    public function print_pdf()
    {
        $stock_actual = $this->input->get('stock_actual', TRUE);
        $plant        = $this->input->get('plant', TRUE);

        $username = $this->session->userdata('username');
        $role_id  = (int)$this->session->userdata('role_id');

        if (!$stock_actual || !$plant) {
            show_error('Parameter STOCK_ACTUAL atau PLANT tidak lengkap');
        }

        // 🔐 VALIDASI PLANT MILIK USER (NON ADMIN)
        if ($role_id !== 1) {
            if (!$this->StockActual_model->user_has_plant($username, $plant)) {
                show_error('Anda tidak memiliki akses ke plant ini');
            }
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                sa.STOCK_ACTUAL,
                sa.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                sa.SA_DATE,
                sa.SA_TIME,
                sa.PIC,
                sa.REMARK
            ')
            ->from('mst_stock_actual sa')
            ->join('cd_code aj', "aj.CODE = sa.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->where('sa.STOCK_ACTUAL', $stock_actual)
            ->where('sa.PLANT', $plant)
            ->where('sa.DELETED IS NULL')
            ->get()
            ->row();

        if (!$header) {
            show_error('Stock Actual tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $rows = $this->db
            ->select('
                d.SEQ_NO,
                d.ITEM,
                d.ITEM_NAME AS FULL_NAME,
                d.ACTUAL_QTY,
                d.ACTUAL_BERAT,
                d.REMARK
            ')
            ->from('mst_stock_actual_detail d')
            ->where('d.STOCK_ACTUAL', $stock_actual)
            ->where('d.PLANT', $plant)
            ->where('d.DELETED IS NULL')
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        /* ================= HITUNG STOCK & MARGIN ================= */
        $detail = [];

        foreach ($rows as $r) {

            $stock = $this->StockActual_model->get_stock_by_item($r->ITEM, $plant);

            $stockQty = (float) $stock['stock_qty'];
            $stockBw  = (float) $stock['stock_bw'];

            // 🔥 business rule: actual 0 → samakan stock
            $actualQty = ((float)$r->ACTUAL_QTY === 0) ? $stockQty : (float)$r->ACTUAL_QTY;
            $actualBw  = ((float)$r->ACTUAL_BERAT === 0) ? $stockBw : (float)$r->ACTUAL_BERAT;

            $detail[] = (object)[
                'SEQ_NO'       => $r->SEQ_NO,
                'ITEM'         => $r->ITEM,
                'FULL_NAME'    => $r->FULL_NAME,

                'STOCK_QTY'    => $stockQty,
                'STOCK_BW'     => $stockBw,

                'ACTUAL_QTY'   => $actualQty,
                'ACTUAL_BERAT' => $actualBw,

                'MARGIN_QTY'   => $stockQty - $actualQty,
                'MARGIN_BW'    => $stockBw - $actualBw,

                'REMARK'       => $r->REMARK
            ];
        }

        $data = compact('header', 'detail');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/stock_actual/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();

        $this->pdf->stream(
            "STOCK_ACTUAL_{$stock_actual}.pdf",
            ['Attachment' => false]
        );
    }

    function format_decimal_id($number, $dec = 2)
    {
        return number_format((float)$number, $dec, ',', '.');
    }

    function format_rupiah($number)
    {
        return number_format((float)$number, 0, ',', '.');
    }

    private function normalize_number($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = trim($value);

        // format indonesia: 1.234,56
        // hapus ribuan, ganti koma jadi titik

        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        return (float)$value;
    }

}
