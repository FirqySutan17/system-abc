<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Total Account
                </div>

                <div class="fs-4 fw-bold text-primary"
                     id="kpi_total_account">
                    0
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Total Amount
                </div>

                <div class="fs-4 fw-bold text-success"
                     id="kpi_total_amount">
                    0
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Plant
                </div>

                <div class="fs-5 fw-bold text-primary"
                     id="kpi_plant">
                    -
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Month
                </div>

                <div class="fs-5 fw-bold text-info"
                     id="kpi_month">
                    -
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card mb-4"
     style="background:transparent;border:none!important;box-shadow:none!important">

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Plant
            </label>

            <select id="pl_filter_plant"
                    class="form-select">

                <?php foreach($plants as $i => $p): ?>
                    <option value="<?= $p->CODE ?>"
                        <?= $i==0 ? 'selected':'' ?>>
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="col-md-3">

            <label class="form-label fw-semibold">
                Closing Month
            </label>

            <input type="month"
                   id="pl_month"
                   class="form-control">

        </div>

        <div class="col-md-6 d-flex align-items-end justify-content-end">

            <button class="btn btn-primary me-2"
                    id="pl_btnFilter">
                Search
            </button>

            <div class="btn-group">

                <button class="btn btn-success"
                        id="exportExcelMon">
                    Excel
                </button>

                <button class="btn btn-danger"
                        id="exportPDFMon">
                    PDF
                </button>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm">

    <div id="loadingOverlay">
        Loading P/L Report...
    </div>

    <div class="card-body pt-2 px-0 pb-3">

        <div class="table-responsive">

            <table class="table table-bordered mb-0"
                   id="monthlyPlTable">

                <thead>

                    <tr class="table-dark">

                        <th>Account</th>
                        <th>Account Name</th>
                        <th class="text-end">Amount</th>

                    </tr>

                </thead>

                <tbody></tbody>

                <tfoot>

                    <tr class="fw-bold">

                        <td colspan="2"
                            class="text-end profit-cell">
                            GRAND TOTAL
                        </td>

                        <td class="text-end"
                            id="pl_gt_amount">
                            0
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between mt-3">

    <div id="pl_info"></div>

    <div id="pl_pagination"></div>

</div>

<style>

.card{
    border-radius:14px;
    position:relative;
}

.table-responsive{
    max-height:700px;
}

thead th{
    position:sticky;
    top:0;
    z-index:10;
}

tfoot tr{
    position:sticky;
    bottom:0;
    z-index:10;
}

.table-dark th{
    background:#2f3c4f !important;
    color:#fff !important;
    border-color:#3f4d63 !important;
}

#monthlyPlTable tbody tr:hover{
    background:#f8fafc;
}

.profit-cell{
    background:#f0fff5;
    color:#198754;
    font-weight:700;
}

tfoot tr{
    background:#eef4ff !important;
    font-weight:700;
}

tfoot td{
    border-top:3px solid #2f3c4f !important;
}

#monthlyPlTable th,
#monthlyPlTable td{
    white-space:nowrap;
}

.kpi-card .card-body{
    padding:18px 22px;
}

.kpi-label{
    font-size:12px;
    color:#6c757d;
    text-transform:uppercase;
    letter-spacing:1px;
}

.kpi-card{
    transition:.2s;
}

.kpi-card:hover{
    transform:translateY(-2px);
}

#loadingOverlay{
    display:none;
    position:absolute;
    inset:0;
    background:rgba(255,255,255,.8);
    z-index:999;
    text-align:center;
    padding-top:150px;
    font-size:18px;
    font-weight:600;
}

</style>

<script>
    window.MonthlyClosingPL = {

        loaded:false,
        page:1,

        init(){
            if(this.loaded) return;
            this.loaded = true;

            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter(){
            $('#pl_filter_plant').select2({ width:'100%' });

            const now = new Date();
            const ym = now.toISOString().slice(0,7);

            $('#pl_month').val(ym);
        },

        bindEvent(){

            $('#pl_btnFilter').on('click',()=>{
                this.page = 1;
                this.load();
            });

            $('#pl_filter_plant,#pl_month')
                .on('change',()=>{
                    this.page = 1;
                    this.load();
                });

            /* ================= EXPORT ================= */

            $('#exportExcelMon').click(e=>{
                e.preventDefault();

                const plant = $('#pl_filter_plant').val();
                const month = this.toYM($('#pl_month').val());

                if(!month){
                    alert('Month wajib diisi');
                    return;
                }

                const params = $.param({
                    plant : plant,
                    month : month
                });

                window.open(
                    '<?= base_url("report-closing-pl/export_excel_monthly_closing_pl"); ?>?' + params,
                    '_blank'
                );
            });

            $('#exportPDFMon').click(e=>{
                e.preventDefault();

                const plant = $('#pl_filter_plant').val();
                const month = this.toYM($('#pl_month').val());

                if(!month){
                    alert('Month wajib diisi');
                    return;
                }

                const params = $.param({
                    plant : plant,
                    month : month
                });

                window.open(
                    '<?= base_url("report-closing-pl/export_pdf_monthly_closing_pl"); ?>?' + params,
                    '_blank'
                );
            });
        },

        load(page=null){
            if(page!==null) this.page = page;

            const params = {
                page  : this.page,
                limit : 17,
                plant : $('#pl_filter_plant').val(),
                month : this.toYM($('#pl_month').val())
            };

            $.get(
                '<?= base_url("report-closing-pl/load_monthly_closing_pl"); ?>',
                params,
                resp=>{
                    this.render(resp.rows||[]);
                    this.renderGrand(resp.grand||{});
                    this.renderSummary(resp);
                    $('#pl_pagination').html(resp.pagination||'');
                    $('#pl_info').text(`Total data : ${resp.total||0}`);
                },
                'json'
            );
        },

        renderSummary(resp){

            $('#kpi_total_account')
                .text(resp.total || 0);

            $('#kpi_total_amount')
                .text(this.rupiah(resp.grand.amount));

            $('#kpi_plant')
                .text(
                    $('#pl_filter_plant option:selected').text()
                );

            $('#kpi_month')
                .text(
                    this.formatYM(
                        this.toYM($('#pl_month').val())
                    )
                );
        },

        render(rows){

            const tbody = $('#monthlyPlTable tbody');

            tbody.empty();

            if(!rows.length){

                tbody.html(`
                    <tr>
                        <td colspan="3"
                            class="text-center py-4">
                            No data found
                        </td>
                    </tr>
                `);

                return;
            }

            rows.forEach(r => {

                tbody.append(`

                    <tr>

                        <td>
                            ${r.account_cd || '-'}
                        </td>

                        <td>
                            ${r.ACCOUNT_NAME || '-'}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.amount)}
                        </td>

                    </tr>

                `);

            });

        },

        renderGrand(g){
            $('#pl_gt_amount').text(this.rupiah(g.amount||0));
        },

        /* ===== UTIL ===== */

        toYM(v){
            return v ? v.replace('-','') : '';
        },

        formatYM(ym){
            return ym ? ym.slice(4,6)+'/'+ym.slice(0,4) : '-';
        },

        rupiah(x){
            const val = parseFloat(x);
            return (isNaN(val) ? 0 : val).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    };

    $(document).on('click','#pl_pagination a',function(e){
        e.preventDefault();
        const p = $(this).data('page');
        if(p) MonthlyClosingPL.load(p);
    });

    $(document).ready(()=>{
        MonthlyClosingPL.init();
    });
</script>