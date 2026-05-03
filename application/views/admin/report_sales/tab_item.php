<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="filter_plant_item" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ITEM -->
    <div class="col-md-2">
        <label class="form-label">Item</label>
        <select id="filter_item_by" class="form-control">
            <option value="">-- All Item --</option>
        </select>
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="filter_date_from_item" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="filter_date_to_item" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilterItem" style="margin-top:-23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>
    <div class="col-md-1"></div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcelItem">Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDFItem">Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>


<div class="table-responsive">
    <table class="table table-bordered" id="salesItemTable">

        <thead>
            <tr>
                <th class="text-center" style="vertical-align: middle">PLANT</th>
                <th class="text-center" style="vertical-align: middle">ITEM</th>
                <th class="text-center" style="vertical-align: middle">QTY</th>
                <th class="text-center" style="vertical-align: middle">BERAT</th>
                <th class="text-center" style="vertical-align: middle">AMOUNT</th>
            </tr>
        </thead>

        <tbody></tbody>

        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="2" class="text-end">GRAND TOTAL</td>
                <td id="gt_qty_item" class="text-end">0</td>
                <td id="gt_berat_item" class="text-end">0</td>
                <td id="gt_amount_item" class="text-end">0</td>
            </tr>
        </tfoot>

    </table>
</div>


<div class="d-flex justify-content-between mt-3">
    <div id="info_item"></div>
    <div id="pagination_item"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
</style>

<script>

    var stateItem = {
        page: 1,
        limit: 24
    };

    $('#filter_item_by').select2({

        width: '100%',

        allowClear: true,

        placeholder: 'Search item',

        ajax: {

            url: '<?= base_url("report-sales/get_items"); ?>',

            dataType: 'json',

            delay: 250,

            data: function(params) {
                return {
                    q: params.term
                };
            },

            processResults: function(data) {

                return {
                    results: data
                };

            },

            cache: true
        }

    });


    $(document).ready(function(){

        $('#filter_plant_item').select2({width:'100%'});

        const today = new Date();
        let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

        $('#filter_date_from_item')
            .val(firstDay.toISOString().slice(0,10));

        $('#filter_date_to_item')
            .val(today.toISOString().slice(0,10));

        loadPageItem();

        $('#btnFilterItem').click(()=>{
            loadPageItem(1);
        });

    });



    /* ================= UTIL ================= */

    function rupiah(x){
        return parseFloat(x||0)
            .toFixed(0)
            .replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    }

    function formatDecimal(x){
        return parseFloat(x || 0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }



    /* ================= LOAD ================= */

    function loadPageItem(p=1){

        stateItem.page = p;

        const params = {

            page: p,
            limit: stateItem.limit,

            plant: $('#filter_plant_item').val(),
            item: $('#filter_item_by').val(),

            date1: $('#filter_date_from_item').val(),
            date2: $('#filter_date_to_item').val()
        };


        $.get(
            '<?= base_url("report-sales/load_sales_item"); ?>',
            params,
            function(resp){

                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                $('#salesItemTable tbody').empty();

                if(!resp.rows.length){

                    $('#salesItemTable tbody')
                        .html('<tr><td colspan="5" class="text-center">No data</td></tr>');

                }else{

                    renderItemTable(resp.rows);

                }


                $('#gt_qty_item')
                    .text(formatDecimal(resp.grand.qty));

                $('#gt_berat_item')
                    .text(formatDecimal(resp.grand.berat));

                $('#gt_amount_item')
                    .text(rupiah(resp.grand.amount));


                $('#pagination_item').html(resp.pagination);

                $('#info_item').html(
                    `Showing ${(p-1)*stateItem.limit+1}
                    to ${Math.min(p*stateItem.limit,resp.total)}
                    of ${resp.total}`
                );

            }
        );

    }



    /* ================= RENDER ================= */

    function renderItemTable(rows){

        const grouped = {};

        rows.forEach(r=>{
            if(!grouped[r.PLANT]) {
                grouped[r.PLANT] = [];
            }
            grouped[r.PLANT].push(r);
        });


        Object.values(grouped).forEach(group=>{

            let subQty = 0;
            let subBerat = 0;
            let subAmount = 0;

            group.forEach(r=>{

                subQty += parseFloat(r.QTY);
                subBerat += parseFloat(r.BERAT);
                subAmount += parseFloat(r.AMOUNT);

                let tr = `
                <tr>

                    <td class="text-center detail-row" style="vertical-align: middle">
                        <b>${r.PLANT_NAME}</b>
                    </td>

                    <td class="text-center detail-row" style="vertical-align: middle">
                        ${r.ITEM_NAME}<br>
                        <b>${r.ITEM}</b>
                    </td class="text-center detail-row" style="vertical-align: middle">

                    <td class="text-end detail-row" style="vertical-align: middle">
                        ${formatDecimal(r.QTY)}
                    </td>

                    <td class="text-end detail-row" style="vertical-align: middle">
                        ${formatDecimal(r.BERAT)}
                    </td>

                    <td class="text-end detail-row" style="vertical-align: middle">
                        ${rupiah(r.AMOUNT)}
                    </td>

                </tr>
                `;

                $('#salesItemTable tbody').append(tr);

            });


            // ===== SUBTOTAL =====

            let sub = `
            <tr class="table-warning fw-bold">

                <td colspan="2" class="text-end detail-row">
                    SUBTOTAL ${group[0].PLANT_NAME}
                </td>

                <td class="text-end detail-row">
                    ${formatDecimal(subQty)}
                </td>

                <td class="text-end detail-row">
                    ${formatDecimal(subBerat)}
                </td>

                <td class="text-end detail-row">
                    ${rupiah(subAmount)}
                </td>

            </tr>
            `;

            $('#salesItemTable tbody').append(sub);

        });

    }



    /* ================= EXPORT ================= */

    function exportParamsItem(){

        return $.param({

            plant: $('#filter_plant_item').val(),
            item: $('#filter_item_by').val(),

            date1: $('#filter_date_from_item').val(),
            date2: $('#filter_date_to_item').val()

        });

    }


    $('#exportExcelItem').click(function(e){

        e.preventDefault();

        window.open(
            '<?= base_url("report-sales/export_excel_sales_item"); ?>?'
            + exportParamsItem()
        );

    });


    $('#exportPDFItem').click(function(e){

        e.preventDefault();

        window.open(
            '<?= base_url("report-sales/export_pdf_sales_item"); ?>?'
            + exportParamsItem()
        );

    });


    $(document).on(
        'click',
        '#pagination_item a',
        function(e){

            e.preventDefault();

            loadPageItem(
                $(this).data('page')
            );

        }
    );

</script>