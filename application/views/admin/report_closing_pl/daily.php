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
        <select id="filter_plant_pl" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <input type="date" id="date" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top:-23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-6"></div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown" style="margin-top:-23px">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcel">
                    <i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDF">
                    <i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="dailyClosingPlTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">ACCOUNT</th>
                <th class="text-center">ACCOUNT NAME</th>
                <th class="text-end">AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <!-- <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="4" class="text-end">GRAND TOTAL</td>
                <td class="text-end" id="gt_amount">0</td>
            </tr>
        </tfoot> -->
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="pagination"></div>
</div>

<script>
var state = {
    page: 1,
    limit: 17
};

$(document).ready(function(){

    $('#filter_plant_pl').select2({ width:'100%' });

    // default date = current month
    const today = new Date();
    $('#date').val(today.toISOString().slice(0,10));

    $('#exportExcel').click(function(e){
        e.preventDefault();

        const plant = $('#filter_plant_pl').val();
        const date  = $('#date').val();

        if(!date){
            alert('Date wajib diisi');
            return;
        }

        const params = $.param({
            plant: plant,
            date: date
        });

        window.open(
            '<?= base_url("report-closing-pl/export_excel_daily_closing_pl"); ?>?' + params,
            '_blank'
        );
    });


    $('#exportPDF').click(function(e){
        e.preventDefault();

        const plant = $('#filter_plant_pl').val();
        const date  = $('#date').val();

        if(!date){
            alert('Date wajib diisi');
            return;
        }

        const params = $.param({
            plant: plant,
            date: date
        });

        window.open(
            '<?= base_url("report-closing-pl/export_pdf_daily_closing_pl"); ?>?' + params,
            '_blank'
        );
    });

    loadPage();

    $('#btnFilter').click(()=>loadPage(1));
});

/* ================= UTIL ================= */

function rupiah(x){
    const val = parseFloat(x);
    return (isNaN(val) ? 0 : val).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

function formatYMD(ymd){
    return ymd
        ? ymd.substr(6,2)+'/'+ymd.substr(4,2)+'/'+ymd.substr(0,4)
        : '-';
}

/* ================= LOAD ================= */

function loadPage(p=1){
    state.page = p;

    const params = {
        page: p,
        limit: state.limit,
        plant: $('#filter_plant_pl').val(),
        date: $('#date').val()
    };

    $.get('<?= base_url("report-closing-pl/load_daily_closing_pl"); ?>', params, function(resp){

        resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

        const tbody = $('#dailyClosingPlTable tbody').empty();

        if(!resp.rows.length){
            tbody.html('<tr><td colspan="5" class="text-center">No data</td></tr>');
        } else {
            resp.rows.forEach(r=>{
                tbody.append(`
                    <tr>
                        <td class="text-center"><b>${r.plant_name}</b></td>
                        <td class="text-center">${formatYMD(r.ymd)}</td>

                        <td class="text-center">${r.account_cd}</td>
                        <td>${r.ACCOUNT_NAME}</td>

                        <td class="text-end">${rupiah(r.amount)}</td>
                    </tr>
                `);
            });
        }

        $('#gt_amount').text(rupiah(resp.grand.amount));
        $('#pagination').html(resp.pagination);
        $('#info').html(
            `Showing ${(p-1)*state.limit+1} to ${Math.min(p*state.limit,resp.total)} of ${resp.total}`
        );
    });
}

/* ================= PAGINATION ================= */

$(document).on('click','#pagination a',function(e){
    e.preventDefault();
    loadPage($(this).data('page'));
});
</script>