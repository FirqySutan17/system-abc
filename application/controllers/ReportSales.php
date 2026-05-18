<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportSales extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_productions_sales')) {
            show_404();
        }
        $this->load->model('ReportSales_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data['plants'] = $this->db
            ->where('HEAD_CODE', 'PLANT')
            ->order_by('CODE_NAME', 'ASC')
            ->get('abc_cd_code')
            ->result();

        $data['customers'] = $this->db
            ->order_by('FULL_NAME', 'ASC')
            ->get('abc_cd_customer')
            ->result();

        $this->load->view('templates/header', ['title' => 'Report Sales']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_sales/index', $data);
        $this->load->view('templates/footer');
    }

    public function load_sales()
    {
        ob_clean();

        header('Content-Type: application/json');

        $page = (int)$this->input->get('page');

        if ($page <= 0) {
            $page = 1;
        }

        $limit = (int)$this->input->get('limit');

        if ($limit <= 0) {
            $limit = 10;
        }

        $start = ($page - 1) * $limit;

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $filters = [

            'search' => trim(
                $this->input->get('search', true) ?? ''
            ),

            'plant' => trim(
                $this->input->get('plant', true) ?? ''
            ),

            'customer' => trim(
                $this->input->get('customer', true) ?? ''
            ),

            'status' => trim(
                $this->input->get('status', true) ?? ''
            ),

            'date_from' => trim(
                $this->input->get('date_from', true) ?? ''
            ),

            'date_to' => trim(
                $this->input->get('date_to', true) ?? ''
            )
        ];

        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $order = $this->input->get(
            'order',
            true
        ) ?: 'SALES_DATE';

        $dirInput = $this->input
            ->get('dir', true) ?? '';

        $dir = strtoupper($dirInput) === 'ASC'
            ? 'ASC'
            : 'DESC';

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $rows = $this->ReportSales_model
            ->get_sales_report(
                $limit,
                $start,
                $filters,
                $order,
                $dir
            );

        $total = $this->ReportSales_model
            ->count_sales_report(
                $filters
            );

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $pages = ceil($total / $limit);

        $pagination = $this->build_pagination(
            $pages,
            $page
        );

        $start_data = $total > 0
            ? (($page - 1) * $limit) + 1
            : 0;

        $end_data = min(
            $page * $limit,
            $total
        );

        echo json_encode([

            'rows' => $rows,

            'total' => $total,

            'pagination' => $pagination,

            'page' => $page,

            'start' => $start_data,

            'end' => $end_data

        ]);

        exit;
    }

    public function export_excel_sales()
    {
        $username = $this->session->userdata('username');

        // ======================
        // PLANT FILTER (LIKE PRODUCTION)
        // ======================
        $userPlants = $this->ReportSales_model->get_user_plants($username);
        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!$this->ReportSales_model->user_has_plant($username, $selectedPlant)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }

        $filters = [
            'plant'     => $plantFilter,
            'customer'  => $this->input->get('customer', TRUE),
            'sales'     => $this->input->get('sales', TRUE),
            'item'      => $this->input->get('item', TRUE), // ✅ tambah
            'status'    => $this->input->get('status', TRUE),
            'date_from' => $this->input->get('date_from', TRUE),
            'date_to'   => $this->input->get('date_to', TRUE),
        ];

        $rows = $this->ReportSales_model
            ->get_sales_report(0, 0, $filters, 'SALES_DATE', 'DESC', $username);

        if (empty($rows)) show_error('No data found for export');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'PLANT',
            'DATE',
            'NO SALES',
            'CUSTOMER',
            'ITEM',
            'QTY',
            'BERAT',
            'HARGA',
            'DISCOUNT',
            'AMOUNT',
            'STATUS',
            'REMAIN',
            'NO NOTA / REMARK'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;
        $grandQty = 0;
        $grandBerat = 0;
        $grandTotal = 0;

        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->SALES.'|'.$r->PLANT][] = $r;
        }

        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {

                $qty    = (float)$r->QTY;
                $berat  = (float)$r->BERAT;
                $amount = (float)$r->DETAIL_AMOUNT;

                $grandQty   += $qty;
                $grandBerat += $berat;
                $grandTotal += $amount;

                $sheet->setCellValue("E{$rowExcel}", $r->FULL_NAME);
                $sheet->setCellValue("F{$rowExcel}", $qty);
                $sheet->setCellValue("G{$rowExcel}", $berat);
                $sheet->setCellValue("H{$rowExcel}", $r->HARGA);
                $sheet->setCellValue("I{$rowExcel}", $r->DISCOUNT);
                $sheet->setCellValue("J{$rowExcel}", $amount);
                $sheet->setCellValue("K{$rowExcel}", $r->STATUS_REPORT);
                $sheet->setCellValue("L{$rowExcel}", $r->REMAIN);
                $sheet->setCellValue("M{$rowExcel}", $r->NOTA);

                if ($i === 0) {
                    $sheet->setCellValue("A{$rowExcel}", $r->PLANT_NAME);
                    $sheet->setCellValue("B{$rowExcel}", date('d/m/Y', strtotime($r->SALES_DATE)));
                    $sheet->setCellValue("C{$rowExcel}", $r->SALES);
                    $sheet->setCellValue("D{$rowExcel}", $r->CUSTOMER_NAME);
                }

                $rowExcel++;
            }

            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (['A','B','C','D'] as $c) {
                    $sheet->mergeCells("{$c}{$startRow}:{$c}{$endRow}");
                }
            }
        }

        // ===== GRAND TOTAL =====
        $sheet->setCellValue("E{$rowExcel}", "GRAND TOTAL");
        $sheet->setCellValue("F{$rowExcel}", $grandQty);
        $sheet->setCellValue("G{$rowExcel}", $grandBerat);
        $sheet->setCellValue("J{$rowExcel}", $grandTotal);

        $sheet->getStyle("E{$rowExcel}:J{$rowExcel}")
            ->getFont()->setBold(true);

        foreach (range('A','M') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_sales.xlsx"');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf_sales()
    {
        $username = $this->session->userdata('username');

        // ======================
        // PLANT FILTER
        // ======================
        $userPlants = $this->ReportSales_model->get_user_plants($username);
        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!$this->ReportSales_model->user_has_plant($username, $selectedPlant)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }

        $filters = [
            'plant'     => $plantFilter,
            'customer'  => $this->input->get('customer', TRUE),
            'sales'     => $this->input->get('sales', TRUE),

            'item'      => $this->input->get('item', TRUE), // ✅ tambah

            'status'    => $this->input->get('status', TRUE),
            'date_from' => $this->input->get('date_from', TRUE),
            'date_to'   => $this->input->get('date_to', TRUE),
        ];

        $rows = $this->ReportSales_model
            ->get_sales_report(0, 0, $filters, 'SALES_DATE', 'DESC', $username);

        if (empty($rows)) show_error('No data found for export');

        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->SALES.'|'.$r->PLANT][] = $r;
        }

        $grandQty = 0;
        $grandBerat = 0;
        $grandTotal = 0;

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{background:#eee;text-align:center}
            .right{text-align:right}
            .center{text-align:center}
            .bold{font-weight:bold}
        </style>

        <h3 style="text-align:center">REPORT SALES</h3>
        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>NO SALES</th>
                    <th>CUSTOMER</th>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>DISCOUNT</th>
                    <th>AMOUNT</th>
                    <th>STATUS</th>
                    <th>REMAIN</th>
                    <th>NOTA</th>
                </tr>
            </thead><tbody>';

        foreach ($grouped as $group) {

            $rowspan = count($group);

            foreach ($group as $i => $r) {

                $qty    = (float)$r->QTY;
                $berat  = (float)$r->BERAT;
                $amount = (float)$r->DETAIL_AMOUNT;

                $grandQty   += $qty;
                $grandBerat += $berat;
                $grandTotal += $amount;

                $html .= '<tr>';

                if ($i === 0) {
                    $html .= '
                        <td rowspan="'.$rowspan.'" class="center">'.$r->PLANT_NAME.'</td>
                        <td rowspan="'.$rowspan.'" class="center">'.date('d/m/Y', strtotime($r->SALES_DATE)).'</td>
                        <td rowspan="'.$rowspan.'" class="center"><b>'.$r->SALES.'</b></td>
                        <td rowspan="'.$rowspan.'">'.$r->CUSTOMER_NAME.'</td>
                    ';
                }

                $html .= '
                    <td>'.$r->FULL_NAME.'</td>
                    <td class="right">'.number_format($qty,2).'</td>
                    <td class="right">'.number_format($berat,2).'</td>
                    <td class="right">'.number_format($r->HARGA,0,",",".").'</td>
                    <td class="right">'.number_format($r->DISCOUNT,0,",",".").'</td>
                    <td class="right">'.number_format($amount,0,",",".").'</td>
                    <td class="center">'.$r->STATUS_REPORT.'</td>
                    <td class="right">'.number_format($r->REMAIN,0,",",".").'</td>
                    <td class="center">'.$r->NOTA.'</td>
                ';

                $html .= '</tr>';
            }
        }

        $html .= '
            <tr>
                <td colspan="5" class="right bold">GRAND TOTAL</td>
                <td class="right bold">'.number_format($grandQty,2).'</td>
                <td class="right bold">'.number_format($grandBerat,2).'</td>
                <td></td>
                <td></td>
                <td class="right bold">'.number_format($grandTotal,0,",",".").'</td>
                <td></td>
                <td></td>
            </tr>
        </tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation'=>'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('report_sales.pdf', 'I');
    }

    public function get_items()
    {
        $q = $this->input->get('q', TRUE);

        $rows = $this->ReportSales_model
            ->get_items($q);

        $data = [];

        foreach ($rows as $r) {

            $data[] = [

                'id' => $r->ITEM,

                'text' =>
                    $r->ITEM . ' - ' .
                    $r->FULL_NAME

            ];
        }

        echo json_encode($data);
    }

    public function load_sales_item()
    {
        $username = $this->session->userdata('username');

        $page  = (int)$this->input->get('page') ?: 1;
        $limit = (int)$this->input->get('limit') ?: 24;

        // ======================
        // PLANT FILTER
        // ======================

        $userPlants = $this->ReportSales_model
            ->get_user_plants($username);

        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }


        // ======================
        // FILTER
        // ======================

        $filters = [

            'plant' => $plantFilter,
            'item'  => $this->input->get('item', TRUE),

            'date1' => $this->input->get('date1', TRUE),
            'date2' => $this->input->get('date2', TRUE),
        ];


        $start = ($page - 1) * $limit;


        $rows = $this->ReportSales_model
            ->get_sales_item(
                $limit,
                $start,
                $filters
            );


        $totalRows = $this->ReportSales_model
            ->count_sales_item(
                $filters
            );


        // ===== GRAND TOTAL =====

        $allRows = $this->ReportSales_model
            ->get_sales_item(
                0,
                0,
                $filters
            );


        $grand = [
            'qty'=>0,
            'berat'=>0,
            'amount'=>0
        ];


        foreach ($allRows as $r) {

            $grand['qty']    += (float)$r->QTY;
            $grand['berat']  += (float)$r->BERAT;
            $grand['amount'] += (float)$r->AMOUNT;

        }


        $pages = $limit > 0
            ? ceil($totalRows / $limit)
            : 1;


        $pagination =
            $this->build_pagination(
                $pages,
                $page,
                'ajax'
            );


        echo json_encode([

            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page

        ]);
    }

    public function export_excel_sales_item()
    {
        $username = $this->session->userdata('username');

        $userPlants =
            $this->ReportSales_model
                ->get_user_plants($username);

        $selectedPlant =
            $this->input->get('plant', TRUE);


        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }


        $filters = [

            'plant' => $plantFilter,
            'item'  => $this->input->get('item', TRUE),

            'date1' => $this->input->get('date1', TRUE),
            'date2' => $this->input->get('date2', TRUE),
        ];


        $rows =
            $this->ReportSales_model
                ->get_sales_item(0, 0, $filters);


        if (empty($rows))
            show_error('No data');


        $spreadsheet =
            new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet =
            $spreadsheet->getActiveSheet();


        $headers = [
            'PLANT',
            'ITEM',
            'QTY',
            'BERAT',
            'AMOUNT'
        ];


        $col = 'A';

        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }


        // ===== GROUP BY PLANT =====

        $grouped = [];

        foreach ($rows as $r) {
            $grouped[$r->PLANT][] = $r;
        }


        $rowExcel = 2;

        $gQty = 0;
        $gBerat = 0;
        $gAmount = 0;


        foreach ($grouped as $plant => $group) {

            $subQty = 0;
            $subBerat = 0;
            $subAmount = 0;

            foreach ($group as $r) {

                $sheet->setCellValue(
                    "A{$rowExcel}",
                    $r->PLANT_NAME
                );

                $sheet->setCellValue(
                    "B{$rowExcel}",
                    $r->ITEM_NAME.' ('.$r->ITEM.')'
                );

                $sheet->setCellValue(
                    "C{$rowExcel}",
                    $r->QTY
                );

                $sheet->setCellValue(
                    "D{$rowExcel}",
                    $r->BERAT
                );

                $sheet->setCellValue(
                    "E{$rowExcel}",
                    $r->AMOUNT
                );


                $subQty   += $r->QTY;
                $subBerat += $r->BERAT;
                $subAmount+= $r->AMOUNT;

                $gQty   += $r->QTY;
                $gBerat += $r->BERAT;
                $gAmount+= $r->AMOUNT;

                $rowExcel++;
            }


            // ===== SUBTOTAL =====

            $sheet->setCellValue(
                "A{$rowExcel}",
                "SUBTOTAL ".$group[0]->PLANT_NAME
            );

            $sheet->setCellValue(
                "C{$rowExcel}",
                $subQty
            );

            $sheet->setCellValue(
                "D{$rowExcel}",
                $subBerat
            );

            $sheet->setCellValue(
                "E{$rowExcel}",
                $subAmount
            );

            $sheet->getStyle("A{$rowExcel}:E{$rowExcel}")
                ->getFont()->setBold(true);

            $rowExcel++;
        }


        // ===== GRAND TOTAL =====

        $sheet->setCellValue(
            "A{$rowExcel}",
            "GRAND TOTAL"
        );

        $sheet->setCellValue(
            "C{$rowExcel}",
            $gQty
        );

        $sheet->setCellValue(
            "D{$rowExcel}",
            $gBerat
        );

        $sheet->setCellValue(
            "E{$rowExcel}",
            $gAmount
        );


        $sheet->getStyle("A{$rowExcel}:E{$rowExcel}")
            ->getFont()->setBold(true);


        foreach (range('A','E') as $c) {
            $sheet->getColumnDimension($c)
                ->setAutoSize(true);
        }


        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        header(
            'Content-Disposition: attachment; filename="report_sales_item.xlsx"'
        );


        $writer =
            new \PhpOffice\PhpSpreadsheet\Writer\Xlsx(
                $spreadsheet
            );

        $writer->save('php://output');

        exit;
    }

    public function export_pdf_sales_item()
    {
        $username = $this->session->userdata('username');

        $userPlants =
            $this->ReportSales_model
                ->get_user_plants($username);

        $selectedPlant =
            $this->input->get('plant', TRUE);


        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }


        $filters = [

            'plant' => $plantFilter,
            'item'  => $this->input->get('item', TRUE),

            'date1' => $this->input->get('date1', TRUE),
            'date2' => $this->input->get('date2', TRUE),
        ];


        $rows =
            $this->ReportSales_model
                ->get_sales_item(0,0,$filters);


        $grouped = [];

        foreach ($rows as $r) {
            $grouped[$r->PLANT][] = $r;
        }


        $gQty=0;
        $gBerat=0;
        $gAmount=0;


        $html = "

        <style>
            table{border-collapse:collapse;width:100%;font-size:10px}
            th,td{border:1px solid #000;padding:4px}
            th{background:#eee}
            .r{text-align:right}
            .b{font-weight:bold}
        </style>

        <h3 align='center'>REPORT SALES BY ITEM</h3>

        <table>

        <tr>
            <th>PLANT</th>
            <th>ITEM</th>
            <th>QTY</th>
            <th>BERAT</th>
            <th>AMOUNT</th>
        </tr>

        ";


        foreach ($grouped as $plant => $group) {

            $subQty=0;
            $subBerat=0;
            $subAmount=0;

            foreach ($group as $r) {

                $html .= "

                <tr>

                    <td>{$r->PLANT_NAME}</td>

                    <td>
                        {$r->ITEM_NAME}<br>
                        <b>{$r->ITEM}</b>
                    </td>

                    <td class='r'>".number_format($r->QTY,2)."</td>
                    <td class='r'>".number_format($r->BERAT,2)."</td>
                    <td class='r'>".number_format($r->AMOUNT,0,",",".")."</td>

                </tr>
                ";

                $subQty += $r->QTY;
                $subBerat += $r->BERAT;
                $subAmount += $r->AMOUNT;

                $gQty += $r->QTY;
                $gBerat += $r->BERAT;
                $gAmount += $r->AMOUNT;
            }


            $html .= "

            <tr class='b'>

                <td colspan='2'>
                    SUBTOTAL {$group[0]->PLANT_NAME}
                </td>

                <td class='r'>".number_format($subQty,2)."</td>
                <td class='r'>".number_format($subBerat,2)."</td>
                <td class='r'>".number_format($subAmount,0,",",".")."</td>

            </tr>
            ";
        }


        $html .= "

        <tr class='b'>

            <td colspan='2'>GRAND TOTAL</td>

            <td class='r'>".number_format($gQty,2)."</td>
            <td class='r'>".number_format($gBerat,2)."</td>
            <td class='r'>".number_format($gAmount,0,",",".")."</td>

        </tr>

        </table>
        ";


        $mpdf = new \Mpdf\Mpdf([
            'orientation'=>'L'
        ]);

        $mpdf->WriteHTML($html);

        $mpdf->Output(
            'report_sales_item.pdf',
            'I'
        );
    }

    private function build_pagination($totalPages, $currentPage, $mode = 'url')
    {
        if ($totalPages <= 1) return '';

        $html = '<ul class="pagination pagination-sm mb-0">';

        $range = 2; // tampilkan ±2 halaman dari current

        $start = max(1, $currentPage - $range);
        $end   = min($totalPages, $currentPage + $range);

        // ================= PREV =================
        if ($currentPage > 1) {
            $prev = $currentPage - 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$prev'>&laquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$prev' class='page-link'>&laquo;</a>
                    </li>
                ";
            }
        }

        // ================= FIRST PAGE =================
        if ($start > 1) {
            $html .= $this->page_link(1, $currentPage, $mode);
            if ($start > 2) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }

        // ================= MIDDLE PAGES =================
        for ($i = $start; $i <= $end; $i++) {
            $html .= $this->page_link($i, $currentPage, $mode);
        }

        // ================= LAST PAGE =================
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $html .= $this->page_link($totalPages, $currentPage, $mode);
        }

        // ================= NEXT =================
        if ($currentPage < $totalPages) {
            $next = $currentPage + 1;

            if ($mode === 'ajax') {
                $html .= "
                    <li class='page-item'>
                        <a href='#' class='page-link' data-page='$next'>&raquo;</a>
                    </li>
                ";
            } else {
                $html .= "
                    <li class='page-item'>
                        <a href='?page=$next' class='page-link'>&raquo;</a>
                    </li>
                ";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    private function page_link($page, $currentPage, $mode)
    {
        $active = $page == $currentPage ? 'active' : '';

        if ($mode === 'ajax') {
            return "
                <li class='page-item $active'>
                    <a href='#' class='page-link' data-page='$page'>$page</a>
                </li>
            ";
        } else {
            return "
                <li class='page-item $active'>
                    <a href='?page=$page' class='page-link'>$page</a>
                </li>
            ";
        }
    }

    private function convert_date($date)
    {
        if (!$date) return null;

        // support yyyy-mm-dd (dari input date)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // support dd/mm/yyyy (kalau ada)
        $d = DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }

}
