<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>
<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="pl_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- MONTH FROM -->
    <div class="col-md-2">
        <label class="form-label">Month</label>
        <input type="month" id="pl_month" class="form-control">
    </div>

    <!-- FILTER BUTTON -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="pl_btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-6"></div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcelMon">
                    <i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDFMon">
                    <i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="monthlyPlTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">MONTH</th>
                <th class="text-center">ACCOUNT</th>
                <th class="text-center">ACCOUNT NAME</th>
                <th class="text-end">AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <!-- <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="4" class="text-end detail-row">GRAND TOTAL</td>
                <td class="text-end detail-row" id="pl_gt_amount">0</td>
            </tr>
        </tfoot> -->
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="pl_info"></div>
    <div id="pl_pagination"></div>
</div>

<style>
.detail-row{
    border:2px solid #efefef !important;
    vertical-align:middle !important;
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
                    $('#pl_pagination').html(resp.pagination||'');
                    $('#pl_info').text(`Total data : ${resp.total||0}`);
                },
                'json'
            );
        },

        render(rows){
            const tbody = $('#monthlyPlTable tbody').empty();

            if(!rows.length){
                tbody.html('<tr><td colspan="5" class="text-center">No data</td></tr>');
                return;
            }

            rows.forEach(r=>{
                tbody.append(`
                    <tr>

                        <td class="text-center"><b>${r.plant_name}</b></td>

                        <td class="text-center">
                            ${this.formatYM(r.ym)}
                        </td>

                        <td class="text-center">
                            ${r.account_cd||'-'}
                        </td>

                        <td>
                            ${r.ACCOUNT_NAME||'-'}
                        </td>

                        <td class="text-end">
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