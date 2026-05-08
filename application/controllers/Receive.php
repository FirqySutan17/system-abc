<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('inventory_receive')) {
            show_404();
        }
        $this->load->model('Receive_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Receive']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/receive/list');   // your list view (the one you provided)
        $this->load->view('templates/footer');
    }

    /**
     * Load data for table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page', true);
        $limit  = (int)$this->input->get('limit', true);
        $search = $this->input->get('search', true);
        $order  = $this->input->get('order', true) ?: 'RECEIVE_DATE';
        $dir    = $this->input->get('dir', true) ?: 'DESC';

        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : 10;
        $start = ($page - 1) * $limit;

        $role_id = $this->session->userdata('role_id');
        $username = $this->session->userdata('username');
        $plant    = $this->session->userdata('plant'); // JSON ["10","1001",...]

        $rows = $this->Receive_model->get_data(
            $limit,
            $start,
            $role_id,
            $plant,
            $username,
            $search,
            $order,
            $dir
        );

        $total = $this->Receive_model->count_data(
            $role_id,
            $plant,
            $username,
            $search
        );

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'limit'      => $limit,
            'pagination' => $this->build_pagination($page, $limit, $total)
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

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');

        $data = $this->Receive_model->get_user_plant_options($username);

        echo json_encode($data);
    }

    public function get_plant()
    {
        $data = $this->Receive_model->get_plant_select2();
        echo json_encode($data);
    }

    /**
     * Select2: supplier
     */
    public function get_supplier()
    {
        $term = $this->input->get('q');
        $data = $this->Receive_model->search_supplier($term);
        echo json_encode($data);
    }

    /**
     * Select2: material
     */
    public function get_material()
    {
        $term = $this->input->get('q');
        $data = $this->Receive_model->search_material($term);
        echo json_encode($data);
    }

    /**
     * Select2: PO list (for selecting PO in form)
     */
    public function get_po()
    {
        $q = $this->input->get('q', true);

        $role_id = (int) $this->session->userdata('role_id');
        $plant   = $this->input->get('plant', true); // ✅ AMBIL DARI JS

        if (empty($plant)) {
            echo json_encode([]);
            return;
        }

        $data = $this->Receive_model->search_po($role_id, $plant, $q, 20);
        echo json_encode($data);
    }

    /**
     * Load PO detail (ajax) - when user selects PO in form
     */
    public function load_po_detail()
    {
        $po    = $this->input->get('po', TRUE);
        $plant = $this->input->get('plant', TRUE);

        if (!$po || !$plant) {
            echo json_encode(['status'=>false,'message'=>'PO & Plant required']);
            return;
        }

        $detail = $this->Receive_model->get_po_detail($po, $plant);
        echo json_encode(['status'=>true,'detail'=>$detail]);
    }

    public function create()
    {
        header('Content-Type: application/json');

        $post     = $this->input->post(NULL, TRUE);
        $username = $this->session->userdata('username');

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HEADER
        |--------------------------------------------------------------------------
        */
        if (
            empty($post['PLANT']) ||
            empty($post['PO']) ||
            empty($post['RECEIVE_DATE'])
        ) {
            echo json_encode([
                'status'  => false,
                'message' => 'Plant, PO, dan Tanggal Receive wajib diisi'
            ]);
            return;
        }

        $plant = trim($post['PLANT']);
        $po    = trim($post['PO']);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PO
        |--------------------------------------------------------------------------
        */
        $poHeader = $this->Receive_model->get_po_header($po, $plant);

        if (!$poHeader) {
            echo json_encode([
                'status'  => false,
                'message' => 'PO tidak ditemukan'
            ]);
            return;
        }

        if (!empty($poHeader['STATUS'])) {
            echo json_encode([
                'status'  => false,
                'message' => 'PO ini sudah pernah direceive'
            ]);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER AUTO FROM PO
        |--------------------------------------------------------------------------
        */
        $supplier = $poHeader['SUPPLIER'];

        /*
        |--------------------------------------------------------------------------
        | PARSE DETAIL
        |--------------------------------------------------------------------------
        */
        $detailRaw = $this->input->post('DETAIL', false);

        if ($detailRaw === null) {
            echo json_encode([
                'status'  => false,
                'message' => 'DETAIL tidak terkirim'
            ]);
            return;
        }

        $detailArr = json_decode($detailRaw, true);

        if (empty($detailArr) || !is_array($detailArr)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Detail material wajib diisi'
            ]);
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | GENERATE NUMBER
        |--------------------------------------------------------------------------
        */
        $receiveNo = $this->Receive_model->generate_receive_no($plant);
        $slipNo    = $this->Receive_model->generate_slip_no();

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */
        $this->db->trans_begin();

        try {

            /*
            |--------------------------------------------------------------------------
            | INSERT HEADER
            |--------------------------------------------------------------------------
            */
            $header = [
                'PLANT'        => $plant,
                'RECEIVE'      => $receiveNo,
                'NOTA'         => $post['NOTA'] ?? null,
                'PO'           => $po,
                'RECEIVE_DATE' => date('Y-m-d', strtotime($post['RECEIVE_DATE'])),
                'SUPPLIER'     => $supplier,
                'PEMBAYARAN'   => $post['PEMBAYARAN'] ?? null,
                'JENIS_PAY'    => $post['JENIS_PAY'] ?? null,
                'NO_REF'       => $post['NO_REF'] ?? null,
                'SLIP_NO'      => $slipNo,
                'REMARK'       => $post['REMARK'] ?? null,
                'CREATED_AT'   => date('Y-m-d H:i:s'),
                'CREATED_BY'   => $username
            ];

            $this->Receive_model->insert_receive_header($header);

            /*
            |--------------------------------------------------------------------------
            | UPLOAD ATTACHMENT
            |--------------------------------------------------------------------------
            */
            if (!empty($_FILES['ATTACHMENT']['name'])) {

                $upload = $this->Receive_model->upload_file(
                    'ATTACHMENT',
                    $plant,
                    $receiveNo,
                    $post['RECEIVE_DATE']
                );

                if ($upload) {

                    $this->Receive_model->update_receive_header(
                        $receiveNo,
                        [
                            'ATTACH_FILE_NAME'      => $upload['filename'],
                            'ATTACH_ORIGINAL_NAME'  => $_FILES['ATTACHMENT']['name'],
                            'ATTACH_PATH'           => $upload['path'],
                            'ATTACH_EXT'            => pathinfo($upload['filename'], PATHINFO_EXTENSION),
                            'ATTACH_SIZE'           => $_FILES['ATTACHMENT']['size']
                        ],
                        $plant
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT DETAIL
            |--------------------------------------------------------------------------
            */
            $rows = [];
            $seq  = 1;

            foreach ($detailArr as $row) {

                if (empty($row['MATERIAL'])) continue;

                $jumlah      = (float) ($row['JUMLAH'] ?? 0);
                $berat       = (float) ($row['BERAT'] ?? 0);
                $harga       = (float) $this->normalize_number($row['HARGA'] ?? 0);
                $total       = (float) $this->normalize_number($row['TOTAL'] ?? 0);
                $susutJumlah = (float) ($row['SUSUT_JUMLAH'] ?? 0);
                $susutBerat  = (float) ($row['SUSUT_BERAT'] ?? 0);

                $rows[] = [
                    'PLANT'         => $plant,
                    'RECEIVE'       => $receiveNo,
                    'SEQ_NO'        => $seq,
                    'PO'            => $po,
                    'PO_SEQ'        => $row['PO_SEQ'] ?? null,
                    'CUSTOMER'      => $row['CUSTOMER'] ?? null,
                    'MATERIAL'      => $row['MATERIAL'],
                    'JUMLAH'        => $jumlah,
                    'BERAT'         => $berat,
                    'HARGA'         => $harga,
                    'TOTAL'         => $total,
                    'SUSUT_JUMLAH'  => $susutJumlah,
                    'SUSUT_BERAT'   => $susutBerat,
                    'KETERANGAN'    => $row['KETERANGAN'] ?? null,
                    'STATUS'        => 'RECEIVED',
                    'CREATED_AT'    => date('Y-m-d H:i:s'),
                    'CREATED_BY'    => $username
                ];

                $seq++;
            }

            if (empty($rows)) {
                throw new Exception('Detail receive tidak valid');
            }

            $this->Receive_model->insert_receive_detail_batch($rows);

            /*
            |--------------------------------------------------------------------------
            | UPDATE PO STATUS
            |--------------------------------------------------------------------------
            */
            $this->Receive_model->update_po_status(
                $po,
                $plant,
                'RECEIVED'
            );

            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Gagal menyimpan Receive');
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'  => true,
                'receive' => $receiveNo,
                'slip_no' => $slipNo,
                'message' => 'Receive berhasil disimpan'
            ]);

        } catch (Exception $e) {

            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Edit: return header + detail by RECEIVE (ajax)
     */
    public function edit()
    {
        $receive = $this->input->get('receive', TRUE);
        $plant   = $this->input->get('plant', TRUE);
        $role_id = (int)$this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive & Plant required'
            ]);
            return;
        }

        // 🔐 VALIDASI PLANT UNTUK NON ADMIN
        if ($role_id !== 1) {
            if (!$this->Receive_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized plant'
                ]);
                return;
            }
        }

        $header = $this->Receive_model->get_receive_header($plant, $receive);
        $detail = $this->Receive_model->get_receive_detail($plant, $receive);

        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive tidak ditemukan'
            ]);
            return;
        }

        // format date untuk input type="date"
        $header['RECEIVE_DATE'] = date('Y-m-d', strtotime($header['RECEIVE_DATE']));

        echo json_encode([
            'status' => true,
            'header' => $header,
            'detail' => $detail
        ]);
    }

    public function update()
    {
        $data     = $this->input->post(NULL, TRUE);
        $receive  = $data['RECEIVE'] ?? null;
        $plant    = $data['PLANT'] ?? null;
        $role_id  = (int)$this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive & Plant required'
            ]);
            return;
        }

        // 🔐 VALIDASI PLANT UNTUK NON ADMIN
        if ($role_id !== 1) {
            if (!$this->Receive_model->user_has_plant($username, $plant)) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized plant'
                ]);
                return;
            }
        }

        // ambil header lama (untuk cek PO lama)
        $oldHeader = $this->Receive_model->get_receive_header($plant, $receive);
        if (!$oldHeader) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive tidak ditemukan'
            ]);
            return;
        }

        $oldPo = $oldHeader['PO'] ?? null;
        $newPo = $data['PO'] ?? null;
        $newPo = ($newPo === '' || strtolower($newPo) === 'null') ? null : $newPo;

        $header = [
            'RECEIVE_DATE' => date('Y-m-d', strtotime($data['RECEIVE_DATE'])),
            'SUPPLIER'     => $data['SUPPLIER'],
            'PO'           => $newPo,
            'PEMBAYARAN' => $data['PEMBAYARAN_EDIT'] ?? null,
            'JENIS_PAY'  => $data['JENIS_PAY_EDIT'] ?? null,
            'NOTA'         => $data['NOTA'] ?? null,
            'NO_REF'       => $data['NO_REF'] ?? null,
            'REMARK'       => $data['REMARK'] ?? null,
            'UPDATED_AT'   => date('Y-m-d H:i:s'),
            'UPDATED_BY'   => $username
        ];

        $this->db->trans_start();

        // =========================
        // UPDATE HEADER
        // =========================
        $this->Receive_model->update_receive_header($receive, $header, $plant);

        // =========================
        // UPDATE STATUS PO
        // =========================

        // PO lama → dikosongkan
        if ($oldPo && $oldPo !== $newPo) {
            $this->Receive_model->reset_po_status($oldPo, $plant);
        }

        // PO baru dikunci
        if ($newPo) {
            $this->Receive_model->set_po_received(
                $newPo,
                $plant,
                $username
            );
        }

        // =========================
        // DETAIL
        // =========================
        $lastSeq = (int) $this->Receive_model->get_max_seq_no($plant, $receive);
        $seq     = $lastSeq + 1;

        $keepSeq   = [];
        $insertRow = [];

        if (!empty($data['DETAIL']) && is_array($data['DETAIL'])) {

            foreach ($data['DETAIL'] as $row) {

                // DETAIL LAMA
                if (!empty($row['SEQ_NO'])) {

                    $keepSeq[] = $row['SEQ_NO'];

                    $this->db->where([
                        'PLANT'   => $plant,
                        'RECEIVE' => $receive,
                        'SEQ_NO'  => $row['SEQ_NO']
                    ])->update('abc_mst_receive_detail', [
                        'PO'         => $newPo,
                        'MATERIAL'   => $row['MATERIAL'],
                        'JUMLAH'     => (float)$row['JUMLAH'],
                        'BERAT'      => (float)$row['BERAT'],
                        'HARGA'      => (float)$row['HARGA'],
                        'TOTAL'      => (float)$row['TOTAL'],
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $username
                    ]);

                }
                // DETAIL BARU
                else {

                    $insertRow[] = [
                        'PLANT'      => $plant,
                        'RECEIVE'    => $receive,
                        'SEQ_NO'     => $seq,
                        'PO'         => $newPo,
                        'MATERIAL'   => $row['MATERIAL'],
                        'JUMLAH'     => (float)$row['JUMLAH'],
                        'BERAT'      => (float)$row['BERAT'],
                        'HARGA'      => (float)$row['HARGA'],
                        'TOTAL'      => (float)$row['TOTAL'],
                        'CREATED_AT' => date('Y-m-d H:i:s'),
                        'CREATED_BY' => $username
                    ];

                    $keepSeq[] = $seq;
                    $seq++;
                }
            }
        }

        // INSERT DETAIL BARU
        if (!empty($insertRow)) {
            $this->Receive_model->insert_receive_detail_batch($insertRow);
        }

        // HAPUS DETAIL YANG DIDELETE DI UI
        if (!empty($keepSeq)) {
            $this->Receive_model->delete_receive_detail_not_in_seq(
                $plant,
                $receive,
                $keepSeq
            );
        }

        if (!empty($_FILES['ATTACHMENT']['name'])) {

            $upload = $this->Receive_model->upload_file(
                'ATTACHMENT',
                $plant,
                $receive,
                $data['RECEIVE_DATE']
            );

            if ($upload) {
                $this->Receive_model->update_receive_header(
                    $receive,
                    [
                        'ATTACH_FILE_NAME'      => $upload['filename'],
                        'ATTACH_ORIGINAL_NAME' => $_FILES['ATTACHMENT']['name'],
                        'ATTACH_PATH'           => $upload['path'],
                        'ATTACH_EXT'            => pathinfo($upload['filename'], PATHINFO_EXTENSION),
                        'ATTACH_SIZE'           => $_FILES['ATTACHMENT']['size'],
                        'UPDATED_AT'            => date('Y-m-d H:i:s'),
                        'UPDATED_BY'            => $this->session->userdata('username')
                    ],
                    $plant
                );
            }
        }

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Receive berhasil diupdate'
                : 'Gagal update receive'
        ]);
    }

    /**
     * Remove receive by RECEIVE
     */
    public function remove()
    {
        $receive = $this->input->post('receive', true);
        $plant   = $this->input->post('plant', true);
        $role_id = (int) $this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        if (!$receive || !$plant) {
            echo json_encode([
                'status' => false,
                'message' => 'RECEIVE dan PLANT wajib dikirim'
            ]);
            return;
        }

        // ================= VALIDASI HEADER =================
        $header = $this->Receive_model->get_receive_header($plant, $receive);

        if (!$header) {
            echo json_encode([
                'status' => false,
                'message' => 'Data receive tidak ditemukan'
            ]);
            return;
        }

        // ================= NON ADMIN CHECK =================
        if ($role_id !== 1) {
            if ($header['CREATED_BY'] !== $username) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Anda tidak berhak menghapus receive ini'
                ]);
                return;
            }
        }

        $this->db->trans_start();

        // ================= ROLLBACK STATUS PO =================
        if (!empty($header['PO'])) {
            $this->Receive_model->update_po_status(
                $header['PO'],
                $plant,
                null // kembalikan ke NULL
            );
        }

        // ================= DELETE DETAIL =================
        $this->Receive_model->delete_receive_detail_by_receive($receive, $plant);

        // ================= DELETE HEADER =================
        $this->Receive_model->delete_receive_header_by_receive_and_plant($receive, $plant);

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Receive berhasil dihapus'
                : 'Gagal menghapus receive'
        ]);
    }

    /**
     * Print PDF stub (you can implement PDF generation here)
     */
    public function print_slip_pdf()
    {
        $receive = $this->input->get('receive');
        $plant   = $this->input->get('plant');

        if (!$receive || !$plant) {
            show_error('Parameter RECEIVE atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                r.RECEIVE,
                r.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                r.RECEIVE_DATE,
                r.PO,
                r.NOTA,
                r.NO_REF,
                r.SUPPLIER,
                s.FULL_NAME AS SUPPLIER_NAME,
                r.SLIP_NO,
                r.REMARK
            ')
            ->from('abc_mst_receive r')
            ->join('cd_code aj', "aj.CODE = r.PLANT AND aj.HEAD_CODE = 'AJ'", 'left')
            ->join('cd_customer s', "s.CUST = r.SUPPLIER", 'left')
            ->where('r.RECEIVE', $receive)
            ->where('r.PLANT', $plant)
            ->get()
            ->row();

        if (!$header) {
            show_error('Receive tidak ditemukan');
        }

        /* ================= HANDLE PO NULL ================= */
        $header->PO_TEXT = empty($header->PO)
            ? 'Direct Receive'
            : $header->PO;

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.MATERIAL,
                m.material_name AS MATERIAL_NAME,
                d.JUMLAH,
                d.BERAT,
                d.HARGA,
                d.TOTAL
            ')
            ->from('abc_mst_receive_detail d')
            ->join('cd_material m', 'm.material = d.MATERIAL', 'left')
            ->where('d.RECEIVE', $receive)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result();

        /* ================= SUMMARY ================= */
        $summary = [
            'total_jumlah' => 0,
            'total_berat'  => 0,
            'grand_total'  => 0,
        ];

        foreach ($detail as $row) {
            $summary['total_jumlah'] += (float)$row->JUMLAH;
            $summary['total_berat']  += (float)$row->BERAT;
            $summary['grand_total']  += $this->normalize_number($row->TOTAL);
        }

        $data = compact('header', 'detail', 'summary');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/receive/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "SLIP_RECEIVE_{$receive}.pdf",
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

        // hapus titik ribuan, ganti koma desimal (jika ada)
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }

}
