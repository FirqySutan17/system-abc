<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    public function get_user_plants($username)
    {
        $row = $this->db->select('plant')->where('username', $username)->get('users')->row();
        if (!$row || empty($row->plant)) return [];
        $plants = json_decode($row->plant, true);
        return is_array($plants) ? array_map('strval', $plants) : [];
    }

    public function get_all_plants()
    {
        return $this->db->select('CODE AS id, CODE_NAME AS text')
            ->from('abc_cd_code')->where('HEAD_CODE', 'PLANT')->where('CODE <>', '*')
            ->where('USE_YN', 'Y')->order_by('CODE_NAME', 'ASC')->get()->result_array();
    }

    public function get_plants_by_codes($codes)
    {
        if (empty($codes)) return [];
        return $this->db->select('CODE AS id, CODE_NAME AS text')
            ->from('abc_cd_code')->where('HEAD_CODE', 'PLANT')->where_in('CODE', $codes)
            ->where('CODE <>', '*')->order_by('CODE_NAME', 'ASC')->get()->result_array();
    }

    public function lock_supplier_pos($plant, $supplier)
    {
        return $this->db->query("SELECT PO FROM abc_mst_po WHERE PLANT = ? AND SUPPLIER = ? AND DELETED IS NULL FOR UPDATE", [$plant, $supplier])->result_array();
    }

    public function lock_po_row($plant, $po)
    {
        return $this->db->query("SELECT PO FROM abc_mst_po WHERE PLANT = ? AND PO = ? AND DELETED IS NULL FOR UPDATE", [$plant, $po])->row_array();
    }

    public function get_suppliers_with_open_po($plant, $search = '', $excludePayment = null)
    {
        $excludeSql = ($excludePayment !== null && $excludePayment !== '')
            ? ' AND h.PAYMENT <> ' . $this->db->escape($excludePayment)
            : '';
        $sql = "
            SELECT DISTINCT c.CUST AS id, CONCAT(c.CUST, ' - ', c.FULL_NAME) AS text
            FROM abc_cd_customer c
            INNER JOIN abc_mst_po p
                ON p.SUPPLIER COLLATE utf8mb4_unicode_ci = c.CUST COLLATE utf8mb4_unicode_ci
               AND p.PLANT = ?
               AND p.DELETED IS NULL
            WHERE c.CUST_KIND = 'SUPPLIER'
              AND c.CUST_CLASS = 'SUPPLIER'
              AND c.STATUS = 'Y'
              AND p.TOTAL > 0
              AND (
                    COALESCE(p.TOTAL,0) -
                    COALESCE((
                        SELECT SUM(d.AMOUNT_ALLOCATED)
                        FROM abc_mst_payment_detail d
                        INNER JOIN abc_mst_payment h
                          ON h.PAYMENT = d.PAYMENT AND h.PLANT = d.PLANT
                        WHERE d.PO_NO = p.PO AND d.PLANT = p.PLANT
                          AND d.DELETED IS NULL
                          AND h.DELETED IS NULL AND h.STATUS = 'POSTED'
                          " . $excludeSql . "
                    ),0)
                  ) > 0
        ";
        $params = [$plant];
        if ($search !== '') {
            $sql .= " AND (c.CUST LIKE ? OR c.FULL_NAME LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $sql .= " ORDER BY c.CUST ASC";
        return $this->db->query($sql, $params)->result_array();
    }

    public function get_open_pos($plant, $supplier, $excludePayment = null)
    {
        $sql = "
            SELECT p.PO, p.PLANT, p.PO_DATE, p.SUPPLIER, p.MATERIAL,
                   m.MATERIAL_NAME,
                   COALESCE(p.TOTAL,0) AS TOTAL,
                   COALESCE(p.PAID_AMOUNT,0) AS STORED_PAID_AMOUNT,
                   COALESCE((
                       SELECT SUM(d.AMOUNT_ALLOCATED)
                       FROM abc_mst_payment_detail d
                       INNER JOIN abc_mst_payment h
                         ON h.PAYMENT = d.PAYMENT AND h.PLANT = d.PLANT
                       WHERE d.PO_NO = p.PO AND d.PLANT = p.PLANT
                         AND d.DELETED IS NULL
                         AND h.DELETED IS NULL AND h.STATUS = 'POSTED'
                         " . ($excludePayment !== null && $excludePayment !== '' ? " AND h.PAYMENT <> " . $this->db->escape($excludePayment) : "") . "
                   ),0) AS PAID_AMOUNT_CALC,
                   GREATEST(COALESCE(p.TOTAL,0) - COALESCE((
                       SELECT SUM(d.AMOUNT_ALLOCATED)
                       FROM abc_mst_payment_detail d
                       INNER JOIN abc_mst_payment h
                         ON h.PAYMENT = d.PAYMENT AND h.PLANT = d.PLANT
                       WHERE d.PO_NO = p.PO AND d.PLANT = p.PLANT
                         AND d.DELETED IS NULL
                         AND h.DELETED IS NULL AND h.STATUS = 'POSTED'
                         " . ($excludePayment !== null && $excludePayment !== '' ? " AND h.PAYMENT <> " . $this->db->escape($excludePayment) : "") . "
                   ),0),0) AS OUTSTANDING
            FROM abc_mst_po p
            LEFT JOIN abc_cd_material m
              ON m.MATERIAL COLLATE utf8mb4_unicode_ci = p.MATERIAL COLLATE utf8mb4_unicode_ci
            WHERE p.PLANT = ?
              AND p.SUPPLIER = ?
              AND p.DELETED IS NULL
              AND COALESCE(p.TOTAL,0) > 0
            ORDER BY p.PO_DATE ASC, p.PO ASC
        ";
        return $this->db->query($sql, [$plant, $supplier])->result_array();
    }

    public function get_po_for_allocation($plant, $supplier, $po)
    {
        $rows = $this->get_open_pos($plant, $supplier);
        foreach ($rows as $row) {
            if ((string)$row['PO'] === (string)$po && (float)$row['OUTSTANDING'] > 0) {
                return $row;
            }
        }
        return null;
    }

    public function simulate_fifo($plant, $supplier, $amount, $excludePayment = null)
    {
        $amount = round((float)$amount, 2);
        $result = [];
        foreach ($this->get_open_pos($plant, $supplier, $excludePayment) as $po) {
            if ($amount <= 0) break;
            $outstanding = round((float)$po['OUTSTANDING'], 2);
            if ($outstanding <= 0) continue;
            $allocation = round(min($amount, $outstanding), 2);
            if ($allocation <= 0) continue;
            $result[] = [
                'PO_NO' => $po['PO'],
                'PO_DATE' => $po['PO_DATE'],
                'MATERIAL' => $po['MATERIAL'],
                'MATERIAL_NAME' => $po['MATERIAL_NAME'],
                'PO_TOTAL' => (float)$po['TOTAL'],
                'PAID_BEFORE' => (float)$po['PAID_AMOUNT_CALC'],
                'OUTSTANDING' => $outstanding,
                'AMOUNT_ALLOCATED' => $allocation,
                'REMARK' => ''
            ];
            $amount = round($amount - $allocation, 2);
        }
        return $result;
    }

    public function generate_payment_no($plant)
    {
        $prefix = 'PAY' . date('ymd');
        $row = $this->db->select_max('PAYMENT', 'MAX_PAYMENT')
            ->from('abc_mst_payment')
            ->where('PLANT', $plant)
            ->like('PAYMENT', $prefix, 'after')
            ->get()->row_array();

        $max = $row['MAX_PAYMENT'] ?? '';
        $seq = ($max !== '') ? ((int)substr($max, -4) + 1) : 1;
        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function insert_details($rows)
    {
        if (empty($rows)) return false;
        return $this->db->insert_batch('abc_mst_payment_detail', $rows) !== false;
    }

    public function get_header($plant, $payment)
    {
        return $this->db->select("h.*, c.FULL_NAME AS SUPPLIER_NAME, p.CODE_NAME AS PLANT_NAME")
            ->from('abc_mst_payment h')
            ->join('abc_cd_customer c', 'c.CUST = h.SUPPLIER', 'left')
            ->join('abc_cd_code p', "p.CODE = h.PLANT AND p.HEAD_CODE = 'PLANT'", 'left')
            ->where('h.PLANT', $plant)->where('h.PAYMENT', $payment)
            ->where('h.DELETED IS NULL', null, false)->get()->row_array();
    }

    public function get_header_for_update($plant, $payment)
    {
        return $this->db->query("
            SELECT * FROM abc_mst_payment
            WHERE PAYMENT = ? AND PLANT = ? AND DELETED IS NULL
            FOR UPDATE
        ", [$payment, $plant])->row_array();
    }

    public function get_details($plant, $payment)
    {
        return $this->db->select('d.*, p.PO_DATE, p.MATERIAL, m.MATERIAL_NAME, p.TOTAL AS CURRENT_PO_TOTAL')
            ->from('abc_mst_payment_detail d')
            ->join('abc_mst_po p', 'p.PO = d.PO_NO AND p.PLANT = d.PLANT', 'left')
            ->join('abc_cd_material m', 'm.MATERIAL = p.MATERIAL', 'left')
            ->where('d.PAYMENT', $payment)->where('d.PLANT', $plant)
            ->where('d.DELETED IS NULL', null, false)
            ->order_by('d.SEQ_NO', 'ASC')->get()->result_array();
    }

    public function soft_delete_details($plant, $payment, $username)
    {
        return $this->db->where('PLANT', $plant)->where('PAYMENT', $payment)
            ->where('DELETED IS NULL', null, false)
            ->update('abc_mst_payment_detail', [
                'DELETED' => date('Y-m-d H:i:s'),
                'UPDATED_AT' => date('Y-m-d H:i:s'),
                'UPDATED_BY' => $username
            ]);
    }

    public function recalculate_po_balance($plant, $po)
    {
        $this->lock_po_row($plant, $po);
        $row = $this->db->query("
            SELECT p.TOTAL,
                   COALESCE(SUM(
                       CASE WHEN h.STATUS = 'POSTED' AND h.DELETED IS NULL AND d.DELETED IS NULL
                            THEN d.AMOUNT_ALLOCATED ELSE 0 END
                   ),0) AS PAID
            FROM abc_mst_po p
            LEFT JOIN abc_mst_payment_detail d
              ON d.PO_NO = p.PO AND d.PLANT = p.PLANT
            LEFT JOIN abc_mst_payment h
              ON h.PAYMENT = d.PAYMENT AND h.PLANT = d.PLANT
            WHERE p.PO = ? AND p.PLANT = ? AND p.DELETED IS NULL
            GROUP BY p.PO, p.PLANT, p.TOTAL
        ", [$po, $plant])->row_array();

        if (!$row) return false;
        $total = round((float)$row['TOTAL'], 2);
        $paid = round((float)$row['PAID'], 2);
        $remaining = round(max(0, $total - $paid), 2);
        if ($paid > $total + 0.009) {
            throw new Exception('Akumulasi pembayaran PO ' . $po . ' melebihi total PO.');
        }
        $status = $paid <= 0 ? 'UNPAID' : ($remaining <= 0.009 ? 'PAID' : 'PARTIAL');

        return $this->db->where('PO', $po)->where('PLANT', $plant)->update('abc_mst_po', [
            'PAID_AMOUNT' => $paid,
            'REMAINING' => $remaining,
            'PAYMENT_STATUS' => $status,
            'UPDATED_AT' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_data($limit, $start, $search = '', $plant = '')
    {
        $this->db->select("h.*, c.FULL_NAME AS SUPPLIER_NAME, pc.CODE_NAME AS PLANT_NAME");
        $this->db->from('abc_mst_payment h');
        $this->db->join('abc_cd_customer c', 'c.CUST = h.SUPPLIER', 'left');
        $this->db->join('abc_cd_code pc', "pc.CODE = h.PLANT AND pc.HEAD_CODE = 'PLANT'", 'left');
        $this->db->where('h.DELETED IS NULL', null, false);

        if (is_array($plant)) {
            if (empty($plant)) $this->db->where('1 = 0', null, false);
            else $this->db->where_in('h.PLANT', $plant);
        } elseif ($plant !== '') {
            $this->db->where('h.PLANT', $plant);
        }

        if ($search !== '') {
            $this->db->group_start()->like('h.PAYMENT', $search)
                ->or_like('h.SLIP_NO', $search)->or_like('h.SUPPLIER', $search)
                ->or_like('c.FULL_NAME', $search)->or_like('h.REMARK', $search)
                ->group_end();
        }

        return $this->db->order_by('h.PAYMENT_DATE', 'DESC')->order_by('h.PAYMENT', 'DESC')
            ->limit((int)$limit, (int)$start)->get()->result_array();
    }

    public function count_data($search = '', $plant = '')
    {
        $this->db->from('abc_mst_payment h');
        $this->db->join('abc_cd_customer c', 'c.CUST = h.SUPPLIER', 'left');
        $this->db->where('h.DELETED IS NULL', null, false);

        if (is_array($plant)) {
            if (empty($plant)) $this->db->where('1 = 0', null, false);
            else $this->db->where_in('h.PLANT', $plant);
        } elseif ($plant !== '') {
            $this->db->where('h.PLANT', $plant);
        }

        if ($search !== '') {
            $this->db->group_start()->like('h.PAYMENT', $search)
                ->or_like('h.SLIP_NO', $search)->or_like('h.SUPPLIER', $search)
                ->or_like('c.FULL_NAME', $search)->or_like('h.REMARK', $search)
                ->group_end();
        }
        return $this->db->count_all_results();
    }
}
