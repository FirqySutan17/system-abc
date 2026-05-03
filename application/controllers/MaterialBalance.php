<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaterialBalance extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('inventory_material_balance')) {
            show_404();
        }
        $this->load->model('MaterialBalance_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Material Balance']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/material_balance/list');
        $this->load->view('templates/footer');
    }

    /**
     * Load data table (ajax)
     */
    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $search = $this->input->get('search', TRUE);
        $order  = $this->input->get('order', TRUE) ?: 'RECEIVE_DATE';
        $dir    = strtoupper($this->input->get('dir', TRUE)) === 'DESC' ? 'DESC' : 'ASC';

        $start = ($page - 1) * $limit;

        $rows  = $this->ReceiveLb_model->get_data($limit, $start, $search, $order, $dir);
        $total = $this->ReceiveLb_model->count_data($search);

        echo json_encode([
            'rows'  => $rows,
            'total' => $total,
            'page'  => $page
        ]);
    }

    public function get_supplier()
    {
        $term = $this->input->get('q');
        $data = $this->ReceiveLb_model->search_supplier($term);
        echo json_encode($data);
    }

    /**
     * Create Receive LB
     */
    public function create()
    {
        $data = $this->input->post(NULL, TRUE);

        if (empty($data['RECEIVE_DATE'])) {
            echo json_encode(['status'=>false,'message'=>'Tanggal receive wajib diisi']);
            return;
        }

        if (empty($data['SUPPLIER'])) {
            echo json_encode(['status'=>false,'message'=>'Supplier wajib diisi']);
            return;
        }

        $plant     = $this->session->userdata('plant');
        $receiveNo = $this->ReceiveLb_model->generate_receive_no($plant);

        $insert = [
            'PLANT'          => $plant,
            'RECEIVE'        => $receiveNo,
            'RECEIVE_DATE'   => date('Y-m-d H:i:s', strtotime($data['RECEIVE_DATE'])),

            // === radio ===
            'PEMBAYARAN'     => $data['PEMBAYARAN'] ?? null,
            'JENIS_PAY'      => $data['JENIS_PAY'] ?? null,

            // === optional text ===
            'SLIP_NO'        => $data['SLIP_NO'] ?? null,
            'DO'             => $data['DO'] ?? null,
            'DRIVER'         => $data['DRIVER'] ?? null,
            'NO_CAR'         => $data['NO_CAR'] ?? null,
            'SUPPLIER'       => $data['SUPPLIER'],
            'REMARK'         => $data['REMARK'] ?? null,

            // === datetime ===
            'ARRIVE_SCHEDULE'=> !empty($data['ARRIVE_SCHEDULE']) 
                                    ? date('Y-m-d H:i:s', strtotime($data['ARRIVE_SCHEDULE'])) 
                                    : null,
            'DEPART_SCHEDULE'=> !empty($data['DEPART_SCHEDULE']) 
                                    ? date('Y-m-d H:i:s', strtotime($data['DEPART_SCHEDULE'])) 
                                    : null,

            // === numeric ===
            'QTY'            => (float)($data['QTY'] ?? 0),
            'WEIGHT'         => (float)($data['WEIGHT'] ?? 0),
            'AVG_BW'         => (float)($data['AVG_BW'] ?? 0),
            'PRICE'          => (float)($data['PRICE'] ?? 0),
            'AMOUNT'         => (float)($data['AMOUNT'] ?? 0),

            'DEAD'           => (float)($data['DEAD'] ?? 0),
            'DEAD_WEIGHT'    => (float)($data['DEAD_WEIGHT'] ?? 0),
            'SHRINK'         => (float)($data['SHRINK'] ?? 0),
            'RECEIVE_AMOUNT' => (float)($data['RECEIVE_AMOUNT'] ?? 0),

            'CREATED_AT'     => date('Y-m-d H:i:s'),
            'CREATED_BY'     => $this->session->userdata('username')
        ];

        $this->ReceiveLb_model->insert($insert);

        echo json_encode([
            'status'  => true,
            'receive' => $receiveNo,
            'message' => 'Receive Live Bird berhasil disimpan'
        ]);
    }

    /**
     * Edit (get by RECEIVE)
     */
    public function edit()
    {
        $receive = $this->input->get('receive', TRUE);
        $plant   = $this->input->get('plant', TRUE);
        $role_id = (int)$this->session->userdata('role_id');

        if (!$receive || (!$plant && $role_id === 1)) {
            echo json_encode(['status'=>false,'message'=>'Invalid RECEIVE / Plant']);
            return;
        }

        if ($role_id === 1) {
            // Admin pakai plant dari GET
            $data = $this->ReceiveLb_model->get_by_receive_and_plant($receive, $plant);
        } else {
            // User pakai plant session
            $plant = $this->session->userdata('plant');
            $data  = $this->ReceiveLb_model->get_by_receive_and_plant($receive, $plant);
        }

        echo json_encode(['status'=>true,'data'=>$data]);
    }

    /**
     * Update Receive LB
     */
    public function update()
    {
        $data = $this->input->post(NULL, TRUE);

        if (empty($data['RECEIVE'])) {
            echo json_encode(['status'=>false,'message'=>'Nomor receive tidak valid']);
            return;
        }

        $plant = $this->session->userdata('plant'); // 🔑 KUNCI

        $update = [
            'RECEIVE_DATE'   => date('Y-m-d H:i:s', strtotime($data['RECEIVE_DATE'])),

            'PEMBAYARAN'     => $data['PEMBAYARAN'] ?? null,
            'JENIS_PAY'      => $data['JENIS_PAY'] ?? null,
            'SLIP_NO'        => $data['SLIP_NO'],
            'DO'             => $data['DO'] ?? null,
            'DRIVER'         => $data['DRIVER'] ?? null,
            'NO_CAR'         => $data['NO_CAR'] ?? null,
            'SUPPLIER'       => $data['SUPPLIER'],
            'REMARK'         => $data['REMARK'] ?? null,

            'ARRIVE_SCHEDULE'=> !empty($data['ARRIVE_SCHEDULE'])
                                    ? date('Y-m-d H:i:s', strtotime($data['ARRIVE_SCHEDULE']))
                                    : null,
            'DEPART_SCHEDULE'=> !empty($data['DEPART_SCHEDULE'])
                                    ? date('Y-m-d H:i:s', strtotime($data['DEPART_SCHEDULE']))
                                    : null,

            'QTY'            => (float)($data['QTY'] ?? 0),
            'WEIGHT'         => (float)($data['WEIGHT'] ?? 0),
            'AVG_BW'         => (float)($data['AVG_BW'] ?? 0),
            'PRICE'          => (float)($data['PRICE'] ?? 0),
            'AMOUNT'         => (float)($data['AMOUNT'] ?? 0),

            'DEAD'           => (float)($data['DEAD'] ?? 0),
            'DEAD_WEIGHT'    => (float)($data['DEAD_WEIGHT'] ?? 0),
            'SHRINK'         => (float)($data['SHRINK'] ?? 0),
            'RECEIVE_AMOUNT' => (float)($data['RECEIVE_AMOUNT'] ?? 0),

            'UPDATED_AT'     => date('Y-m-d H:i:s'),
            'UPDATED_BY'     => $this->session->userdata('username')
        ];

        // 🔥 PAKAI PLANT + RECEIVE
        $this->ReceiveLb_model->update($plant, $data['RECEIVE'], $update);

        echo json_encode([
            'status'  => true,
            'message' => 'Receive Live Bird berhasil diperbarui'
        ]);
    }

    /**
     * Soft delete
     */
    public function remove()
    {
        $receive = $this->input->post('receive', TRUE);
        $plant   = $this->input->post('plant', TRUE);
        $role_id = (int)$this->session->userdata('role_id');

        if (!$receive || !$plant) {
            echo json_encode(['status'=>false,'message'=>'Invalid parameter']);
            return;
        }

        // 🔐 non-admin hanya boleh plant sendiri
        if ($role_id !== 1 && $plant !== $this->session->userdata('plant')) {
            echo json_encode(['status'=>false,'message'=>'Unauthorized']);
            return;
        }

        $this->ReceiveLb_model->soft_delete(
            $plant,
            $receive,
            $this->session->userdata('username')
        );

        echo json_encode([
            'status'  => true,
            'message' => 'Receive LB berhasil dihapus'
        ]);
    }

}
