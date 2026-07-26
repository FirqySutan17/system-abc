<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_model extends CI_Model {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_user_plants($username)
    {
        $row = $this->db->select('plant')
            ->where('username', $username)
            ->get('users')
            ->row();

        if (!$row || empty($row->plant)) return [];

        $plants = json_decode($row->plant, true);
        return is_array($plants) ? $plants : [];
    }

    /* 🔑 DIPAKAI SELECT2 PLANT */
    public function get_user_plant_options($username)
    {
        // ambil plant user dari tabel users (json)
        $plants = $this->get_user_plants($username);
        if (empty($plants)) return [];

        $this->db->select("
            CODE as id,
            CONCAT(CODE, ' - ', CODE_NAME) as text
        ");
        $this->db->from('abc_cd_code');
        $this->db->where('HEAD_CODE', 'AJ');
        $this->db->where_in('CODE', $plants);
        $this->db->order_by('CODE', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_data(
        $limit,
        $start,
        $role_id,
        $plant,
        $username,
        $search = '',
        $order = 'RECEIVE_DATE',
        $dir = 'DESC',
        $status = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $role_id = (int)$role_id;

        $this->db->select("
            r.*,

            supplier.FULL_NAME AS SUPPLIER_NAME,

            plant.CODE_NAME AS PLANT_NAME,

            po.PO_TYPE,

            po_type.CODE_NAME AS PO_TYPE_NAME,

            po.MATERIAL,

            material.MATERIAL_NAME,

            SUM(rd.JUMLAH) AS TOTAL_QTY,

            SUM(rd.BERAT) AS TOTAL_BERAT,

            COUNT(DISTINCT rd.CUSTOMER)
                AS TOTAL_CUSTOMER,

            COUNT(DISTINCT rd.SALES_NO)
                AS TOTAL_SALES
        ", false);

        $this->db->from('abc_mst_receive r');

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            '
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code plant',
            "
                plant.CODE COLLATE utf8mb4_unicode_ci =
                r.PLANT COLLATE utf8mb4_unicode_ci
                AND plant.HEAD_CODE = 'PLANT'
            ",
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | RECEIVE DETAIL
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_receive_detail rd',
            '
                rd.RECEIVE = r.RECEIVE
                AND rd.PLANT = r.PLANT
                AND rd.DELETED IS NULL
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PO
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_po po',
            '
                po.PO = r.PO
                AND po.PLANT = r.PLANT
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PO TYPE
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_code po_type',
            "
                po_type.CODE COLLATE utf8mb4_unicode_ci =
                po.PO_TYPE COLLATE utf8mb4_unicode_ci
                AND po_type.HEAD_CODE = 'PO'
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
            '
                material.MATERIAL COLLATE utf8mb4_unicode_ci =
                po.MATERIAL COLLATE utf8mb4_unicode_ci
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
            'r.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if($search !== ''){

            $this->db->group_start();

            $this->db->like(
                'r.RECEIVE',
                $search
            );

            $this->db->or_like(
                'r.PO',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'material.MATERIAL_NAME',
                $search
            );

            $this->db->or_like(
                'r.REMARK',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if(!empty($status)){

            $this->db->where(
                'r.STATUS_RECEIVE',
                $status
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'r.RECEIVE_DATE >=',
                $dateFrom . ' 00:00:00'
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'r.RECEIVE_DATE <=',
                $dateTo . ' 23:59:59'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | GROUP BY
        |--------------------------------------------------------------------------
        */

        $this->db->group_by('r.RECEIVE');

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $this->db->order_by(
            'r.' . $order,
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
        $plant,
        $username,
        $search = '',
        $status = '',
        $dateFrom = '',
        $dateTo = ''
    )
    {
        $role_id = (int)$role_id;

        $this->db->from('abc_mst_receive r');

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_cd_customer supplier',
            '
                supplier.CUST COLLATE utf8mb4_unicode_ci =
                r.SUPPLIER COLLATE utf8mb4_unicode_ci
            ',
            'left',
            false
        );

        /*
        |--------------------------------------------------------------------------
        | PO
        |--------------------------------------------------------------------------
        */

        $this->db->join(
            'abc_mst_po po',
            '
                po.PO = r.PO
                AND po.PLANT = r.PLANT
            ',
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
            '
                material.MATERIAL COLLATE utf8mb4_unicode_ci =
                po.MATERIAL COLLATE utf8mb4_unicode_ci
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
            'r.DELETED IS NULL',
            null,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | ROLE FILTER
        |--------------------------------------------------------------------------
        */

        if($role_id !== 1){

            $plants = json_decode($plant, true);

            if(!is_array($plants)){
                $plants = explode(',', $plant);
            }

            $this->db->where_in(
                'r.PLANT',
                $plants
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if($search !== ''){

            $this->db->group_start();

            $this->db->like(
                'r.RECEIVE',
                $search
            );

            $this->db->or_like(
                'r.PO',
                $search
            );

            $this->db->or_like(
                'supplier.FULL_NAME',
                $search
            );

            $this->db->or_like(
                'material.MATERIAL_NAME',
                $search
            );

            $this->db->or_like(
                'r.REMARK',
                $search
            );

            $this->db->group_end();

        }

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        if(!empty($status)){

            $this->db->where(
                'r.STATUS_RECEIVE',
                $status
            );

        }

        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        */

        if(!empty($dateFrom)){

            $this->db->where(
                'r.RECEIVE_DATE >=',
                $dateFrom . ' 00:00:00'
            );

        }

        if(!empty($dateTo)){

            $this->db->where(
                'r.RECEIVE_DATE <=',
                $dateTo . ' 23:59:59'
            );

        }

        return $this->db->count_all_results();
    }

    public function getLookupPO()
    {
        return $this->db

            ->select("
                PO,
                SUPPLIER,
                MATERIAL,
                TOTAL_TERIMA_QTY,
                TOTAL_TERIMA_BW,
                HARGA
            ")

            ->from("abc_mst_po")

            ->where("STATUS", "OPEN")

            ->where("DELETED IS NULL",null,false)

            ->order_by("PO","DESC")

            ->get()

            ->result_array();
    }

    public function lookupPO($plant, $keyword = '')
    {
        $this->db
            ->select("
                p.PO,
                p.PLANT,

                p.SUPPLIER,
                s.FULL_NAME AS SUPPLIER_NAME,

                p.MATERIAL,
                m.MATERIAL_NAME,

                p.JUMLAH,
                p.BERAT,
                p.AVG_BW,

                p.MATI_QTY,
                p.MATI_BW,
                p.SUSUT_BW,

                p.TOTAL_TERIMA_QTY,
                p.TOTAL_TERIMA_BW,

                p.HARGA,
                p.TOTAL,

                p.NO_TRUCK,
                p.DRIVER
            ")
            ->from("abc_mst_po p")

            ->join(
                "abc_cd_customer s",
                "s.CUST = p.SUPPLIER",
                "left"
            )

            ->join(
                "abc_cd_material m",
                "m.MATERIAL = p.MATERIAL",
                "left"
            )

            ->where("p.PLANT", $plant)

            ->where("p.STATUS", "OPEN")

            ->where("p.DELETED IS NULL", null, false);

        if ($keyword != '') {

            $this->db
                ->group_start()

                ->like("p.PO", $keyword)

                ->or_like("s.FULL_NAME", $keyword)

                ->or_like("m.MATERIAL_NAME", $keyword)

                ->group_end();
        }

        return $this->db
            ->order_by("p.PO", "DESC")
            ->limit(20)
            ->get()
            ->result_array();
    }

    public function get_po_actual($plant, $po)
    {
        return $this->db

            ->select("
                p.*,
                s.FULL_NAME AS SUPPLIER_NAME,
                m.MATERIAL_NAME
            ")

            ->from("abc_mst_po p")

            ->join(
                "abc_cd_customer s",
                "s.CUST = p.SUPPLIER",
                "left"
            )

            ->join(
                "abc_cd_material m",
                "m.MATERIAL = p.MATERIAL",
                "left"
            )

            ->where("p.PLANT", $plant)

            ->where("p.PO", $po)

            ->where("p.DELETED IS NULL", null, false)

            ->get()

            ->row_array();
    }

    public function lookupCustomer(
        $keyword=""
    )
    {
        $this->db
        ->select("
            CUST AS CUSTOMER,
            FULL_NAME AS CUSTOMER_NAME,
            CUST_KIND
        ")
        ->from("abc_cd_customer")
        ->where("STATUS","Y")
        ->where("CUST_KIND","CUSTOMER")
        ->where("CUST_CLASS","CUSTOMER");

        if($keyword!="")
        {
            $this->db

                ->group_start()

                ->like(
                    "CUST",
                    $keyword
                )

                ->or_like(
                    "FULL_NAME",
                    $keyword
                )

                ->group_end();
        }

        return

            $this->db

            ->order_by(
                "FULL_NAME",
                "ASC"
            )

            ->get()

            ->result_array();
    }

    public function get_plant_select2()
    {
        return $this->db
            ->select('CODE as id, CODE_NAME as text')
            ->from('abc_cd_code')
            ->where('HEAD_CODE', 'PLANT')
            ->where('CODE <>', '*')
            ->where('USE_YN', 'Y')
            ->order_by('CODE_NAME', 'ASC')
            ->get()
            ->result_array();
    }

    /* 🔑 DIPAKAI CONTROLLER */
    public function user_has_plant($username, $plant)
    {
        $plants = $this->get_user_plants($username);
        return in_array((string)$plant, array_map('strval', $plants));
    }

    // public function insertReceiveHeader($data)
    // {
    //     $ok = $this->db->insert(
    //         'abc_mst_receive',
    //         $data
    //     );

    //     if(!$ok){

    //         throw new Exception(
    //             'Gagal menyimpan Header Receive.'
    //         );

    //     }

    //     return true;
    // }

    public function insertReceiveHeader(array $data)
    {
        return $this->db->insert(
            'abc_mst_receive',
            $data
        );
    }

    // public function insertReceiveDetail($rows)
    // {
    //     if(empty($rows)){
    //         return true;
    //     }

    //     $ok = $this->db->insert_batch(
    //         'abc_mst_receive_detail',
    //         $rows
    //     );

    //     if($ok === false){

    //         throw new Exception(
    //             'Gagal menyimpan Detail Receive.'
    //         );

    //     }

    //     return true;
    // }

    public function insertReceiveDetail(array $rows)
    {
        return $this->db->insert_batch(
            'abc_mst_receive_detail',
            $rows
        );
    }

    public function insertSalesHeader(array $data)
    {
        return $this->db->insert(
            'abc_mst_sales',
            $data
        );
    }

    public function insertSalesDetail(array $rows)
    {
        return $this->db->insert_batch(
            'abc_mst_sales_detail',
            $rows
        );
    }

    // public function insertSaving($rows)
    // {
    //     if(empty($rows)){
    //         return true;
    //     }

    //     $ok = $this->db->insert_batch(
    //         'abc_mst_saving',
    //         $rows
    //     );

    //     if($ok === false){

    //         throw new Exception(
    //             'Gagal menyimpan Tabungan.'
    //         );

    //     }

    //     return true;
    // }

    public function insertSaving(array $rows)
    {
        if (empty($rows)) {
            return true;
        }

        return $this->db->insert_batch(
            'abc_mst_saving',
            $rows
        );
    }

    // public function insertSales($rows)
    // {
    //     if(empty($rows)){
    //         return true;
    //     }

    //     $ok = $this->db->insert_batch(
    //         'abc_mst_sales',
    //         $rows
    //     );

    //     if($ok === false){

    //         throw new Exception(
    //             'Gagal menyimpan Sales.'
    //         );

    //     }

    //     return true;
    // }

    // public function insertCompanyStock($row)
    // {
    //     if(empty($row)){
    //         return true;
    //     }

    //     if(
    //         $row['QTY'] <= 0 &&
    //         $row['BW'] <= 0
    //     ){
    //         return true;
    //     }

    //     $ok = $this->db->insert(
    //         'abc_mst_stock_company',
    //         $row
    //     );

    //     if(!$ok){

    //         throw new Exception(
    //             'Gagal menyimpan Company Stock.'
    //         );

    //     }

    //     return true;
    // }

    public function upsertCompanyStock(array $rows)
    {
        foreach ($rows as $row) {

            $exists = $this->db
                ->where('PLANT', $row['PLANT'])
                ->where('MATERIAL', $row['MATERIAL'])
                ->get('abc_mst_company_stock')
                ->row();

            if ($exists) {

                $qty = $exists->QTY + $row['QTY'];
                $bw  = $exists->BW + $row['BW'];

                $avg = $qty > 0 ? round($bw / $qty, 2) : 0;

                $this->db
                    ->where('PLANT', $row['PLANT'])
                    ->where('MATERIAL', $row['MATERIAL'])
                    ->update('abc_mst_company_stock', [
                        'QTY' => $qty,
                        'BW' => $bw,
                        'AVG_BW' => $avg,
                        'UPDATED_AT' => date('Y-m-d H:i:s'),
                        'UPDATED_BY' => $row['CREATED_BY']
                    ]);

            } else {

                $this->db->insert('abc_mst_company_stock', $row);

            }
        }

        return true;
    }

    public function updateCompanyStock(array $rows)
    {
        foreach ($rows as $row) {

            $stock = $this->db
                ->where('PLANT', $row['PLANT'])
                ->where('MATERIAL', $row['MATERIAL'])
                ->get('abc_mst_company_stock')
                ->row();

            if ($stock) {

                $qty = $stock->QTY + $row['QTY'];
                $bw  = $stock->BW + $row['BW'];

                $avg = ($qty > 0)
                    ? round($bw / $qty, 2)
                    : 0;

                $this->db
                    ->where('PLANT', $row['PLANT'])
                    ->where('MATERIAL', $row['MATERIAL'])
                    ->update(
                        'abc_mst_company_stock',
                        [

                            'QTY' => $qty,

                            'BW' => $bw,

                            'AVG_BW' => $avg,

                            'UPDATED_AT' => date('Y-m-d H:i:s'),

                            'UPDATED_BY' => $row['CREATED_BY']

                        ]
                    );

            } else {

                $this->db->insert(
                    'abc_mst_company_stock',
                    $row
                );

            }
        }

        return true;
    }

    public function insertCompanyStock(array $rows)
    {
        $row['DELETED'] = 'N';
        if (empty($rows)) {
            return true;
        }

        return $this->db->insert_batch(
            'abc_mst_company_stock',
            $rows
        );
    }

    public function insertCompanyStockCard(
        array $rows,
        array $receiveHeader
    )
    {
        foreach ($rows as $row) {

            $stock = $this->db
                ->where('PLANT', $row['PLANT'])
                ->where('MATERIAL', $row['MATERIAL'])
                ->get('abc_mst_company_stock')
                ->row();

            $this->db->insert(
                'abc_trx_company_stock_card',
                [

                    'CARD_NO' => $this->generateCompanyStockCardNo(),

                    'PLANT' => $row['PLANT'],

                    'MATERIAL' => $row['MATERIAL'],

                    'TRANSACTION_DATE' => $receiveHeader['RECEIVE_DATE'],

                    'REFERENCE_NO' => $receiveHeader['RECEIVE'],

                    'REFERENCE_TYPE' => 'RECEIVE',

                    'QTY_IN' => $row['QTY'],

                    'BW_IN' => $row['BW'],

                    'QTY_OUT' => 0,

                    'BW_OUT' => 0,

                    'BALANCE_QTY' => $stock->QTY,

                    'BALANCE_BW' => $stock->BW,

                    'BALANCE_AVG_BW' => $stock->AVG_BW,

                    'AVG_BW' => $row['AVG_BW'],

                    'REMARK' => 'Receive ' . $receiveHeader['RECEIVE'],

                    'CREATED_AT' => date('Y-m-d H:i:s'),

                    'CREATED_BY' => $row['CREATED_BY']

                ]
            );
        }

        return true;
    }

    public function deleteReceive($receive)
    {
        $this->db->trans_begin();

        try {

            $this->validateDelete($receive);

            $context = $this->getDeleteContext($receive);
            if (empty($context['detail'])) {
                throw new Exception('Receive detail not found.');
            }

            $this->rollbackPO($context);

            $this->deleteCompanyStockCard($receive);

            $this->refreshCompanyStock($context);

            $this->deleteSales($receive);

            $this->deleteSaving($receive);

            $this->deleteReceiveDetail($receive);

            $this->deleteReceiveHeader($receive);

            if (!$this->db->trans_status()) {
                throw new Exception("Delete transaction failed.");
            }

            $this->db->trans_commit();

            return [
                'status' => true,
                'message' => 'Receive deleted successfully.'
            ];

        } catch (Exception $e) {

            $this->db->trans_rollback();

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];

        }
    }

    private function validateDelete($receive)
    {
        $header = $this->db
            ->where('RECEIVE', $receive)
            ->get('abc_mst_receive')
            ->row_array();

        if (!$header) {
            throw new Exception('Receive not found.');
        }

        if ($header['STATUS'] === 'PAID') {
            throw new Exception(
                'Receive has been paid and cannot be deleted.'
            );
        }
    }

    private function rollbackPO($context)
    {
        $this->db
            ->where('PO', $context['header']['PO'])
            ->update('abc_mst_po', [

                'STATUS' => 'OPEN',

                'UPDATED_AT' => date('Y-m-d H:i:s'),

                'UPDATED_BY' => $this->session->userdata('username')

            ]);
    }

    private function deleteCompanyStockCard($receive)
    {
        $this->db
            ->where('REFERENCE_TYPE', 'RECEIVE')
            ->where('REFERENCE_NO', $receive)
            ->delete('abc_trx_company_stock_card');
    }

    private function refreshCompanyStock($context)
    {
        $materials = [];

        foreach ($context['detail'] as $row) {

            $key = $row['PLANT'] . '_' . $row['MATERIAL'];

            $materials[$key] = [
                'PLANT' => $row['PLANT'],
                'MATERIAL' => $row['MATERIAL']
            ];
        }

        foreach ($materials as $item) {

            $this->refreshSingleCompanyStock(
                $item['PLANT'],
                $item['MATERIAL']
            );
        }
    }

    private function refreshSingleCompanyStock($plant, $material)
    {
        $row = $this->db->query("
            SELECT

                COALESCE(SUM(QTY_IN),0) -
                COALESCE(SUM(QTY_OUT),0) AS QTY,

                COALESCE(SUM(BW_IN),0) -
                COALESCE(SUM(BW_OUT),0) AS BW

            FROM abc_trx_company_stock_card

            WHERE PLANT = ?

            AND MATERIAL = ?
        ", [$plant, $material])->row_array();

        $avg = 0;

        if ($row['QTY'] > 0) {
            $avg = $row['BW'] / $row['QTY'];
        }

        $this->db
            ->where('PLANT', $plant)
            ->where('MATERIAL', $material)
            ->update('abc_mst_company_stock', [

                'QTY' => $row['QTY'],
                'BW' => $row['BW'],
                'AVG_BW' => $avg

            ]);
    }

    private function deleteSales($receive)
    {
        $sales = $this->db
            ->select('SALES')
            ->where('RECEIVE', $receive)
            ->get('abc_mst_sales')
            ->result_array();

        foreach ($sales as $row) {

            $this->db
                ->where('SALES', $row['SALES'])
                ->delete('abc_mst_sales_detail');
        }

        $this->db
            ->where('RECEIVE', $receive)
            ->delete('abc_mst_sales');
    }

    private function deleteSaving($receive)
    {
        $this->db
            ->where('RECEIVE', $receive)
            ->delete('abc_mst_saving');
    }

    private function deleteReceiveDetail($receive)
    {
        $this->db
            ->where('RECEIVE', $receive)
            ->delete('abc_mst_receive_detail');
    }

    private function deleteReceiveHeader($receive)
    {
        $this->db
            ->where('RECEIVE', $receive)
            ->delete('abc_mst_receive');
    }

    private function getDeleteContext($receive)
    {

        $header = $this->db

            ->where('RECEIVE',$receive)

            ->get('abc_mst_receive')

            ->row_array();

        if(empty($header)){

            return [];

        }

        $detail = $this->db

            ->where('RECEIVE',$receive)

            ->get('abc_mst_receive_detail')

            ->result_array();

        return [

            'header'=>$header,

            'detail'=>$detail

        ];

    }

    public function updatePO(array $data)
    {
        return $this->db
            ->where('PO', $data['PO'])
            ->update(
                'abc_mst_po',
                [
                    'STATUS'     => $data['STATUS'],
                    'UPDATED_AT' => $data['UPDATED_AT'],
                    'UPDATED_BY' => $data['UPDATED_BY']
                ]
            );
    }

    public function insert_po($data)
    {
        return $this->db->insert('abc_mst_po', $data);
    }

    public function insert_po_detail_batch($rows)
    {
        if(empty($rows)) return false;
        return $this->db->insert_batch('abc_mst_po_detail', $rows);
    }

    public function get_po_header($po, $plant)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->get('abc_mst_po')
            ->row_array();
    }

    public function get_po_detail($po, $plant)
    {
        return $this->db
            ->select("
                d.*,
                m.MATERIAL_NAME,
                c.FULL_NAME AS CUSTOMER_NAME
            ")
            ->from('abc_mst_po_detail d')
            ->join(
                'abc_cd_material m',
                'm.MATERIAL COLLATE utf8mb4_unicode_ci = d.MATERIAL COLLATE utf8mb4_unicode_ci',
                'left',
                false
            )
            ->join(
                'abc_cd_customer c',
                'c.CUST = d.CUSTOMER',
                'left'
            )
            ->where('d.PO', $po)
            ->where('d.PLANT', $plant)
            ->order_by('d.SEQ_NO', 'ASC')
            ->get()
            ->result_array();
    }

    public function search_po($role_id, $plant, $q = null, $limit = 20)
    {
        $this->db->select('
            r.PO,
            r.PLANT,
            r.SUPPLIER,
            c.FULL_NAME AS SUPPLIER_NAME,
            cd.CODE_NAME AS PLANT_NAME
        ');
        $this->db->from('abc_mst_po r');

        $this->db->join(
            'abc_cd_customer c',
            'r.SUPPLIER = c.CUST',
            'left'
        );

        $this->db->join(
            'abc_cd_code cd',
            "cd.CODE = r.PLANT AND cd.HEAD_CODE = 'PLANT'",
            'left',
            false
        );

        // 🔐 FILTER
        $this->db->where('r.PLANT', $plant);
        $this->db->where('r.STATUS', 'OPEN');

        if (!empty($q)) {
            $this->db->like('r.PO', $q);
        }

        $this->db->order_by('r.PO', 'DESC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id'            => $r['PO'],
                'text'          => $r['PLANT_NAME']
                                    .' - '.$r['PO']
                                    .' - '.$r['SUPPLIER_NAME'],
                'plant'         => $r['PLANT'],
                'supplier'      => $r['SUPPLIER'],
                'supplier_text' => $r['SUPPLIER'].' - '.$r['SUPPLIER_NAME']
            ];
        }

        return $data;
    }

    /* ---------------------------------------------------------
       RECEIVE OPERATIONS
    --------------------------------------------------------- */

    public function insert_receive_header($data)
    {
        return $this->db->insert('abc_mst_receive', $data);
    }

    public function insert_receive_detail_batch($rows)
    {
        return empty($rows) ? false : $this->db->insert_batch('abc_mst_receive_detail', $rows);
    }

    public function set_po_received($po, $plant, $username)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('abc_mst_po', [
                'STATUS'      => 'Y',
                'UPDATED_AT'  => date('Y-m-d H:i:s'),
                'UPDATED_BY'  => $username
            ]);
    }

    public function reset_po_status($po, $plant)
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('abc_mst_po', [
                'STATUS'     => null,
                'UPDATED_AT'=> date('Y-m-d H:i:s')
            ]);
    }

    public function get_receive_header($plant, $receive)
    {
        return $this->db

            ->select('
                r.*,

                c.CUST,

                c.FULL_NAME AS SUPPLIER_NAME,

                cd.CODE_NAME AS PLANT_NAME,

                po.MATERIAL,

                po.JUMLAH,

                po.BERAT,

                po.HARGA,

                po.TOTAL,

                po.NO_TRUCK,

                po.DRIVER,

                m.MATERIAL_NAME
            ')

            ->from('abc_mst_receive r')

            ->join(
                'abc_cd_customer c',
                'r.SUPPLIER = c.CUST',
                'left'
            )

            ->join(
                'abc_cd_code cd',
                "
                    cd.CODE = r.PLANT
                    AND cd.HEAD_CODE = 'PLANT'
                ",
                'left',
                false
            )

            ->join(
                'abc_mst_po po',
                '
                    po.PO = r.PO
                    AND po.PLANT = r.PLANT
                ',
                'left'
            )

            ->join(
                'abc_cd_material m',
                '
                    m.MATERIAL = po.MATERIAL
                ',
                'left'
            )

            ->where('r.PLANT', $plant)

            ->where('r.RECEIVE', $receive)

            ->get()

            ->row_array();
    }

    public function get_receive_detail($plant, $receive)
    {
        return $this->db
            ->select("
                d.*,
                m.MATERIAL_NAME,
                c.FULL_NAME AS CUSTOMER_NAME,
                pt.CODE_NAME AS PO_TYPE_NAME
            ")

            ->from('abc_mst_receive_detail d')

            ->join(
                'abc_cd_material m',
                'm.MATERIAL = d.MATERIAL',
                'left'
            )

            ->join(
                'abc_cd_customer c',
                'c.CUST = d.CUSTOMER',
                'left'
            )

            ->join(
                'abc_cd_code pt',
                "
                    TRIM(pt.CODE) = TRIM(d.PO_TYPE)
                    AND pt.HEAD_CODE = 'PO'
                ",
                'left',
                false
            )

            ->where('d.PLANT', $plant)

            ->where('d.RECEIVE', $receive)

            ->order_by('d.SEQ_NO', 'ASC')

            ->get()

            ->result_array();
    }

    public function update_po_status($po, $plant, $status = 'RECEIVED')
    {
        return $this->db
            ->where('PO', $po)
            ->where('PLANT', $plant)
            ->update('abc_mst_po', [
                'STATUS'     => $status,
                'UPDATED_AT' => date('Y-m-d H:i:s')
            ]);
    }

    public function update_receive_header($receive, $data, $plant = null)
    {
        $this->db->where('RECEIVE', $receive);
        if ($plant !== null) {
            $this->db->where('PLANT', $plant);
        }
        return $this->db->update('abc_mst_receive', $data);
    }



    public function get_max_seq_no($plant, $receive)
    {
        $row = $this->db
            ->select_max('SEQ_NO')
            ->where('PLANT', $plant)
            ->where('RECEIVE', $receive)
            ->get('abc_mst_receive_detail')
            ->row();

        return (int) ($row->SEQ_NO ?? 0);
    }

    private function generateRunningNumber(
        string $table,
        string $column,
        string $prefix
    )
    {
        $row = $this->db
            ->select("MAX($column) AS last_no", false)
            ->like($column, $prefix, 'after')
            ->get($table)
            ->row();

        $seq = 1;

        if (!empty($row->last_no)) {

            $seq = (int) substr($row->last_no, -4) + 1;

        }

        return $prefix .
            str_pad(
                $seq,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    public function generateReceiveNo()
    {
        return $this->generateRunningNumber(
            'abc_mst_receive',
            'RECEIVE',
            'RC' . date('Ymd')
        );
    }

    public function generateSlipNo()
    {
        return $this->generateRunningNumber(
            'abc_mst_receive',
            'SLIP_NO',
            'SL' . date('Ymd')
        );
    }

    public function generateSalesNo()
    {
        return $this->generateRunningNumber(
            'abc_mst_sales',
            'SALES',
            'SLS' . date('Ymd')
        );
    }

    public function generateSavingNo()
    {
        return $this->generateRunningNumber(
            'abc_mst_saving',
            'SV_NO',
            'SV' . date('Ymd')
        );
    }

    public function generateCompanyStockNo()
    {
        return $this->generateRunningNumber(
            'abc_stock_card',
            'ID',
            'CS' . date('Ymd')
        );
    }

    public function generateCompanyStockCardNo()
    {
        $prefix = 'CSC' . date('Ymd');

        $this->db->select('CARD_NO');
        $this->db->from('abc_trx_company_stock_card');
        $this->db->like('CARD_NO', $prefix, 'after');
        $this->db->order_by('CARD_NO', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row();

        if ($row) {
            $last = (int) substr($row->CARD_NO, -4);
            $running = $last + 1;
        } else {
            $running = 1;
        }

        return $prefix . str_pad($running, 4, '0', STR_PAD_LEFT);
    }


    /* ---------------------------------------------------------
       SELECT2 HELPERS
    --------------------------------------------------------- */

    public function search_supplier($q = null, $limit = 20)
    {
        $this->db->select('CUST as id, FULL_NAME as name');
        $this->db->from('abc_cd_customer');
        $this->db->where('CUST_KIND', 'SUPPLIER');
        $this->db->where('CUST_CLASS', 'SUPPLIER');
        $this->db->where('STATUS', 'N');

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
                'text' => $r['id'] . ' - ' . $r['name']
            ];
        }
        return $out;
    }

    public function search_material($q = null, $limit = 20)
    {
        $this->db->select('MATERIAL as id, MATERIAL_NAME');
        $this->db->from('abc_cd_material');

        if ($q) {
            $this->db->group_start();
            $this->db->like('MATERIAL', $q);
            $this->db->or_like('MATERIAL_NAME', $q);
            $this->db->group_end();
        }

        $this->db->order_by('MATERIAL', 'ASC');
        $this->db->limit($limit);

        $rows = $this->db->get()->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'   => $r['id'],
                'text' => $r['id'] . ' - ' . $r['MATERIAL_NAME']
            ];
        }

        return $out;
    }

    public function get_all_receives()
    {
        return $this->db->get('abc_mst_receive')->result_array();
    }
}
