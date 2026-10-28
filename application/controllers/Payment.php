<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Payment PO menggunakan permission modul PO.
        if (!has_permission('inventory_po')) {
            show_404();
        }

        $this->load->model('Payment_model');
        $this->load->library('session');
        $this->load->helper(['url']);
    }

    private function json($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function user_plants()
    {
        return array_map('strval', $this->Payment_model->get_user_plants(
            (string)$this->session->userdata('username')
        ));
    }

    private function validate_plant($plant)
    {
        $roleId = (int)$this->session->userdata('role_id');
        return $roleId === 1 || in_array((string)$plant, $this->user_plants(), true);
    }

    public function index()
    {
        $this->load->view('templates/header', ['title' => 'Payment PO']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/payment/list');
        $this->load->view('templates/footer');
    }

    public function get_plant()
    {
        $roleId = (int)$this->session->userdata('role_id');
        $rows = $roleId === 1
            ? $this->Payment_model->get_all_plants()
            : $this->Payment_model->get_plants_by_codes($this->user_plants());

        $this->json($rows);
    }

    public function get_supplier()
    {
        $plant = trim((string)$this->input->get('plant', true));
        if ($plant === '' || !$this->validate_plant($plant)) {
            $this->json([]);
            return;
        }

        $payment = trim((string)$this->input->get('payment', true));
        $this->json($this->Payment_model->get_suppliers_with_open_po($plant, $this->input->get('q', true), $payment !== '' ? $payment : null));
    }

    public function get_open_po()
    {
        $plant = trim((string)$this->input->get('plant', true));
        $supplier = trim((string)$this->input->get('supplier', true));
        $payment = trim((string)$this->input->get('payment', true)); // optional when editing

        if ($plant === '' || $supplier === '' || !$this->validate_plant($plant)) {
            $this->json([]);
            return;
        }

        $this->json($this->Payment_model->get_open_pos($plant, $supplier, $payment));
    }

    public function preview_fifo()
    {
        $plant = trim((string)$this->input->post('PLANT', true));
        $supplier = trim((string)$this->input->post('SUPPLIER', true));
        $amount = $this->decimal($this->input->post('TOTAL_INPUT'));
        $excludePayment = trim((string)$this->input->post('PAYMENT', true));

        if ($plant === '' || $supplier === '' || $amount <= 0 || !$this->validate_plant($plant)) {
            $this->json(['status' => false, 'message' => 'Plant, supplier, dan nominal pembayaran wajib valid.']);
            return;
        }

        $allocations = $this->Payment_model->simulate_fifo($plant, $supplier, $amount, $excludePayment !== '' ? $excludePayment : null);
        $allocated = array_sum(array_column($allocations, 'AMOUNT_ALLOCATED'));

        $this->json([
            'status' => true,
            'data' => $allocations,
            'allocated' => round($allocated, 2),
            'unallocated' => round($amount - $allocated, 2),
            'exact' => abs($amount - $allocated) < 0.01
        ]);
    }

    public function load_data()
    {
        $page = max(1, (int)$this->input->get('page'));
        $limit = min(100, max(1, (int)$this->input->get('limit')));
        $start = ($page - 1) * $limit;
        $search = trim((string)$this->input->get('search', true));
        $plant = trim((string)$this->input->get('plant', true));

        $allowedPlants = $this->user_plants();
        if ((int)$this->session->userdata('role_id') !== 1) {
            if ($plant !== '' && !in_array($plant, $allowedPlants, true)) {
                $this->json(['status' => false, 'message' => 'Plant tidak memiliki akses.', 'rows' => [], 'total' => 0]);
                return;
            }
            if ($plant === '') {
                $plant = $allowedPlants;
            }
        }

        $rows = $this->Payment_model->get_data($limit, $start, $search, $plant);
        $total = $this->Payment_model->count_data($search, $plant);

        $this->json([
            'status' => true,
            'rows' => $rows,
            'total' => (int)$total,
            'page' => $page,
            'pages' => max(1, (int)ceil($total / $limit))
        ]);
    }

    public function detail()
    {
        $payment = trim((string)$this->input->get('payment', true));
        $plant = trim((string)$this->input->get('plant', true));

        if ($payment === '' || $plant === '' || !$this->validate_plant($plant)) {
            $this->json(['status' => false, 'message' => 'Parameter pembayaran tidak valid.']);
            return;
        }

        $header = $this->Payment_model->get_header($plant, $payment);
        if (!$header) {
            $this->json(['status' => false, 'message' => 'Pembayaran tidak ditemukan.']);
            return;
        }

        $this->json([
            'status' => true,
            'header' => $header,
            'detail' => $this->Payment_model->get_details($plant, $payment)
        ]);
    }

    public function create()
    {
        $this->save_payment(false);
    }

    public function update()
    {
        $this->save_payment(true);
    }

    private function save_payment($isUpdate)
    {
        $post = $this->input->post(NULL, true);
        $plant = trim((string)($post['PLANT'] ?? ''));
        $supplier = trim((string)($post['SUPPLIER'] ?? ''));
        $date = trim((string)($post['PAYMENT_DATE'] ?? ''));
        $method = strtoupper(trim((string)($post['PEMBAYARAN'] ?? '')));
        $mode = strtoupper(trim((string)($post['PAYMENT_MODE'] ?? 'FIFO')));
        $slip = trim((string)($post['SLIP_NO'] ?? ''));
        $remark = trim((string)($post['REMARK'] ?? ''));
        $paymentNo = trim((string)($post['PAYMENT'] ?? ''));
        $amount = round($this->decimal($post['TOTAL_INPUT'] ?? 0), 2);
        $username = (string)$this->session->userdata('username');

        if ($plant === '' || !$this->validate_plant($plant)) {
            $this->json(['status' => false, 'message' => 'Plant tidak valid atau tidak memiliki akses.']);
            return;
        }
        if ($supplier === '' || $date === '' || $method === '') {
            $this->json(['status' => false, 'message' => 'Supplier, tanggal, dan metode pembayaran wajib diisi.']);
            return;
        }
        if (!in_array($mode, ['FIFO', 'MANUAL'], true)) {
            $this->json(['status' => false, 'message' => 'Mode pembayaran tidak valid.']);
            return;
        }
        if ($amount <= 0) {
            $this->json(['status' => false, 'message' => 'Nominal pembayaran harus lebih dari 0.']);
            return;
        }
        if (!in_array($method, ['CASH', 'TRANSFER'], true)) {
            $this->json(['status' => false, 'message' => 'Metode pembayaran harus CASH atau TRANSFER.']);
            return;
        }
        if ($isUpdate && $paymentNo === '') {
            $this->json(['status' => false, 'message' => 'Nomor pembayaran tidak ditemukan.']);
            return;
        }

        $manualDetails = $post['DETAIL'] ?? [];
        if ($mode === 'MANUAL' && !is_array($manualDetails)) {
            $this->json(['status' => false, 'message' => 'Format detail pembayaran tidak valid.']);
            return;
        }

        $this->db->trans_begin();
        try {
            // Kunci PO milik supplier ini selama validasi dan alokasi agar tidak dibayar bersamaan.
            $this->Payment_model->lock_supplier_pos($plant, $supplier);

            if ($isUpdate) {
                $existing = $this->Payment_model->get_header_for_update($plant, $paymentNo);
                if (!$existing || $existing['STATUS'] !== 'POSTED') {
                    throw new Exception('Pembayaran tidak ditemukan atau sudah dibatalkan.');
                }

                $oldDetails = $this->Payment_model->get_details($plant, $paymentNo);
                foreach ($oldDetails as $old) {
                    $this->Payment_model->lock_po_row($plant, $old['PO_NO']);
                }
                $this->Payment_model->soft_delete_details($plant, $paymentNo, $username);
                foreach ($oldDetails as $old) {
                    $this->Payment_model->recalculate_po_balance($plant, $old['PO_NO']);
                }
            } else {
                $paymentNo = $this->Payment_model->generate_payment_no($plant);
            }

            if ($mode === 'FIFO') {
                $details = $this->Payment_model->simulate_fifo($plant, $supplier, $amount, $isUpdate ? $paymentNo : null);
            } else {
                $details = $this->normalize_manual_details($manualDetails);
            }

            if (empty($details)) {
                throw new Exception('Tidak ada PO terbuka untuk dialokasikan.');
            }

            $allocatedTotal = 0;
            $poSeen = [];
            $seq = 1;
            $validatedDetails = [];

            foreach ($details as $row) {
                $poNo = trim((string)($row['PO_NO'] ?? ''));
                $allocate = round($this->decimal($row['AMOUNT_ALLOCATED'] ?? 0), 2);
                if ($poNo === '' || $allocate <= 0) {
                    throw new Exception('Setiap detail harus memiliki PO dan nominal alokasi lebih dari 0.');
                }
                if (isset($poSeen[$poNo])) {
                    throw new Exception('PO ' . $poNo . ' terpilih lebih dari satu kali.');
                }
                $poSeen[$poNo] = true;

                $po = $this->Payment_model->get_po_for_allocation($plant, $supplier, $poNo);
                if (!$po) {
                    throw new Exception('PO ' . $poNo . ' tidak ditemukan, bukan milik supplier ini, atau sudah dihapus.');
                }

                $outstanding = round((float)$po['OUTSTANDING'], 2);
                if ($allocate - $outstanding > 0.009) {
                    throw new Exception('Alokasi PO ' . $poNo . ' melebihi sisa tagihan Rp ' . number_format($outstanding, 2, ',', '.'));
                }

                $validatedDetails[] = [
                    'PAYMENT' => $paymentNo,
                    'PLANT' => $plant,
                    'SEQ_NO' => $seq++,
                    'PO_NO' => $poNo,
                    'PO_TOTAL' => round((float)$po['TOTAL'], 2),
                    'PAID_BEFORE' => round((float)$po['PAID_AMOUNT_CALC'], 2),
                    'AMOUNT_ALLOCATED' => $allocate,
                    'REMAINING_AFTER' => round(max(0, $outstanding - $allocate), 2),
                    'REMARK' => trim((string)($row['REMARK'] ?? '')),
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'CREATED_BY' => $username
                ];
                $allocatedTotal = round($allocatedTotal + $allocate, 2);
            }

            if (abs($allocatedTotal - $amount) > 0.009) {
                throw new Exception(
                    'Total alokasi (Rp ' . number_format($allocatedTotal, 2, ',', '.') .
                    ') harus sama dengan total pembayaran (Rp ' . number_format($amount, 2, ',', '.') . ').'
                );
            }

            if (!$isUpdate) {
                $header = [
                    'PAYMENT' => $paymentNo,
                    'PLANT' => $plant,
                    'PAYMENT_DATE' => $date . ' 00:00:00',
                    'PAYMENT_MODE' => $mode,
                    'PEMBAYARAN' => $method,
                    'SLIP_NO' => $slip ?: null,
                    'SUPPLIER' => $supplier,
                    'REMARK' => $remark ?: null,
                    'TOTAL' => $allocatedTotal,
                    'STATUS' => 'POSTED',
                    'CREATED_AT' => date('Y-m-d H:i:s'),
                    'CREATED_BY' => $username
                ];
                $this->db->insert('abc_mst_payment', $header);
            } else {
                $header = [
                    'PAYMENT_DATE' => $date . ' 00:00:00',
                    'PAYMENT_MODE' => $mode,
                    'PEMBAYARAN' => $method,
                    'SLIP_NO' => $slip ?: null,
                    'SUPPLIER' => $supplier,
                    'REMARK' => $remark ?: null,
                    'TOTAL' => $allocatedTotal,
                    'UPDATED_AT' => date('Y-m-d H:i:s'),
                    'UPDATED_BY' => $username
                ];
                $this->db->where('PAYMENT', $paymentNo)->where('PLANT', $plant)
                    ->where('STATUS', 'POSTED')->where('DELETED IS NULL', null, false)
                    ->update('abc_mst_payment', $header);
            }

            if (!$this->Payment_model->insert_details($validatedDetails)) {
                throw new Exception('Gagal menyimpan detail alokasi pembayaran.');
            }

            foreach ($validatedDetails as $detail) {
                $this->Payment_model->recalculate_po_balance($plant, $detail['PO_NO']);
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Transaksi database gagal.');
            }

            $this->db->trans_commit();
            $this->json([
                'status' => true,
                'message' => $isUpdate ? 'Pembayaran berhasil diperbarui.' : 'Pembayaran berhasil disimpan.',
                'payment' => $paymentNo
            ]);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function normalize_manual_details($details)
    {
        $result = [];
        foreach ($details as $row) {
            if (!is_array($row)) continue;
            $amount = round($this->decimal($row['AMOUNT_ALLOCATED'] ?? 0), 2);
            if ($amount <= 0) continue;
            $result[] = [
                'PO_NO' => trim((string)($row['PO_NO'] ?? '')),
                'AMOUNT_ALLOCATED' => $amount,
                'REMARK' => trim((string)($row['REMARK'] ?? ''))
            ];
        }
        return $result;
    }

    public function remove()
    {
        $payment = trim((string)$this->input->post('payment', true));
        $plant = trim((string)$this->input->post('plant', true));
        $username = (string)$this->session->userdata('username');

        if ($payment === '' || $plant === '' || !$this->validate_plant($plant)) {
            $this->json(['status' => false, 'message' => 'Parameter pembayaran tidak valid.']);
            return;
        }

        $this->db->trans_begin();
        try {
            $header = $this->Payment_model->get_header_for_update($plant, $payment);
            if (!$header || $header['STATUS'] !== 'POSTED') {
                throw new Exception('Pembayaran tidak ditemukan atau sudah dibatalkan.');
            }

            $details = $this->Payment_model->get_details($plant, $payment);
            // Pertahankan detail sebagai histori; status VOID membuat alokasi tidak dihitung lagi.
            $this->db->where('PAYMENT', $payment)->where('PLANT', $plant)
                ->update('abc_mst_payment', [
                    'STATUS' => 'VOID',
                    'UPDATED_AT' => date('Y-m-d H:i:s'),
                    'UPDATED_BY' => $username
                ]);

            foreach ($details as $row) {
                $this->Payment_model->recalculate_po_balance($plant, $row['PO_NO']);
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Gagal membatalkan pembayaran.');
            }
            $this->db->trans_commit();
            $this->json(['status' => true, 'message' => 'Pembayaran berhasil dibatalkan.']);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function decimal($value)
    {
        if ($value === null || $value === '') return 0;
        if (is_numeric($value)) return (float)$value;
        $value = trim((string)$value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return (float)$value;
    }
}
