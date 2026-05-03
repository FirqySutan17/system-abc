<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">REPROCESS - INPUT</h5>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" class="form-control" placeholder="Cari process..." />
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#processAdd">
                        Tambah Process
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th style="text-align:center;">No Process</th>
                            <th style="text-align:center;">Plant</th>
                            <th style="text-align:center;">Process Class</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Ket.</th>
                            <th style="text-align:center;">#</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<style>
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
    label {
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
</style>

<div class="modal fade" id="processAdd">
    <div class="modal-dialog modal-xl">
        <form id="fprocessAdd">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>PROCESS - TAMBAH</h5>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No Process</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background: #efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" name="PROCESS_DATE" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" name="PLANT" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Process Class *</label>
                            <select id="processClassAdd" name="PROCESS_CLASS" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" class="form-control">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Item</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addRowAdd">Tambah Item</button>
                    </div>

                    <table class="table table-bordered" id="detailAdd">
                        <thead>
                            <tr>
                                <th style="width:20%; text-align: center; vertical-align: middle">Item</th>
                                <th style="width:20%; text-align: center; vertical-align: middle">To Item</th>
                                <th style="text-align: center; vertical-align: middle">Qty</th>
                                <th style="text-align: center; vertical-align: middle">Berat</th>
                                <th style="text-align: center; vertical-align: middle">Remark</th>
                                <th style="text-align: center; vertical-align: middle">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="processEdit">
    <div class="modal-dialog modal-xl">
        <form id="fprocessEdit">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>PROCESS - EDIT</h5>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No Process</label>
                            <input id="PROCESS_EDIT" class="form-control" readonly style="background: #efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal</label>
                            <input id="PROCESS_DATE_EDIT" name="PROCESS_DATE" type="date" class="form-control"required>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Plant</label>
                            <select id="PLANT_EDIT" class="form-control" disabled></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Process Class</label>
                            <select id="PROCESS_CLASS_EDIT" name="PROCESS_CLASS" class="form-control"></select>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input id="REMARK_EDIT" name="REMARK" class="form-control">
                        </div>

                    </div>

                    <table class="table table-bordered" id="detailEdit">
                        <thead>
                            <tr>
                                <th style="width:20%; text-align: center; vertical-align: middle">Item</th>
                                <th style="width:20%; text-align: center; vertical-align: middle">To Item</th>
                                <th style="text-align: center; vertical-align: middle">Qty</th>
                                <th style="text-align: center; vertical-align: middle">Berat</th>
                                <th style="text-align: center; vertical-align: middle">Remark</th>
                                <th style="text-align: center; vertical-align: middle">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    var state = { page: 1, limit: 10, search: '' };
    let ajaxRequest = null;

    /* =========================
    LOAD LIST
    ========================= */
    function loadPage(page = 1) {
        state.page = page;

        if (ajaxRequest) ajaxRequest.abort();

        ajaxRequest = $.get('<?= base_url("reprocess/load_data"); ?>', state, function(resp){
            ajaxRequest = null;

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

            let tbody = $('#table-body').empty();

            resp.rows.forEach(function(r){
                tbody.append(`
                    <tr>
                        <td style="text-align:center; vertical-align: middle"><b>#${r.process_no}</b></td>
                        <td style="text-align:center; vertical-align: middle"><b>${r.plant_name}</b></td>
                        <td style="text-align:center; vertical-align: middle">${r.process_class_name}</td>
                        <td style="text-align:center; vertical-align: middle">${formatDate(r.process_date)}</td>
                        <td style="text-align:center; vertical-align: middle">${r.remark || ''}</td>
                        <td style="text-align:center; vertical-align: middle">
                            <button class="btn btn-sm btn-warning editBtn" data-id="${r.process_no}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${r.process_no}">Hapus</button>
                        </td>
                    </tr>
                `);
            });

            $('#pagination').html(resp.pagination || '');
        });
    }

    /* =========================
    FORMAT DATE
    ========================= */
    function formatDate(dateString) {
        if (!dateString) return '-';
        let d = new Date(dateString);
        if (isNaN(d)) return dateString;
        return d.toLocaleDateString('id-ID');
    }

    /* =========================
    SELECT2 PLANT
    ========================= */
    function initPlant(el, modal){
        $(el).select2({
            dropdownParent: $(modal),
            width:'100%',
            placeholder:'-- PILIH PLANT --',
            ajax:{
                url:'<?= base_url("reprocess/get_plant_by_user"); ?>',
                dataType:'json',
                delay:250,
                processResults:d=>({results:d})
            }
        });
    }

    /* =========================
    SELECT2 PROCESS CLASS
    ========================= */
    function initProcessClass(el, modal){
        $(el).select2({
            dropdownParent: $(modal),
            width:'100%',
            placeholder:'-- PILIH PROCESS CLASS --',
            ajax:{
                url:'<?= base_url("reprocess/get_process_class"); ?>',
                dataType:'json',
                delay:250,
                processResults:d=>({results:d})
            }
        });
    }

    /* =========================
    SELECT2 ITEM
    ========================= */
    function initItem(el, modal){
        $(el).select2({
            placeholder:'Pilih Item',
            dropdownParent: $(modal),
            width:'100%',
            minimumInputLength:2,
            ajax:{
                url:'<?= base_url("reprocess/get_item"); ?>',
                dataType:'json',
                delay:300,
                data:p=>({q:p.term}),
                processResults:d=>({results:d})
            }
        });
    }

    /* =========================
    ADD ROW
    ========================= */
    function addRow(target, modal){
        let row = `
            <tr>
                <td><select class="form-control item"></select></td>
                <td><select class="form-control to_item"></select></td>
                <td><input class="form-control qty decimal text-end"></td>
                <td><input class="form-control berat decimal text-end"></td>
                <td><input class="form-control remark"></td>
                <td style="text-align: center; vertical-align: middle"><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

        $(`${target} tbody`).append(row);

        let tr = $(`${target} tbody tr`).last();

        initItem(tr.find('.item'), modal);
        initItem(tr.find('.to_item'), modal);
    }

    /* =========================
    REMOVE ROW
    ========================= */
    $(document).on('click','.removeRow', function(){
        $(this).closest('tr').remove();
    });

    /* =========================
    DECIMAL PARSE + FORMAT
    ========================= */
    function parseDecimalID(val){
        if (!val) return 0;
        return parseFloat(
            val.toString()
            .replace(/\./g,'')
            .replace(',','.')
        ) || 0;
    }

    function formatDecimalID(num){
        return Number(num || 0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }

    $(document).on('input','.decimal',function(){
        let val = $(this).val();
        val = val.replace(/[^0-9.,]/g,'');

        let parts = val.split(',');
        if(parts.length > 2){
            val = parts[0] + ',' + parts[1];
        }

        $(this).val(val);
    });

    $(document).on('blur','.decimal',function(){
        let num = parseDecimalID($(this).val());
        $(this).val(formatDecimalID(num));
    });

    /* =========================
    SEARCH
    ========================= */
    let searchTimer = null;

    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);
        let val = $(this).val();

        searchTimer = setTimeout(()=>{
            state.search = val;
            loadPage(1);
        },400);
    });

    /* =========================
    SUBMIT CREATE
    ========================= */
    $('#fprocessAdd').submit(function(e){
        e.preventDefault();

        let DETAIL = [];

        $('#detailAdd tbody tr').each(function(){

            let item    = $(this).find('.item').val();
            let to_item = $(this).find('.to_item').val();
            let qty     = parseDecimalID($(this).find('.qty').val());
            let berat   = parseDecimalID($(this).find('.berat').val());

            if(!item || !to_item) return;

            if(item === to_item){
                alert('Item tidak boleh sama dengan To Item');
                throw 'invalid';
            }

            if(qty <= 0 && berat <= 0){
                alert('Qty atau Berat harus diisi');
                throw 'invalid';
            }

            DETAIL.push({
                ITEM:item,
                TO_ITEM:to_item,
                QTY:qty,
                BERAT:berat,
                REMARK:$(this).find('.remark').val()
            });
        });

        if(!DETAIL.length){
            alert('Detail tidak boleh kosong');
            return;
        }

        let fd = new FormData(this);
        fd.append('DETAIL', JSON.stringify(DETAIL));

        $.ajax({
            url:'<?= base_url("reprocess/create"); ?>',
            method:'POST',
            data:fd,
            processData:false,
            contentType:false,
            dataType:'json',
            success:function(res){
                alert(res.message);

                if(res.status){
                    $('#processAdd').modal('hide');
                    $('#fprocessAdd')[0].reset();
                    $('#detailAdd tbody').empty();
                    loadPage(state.page);
                }
            }
        });
    });

    $(document).on('change','.item, .to_item', function(){

        let row = $(this).closest('tr');

        let item = row.find('.item').val();
        let toItem = row.find('.to_item').val();

        if(item && toItem && item === toItem){
            alert('Item tidak boleh sama');
            row.find('.to_item').val(null).trigger('change');
        }
    });

    /* =========================
    EDIT
    ========================= */
    $(document).on('click','.editBtn', function(){

        let id = $(this).data('id');

        $('#fprocessEdit')[0].reset();
        $('#detailEdit tbody').empty();

        $.get('<?= base_url("reprocess/edit"); ?>',{process:id},function(resp){

            if(typeof resp === 'string') resp = JSON.parse(resp);

            if(!resp.status){
                alert(resp.message);
                return;
            }

            let h = resp.header;
            let d = resp.detail;

            $('#PROCESS_EDIT').val(h.process_no);
            $('#PROCESS_DATE_EDIT').val(h.process_date.split(' ')[0]);

            // plant
            let optPlant = new Option(h.plant_name, h.plant, true, true);
            $('#PLANT_EDIT').empty().append(optPlant).trigger('change');

            // process class
            let optClass = new Option(h.process_class_name, h.process_class, true, true);
            $('#PROCESS_CLASS_EDIT').empty().append(optClass).trigger('change');

            $('#REMARK_EDIT').val(h.remark);

            // detail
            d.forEach(function(row){

                addRow('#detailEdit','#processEdit');

                let tr = $('#detailEdit tbody tr').last();

                let optItem = new Option(
                    row.item + ' - ' + row.item_name,
                    row.item,
                    true,
                    true
                );

                let optToItem = new Option(
                    row.to_item + ' - ' + row.to_item_name,
                    row.to_item,
                    true,
                    true
                );

                tr.find('.item').append(optItem).trigger('change');
                tr.find('.to_item').append(optToItem).trigger('change');

                tr.find('.qty').val(formatDecimalID(row.qty));
                tr.find('.berat').val(formatDecimalID(row.berat));
                tr.find('.remark').val(row.remark);
            });

            $('#processEdit').modal('show');

        },'json');
    });

    /* =========================
    SUBMIT UPDATE
    ========================= */
    $('#fprocessEdit').submit(function(e){
        e.preventDefault();

        let DETAIL = [];

        $('#detailEdit tbody tr').each(function(){

            let item    = $(this).find('.item').val();
            let to_item = $(this).find('.to_item').val();
            let qty     = parseDecimalID($(this).find('.qty').val());
            let berat   = parseDecimalID($(this).find('.berat').val());

            if(!item || !to_item) return;

            if(item === to_item){
                alert('Item tidak boleh sama dengan To Item');
                throw 'invalid';
            }

            if(qty <= 0 && berat <= 0){
                alert('Qty atau Berat harus diisi');
                throw 'invalid';
            }

            DETAIL.push({
                ITEM:item,
                TO_ITEM:to_item,
                QTY:qty,
                BERAT:berat,
                REMARK:$(this).find('.remark').val()
            });
        });

        if(!DETAIL.length){
            alert('Detail kosong');
            return;
        }

        let fd = new FormData(this);
        fd.append('PROCESS_NO', $('#PROCESS_EDIT').val());
        fd.append('DETAIL', JSON.stringify(DETAIL));

        $.ajax({
            url:'<?= base_url("reprocess/update"); ?>',
            method:'POST',
            data:fd,
            processData:false,
            contentType:false,
            dataType:'json',
            success:function(res){
                alert(res.message);

                if(res.status){
                    $('#processEdit').modal('hide');
                    loadPage(state.page);
                }
            }
        });
    });

    /* =========================
    DELETE
    ========================= */
    $(document).on('click','.deleteBtn', function(){

        let id = $(this).data('id');

        Swal.fire({
            title:'Hapus Process?',
            text:`Process ${id} akan dihapus permanen`,
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, hapus',
            cancelButtonText:'Batal'
        }).then((result)=>{

            if(!result.isConfirmed) return;

            $.post('<?= base_url("reprocess/remove"); ?>',{process:id},function(res){

                if(res.status){
                    showToast('success',res.message);
                    loadPage(state.page);
                }else{
                    showToast('error',res.message);
                }

            },'json');

        });
    });

    /* =========================
    INIT
    ========================= */
    $(function(){

        loadPage(1);

        initPlant('#plantAdd','#processAdd');
        initProcessClass('#processClassAdd','#processAdd');

        initPlant('#PLANT_EDIT','#processEdit');
        initProcessClass('#PROCESS_CLASS_EDIT','#processEdit');

        $('#addRowAdd').click(()=> addRow('#detailAdd','#processAdd'));
        $('#addRowEdit').click(()=> addRow('#detailEdit','#processEdit'));

    });

    /* =========================
    TOAST
    ========================= */
    function showToast(type, message){
        const Toast = Swal.mixin({
            toast:true,
            position:'top-end',
            timer:3000,
            showConfirmButton:false
        });

        Toast.fire({
            icon:type,
            title:message
        });
    }
</script>

<script>
    $('#salesAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split("T")[0];
        const dateInput = $(this).find('input[name="SALES_DATE"]')[0];
        if(dateInput){
            dateInput.value = today; // hari ini
        }
    });

    $(document).on('input','#BAYAR_AWAL_EDIT', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });
</script>
