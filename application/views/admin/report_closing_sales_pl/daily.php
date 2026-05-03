<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>

<style>
    th, td {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>
<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Date</label>
        <input type="date" id="filter_date" class="form-control">
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-5">
    </div>

    <!-- EXPORT -->
    <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered" id="salesPlTable">
        <thead class="table-light">

            <tr>
                <th rowspan="2" class="text-center">PLANT</th>
                <th rowspan="2" class="text-center">DATE</th>
                <th rowspan="2" class="text-center">ITEM</th>
                <th rowspan="2" class="text-center">ITEM NAME</th>
                <th rowspan="2" class="text-center">CLASS</th>

                <th colspan="3" class="text-center">BEGINNING</th>
                <th colspan="3" class="text-center">PRODUCTION</th>
                <th colspan="3" class="text-center">PURCHASE</th>
                <th colspan="3" class="text-center">ADJUST</th>
                <th colspan="3" class="text-center">COGS</th>

                <th rowspan="2" class="text-center">ENDING</th>
                <th rowspan="2" class="text-center">NET SALES</th>
                <th rowspan="2" class="text-center">PROFIT</th>
            </tr>

            <tr>
                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>
            </tr>

        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">

                <td colspan="5" class="text-end">GRAND TOTAL</td>

                <td id="gt_bg_bw" class="text-end"></td>
                <td id="gt_bg_up" class="text-end"></td>
                <td id="gt_begin" class="text-end"></td>

                <td id="gt_pd_bw" class="text-end"></td>
                <td id="gt_pd_up" class="text-end"></td>
                <td id="gt_prod" class="text-end"></td>

                <td id="gt_pr_bw" class="text-end"></td>
                <td id="gt_pr_up" class="text-end"></td>
                <td id="gt_purchase" class="text-end"></td>

                <td id="gt_ds_bw" class="text-end"></td>
                <td id="gt_ds_up" class="text-end"></td>
                <td id="gt_adjust" class="text-end"></td>

                <td id="gt_cogs_bw" class="text-end"></td>
                <td id="gt_cogs_up" class="text-end"></td>
                <td id="gt_cogs" class="text-end"></td>

                <td id="gt_end" class="text-end"></td>
                <td id="gt_net" class="text-end"></td>
                <td id="gt_profit" class="text-end"></td>

            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-2">
    <div id="info"></div>
    <div id="pagination"></div>
</div>

<script>
    var state = { page:1, limit:50 };

    $(document).ready(function(){

        $('#filter_plant').select2({ width:'100%' });

        const today = new Date();
        $('#filter_date').val(today.toISOString().slice(0,10));

        loadPage();

        $('#btnFilter').click(()=>loadPage(1));
    });

    $('#exportExcel').click(function(e){

        e.preventDefault();

        const plant = $('#filter_plant').val();
        const date  = $('#filter_date').val();

        if(!date){
            alert('Date required');
            return;
        }

        const url = "<?= base_url('report-closing-sales-pl/export_excel_daily_sales_pl'); ?>?plant="+plant+"&date="+date;

        window.open(url,'_blank');

    });


    $('#exportPDF').click(function(e){

        e.preventDefault();

        const plant = $('#filter_plant').val();
        const date  = $('#filter_date').val();

        if(!date){
            alert('Date required');
            return;
        }

        const url = "<?= base_url('report-closing-sales-pl/export_pdf_daily_sales_pl'); ?>?plant="+plant+"&date="+date;

        window.open(url,'_blank');

    });

    /* ================= UTIL ================= */

    function rupiah(x){
        return parseFloat(x||0).toLocaleString('id-ID');
    }

    function num(x){
        return parseFloat(x||0).toLocaleString('id-ID',{minimumFractionDigits:2});
    }

    function formatYMD(v){
        return v ? v.substr(6,2)+'/'+v.substr(4,2)+'/'+v.substr(0,4) : '-';
    }

    /* ================= LOAD ================= */

    function loadPage(p=1){

        state.page=p;

        $.get('<?= base_url("report-closing-sales-pl/load_daily_sales_pl"); ?>',{
            page:p,
            limit:state.limit,
            plant:$('#filter_plant').val(),
            date:$('#filter_date').val()
            },function(resp){
                resp=typeof resp==='string'?JSON.parse(resp):resp;
                const tbody=$('#salesPlTable tbody').empty();
                if(!resp.rows.length){
                    tbody.html('<tr><td colspan="23" class="text-center">No data</td></tr>');
                }else{
                resp.rows.forEach(r=>{
                tbody.append(`
                    <tr>
                        <td class="text-center"><b>${r.plant_name}</b></td>
                        <td class="text-center">${formatYMD(r.ymd)}</td>
                        <td class="text-center"><b>${r.item}</b></td>
                        <td class="text-center">${r.item_name}</td>
                        <td class="text-center">${r.class_name}</td>

                        <td class="text-end">${rupiah(r.bg_bw)}</td>
                        <td class="text-end">${num(r.bg_up)}</td>
                        <td class="text-end">${rupiah(r.begin_amt)}</td>

                        <td class="text-end">${rupiah(r.production_bw)}</td>
                        <td class="text-end">${num(r.production_up)}</td>
                        <td class="text-end">${rupiah(r.production_amt)}</td>

                        <td class="text-end">${rupiah(r.purchase_bw)}</td>
                        <td class="text-end">${num(r.purchase_up)}</td>
                        <td class="text-end">${rupiah(r.purchase_amt)}</td>

                        <td class="text-end">${rupiah(r.adjust_bw)}</td>
                        <td class="text-end">${num(r.adjust_up)}</td>
                        <td class="text-end">${rupiah(r.adjust_amt)}</td>

                        <td class="text-end">${rupiah(r.cogs_bw)}</td>
                        <td class="text-end">${num(r.cogs_up)}</td>
                        <td class="text-end">${rupiah(r.cogs_amt)}</td>

                        <td class="text-end">${rupiah(r.ending_amt)}</td>
                        <td class="text-end">${rupiah(r.sales_net_amt)}</td>
                        <td class="text-end">${rupiah(r.sales_profit_amt)}</td>
                    </tr>
                `);
        });

        }
            const g=resp.grand||{};

            $('#gt_bg_bw').text(rupiah(g.bg_bw));
            $('#gt_bg_up').text(num(g.bg_up));
            $('#gt_begin').text(rupiah(g.begin_amt));

            $('#gt_pd_bw').text(rupiah(g.production_bw));
            $('#gt_pd_up').text(num(g.production_up));
            $('#gt_prod').text(rupiah(g.production_amt));

            $('#gt_pr_bw').text(rupiah(g.purchase_bw));
            $('#gt_pr_up').text(num(g.purchase_up));
            $('#gt_purchase').text(rupiah(g.purchase_amt));

            $('#gt_ds_bw').text(rupiah(g.adjust_bw));
            $('#gt_ds_up').text(num(g.adjust_up));
            $('#gt_adjust').text(rupiah(g.adjust_amt));

            $('#gt_cogs_bw').text(rupiah(g.cogs_bw));
            $('#gt_cogs_up').text(num(g.cogs_up));
            $('#gt_cogs').text(rupiah(g.cogs_amt));

            $('#gt_end').text(rupiah(g.ending_amt));
            $('#gt_net').text(rupiah(g.sales_net_amt));
            $('#gt_profit').text(rupiah(g.sales_profit_amt));

            $('#pagination').html(resp.pagination);
            $('#info').text(`Total data : ${resp.total}`);

            });
        }

    /* ================= PAGINATION ================= */
    $(document).on('click','#pagination a',function(e){
        e.preventDefault();
        loadPage($(this).data('page'));
    });
</script>