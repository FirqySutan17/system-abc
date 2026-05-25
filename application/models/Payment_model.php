<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ---------------------------------------------------------
       LIST / COUNT (for table ajax)
    --------------------------------------------------------- */

    public function get_data(
        $limit,
        $start,
        $role_id,
        $plants,
        $search = '',
        $order = 'PAYMENT_DATE',
        $dir = 'DESC',
        $pembayaran = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        /*
        |--------------------------------------------------------------------------
        | ALLOWED ORDER
        |--------------------------------------------------------------------------
        */

        $allowedOrder = [
            'PAYMENT',
            'PAYMENT_DATE',
            'PEMBAYARAN',
            'SLIP_NO',
            'PLANT',
            'CREATED_AT'
        ];

        if(!in_array($order, $allowedOrder)){

            $order = 'PAYMENT_DATE';

        }

        $dir = strtoupper($dir) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $this->db->select("
            p.*,

            plant.CODE_NAME AS PLANT_NAME,

            supplier.FULL_NAME AS SUPPLIER_NAME,

            COUNT(DISTINCT d.SEQ_NO)
                AS TOTAL_ITEM,

            SUM(d.BERAT)
                AS TOTAL_BERAT,

            SUM(d.TOTAL)
                AS GRAND_TOTAL
        ", false);

        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from('abc_mst_payment p');

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.CODE COLLATE utf8mb4_unicode_ci =
                p.PLANT COLLATE utf8mb4_unicode_ci
                AND plant.HEAD_CODE = 'PLANT'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            "
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci

                AND supplier.CUST_KIND = 'SUPPLIER'

                AND supplier.CUST_CLASS = 'SUPPLIER'

                AND supplier.STATUS = 'Y'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_payment_detail d',
            '
                d.PAYMENT = p.PAYMENT
                AND d.PLANT = p.PLANT
                AND d.DELETED IS NULL
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER DELETED
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'p.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | ROLE FILTER
        |--------------------------------------------------------------------------
        */

        if($role_id != 1){

            if(empty($plants)){

                return [];

            }

            $this->db->where_in(
                'p.PLANT',
                $plants
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if($search != ''){

            $this->db->group_start();

            $this->db->like(
                'p.PAYMENT',
                $search
            );

            $this->db->or_like(
                'p.PEMBAYARAN',
                $search
            );

            $this->db->or_like(
                'p.SLIP_NO',
                $search
            );

            $this->db->or_like(
                'p.REMARK',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'plant.CODE_NAME',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT FILTER
        |--------------------------------------------------------------------------
        */

        if(!empty($pembayaran)){

            $this->db->where(
                'p.PEMBAYARAN',
                $pembayaran
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'p.PAYMENT_DATE >=',
                $dateFrom . ' 00:00:00'
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'p.PAYMENT_DATE <=',
                $dateTo . ' 23:59:59'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by('p.PAYMENT');

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'p.' . $order,
            $dir
        );

        /*
        |--------------------------------------------------------------------------
        | LIMIT
        |--------------------------------------------------------------------------
        */

        $this->db->limit(
            (int)$limit,
            (int)$start
        );

        return $this->db
            ->get()
            ->result_array();
    }

    public function count_data(
        $role_id,
        $plants,
        $search = '',
        $pembayaran = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        /*
        |--------------------------------------------------------------------------
        | FROM
        |--------------------------------------------------------------------------
        */

        $this->db->from('abc_mst_payment p');

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.CODE COLLATE utf8mb4_unicode_ci =
                p.PLANT COLLATE utf8mb4_unicode_ci
                AND plant.HEAD_CODE = 'PLANT'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            "
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci

                AND supplier.CUST_KIND = 'SUPPLIER'

                AND supplier.CUST_CLASS = 'SUPPLIER'

                AND supplier.STATUS = 'Y'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_payment_detail d',
            '
                d.PAYMENT = p.PAYMENT
                AND d.PLANT = p.PLANT
                AND d.DELETED IS NULL
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER DELETED
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'p.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | ROLE FILTER
        |--------------------------------------------------------------------------
        */

        if($role_id != 1){

            if(empty($plants)){

                return 0;

            }

            $this->db->where_in(
                'p.PLANT',
                $plants
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if($search != ''){

            $this->db->group_start();

            $this->db->like(
                'p.PAYMENT',
                $search
            );

            $this->db->or_like(
                'p.PEMBAYARAN',
                $search
            );

            $this->db->or_like(
                'p.SLIP_NO',
                $search
            );

            $this->db->or_like(
                'p.REMARK',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'plant.CODE_NAME',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT FILTER
        |--------------------------------------------------------------------------
        */

        if(!empty($pembayaran)){

            $this->db->where(
                'p.PEMBAYARAN',
                $pembayaran
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'p.PAYMENT_DATE >=',
                $dateFrom . ' 00:00:00'
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'p.PAYMENT_DATE <=',
                $dateTo . ' 23:59:59'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP
        |--------------------------------------------------------------------------
        */

        $this->db->group_by('p.PAYMENT');

        return $this->db
            ->get()
            ->num_rows();
    }

    public function search_supplier($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('abc_cd_customer');
        $this->db->where('CUST_KIND', 'SUPPLIER');
        $this->db->where('CUST_CLASS', 'SUPPLIER');
        $this->db->where('STATUS', 'Y');

        if ($q) {
            $this->db->group_start();
            $this->db->like('CUST', $q);
            $this->db->or_like('FULL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('CUST', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'].' - '.$r['name']
            ];
        }
        return $out;
    }

    public function get_receive_for_modal($supplier, $plant)
    {
        $this->db->select("
            r.PLANT,
            c.code_name AS PLANT_NAME,
            r.RECEIVE,
            r.RECEIVE_DATE,
            r.SUPPLIER,
            s.FULL_NAME AS SUPPLIER_NAME,

            d.MATERIAL,
            m.MATERIAL_NAME,

            SUM(d.BERAT)  AS TOTAL_BERAT,
            SUM(d.JUMLAH) AS TOTAL_QTY,
            d.HARGA,
            SUM(d.JUMLAH * d.HARGA) AS TOTAL
        ");

        $this->db->from('abc_mst_receive r');
        $this->db->join('abc_mst_receive_detail d', 'r.RECEIVE = d.RECEIVE AND r.PLANT = d.PLANT');
        $this->db->join('abc_cd_customer s', 'r.SUPPLIER = s.CUST', 'left');
        $this->db->join('abc_cd_code c', 'c.code = r.PLANT AND c.head_code = "AJ"', 'left');
        $this->db->join(
            'abc_cd_material m',
            'm.material COLLATE utf8mb4_unicode_ci = d.MATERIAL COLLATE utf8mb4_unicode_ci',
            'left',
            false
        );

        $this->db->where('r.SUPPLIER', $supplier);
        $this->db->where('r.PLANT', $plant);
        $this->db->where('r.DELETED IS NULL', null, false);

        $this->db->group_start();
        $this->db->where('d.STATUS IS NULL', null, false);
        $this->db->or_where('d.STATUS !=', 'Y');
        $this->db->group_end();

        $this->db->group_by('
            r.PLANT,
            c.code_name,
            r.RECEIVE,
            r.RECEIVE_DATE,
            s.FULL_NAME,
            d.MATERIAL,
            m.MATERIAL_NAME,
            d.HARGA
        ');

        $this->db->order_by('r.RECEIVE_DATE', 'DESC');

        return $this->db->get()->result_array();
    }

    public function get_po_picker(
        $plant,
        $supplier,
        $search = ''
    )
    {
        $this->db->select("
            p.PO,

            p.PLANT,

            p.SUPPLIER,

            supplier.FULL_NAME AS SUPPLIER_NAME,

            p.MATERIAL,

            material.MATERIAL_NAME,

            p.JUMLAH,

            p.BERAT,

            p.HARGA,

            p.TOTAL,

            p.REMARK
        ", false);

        $this->db->from('abc_mst_po p');

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            "
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                p.SUPPLIER COLLATE utf8mb4_unicode_ci
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_material material',
            "
                material.MATERIAL COLLATE utf8mb4_unicode_ci =
                p.MATERIAL COLLATE utf8mb4_unicode_ci
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $this->db->where(
            'p.PLANT',
            $plant
        );

        $this->db->where(
            'p.SUPPLIER',
            $supplier
        );

        $this->db->where(
            'p.DELETED IS NULL',
            null,
            false
        );

        $this->db->where(
            'p.STATUS',
            'RECEIVED'
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if(!empty($search)){

            $this->db->group_start();

            $this->db->like(
                'p.PO',
                $search
            );

            $this->db->or_like(
                'p.MATERIAL',
                $search
            );

            $this->db->or_like(
                'material.MATERIAL_NAME',
                $search
            );

            $this->db->group_end();
        }

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'p.PO',
            'DESC'
        );

        return $this->db
            ->get()
            ->result_array();
    }

    /* ---------------------------------------------------------
       AUTO NUMBER GENERATOR
    --------------------------------------------------------- */

    public function generate_payment_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today.'PY';

        $row = $this->db
            ->select('PAYMENT')
            ->from('abc_mst_payment')
            ->where('PLANT',$plant)
            ->like('PAYMENT',$prefix,'after')
            ->order_by('PAYMENT','DESC')
            ->limit(1)
            ->get()
            ->row();

        $seq = $row ? ((int)substr($row->PAYMENT,-4) + 1) : 1;

        return $prefix.str_pad($seq,4,'0',STR_PAD_LEFT);
    }

    public function generate_slip_no($plant)
    {
        $today  = date('Ymd');
        $prefix = $today.'PR';

        $row = $this->db
            ->select('SLIP_NO')
            ->from('abc_mst_payment')
            ->where('PLANT',$plant)
            ->like('SLIP_NO',$prefix,'after')
            ->order_by('SLIP_NO','DESC')
            ->limit(1)
            ->get()
            ->row();

        $seq = $row ? ((int)substr($row->SLIP_NO,-4) + 1) : 1;

        return $prefix.str_pad($seq,4,'0',STR_PAD_LEFT);
    }

    /* ---------------------------------------------------------
       CRUD HEADER
    --------------------------------------------------------- */

    public function insert_header($data)
    {
        return $this->db->insert('abc_mst_payment',$data);
    }


    public function update_header_total($paymentNo, $total, $username = null)
    {
        return $this->db
            ->where('PAYMENT', $paymentNo)
            ->update('abc_mst_payment', [
                'TOTAL'      => $total,
                'UPDATED_AT' => date('Y-m-d H:i:s'),
                'UPDATED_BY' => $username
            ]);
    }

    public function update_header($payment, $data)
    {
        return $this->db->where('PAYMENT', $payment)
                        ->update('abc_mst_payment', $data);
    }

    public function update_header_by_key(
        $payment,
        $plant,
        $data
    ){
        return $this->db

            ->where('PAYMENT', $payment)

            ->where('PLANT', $plant)

            ->update(
                'abc_mst_payment',
                $data
            );
    }

    public function get_plant_select2()
    {
        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->where('CODE !=', '*')
            ->order_by('CODE_NAME', 'ASC')
            ->get()
            ->result_array();
    }

    public function update_header_total_by_key($payment,$plant,$total,$username=null)
    {
        return $this->db
            ->where('PAYMENT',$payment)
            ->where('PLANT',$plant)
            ->update('abc_mst_payment',[
                'TOTAL'=>$total,
                'UPDATED_AT'=>date('Y-m-d H:i:s'),
                'UPDATED_BY'=>$username
            ]);
    }

    public function get_header($payment, $plant)
    {
        $this->db->select("
            p.*,
            plant.CODE_NAME AS PLANT_NAME,
            supplier.FULL_NAME AS SUPPLIER_NAME
        ");

        $this->db->from('abc_mst_payment p');

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.CODE COLLATE utf8mb4_unicode_ci =
                p.PLANT COLLATE utf8mb4_unicode_ci
                AND plant.HEAD_CODE = 'PLANT'
            ",
            'left',
            false
        );

        $this->db->join(
                    'abc_cd_customer supplier',
                    "
                        supplier.CUST COLLATE utf8mb4_unicode_ci =
                        p.SUPPLIER COLLATE utf8mb4_unicode_ci
                    ",
                    'left',
                    false
                );

        $this->db->where('p.PAYMENT', $payment);

        $this->db->where('p.PLANT', $plant);

        $this->db->where('p.DELETED IS NULL', null, false);

        return $this->db
            ->get()
            ->row_array();
    }

    public function get_existing_seq($payment, $plant)
    {
        $rows = $this->db
            ->select('SEQ_NO')
            ->from('abc_mst_payment_detail')
            ->where('PAYMENT', $payment)
            ->where('PLANT', $plant)
            ->where('DELETED IS NULL', null, false)
            ->order_by('SEQ_NO', 'ASC')
            ->get()
            ->result_array();

        // ubah ke array sederhana: [1,2,3]
        return array_map(function($r){
            return (int)$r['SEQ_NO'];
        }, $rows);
    }

    /* ---------------------------------------------------------
       CRUD DETAIL
    --------------------------------------------------------- */

    public function insert_detail($data)
    {
        return $this->db->insert('abc_mst_payment_detail',$data);
    }

    public function get_detail($payment, $plant)
    {
        return $this->db

            ->select("
                d.*,

                material.MATERIAL_NAME
            ", false)

            ->from('abc_mst_payment_detail d')

            ->join(
                'abc_cd_material material',
                "
                    material.MATERIAL COLLATE utf8mb4_unicode_ci =
                    d.MATERIAL COLLATE utf8mb4_unicode_ci
                ",
                'left',
                false
            )

            ->where('d.PAYMENT', $payment)

            ->where('d.PLANT', $plant)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result_array();
    }

    public function delete_detail($payment)
    {
        return $this->db->where('PAYMENT', $payment)
                        ->delete('abc_mst_payment_detail');
    }

    /* ---------------------------------------------------------
       OTHERS
    --------------------------------------------------------- */

    public function get_all()
    {
        $this->db->where('DELETED IS NULL', null, false);
        return $this->db->get('abc_mst_payment')->result_array();
    }
}
