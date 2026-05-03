<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once FCPATH . 'vendor/autoload.php';

class ReportInventory extends MY_Controller {
    

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('report_inventory_po')) {
            show_404();
        }
        $this->load->model('ReportInventory_model');
        $this->load->library('session');
        $this->load->helper(['url','file']);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    public function index()
    {
        $data = [
            'plants'     => $this->ReportInventory_model->get_plant_list(),
            'suppliers'  => $this->ReportInventory_model->get_supplier_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report Inventory']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_inventory/index', $data);
        $this->load->view('templates/footer');
    }

    public function po()
    {
        $data = [
            'plants'     => $this->ReportInventory_model->get_plant_list(),
            'suppliers'  => $this->ReportInventory_model->get_supplier_list(),
            'userPlant'  => $this->session->userdata('plant')
        ];

        $this->load->view('templates/header', ['title' => 'Report PO']);
        $this->load->view('templates/sidebar');
        $this->load->view('admin/report_inventory/po', $data);
        $this->load->view('templates/footer');
    }

    public function load_data()
    {
        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $order  = $this->input->get('order', TRUE) ?: 'PO_DATE';
        $dirInput = $this->input->get('dir', TRUE) ?? 'DESC'; // default 'DESC' kalau null
        $dir = strtoupper($dirInput) === 'DESC' ? 'DESC' : 'ASC';

        $po = $this->input->get('po', TRUE);
        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        // ===== FILTER =====
        $filters = [
            'role_id'   => $this->session->userdata('role_id'),
            'plant'     => $this->input->get('plant', TRUE),
            'supplier'   => $this->input->get('supplier', TRUE),
            'po'         => $this->input->get('po', TRUE),
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $start = ($page - 1) * $limit;

        $rows  = $this->ReportInventory_model->get_po_report(
            $limit,
            $start,
            $filters,
            $order,
            $dir
        );

        $totalRows = $this->ReportInventory_model->count_po_report($filters);

        // ===== TOTAL (TANPA LIMIT) =====
        $allRows = $this->ReportInventory_model
            ->get_po_report(0, 0, $filters, $order, $dir);

        $grand = [
            'jumlah' => 0,
            'berat'  => 0,
            'total'  => 0
        ];

        foreach ($allRows as $r) {
            $grand['jumlah'] += (float) $r->JUMLAH;
            $grand['berat']  += (float) $r->BERAT;
            $grand['total']  += (float) str_replace(['.', ','], '', $r->TOTAL);
        }

        $pages = ceil($totalRows / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand, // 🔥 dikirim ke view
            'pagination' => $pagination,
            'page'       => $page
        ]);
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

    public function export_excel_po()
    {
        // Convert tanggal ke Y-m-d sama seperti load_data()
        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        $filters = [
            'plant'      => $this->input->get('plant', TRUE),
            'supplier'   => $this->input->get('supplier', TRUE),
            'po'         => $this->input->get('po', TRUE),
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model->get_po_report(0,0,$filters,'PO_DATE','DESC'); // ambil semua data

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $headers = ['PLANT','DATE','NO PO','SUPPLIER','MATERIAL','JUMLAH','BERAT','HARGA','TOTAL'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        // ================= GROUP DATA =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PO . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        // ================= FILL DATA =================
        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {
                $harga = (float) str_replace(['.', ','], '', $r->HARGA);
                $total = (float) str_replace(['.', ','], '', $r->TOTAL);

                // Kolom detail (selalu ditulis)
                $sheet->setCellValue("E{$rowExcel}", $r->MATERIAL_NAME);
                $sheet->setCellValue("F{$rowExcel}", $r->JUMLAH);
                $sheet->setCellValue("G{$rowExcel}", $r->BERAT);
                $sheet->setCellValueExplicit("H$rowExcel", $harga, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("I$rowExcel", $total, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                // Kolom header PO → hanya tulis di baris pertama
                if ($i === 0) {
                    $sheet->setCellValue("A{$rowExcel}", $r->PLANT_NAME);
                    $sheet->setCellValue("B{$rowExcel}", date('d/m/Y', strtotime($r->PO_DATE)));
                    $sheet->setCellValue("C{$rowExcel}", $r->PO);
                    $sheet->setCellValue("D{$rowExcel}", $r->SUPPLIER_NAME);
                }

                $rowExcel++;
            }

            // ================= MERGE CELL =================
            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;

                $sheet->mergeCells("A{$startRow}:A{$endRow}");
                $sheet->mergeCells("B{$startRow}:B{$endRow}");
                $sheet->mergeCells("C{$startRow}:C{$endRow}");
                $sheet->mergeCells("D{$startRow}:D{$endRow}");

                // Vertical align tengah
                foreach (['A','B','C','D'] as $col) {
                    $sheet->getStyle("{$col}{$startRow}")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }
        }

        $sheet->setCellValue("E{$rowExcel}", 'GRAND TOTAL');
        $sheet->mergeCells("E{$rowExcel}:H{$rowExcel}");
        $sheet->getStyle("E{$rowExcel}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValueExplicit(
            "I{$rowExcel}",
            array_sum(array_map(function($r){
                return (float) str_replace(['.', ','], '', $r->TOTAL);
            }, $rows)),
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
        );

        $sheet->getStyle("E{$rowExcel}:I{$rowExcel}")->getFont()->setBold(true);

        // Auto width
        foreach (range('A','I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format angka
        $sheet->getStyle("F2:I" . ($rowExcel - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // ================= OUTPUT =================
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_po.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf_po()
    {
        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        $filters = [
            'plant'      => $this->input->get('plant', TRUE),
            'supplier'   => $this->input->get('supplier', TRUE),
            'po'         => $this->input->get('po', TRUE),
            'date_from'  => $date_from,
            'date_to'    => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model
            ->get_po_report(0, 0, $filters, 'PO_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        // ================= GROUP DATA =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->PO . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        // ================= BUILD HTML =================
        $html = '
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                font-size: 9px;
            }
            th, td {
                border: 1px solid #000;
                padding: 4px;
            }
            th {
                background: #eee;
                text-align: center;
            }
            td.text-right { text-align: right; }
            td.text-center { text-align: center; }
        </style>

        <h3 style="text-align:center">REPORT PO</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>NO PO</th>
                    <th>SUPPLIER</th>
                    <th>MATERIAL</th>
                    <th>JUMLAH</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($grouped as $group) {

            $rowspan = count($group);

            foreach ($group as $i => $r) {

                $html .= '<tr>';

                if ($i === 0) {
                    $html .= '
                        <td rowspan="'.$rowspan.'" class="text-center">'.$r->PLANT_NAME.'</td>
                        <td rowspan="'.$rowspan.'" class="text-center">'.date('d/m/Y', strtotime($r->PO_DATE)).'</td>
                        <td rowspan="'.$rowspan.'" class="text-center"><b>'.$r->PO.'</b></td>
                        <td rowspan="'.$rowspan.'">'.$r->SUPPLIER_NAME.'</td>
                    ';
                }

                $harga = (int) str_replace(['.', ','], '', $r->HARGA);
                $total = (int) str_replace(['.', ','], '', $r->TOTAL);  
                $hargaFormatted = number_format($harga, 0, ',', '.');
                $totalFormatted = number_format($total, 0, ',', '.');

                $html .= '
                    <td>'.$r->MATERIAL_NAME.'</td>
                    <td class="text-right">'.number_format($r->JUMLAH, 2).'</td>
                    <td class="text-right">'.number_format($r->BERAT, 2).'</td>
                    <td class="text-right">'.$hargaFormatted.'</td>
                    <td class="text-right">'.$totalFormatted.'</td>
                ';

                $html .= '</tr>';
            }
        }

        $grandJumlah = 0;
        $grandBerat  = 0;
        $grandTotal  = 0;

        foreach ($rows as $r) {
            $grandJumlah += (float)$r->JUMLAH;
            $grandBerat  += (float)$r->BERAT;
            $grandTotal  += (float)str_replace(['.', ','], '', $r->TOTAL);
        }

        $html .= '
        <tr style="font-weight:bold;background:#eee">
            <td colspan="5" class="text-right">GRAND TOTAL</td>
            <td class="text-right">'.number_format($grandJumlah,2).'</td>
            <td class="text-right">'.number_format($grandBerat,2).'</td>
            <td></td>
            <td class="text-right">'.number_format($grandTotal,0,",",".").'</td>
        </tr>
        ';

        $html .= '</tbody></table>';

        // ================= RENDER PDF =================
        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L', // landscape mirip excel
            'margin_left' => 10,
            'margin_right'=> 10,
            'margin_top'  => 10,
            'margin_bottom'=> 10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('report_po.pdf', 'I');
    }

    // RECEIVE REPORT 

    public function load_receive()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $order  = $this->input->get('order', TRUE) ?: 'RECEIVE_DATE';

        $dirInput = $this->input->get('dir', TRUE) ?? 'DESC';
        $dir = strtoupper($dirInput) === 'DESC' ? 'DESC' : 'ASC';

        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        $filters = [
            'role_id'   => $this->session->userdata('role_id'),
            'plant'     => $this->input->get('plant', TRUE),
            'supplier'  => $this->input->get('supplier', TRUE),
            'receive'   => $this->input->get('receive', TRUE) ?: $this->input->get('po', TRUE),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($filters['role_id'] != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $start = ($page - 1) * $limit;

        // ================= DATA PAGE =================
        $rows = $this->ReportInventory_model
            ->get_receive_report($limit, $start, $filters, $order, $dir);

        $totalRows = $this->ReportInventory_model
            ->count_receive_report($filters);

        // ================= GRAND TOTAL (NO LIMIT) =================
        $allRows = $this->ReportInventory_model
            ->get_receive_report(0, 0, $filters, $order, $dir);

        $grand = [
            'jumlah' => 0,
            'berat'  => 0,
            'total'  => 0
        ];

        foreach ($allRows as $r) {
            $grand['jumlah'] += (float)$r->JUMLAH;
            $grand['berat']  += (float)$r->BERAT;
            $grand['total']  += (float)str_replace(['.', ','], '', $r->TOTAL);
        }

        $pages = ceil($totalRows / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $totalRows,
            'grand'      => $grand, // 🔥 penting
            'pagination' => $pagination,
            'page'       => $page
        ]);
        exit;
    }

    public function export_excel_receive()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'supplier'  => $this->input->get('supplier'),
            'receive'   => $this->input->get('receive') ?: $this->input->get('po'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model
            ->get_receive_report(0, 0, $filters, 'RECEIVE_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $headers = [
            'PLANT','DATE','NO RECEIVE','NO PO','SUPPLIER',
            'MATERIAL','JUMLAH','BERAT','HARGA','TOTAL'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        // ================= GROUP =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        // ================= FILL =================
        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {

                $harga = (float) str_replace(['.', ','], '', $r->HARGA);
                $total = (float) str_replace(['.', ','], '', $r->TOTAL);

                // DETAIL
                $sheet->setCellValue("F$rowExcel", $r->MATERIAL_NAME);
                $sheet->setCellValue("G$rowExcel", $r->JUMLAH);
                $sheet->setCellValue("H$rowExcel", $r->BERAT);
                $sheet->setCellValueExplicit("I$rowExcel", $harga, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $sheet->setCellValueExplicit("J$rowExcel", $total, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

                // HEADER RECEIVE
                if ($i === 0) {
                    $sheet->setCellValue("A$rowExcel", $r->PLANT_NAME);
                    $sheet->setCellValue("B$rowExcel", date('d/m/Y', strtotime($r->RECEIVE_DATE)));
                    $sheet->setCellValue("C$rowExcel", $r->RECEIVE);
                    $sheet->setCellValue("D$rowExcel", $r->PO);
                    $sheet->setCellValue("E$rowExcel", $r->SUPPLIER_NAME);
                }

                $rowExcel++;
            }

            // ================= MERGE =================
            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;

                foreach (['A','B','C','D','E'] as $c) {
                    $sheet->mergeCells("$c$startRow:$c$endRow");
                    $sheet->getStyle("$c$startRow")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }
        }

        // ================= GRAND TOTAL =================
        $sheet->setCellValue("F{$rowExcel}", 'GRAND TOTAL');
        $sheet->mergeCells("F{$rowExcel}:I{$rowExcel}");

        $sheet->getStyle("F{$rowExcel}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("G{$rowExcel}", array_sum(array_column($rows,'JUMLAH')));
        $sheet->setCellValue("H{$rowExcel}", array_sum(array_column($rows,'BERAT')));

        $sheet->setCellValueExplicit(
            "J{$rowExcel}",
            array_sum(array_map(function($r){
                return (float)str_replace(['.', ','], '', $r->TOTAL);
            }, $rows)),
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
        );

        $sheet->getStyle("F{$rowExcel}:J{$rowExcel}")
            ->getFont()
            ->setBold(true);

        // AUTO WIDTH
        foreach (range('A','J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // FORMAT ANGKA
        $sheet->getStyle("G2:J".($rowExcel-1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_receive.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_receive()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'supplier'  => $this->input->get('supplier'),
            'receive'   => $this->input->get('receive') ?: $this->input->get('po'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model
            ->get_receive_report(0, 0, $filters, 'RECEIVE_DATE', 'DESC');

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}
            .c{text-align:center}
        </style>

        <h3 style="text-align:center">REPORT RECEIVE</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>NO RECEIVE</th>
                    <th>NO PO</th>
                    <th>SUPPLIER</th>
                    <th>MATERIAL</th>
                    <th>JUMLAH</th>
                    <th>BERAT</th>
                    <th>HARGA</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($grouped as $group) {
            $rowspan = count($group);

            foreach ($group as $i => $r) {
                $harga = number_format((float)str_replace(['.',','],'',$r->HARGA),0,',','.');
                $total = number_format((float)str_replace(['.',','],'',$r->TOTAL),0,',','.');

                $html .= '<tr>';

                if ($i === 0) {
                    $html .= "
                        <td rowspan='$rowspan' class='c'>$r->PLANT_NAME</td>
                        <td rowspan='$rowspan' class='c'>".date('d/m/Y',strtotime($r->RECEIVE_DATE))."</td>
                        <td rowspan='$rowspan' class='c'><b>$r->RECEIVE</b></td>
                        <td rowspan='$rowspan' class='c'>$r->PO</td>
                        <td rowspan='$rowspan'>$r->SUPPLIER_NAME</td>
                    ";
                }

                $html .= "
                    <td>$r->MATERIAL_NAME</td>
                    <td class='r'>".number_format($r->JUMLAH,2)."</td>
                    <td class='r'>".number_format($r->BERAT,2)."</td>
                    <td class='r'>$harga</td>
                    <td class='r'>$total</td>
                </tr>";
            }
        }

        $gtJumlah = 0;
        $gtBerat  = 0;
        $gtTotal  = 0;

        foreach ($rows as $r) {
            $gtJumlah += (float)$r->JUMLAH;
            $gtBerat  += (float)$r->BERAT;
            $gtTotal  += (float)str_replace(['.', ','], '', $r->TOTAL);
        }

        $html .= "
        <tr style='font-weight:bold;background:#eee'>
            <td colspan='6' class='r'>GRAND TOTAL</td>
            <td class='r'>".number_format($gtJumlah,2)."</td>
            <td class='r'>".number_format($gtBerat,2)."</td>
            <td></td>
            <td class='r'>".number_format($gtTotal,0,',','.')."</td>
        </tr>
        ";

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf([
            'orientation'=>'L',
            'margin_left'=>10,
            'margin_right'=>10,
            'margin_top'=>10,
            'margin_bottom'=>10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('report_receive.pdf','I');
    }

    public function load_receive_lb()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page   = (int)$this->input->get('page') ?: 1;
        $limit  = (int)$this->input->get('limit') ?: 10;
        $order  = $this->input->get('order', TRUE) ?: 'RECEIVE_DATE';

        $dirInput = $this->input->get('dir', TRUE) ?? 'DESC';
        $dir = strtoupper($dirInput) === 'DESC' ? 'DESC' : 'ASC';

        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        // ======================
        // PLANT FILTER (STANDARD)
        // ======================
        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant', TRUE);

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $plantFilter = [$selectedPlant];

        } else {

            $plantFilter = $userPlants;
        }

        $filters = [
            'plant'     => $plantFilter,
            'supplier'  => $this->input->get('supplier', TRUE),
            'receive'   => $this->input->get('receive', TRUE),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $start = ($page - 1) * $limit;

        $rows  = $this->ReportInventory_model
            ->get_receive_lb_report($limit, $start, $filters, $order, $dir);

        $total = $this->ReportInventory_model
            ->count_receive_lb_report($filters);

        $grand = $this->ReportInventory_model
            ->get_receive_lb_grand_total($filters);

        $pages = ceil($total / $limit);
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'grand'      => $grand,
            'pagination' => $pagination,
            'page'       => $page
        ]);
        exit;
    }

    public function export_excel_receive_lb()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'supplier'  => $this->input->get('supplier'),
            'receive'   => $this->input->get('receive'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $filters['plant'] = [$selectedPlant];

        } else {

            $filters['plant'] = $userPlants;
        }

        $rows = $this->ReportInventory_model->get_receive_lb_report(0, 0, $filters, 'RECEIVE_DATE', 'DESC');
        $grand = $this->ReportInventory_model->get_receive_lb_grand_total($filters);

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ================= HEADER =================
        $headers = [
            'PLANT','DATE','NO RECEIVE','PEMBAYARAN','JENIS PAY','SLIP NO','DO','SUPPLIER','DRIVER','NO CAR',
            'ARRIVE SCHEDULE','DEPART SCHEDULE','QTY','WEIGHT','AVG BW','PRICE','AMOUNT','DEAD','DEAD WEIGHT','SHRINK','RECEIVE AMOUNT','REMARK','STATUS'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.'1', $h);
            $col++;
        }

        $rowExcel = 2;

        // ================= GROUP =================
        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE.'|'.$r->PLANT;
            $grouped[$key][] = $r;
        }

        // ================= FILL =================
        foreach ($grouped as $group) {

            $startRow = $rowExcel;
            $rowspan  = count($group);

            foreach ($group as $i => $r) {
                // DETAIL
                $sheet->setCellValue("K$rowExcel", $r->ARRIVE_SCHEDULE);
                $sheet->setCellValue("L$rowExcel", $r->DEPART_SCHEDULE);
                $sheet->setCellValue("M$rowExcel", $r->QTY);
                $sheet->setCellValue("N$rowExcel", $r->WEIGHT);
                $sheet->setCellValue("O$rowExcel", $r->AVG_BW);
                $sheet->setCellValue("P$rowExcel", $r->PRICE);
                $sheet->setCellValue("Q$rowExcel", $r->AMOUNT);
                $sheet->setCellValue("R$rowExcel", $r->DEAD);
                $sheet->setCellValue("S$rowExcel", $r->DEAD_WEIGHT);
                $sheet->setCellValue("T$rowExcel", $r->SHRINK);
                $sheet->setCellValue("U$rowExcel", $r->RECEIVE_AMOUNT);
                $sheet->setCellValue("V$rowExcel", $r->REMARK);
                $sheet->setCellValue("W$rowExcel", $r->STATUS);

                // HEADER RECEIVE
                if ($i === 0) {
                    $sheet->setCellValue("A$rowExcel", $r->PLANT_NAME);
                    $sheet->setCellValue("B$rowExcel", date('d/m/Y', strtotime($r->RECEIVE_DATE)));
                    $sheet->setCellValue("C$rowExcel", $r->RECEIVE);
                    $sheet->setCellValue("D$rowExcel", $r->PEMBAYARAN);
                    $sheet->setCellValue("E$rowExcel", $r->JENIS_PAY);
                    $sheet->setCellValue("F$rowExcel", $r->SLIP_NO);
                    $sheet->setCellValue("G$rowExcel", $r->DO);
                    $sheet->setCellValue("H$rowExcel", $r->SUPPLIER_NAME);
                    $sheet->setCellValue("I$rowExcel", $r->DRIVER);
                    $sheet->setCellValue("J$rowExcel", $r->NO_CAR);
                }

                $rowExcel++;
            }

            // ================= MERGE =================
            if ($rowspan > 1) {
                $endRow = $startRow + $rowspan - 1;
                foreach (range('A','J') as $c) {
                    $sheet->mergeCells("$c$startRow:$c$endRow");
                    $sheet->getStyle("$c$startRow")
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }
        }

        // ================= GRAND TOTAL =================
        $sheet->setCellValue("A$rowExcel", 'GRAND TOTAL');
        $sheet->mergeCells("A$rowExcel:J$rowExcel");

        $sheet->setCellValue("N$rowExcel", $grand->total_berat);
        $sheet->setCellValue("Q$rowExcel", $grand->total_amount);

        // STYLE
        $sheet->getStyle("A$rowExcel:W$rowExcel")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK]
            ]
        ]);

        // FORMAT NUMBER
        $sheet->getStyle("N$rowExcel:Q$rowExcel")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // AUTO WIDTH
        foreach (range('A','W') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="report_receive_lb.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_receive_lb()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'supplier'  => $this->input->get('supplier'),
            'receive'   => $this->input->get('receive'),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        $userPlants = json_decode($this->session->userdata('plant'), true);
        $selectedPlant = $this->input->get('plant');

        if (!empty($selectedPlant)) {

            if (!in_array($selectedPlant, $userPlants)) {
                show_error('Unauthorized plant access');
            }

            $filters['plant'] = [$selectedPlant];

        } else {

            $filters['plant'] = $userPlants;
        }

        $rows  = $this->ReportInventory_model
            ->get_receive_lb_report(0, 0, $filters, 'RECEIVE_DATE', 'DESC');

        $grand = $this->ReportInventory_model
            ->get_receive_lb_grand_total($filters);

        if (empty($rows)) {
            show_error('No data found for export');
        }

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r->RECEIVE . '|' . $r->PLANT;
            $grouped[$key][] = $r;
        }

        $html = '
        <style>
            table{border-collapse:collapse;width:100%;font-size:9px}
            th,td{border:1px solid #000;padding:4px}
            th{text-align:center;background:#eee}
            .r{text-align:right}
            .c{text-align:center}
        </style>

        <h3 style="text-align:center">REPORT RECEIVE LB</h3>

        <table>
            <thead>
                <tr>
                    <th>PLANT</th>
                    <th>DATE</th>
                    <th>NO RECEIVE</th>
                    <th>PEMBAYARAN</th>
                    <th>JENIS PAY</th>
                    <th>SLIP NO</th>
                    <th>DO</th>
                    <th>SUPPLIER</th>
                    <th>DRIVER</th>
                    <th>NO CAR</th>
                    <th>ARRIVE SCHEDULE</th>
                    <th>DEPART SCHEDULE</th>
                    <th>QTY</th>
                    <th>WEIGHT</th>
                    <th>AVG BW</th>
                    <th>PRICE</th>
                    <th>AMOUNT</th>
                    <th>DEAD</th>
                    <th>DEAD WEIGHT</th>
                    <th>SHRINK</th>
                    <th>RECEIVE AMOUNT</th>
                    <th>REMARK</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($grouped as $group) {
            $rowspan = count($group);

            foreach ($group as $i => $r) {
                $html .= '<tr>';

                if ($i === 0) {
                    $html .= "
                        <td rowspan='{$rowspan}' class='c'>{$r->PLANT_NAME}</td>
                        <td rowspan='{$rowspan}' class='c'>".date('d/m/Y',strtotime($r->RECEIVE_DATE))."</td>
                        <td rowspan='{$rowspan}' class='c'>{$r->RECEIVE}</td>
                        <td rowspan='{$rowspan}'>{$r->PEMBAYARAN}</td>
                        <td rowspan='{$rowspan}'>{$r->JENIS_PAY}</td>
                        <td rowspan='{$rowspan}'>{$r->SLIP_NO}</td>
                        <td rowspan='{$rowspan}'>{$r->DO}</td>
                        <td rowspan='{$rowspan}'>{$r->SUPPLIER_NAME}</td>
                        <td rowspan='{$rowspan}'>{$r->DRIVER}</td>
                        <td rowspan='{$rowspan}'>{$r->NO_CAR}</td>
                    ";
                }

                $html .= "
                    <td>{$r->ARRIVE_SCHEDULE}</td>
                    <td>{$r->DEPART_SCHEDULE}</td>
                    <td class='r'>{$r->QTY}</td>
                    <td class='r'>{$r->WEIGHT}</td>
                    <td class='r'>{$r->AVG_BW}</td>
                    <td class='r'>{$r->PRICE}</td>
                    <td class='r'>{$r->AMOUNT}</td>
                    <td class='r'>{$r->DEAD}</td>
                    <td class='r'>{$r->DEAD_WEIGHT}</td>
                    <td class='r'>{$r->SHRINK}</td>
                    <td class='r'>{$r->RECEIVE_AMOUNT}</td>
                    <td>{$r->REMARK}</td>
                    <td>{$r->STATUS}</td>
                </tr>";
            }
        }

        $html .= "
            <tr style='font-weight:bold;border-top:2px solid #000'>
                <td colspan='13' class='c'>GRAND TOTAL</td>
                <td class='r'>".number_format($grand->total_berat,2)."</td>
                <td colspan='2'></td>
                <td class='r'>".number_format($grand->total_amount,0,',','.')."</td>
                <td colspan='6'></td>
            </tr>
            </tbody>
        </table>
        ";

        $mpdf = new \Mpdf\Mpdf([
            'orientation'   => 'L',
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_top'    => 10,
            'margin_bottom' => 10
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('report_receive_lb.pdf','I');
    }

    public function load_material_balance()
    {
        ob_clean();
        header('Content-Type: application/json');

        $page  = max((int)$this->input->get('page'), 1);
        $limit = max((int)$this->input->get('limit'), 1);

        $date_from = $this->convert_date($this->input->get('date_from', TRUE));
        $date_to   = $this->convert_date($this->input->get('date_to', TRUE));

        $plantInput = $this->input->get('plant', TRUE);

        $filters = [
            'role_id'   => $this->session->userdata('role_id'),
            'plant'     => ($plantInput === '*' ? '' : $plantInput),
            'material'  => trim($this->input->get('material', TRUE)),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($filters['role_id'] != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $start = ($page - 1) * $limit;

        $rows  = $this->ReportInventory_model->get_material_balance($limit, $start, $filters);
        $total = $this->ReportInventory_model->count_material_balance($filters);

        // 🔥 TOTAL SELURUH DATA (TANPA LIMIT)
        $grandTotal = $this->ReportInventory_model->get_material_balance_total($filters);

        $pages = $limit ? ceil($total / $limit) : 1;
        $pagination = $this->build_pagination($pages, $page, 'ajax');

        echo json_encode([
            'rows'       => $rows,
            'total'      => $total,
            'pagination' => $pagination,
            'page'       => $page,
            'grand'      => $grandTotal
        ]);
        exit;
    }

    public function export_excel_material_balance()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'material'  => trim($this->input->get('material')),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model->get_material_balance(0, 0, $filters);

        if (empty($rows)) show_error('No data found for export');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['PLANT','MATERIAL','BEGIN QTY','BEGIN BW','IN QTY','IN BW','OUT QTY','OUT BW','END QTY','END BW'];

        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).'1', $h);
        }

        $rowExcel = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue("A$rowExcel", $r->plant_name);
            $sheet->setCellValue("B$rowExcel", $r->material.' - '.$r->material_name);
            $sheet->setCellValue("C$rowExcel", $r->BEGIN_qty);
            $sheet->setCellValue("D$rowExcel", $r->BEGIN_bw);
            $sheet->setCellValue("E$rowExcel", $r->in_qty);
            $sheet->setCellValue("F$rowExcel", $r->in_bw);
            $sheet->setCellValue("G$rowExcel", $r->out_qty);
            $sheet->setCellValue("H$rowExcel", $r->out_bw);
            $sheet->setCellValue("I$rowExcel", $r->END_qty);
            $sheet->setCellValue("J$rowExcel", $r->END_bw);
            $rowExcel++;
        }

        foreach (range('A','J') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="material_balance.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    public function export_pdf_material_balance()
    {
        $date_from = $this->convert_date($this->input->get('date_from'));
        $date_to   = $this->convert_date($this->input->get('date_to'));

        $filters = [
            'plant'     => $this->input->get('plant'),
            'material'  => trim($this->input->get('material')),
            'date_from' => $date_from,
            'date_to'   => $date_to,
        ];

        if ($this->session->userdata('role_id') != 1) {
            $filters['plant'] = $this->session->userdata('plant');
        }

        $rows = $this->ReportInventory_model->get_material_balance(0, 0, $filters);

        if (empty($rows)) show_error('No data found for export');

        $html = '
        <style>
            table {border-collapse: collapse; width: 100%; font-size: 10px}
            th, td {border: 1px solid #000; padding: 4px}
            th {background: #eee; text-align:center}
            .r {text-align:right}
            .l {text-align:left}
        </style>
        <h3 style="text-align:center">MATERIAL BALANCE</h3>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">PLANT</th>
                    <th rowspan="2">MATERIAL</th>
                    <th colspan="2">BEGIN</th>
                    <th colspan="2">IN</th>
                    <th colspan="2">OUT</th>
                    <th colspan="2">END</th>
                </tr>
                <tr>
                    <th>QTY</th><th>BW</th>
                    <th>QTY</th><th>BW</th>
                    <th>QTY</th><th>BW</th>
                    <th>QTY</th><th>BW</th>
                </tr>
            </thead><tbody>';

        foreach ($rows as $r) {
            $html .= "<tr>
                <td class='l'>{$r->plant_name}</td>
                <td class='l'>{$r->material} - {$r->material_name}</td>
                <td class='r'>".number_format($r->BEGIN_qty,2,',','.')."</td>
                <td class='r'>".number_format($r->BEGIN_bw,2,',','.')."</td>
                <td class='r'>".number_format($r->in_qty,2,',','.')."</td>
                <td class='r'>".number_format($r->in_bw,2,',','.')."</td>
                <td class='r'>".number_format($r->out_qty,2,',','.')."</td>
                <td class='r'>".number_format($r->out_bw,2,',','.')."</td>
                <td class='r'>".number_format($r->END_qty,2,',','.')."</td>
                <td class='r'>".number_format($r->END_bw,2,',','.')."</td>
            </tr>";
        }

        $html .= '</tbody></table>';

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
        $mpdf->WriteHTML($html);
        $mpdf->Output('material_balance.pdf', 'I');
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
