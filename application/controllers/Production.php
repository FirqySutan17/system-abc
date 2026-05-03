<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('productions_production')) {
            show_404();
        }
        $this->load->model('Production_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Production']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/production/list');
        $this->load->view('templates/footer');
    }

    /**
     * Load data list (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page', true);
        $limit  = (int)$this->input->get('limit', true);
        $search = $this->input->get('search', true);
        $order  = $this->input->get('order', true) ?: 'PRODUCTION_DATE';
        $dir    = $this->input->get('dir', true) ?: 'DESC';

        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : 10;
        $start = ($page - 1) * $limit;

        $role_id = $this->session->userdata('role_id');
        $username = $this->session->userdata('username');
        $plant    = $this->session->userdata('plant'); // JSON

        $rows = $this->Production_model->get_data(
            $limit,
            $start,
            $role_id,
            $plant,
            $username,
            $search,
            $order,
            $dir
        );

        $total = $this->Production_model->count_data(
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

    private function build_pagination($currentPage, $limit, $totalRows)
    {
        $totalPages = (int) ceil($totalRows / $limit);

        if ($totalPages <= 1) return '';

        $html = '<ul class="pagination pagination-sm">';

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $currentPage) ? 'active' : '';
            $html .= '<li class="page-item '.$active.'">
                        <a href="javascript:void(0)" class="page-link" onclick="loadPage('.$i.')">'.$i.'</a>
                    </li>';
        }

        $html .= '</ul>';
        return $html;
    }

    public function get_receive_lb()
    {
        $term = $this->input->get('q', TRUE);
        $data = $this->Production_model->search_receive_lb($term);
        echo json_encode($data);
    }

    private function indo_date($date)
    {
        $months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        $format = date('d F Y', strtotime($date));
        foreach ($months as $en => $id) {
            $format = str_replace($en, $id, $format);
        }
        return $format;
    }

    // public function get_receive_lb_list()
    // {
    //     $user     = $this->session->userdata();
    //     $role_id = (int)$user['role_id'];
    //     $username = $user['username'];

    //     $plants = ($role_id === 1)
    //         ? [] // admin → semua plant
    //         : $this->Production_model->get_user_plants($username);

    //     if ($role_id !== 1 && empty($plants)) {
    //         echo json_encode(['data' => []]);
    //         return;
    //     }
    //     $this->db->select("
    //         r.RECEIVE,
    //         DATE_FORMAT(r.RECEIVE_DATE, '%d %M %Y') AS RECEIVE_DATE,
    //         r.SUPPLIER,
    //         c.FULL_NAME,
    //         r.QTY,
    //         r.DEAD,
    //         r.RECEIVE_AMOUNT,
    //         (r.QTY - r.DEAD) AS TOTAL_QTY,
    //         r.NO_CAR,
    //         r.DRIVER,
    //         r.DO,
    //         DATE_FORMAT(r.ARRIVE_SCHEDULE, '%d %M %Y - %H:%i') AS ARRIVE_SCHEDULE,
    //         r.PLANT,
    //         cc.CODE_NAME AS PLANT_NAME
    //     ");
    //     $this->db->from('mst_receive_lb r');
    //     $this->db->join('cd_customer c', 'c.CUST = r.SUPPLIER', 'left');
    //     $this->db->join('cd_code cc', "cc.CODE = r.PLANT AND cc.HEAD_CODE = 'AJ'", 'left');

    //     // 🔒 BELUM DIPAKAI PRODUCTION
    //     $this->db->where('r.STATUS IS NULL', null, false);

    //     // 🔒 BELUM DIHAPUS
    //     $this->db->where('r.DELETED IS NULL', null, false);

    //     /* 🔐 FILTER PLANT SESUAI HAK USER */
    //     if ($role_id !== 1) {
    //         $this->db->where_in('r.PLANT', $plants);
    //     }
    //     $this->db->order_by('r.RECEIVE_DATE', 'DESC');

    //     $data = $this->db->get()->result();

    //     echo json_encode(['data' => $data]);
    // }

    public function get_receive_lb_list()
    {
        $user     = $this->session->userdata();
        $role_id  = (int)$user['role_id'];
        $username = $user['username'];

        $reqPlant = $this->input->get('plant', true);

        $userPlants = ($role_id === 1)
            ? []
            : $this->Production_model->get_user_plants($username);


        // =====================
        // VALIDASI PLANT
        // =====================

        if ($role_id !== 1) {

            if (!$reqPlant) {
                echo json_encode(['data'=>[]]);
                return;
            }

            if (!in_array($reqPlant, $userPlants)) {
                show_error('Unauthorized plant');
            }

            $plantFilter = [$reqPlant];

        } else {

            // admin
            if ($reqPlant) {
                $plantFilter = [$reqPlant];
            } else {
                $plantFilter = [];
            }
        }


        // =====================
        // QUERY
        // =====================

        $this->db->select("
            r.RECEIVE,
            DATE_FORMAT(r.RECEIVE_DATE, '%d %M %Y') AS RECEIVE_DATE,
            r.SUPPLIER,
            c.FULL_NAME,
            r.QTY,
            r.DEAD,
            r.RECEIVE_AMOUNT,
            (r.QTY - r.DEAD) AS TOTAL_QTY,
            r.NO_CAR,
            r.DRIVER,
            r.DO,
            DATE_FORMAT(r.ARRIVE_SCHEDULE, '%d %M %Y - %H:%i') AS ARRIVE_SCHEDULE,
            r.PLANT,
            cc.CODE_NAME AS PLANT_NAME
        ");

        $this->db->from('mst_receive_lb r');

        $this->db->join(
            'cd_customer c',
            'c.CUST = r.SUPPLIER',
            'left'
        );

        $this->db->join(
            'cd_code cc',
            "cc.CODE = r.PLANT AND cc.HEAD_CODE='AJ'",
            'left'
        );


        // STATUS
        $this->db->where('r.STATUS IS NULL', null, false);

        // DELETED
        $this->db->where('r.DELETED IS NULL', null, false);


        // ✅ PLANT FILTER BENAR
        if (!empty($plantFilter)) {
            $this->db->where_in('r.PLANT', $plantFilter);
        }


        $this->db->order_by('r.RECEIVE_DATE', 'DESC');


        $data = $this->db->get()->result();

        echo json_encode(['data'=>$data]);
    }

    public function get_item_list()
    {
        $this->db->select("ITEM, FULL_NAME");
        $this->db->from("cd_item");
        $this->db->order_by("FULL_NAME", "ASC");

        $rows = $this->db->get()->result();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                "id"   => $r->ITEM,
                "text" => $r->ITEM . " - " . $r->FULL_NAME
            ];
        }

        echo json_encode($data);
    }

    public function get_all_item()
    {
        $items = $this->Production_model->get_all_item();
        echo json_encode($items);
    }

    public function get_plant_by_user()
    {
        $username = $this->session->userdata('username');

        $plants = $this->Production_model->get_plant_select2_by_user($username);

        echo json_encode($plants);
    }

    /**
     * Create Production (header + detail)
     */

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        // =========================
        // VALIDASI BASIC
        // =========================
        if (empty($data['PRODUCTION_DATE'])) {
            echo json_encode(['status'=>false,'message'=>'Tanggal produksi wajib diisi']);
            return;
        }

        if (empty($data['RECEIVE_LB'])) {
            echo json_encode(['status'=>false,'message'=>'Receive LB wajib diisi']);
            return;
        }

        if (empty($data['DETAIL']) || !is_array($data['DETAIL'])) {
            echo json_encode(['status'=>false,'message'=>'Detail produksi tidak boleh kosong']);
            return;
        }

        $username = $this->session->userdata('username');
        $role_id = (int)$this->session->userdata('role_id');
        $userPlants = json_decode($this->session->userdata('plant'), true);

        $receiveLB = $this->db
            ->where('RECEIVE', $data['RECEIVE_LB'])
            ->where('PLANT', $data['PLANT'])
            ->where('STATUS IS NULL', null, false)
            ->where('DELETED IS NULL', null, false)
            ->get('mst_receive_lb')
            ->row_array();

        if (!$receiveLB) {
            echo json_encode([
                'status'  => false,
                'message' => 'Receive LB tidak ditemukan atau sudah dipakai'
            ]);
            return;
        }

        // =========================
        // CEK SUDAH PERNAH DIPAKAI DI PRODUCTION
        // =========================
        $alreadyUsed = $this->db
            ->where('RECEIVE_LB', $data['RECEIVE_LB'])
            ->where('PLANT', $data['PLANT'])
            ->where('DELETED IS NULL', null, false)
            ->get('mst_production')
            ->row();

        if ($alreadyUsed) {
            echo json_encode([
                'status' => false,
                'message'=> 'Receive LB sudah dipakai di Production lain'
            ]);
            return;
        }

        $totalBeratInput = 0;
        foreach ($data['DETAIL'] as $row) {
            $totalBeratInput += isset($row['BERAT']) ? (float)$row['BERAT'] : 0;
        }

        $receiveKg = (float)$receiveLB['RECEIVE_AMOUNT'];

        // if ($totalBeratInput > $receiveKg) {
        //     echo json_encode([
        //         'status'  => false,
        //         'message' => 'Total berat produksi melebihi berat Receive LB ('.number_format($receiveKg,2,',','.').' Kg)'
        //     ]);
        //     return;
        // }

        $plant = $receiveLB['PLANT'];

        // NON ADMIN → cek plant user
        if ($role_id !== 1) {
            if (!in_array((string)$plant, array_map('strval', $userPlants))) {
                echo json_encode([
                    'status'=>false,
                    'message'=>'Anda tidak memiliki akses ke plant ini'
                ]);
                return;
            }
        }

        // =========================
        // GENERATE PRODUCTION NO
        // =========================
        $productionNo = $this->Production_model
            ->generate_production_no($plant, $data['PRODUCTION_DATE']);

        // =========================
        // HEADER
        // =========================
        $header = [
            'PLANT'           => $plant,
            'PRODUCTION'      => $productionNo,
            'RECEIVE_LB'      => $data['RECEIVE_LB'],
            'PRODUCTION_DATE' => date('Y-m-d H:i:s', strtotime($data['PRODUCTION_DATE'])),
            'REMARK'          => $data['REMARK'] ?? null,
            'CREATED_AT'      => date('Y-m-d H:i:s'),
            'CREATED_BY'      => $username
        ];

        $this->db->trans_start();

        // INSERT HEADER
        $this->Production_model->insert_production_header($header);

        // =========================
        // DETAIL
        // =========================
        $rows = [];
        $seq  = 1;

        foreach ($data['DETAIL'] as $row) {

            $qty   = isset($row['QTY']) ? (float)$row['QTY'] : 0;
            $berat = isset($row['BERAT']) ? (float)$row['BERAT'] : 0;

            if ($qty <= 0 && $berat <= 0) continue;
            if (empty($row['ITEM'])) continue;

            $rows[] = [
                'PLANT'      => $plant,
                'PRODUCTION' => $productionNo,
                'SEQ_NO'     => $seq,
                'RECEIVE_LB' => $data['RECEIVE_LB'],
                'ITEM'       => $row['ITEM'],
                'QTY'        => $qty,
                'BERAT'      => $berat,
                'REMARK'     => $row['REMARK'] ?? null,
                'CREATED_AT' => date('Y-m-d H:i:s'),
                'CREATED_BY' => $username
            ];

            $seq++;
        }

        if (empty($rows)) {
            $this->db->trans_rollback();
            echo json_encode(['status'=>false,'message'=>'Tidak ada item yang diinput']);
            return;
        }

        $this->Production_model->insert_production_detail_batch($rows);

        // =========================
        // LOCK RECEIVE LB
        // =========================
        $this->db->where('RECEIVE', $data['RECEIVE_LB']);
        $this->db->update('mst_receive_lb', [
            'STATUS'      => 'Y',
            'UPDATED_AT'  => date('Y-m-d H:i:s'),
            'UPDATED_BY'  => $username
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status'=>false,'message'=>'Gagal menyimpan Production']);
            return;
        }

        echo json_encode([
            'status'     => true,
            'production' => $productionNo,
            'message'    => 'Production berhasil disimpan'
        ]);
    }

    /**
     * Edit: load header + detail
     */
    public function edit()
    {
        $production = $this->input->get('production');
        $plant      = $this->input->get('plant');

        $user    = $this->session->userdata();
        $role_id = (int)$user['role_id'];

        if (!$production || !$plant) {
            echo json_encode(['header' => null, 'detail' => []]);
            return;
        }

        $this->db->select("
            p.PLANT,
            p.PRODUCTION,
            DATE_FORMAT(p.PRODUCTION_DATE, '%Y-%m-%d') AS PRODUCTION_DATE,
            p.RECEIVE_LB,
            p.REMARK,

            r.RECEIVE,
            DATE_FORMAT(r.RECEIVE_DATE, '%Y-%m-%d') AS RECEIVE_DATE,
            r.SUPPLIER,
            CONCAT('(', r.SUPPLIER, ') ', c.FULL_NAME) AS SUPPLIER_NAME,
            r.QTY,
            r.DEAD,
            (r.QTY - r.DEAD) AS TOTAL_QTY,
            r.RECEIVE_AMOUNT AS TOTAL_KG,
            r.NO_CAR,
            r.DRIVER,
            r.DO,
            cd_code.CODE_NAME AS PLANT_NAME,
            DATE_FORMAT(r.ARRIVE_SCHEDULE, '%d %M %Y - %H:%i') AS ARRIVE_SCHEDULE
        ");
        $this->db->from('mst_production p');

        $this->db->group_start();
        $this->db->where('p.DELETED IS NULL', null, false);
        $this->db->or_where('p.DELETED', '0');
        $this->db->group_end();

        // 🔑 JOIN RECEIVE WAJIB + PLANT
        $this->db->join(
            'mst_receive_lb r',
            'r.RECEIVE = p.RECEIVE_LB AND r.PLANT = p.PLANT',
            'left'
        );

        $this->db->join('cd_customer c', 'c.CUST = r.SUPPLIER', 'left');
        $this->db->join(
            'cd_code',
            "cd_code.CODE = p.PLANT AND cd_code.HEAD_CODE = 'AJ'",
            'left'
        );

        // 🔑 FILTER UTAMA
        $this->db->where('p.PRODUCTION', $production);
        $this->db->where('p.PLANT', $plant);

        // 🔐 NON ADMIN: validasi akses (BUKAN filter query)
        if ($role_id !== 1) {
            $plants = json_decode($user['plant'], true);
            if (!is_array($plants) || !in_array($plant, $plants)) {
                echo json_encode(['header' => null, 'detail' => []]);
                return;
            }
        }

        $header = $this->db->get()->row_array();

        if (!$header) {
            echo json_encode(['header' => null, 'detail' => []]);
            return;
        }

        // =========================
        // DETAIL
        // =========================
        $detail = $this->db
            ->select("d.*, i.FULL_NAME")
            ->from("mst_production_detail d")
            ->join("cd_item i", "i.ITEM = d.ITEM", "left")
            ->where("d.PRODUCTION", $production)
            ->where("d.PLANT", $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result_array();

        echo json_encode([
            'header' => $header,
            'detail' => $detail
        ]);
    }

    /**
     * Update Production
     */
    public function update()
    {
        $data       = $this->input->post(NULL, TRUE);
        $production = $data['PRODUCTION'] ?? null;
        $plant      = $data['PLANT'] ?? null;
        $detail     = $data['DETAIL'] ?? [];
        $user       = $this->session->userdata();
        $role_id    = (int)$user['role_id'];

        if (!$production || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Production / Plant tidak valid']);
            return;
        }

        if (empty($data['RECEIVE_LB'])) {
            echo json_encode(['status'=>false,'message'=>'Receive LB wajib diisi']);
            return;
        }

        if (!is_array($detail) || empty($detail)) {
            echo json_encode(['status'=>false,'message'=>'Detail minimal 1 baris']);
            return;
        }

        if ($role_id !== 1) {

            $plants = json_decode($user['plant'], true);

            if (!is_array($plants)) {
                echo json_encode(['header' => null, 'detail' => []]);
                return;
            }

            // 🔑 PAKSA SEMUA JADI STRING
            $plants = array_map('strval', $plants);

            if (!in_array((string)$plant, $plants, true)) {
                echo json_encode(['header' => null, 'detail' => []]);
                return;
            }
        }

        // =========================
        // HEADER
        // =========================
        $header = [
            'RECEIVE_LB'      => $data['RECEIVE_LB'],
            'PRODUCTION_DATE' => date('Y-m-d H:i:s', strtotime($data['PRODUCTION_DATE'])),
            'REMARK'          => $data['REMARK'] ?? null,
            'UPDATED_AT'      => date('Y-m-d H:i:s'),
            'UPDATED_BY'      => $user['username']
        ];

        $this->db->trans_start();

        $receiveLB = $this->db
            ->where('RECEIVE', $data['RECEIVE_LB'])
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->group_start()
                ->where('STATUS IS NULL', null, false)
                ->or_where('STATUS', 'Y') // masih boleh jika milik production ini
            ->group_end()
            ->get('mst_receive_lb')
            ->row_array();

        if (!$receiveLB) {
            echo json_encode(['status'=>false,'message'=>'Receive LB tidak valid atau sudah dipakai production lain']);
            return;
        }

        $totalBeratInput = 0;
        foreach ($detail as $row) {
            $totalBeratInput += isset($row['BERAT']) ? (float)$row['BERAT'] : 0;
        }

        $receiveKg = (float)$receiveLB['RECEIVE_AMOUNT'];

        // if ($totalBeratInput > $receiveKg) {
        //     echo json_encode([
        //         'status'  => false,
        //         'message' => 'Total berat produksi melebihi berat Receive LB ('.number_format($receiveKg,2,',','.').' Kg)'
        //     ]);
        //     return;
        // }

        $oldHeader = $this->Production_model->get_production_header($production, $plant);

        // Jika Receive LB diganti
        if ($oldHeader && $oldHeader['RECEIVE_LB'] !== $data['RECEIVE_LB']) {

            // 🔓 Buka kunci Receive LB lama
            $this->db->where('RECEIVE', $oldHeader['RECEIVE_LB'])
                    ->where('PLANT', $plant)
                    ->update('mst_receive_lb', [
                        'STATUS' => null,
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $user['username']
                    ]);

            // 🔒 Kunci Receive LB baru
            $this->db->where('RECEIVE', $data['RECEIVE_LB'])
                    ->where('PLANT', $plant)
                    ->update('mst_receive_lb', [
                        'STATUS' => 'Y',
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $user['username']
                    ]);
        }

        $realHeader = $this->Production_model->get_production_header($production, $plant);
        if (!$realHeader) {
            echo json_encode(['status'=>false,'message'=>'Production tidak ditemukan']);
            return;
        }

        // UPDATE HEADER
        $this->Production_model
            ->update_production_header($production, $plant, $header);

        // =========================
        // SEQ TERAKHIR
        // =========================
        $lastSeq = $this->db->select_max('SEQ_NO')
                            ->where('PRODUCTION', $production)
                            ->where('PLANT', $plant)
                            ->get('mst_production_detail')
                            ->row()
                            ->SEQ_NO;

        $seq = ($lastSeq ?? 0) + 1;
        $keepSeq = [];

        // =========================
        // LOOP DETAIL
        // =========================
        foreach ($detail as $row) {

            $qty   = isset($row['QTY']) ? (float)$row['QTY'] : 0;
            $berat = isset($row['BERAT']) ? (float)$row['BERAT'] : 0;

            if ($qty <= 0 && $berat <= 0) continue;
            if (empty($row['ITEM'])) continue;

            // =====================
            // DETAIL LAMA
            // =====================
            if (!empty($row['SEQ_NO'])) {

                $keepSeq[] = $row['SEQ_NO'];

                $this->db->where([
                    'PLANT'      => $plant,
                    'PRODUCTION' => $production,
                    'SEQ_NO'     => $row['SEQ_NO']
                ])->update('mst_production_detail', [
                    'ITEM'       => $row['ITEM'],
                    'QTY'        => $qty,
                    'BERAT'      => $berat,
                    'REMARK'     => $row['REMARK'] ?? null,
                    'RECEIVE_LB' => $data['RECEIVE_LB']
                ]);

            }
            // =====================
            // DETAIL BARU
            // =====================
            else {

                $this->db->insert('mst_production_detail', [
                    'PLANT'      => $plant,
                    'PRODUCTION' => $production,
                    'SEQ_NO'     => $seq,
                    'RECEIVE_LB' => $data['RECEIVE_LB'],
                    'ITEM'       => $row['ITEM'],
                    'QTY'        => $qty,
                    'BERAT'      => $berat,
                    'REMARK'     => $row['REMARK'] ?? null,
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'CREATED_BY' => $user['username']
                ]);

                $keepSeq[] = $seq;
                $seq++;
            }
        }

        // =========================
        // DELETE DETAIL YANG DIHAPUS DI UI
        // =========================
        if (!empty($keepSeq)) {
            $this->db->where('PLANT', $plant)
                    ->where('PRODUCTION', $production)
                    ->where_not_in('SEQ_NO', $keepSeq)
                    ->delete('mst_production_detail');
        }

        $this->db->trans_complete();

        echo json_encode([
            'status'  => $this->db->trans_status(),
            'message' => $this->db->trans_status()
                ? 'Production berhasil diupdate'
                : 'Gagal update production'
        ]);
    }

    /**
     * Remove Production
     */
    public function remove()
    {
        $production = $this->input->post('production', TRUE);
        $plant      = $this->input->post('plant', TRUE);
        $user       = $this->session->userdata();

        if (!$production || !$plant) {
            echo json_encode([
                'status'  => false,
                'message' => 'Invalid PRODUCTION atau PLANT'
            ]);
            return;
        }

        $plants = json_decode($user['plant'], true);
        if ($user['role_id'] != 1 && !in_array($plant, $plants)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Anda tidak memiliki akses menghapus production ini'
            ]);
            return;
        }

        // 🔎 Pastikan header ada
        $header = $this->Production_model->get_production_header($production, $plant);
        if (!$header) {
            echo json_encode([
                'status'  => false,
                'message' => 'Data production tidak ditemukan'
            ]);
            return;
        }

        $realHeader = $this->Production_model->get_production_header($production, $plant);
        if (!$realHeader) {
            echo json_encode(['status'=>false,'message'=>'Production tidak ditemukan']);
            return;
        }

        $this->db->trans_start();

        $this->db->where('RECEIVE', $header['RECEIVE_LB'])
        ->where('PLANT', $plant)
        ->update('mst_receive_lb', [
            'STATUS' => null,
            'UPDATED_AT' => date('Y-m-d H:i:s'),
            'UPDATED_BY' => $user['username']
        ]);
        // Hapus detail (semua SEQ_NO otomatis terhapus)
        $this->Production_model->delete_production_detail($production, $plant);

        // Hapus header
        $this->Production_model->delete_production_header($production, $plant);

        $this->db->where('RECEIVE', $header['RECEIVE_LB']);
        $this->db->where('PLANT', $plant);
        $this->db->update('mst_receive_lb', [
            'STATUS'     => null,
            'UPDATED_AT' => date('Y-m-d H:i:s'),
            'UPDATED_BY' => $user['username']
        ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus Production'
            ]);
            return;
        }

        echo json_encode([
            'status'  => true,
            'message' => 'Production berhasil dihapus'
        ]);
    }

    public function print_pdf()
    {
        $production = trim($this->input->get('production', true));
        $plant      = trim($this->input->get('plant', true));

        if (!$production || !$plant) {
            show_error('Parameter PRODUCTION atau PLANT tidak lengkap');
        }

        /* ================= HEADER ================= */
        $header = $this->db
            ->select('
                p.PRODUCTION,
                p.PLANT,
                aj.CODE_NAME AS PLANT_NAME,
                p.RECEIVE_LB,
                p.PRODUCTION_DATE,
                p.REMARK
            ')
            ->from('mst_production p')
            ->join(
                'cd_code aj',
                "aj.CODE COLLATE utf8mb4_unicode_ci = p.PLANT COLLATE utf8mb4_unicode_ci
                AND aj.HEAD_CODE = 'AJ'",
                'left',
                false
            )
            ->join(
                'mst_receive_lb r',
                'r.RECEIVE = p.RECEIVE_LB AND r.PLANT = p.PLANT',
                'left'
            )
            ->select('r.DO, r.SUPPLIER, r.RECEIVE_DATE')
            ->where(
                "p.PRODUCTION COLLATE utf8mb4_unicode_ci = ".$this->db->escape($production),
                null,
                false
            )
            ->where(
                "p.PLANT COLLATE utf8mb4_unicode_ci = ".$this->db->escape($plant),
                null,
                false
            )
            ->group_start()
            ->where('p.DELETED IS NULL', null, false)
            ->or_where('p.DELETED', '0')
            ->group_end()
            ->limit(1)
            ->get()
            ->row();

        if (!$header) {
            show_error('Data Production tidak ditemukan');
        }

        /* ================= DETAIL ================= */
        $detail = $this->db
            ->select('
                d.SEQ_NO,
                d.ITEM,
                m.FULL_NAME AS ITEM_NAME,
                d.QTY,
                d.BERAT,
                d.REMARK
            ')
            ->from('mst_production_detail d')
            ->join(
                'cd_item m',
                'm.ITEM COLLATE utf8mb4_unicode_ci = d.ITEM COLLATE utf8mb4_unicode_ci',
                'left',
                false
            )
            ->where(
                "d.PRODUCTION COLLATE utf8mb4_unicode_ci = ".$this->db->escape($production),
                null,
                false
            )
            ->where(
                "d.PLANT COLLATE utf8mb4_unicode_ci = ".$this->db->escape($plant),
                null,
                false
            )
            ->group_start()
            ->where('d.DELETED IS NULL', null, false)
            ->or_where('d.DELETED', '0')
            ->group_end()
            ->order_by('CAST(d.SEQ_NO AS UNSIGNED)', 'ASC')
            ->get()
            ->result();

        $header->PRODUCTION_DATE = date('d/m/Y', strtotime($header->PRODUCTION_DATE));

        $totalQty = 0;
        $totalBerat = 0;

        foreach ($detail as $d) {
            $totalQty += (float)str_replace(',', '.', str_replace('.', '', $d->QTY));
            $totalBerat += (float)str_replace(',', '.', str_replace('.', '', $d->BERAT));
        }

        $data = compact('header', 'detail', 'totalQty', 'totalBerat');

        /* ================= PDF ================= */
        $html = $this->load->view('admin/production/pdf_template', $data, true);

        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();

        $this->pdf->stream(
            "PRODUCTION_{$production}.pdf",
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
