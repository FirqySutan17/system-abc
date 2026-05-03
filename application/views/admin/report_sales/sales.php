<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- CUSTOMER -->
    <div class="col-md-2">
        <label class="form-label">Customer</label>
        <select id="filter_customer" class="form-control">
            <option value="">-- All Customer --</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= $c->CUST ?>">
                    <?= $c->CUST ?> - <?= $c->FULL_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- NO SALES -->
    <div class="col-md-2">
        <label class="form-label">No Sales</label>
        <input type="text" id="filter_sales" class="form-control" placeholder="Search Sales">
    </div>

    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="filter_date_from" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="filter_date_to" class="form-control">
    </div>

    <div class="col-md-2">
        <label class="form-label">Status</label>
        <select id="filter_status" class="form-control">
            <option value="ALL">ALL</option>
            <option value="LUNAS">LUNAS</option>
            <option value="TEMPO">TEMPO</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Item</label>

        <select id="filter_item" class="form-control">
            <option value="">-- All Item --</option>
        </select>

    </div>

    <!-- BUTTON FILTER -->
    <div class="col-md-8">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top:-23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- EXPORT -->
    <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown" style="margin-top:-23px">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcel">Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDF">Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="salesReportTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">SALES</th>
                <th class="text-center">CUSTOMER</th>
                <th class="text-center">ITEM</th>
                <th class="text-center">QTY</th>
                <th class="text-center">BERAT</th>
                <th class="text-center">HARGA</th>
                <th class="text-center">AMOUNT</th>
                <th class="text-center">STATUS</th>
                <th class="text-center">REMAIN</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="5" class="text-end detail-row">GRAND TOTAL</td>
                <td class="text-end detail-row" id="gt_qty">0.00</td>
                <td class="text-end detail-row" id="gt_berat">0.00</td>
                <td class="text-end detail-row"></td>
                <td class="text-end detail-row" id="gt_amount">0</td>
                <td colspan="2" class="text-end detail-row"></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="pagination"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
    .flex-inline {
        padding: 2px 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        align-content: center;
        flex-wrap: nowrap;
        flex-direction: row;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
</style>

<script>
    var state = {
        page: 1,
        limit: 10,
        plant: '',
        customer: '',
        sales: '',
        date_from: '',
        date_to: '',
        status: 'ALL'
    };

    $(document).ready(function(){

        const userPlants = <?= json_encode(array_column($plants, 'CODE')) ?>;

        $('#filter_plant').select2({ width:'100%', allowClear:true });
        $('#filter_customer').select2({ width:'100%', allowClear:true });
        $('#filter_status').select2({ width:'100%' });

        if(userPlants.length > 0){
            $('#filter_plant').val(userPlants[0]).trigger('change');
        }

        const today = new Date();

        let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

        $('#filter_date_from').val(firstDay.toISOString().slice(0,10));
        $('#filter_date_to').val(today.toISOString().slice(0,10));

        loadPage();

        $('#btnFilter').click(()=>loadPage(1));

        $('#filter_sales').on('keyup', debounce(function(){
            state.sales = $('#filter_sales').val();
            loadPage(1);
        },300));

    });

    $('#filter_item').select2({

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

    /* ================= UTIL ================= */
    function debounce(fn,delay){
        let t; return (...args)=>{
            clearTimeout(t);
            t=setTimeout(()=>fn.apply(this,args),delay);
        }
    }

    function formatDate(d){
        if(!d) return '-';
        const x=new Date(d);
        return `${String(x.getDate()).padStart(2,'0')}/${String(x.getMonth()+1).padStart(2,'0')}/${x.getFullYear()}`;
    }

    function rupiah(x){
        return parseFloat(x||0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    }

    function formatDecimal(x){
        return parseFloat(x || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function dec(x){ return parseFloat(x||0).toFixed(2); }

    /* ================= LOAD ================= */
    function loadPage(p=1){
        state.page=p;

        const params={
            page:p,
            limit:state.limit,
            plant:$('#filter_plant').val(),
            item:$('#filter_item').val(),
            customer:$('#filter_customer').val(),
            sales:$('#filter_sales').val(),
            date_from:$('#filter_date_from').val(),
            date_to:$('#filter_date_to').val(),
            status:$('#filter_status').val()
        };

        $.get('<?= base_url("report-sales/load_sales"); ?>',params,function(resp){
            resp=typeof resp==='string'?JSON.parse(resp):resp;

            $('#salesReportTable tbody').empty();
            if(!resp.rows.length){
                $('#salesReportTable tbody').html('<tr><td colspan="9" class="text-center">No data</td></tr>');
            }else{
                renderTable(resp.rows);
            }

            $('#gt_qty').text(formatDecimal(resp.grand.qty));
            $('#gt_berat').text(formatDecimal(resp.grand.berat));
            $('#gt_amount').text(rupiah(resp.grand.amount));

            $('#pagination').html(resp.pagination);
            $('#info').html(`Showing ${(p-1)*state.limit+1} to ${Math.min(p*state.limit,resp.total)} of ${resp.total}`);
        });
    }

    /* ================= RENDER ================= */
    function renderTable(rows){

        const grouped={};

        rows.forEach(r=>{
            const k=r.SALES+'|'+r.PLANT;
            if(!grouped[k]) grouped[k]=[];
            grouped[k].push(r);
        });

        Object.values(grouped).forEach(g=>{

            g.forEach((r,i)=>{

                let tr='<tr>';

                if(i===0){

                    tr+=`
                    <td rowspan="${g.length}" class="text-center detail-row"><b>${r.PLANT_NAME}</b></td>

                    <td rowspan="${g.length}" class="text-center detail-row">
                        ${formatDate(r.SALES_DATE)}
                    </td>

                    <td rowspan="${g.length}" class="text-center detail-row">
                        <b>#${r.SALES}</b>
                    </td>

                    <td rowspan="${g.length}" class="text-center detail-row">
                        ${r.CUSTOMER_NAME}<br>
                        <b>${r.CUSTOMER}</b>
                    </td>
                    `;
                }

                tr+=`
                    <td class="text-center detail-row">
                        ${r.FULL_NAME}<br>
                        <b>${r.ITEM}</b>
                    </td>

                    <td class="text-center detail-row">
                        ${formatDecimal(r.QTY)}
                    </td>

                    <td class="text-end detail-row">
                        ${formatDecimal(r.BERAT)}
                    </td>

                    <td class="text-end detail-row">
                        ${rupiah(r.HARGA)}
                    </td>

                    <td class="text-end detail-row">
                        ${rupiah(r.DETAIL_AMOUNT)}
                    </td>
                `;

                if(i===0){

                    tr+=`
                    <td rowspan="${g.length}" class="text-center detail-row">
                        <b>${r.STATUS_REPORT}</b>
                    </td>

                    <td rowspan="${g.length}" class="text-end detail-row">
                        ${rupiah(r.REMAIN)}
                    </td>
                    `;
                }

                tr+='</tr>';

                $('#salesReportTable tbody').append(tr);

            });

        });

    }

    /* ================= EXPORT ================= */
    function exportParams(){
        return $.param({
            plant:$('#filter_plant').val(),
            item:$('#filter_item').val(),
            customer:$('#filter_customer').val(),
            sales:$('#filter_sales').val(),
            date_from:$('#filter_date_from').val(),
            date_to:$('#filter_date_to').val(),
            status:$('#filter_status').val()
        });
    }

    $('#exportExcel').click(e=>{
        e.preventDefault();
        window.open('<?= base_url("report-sales/export_excel_sales"); ?>?'+exportParams());
    });
    $('#exportPDF').click(e=>{
        e.preventDefault();
        window.open('<?= base_url("report-sales/export_pdf_sales"); ?>?'+exportParams());
    });

    $(document).on('click','#pagination a',function(e){
        e.preventDefault();
        loadPage($(this).data('page'));
    });
</script>


