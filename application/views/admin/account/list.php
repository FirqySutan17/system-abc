<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">ACCOUNT - BASE DATA</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari account / nama..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group ">
                        <!-- optional export buttons -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="ACCOUNT">Account <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="ACCOUNT_NAME">Account Name <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0">Remark</th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0">Chk In</th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0">Chk DC</th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0">Chk Vendor</th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0 cursor-pointer" data-order="ACCOUNT_TYPE">Type <span class="sort-icon"></span></th>
                            <th style="text-align: center; vertical-align: middle" class="border-bottom-0"></th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="fs-3" style="border: 2px solid #fff;">
                        <!-- filled by AJAX -->
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ADD -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="formAdd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Account - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-4 mb-1">
                        <label class="form-label">Account *</label>
                        <input name="ACCOUNT" class="form-control" placeholder="Contoh: 10000001" required>
                    </div>

                    <div class="col-md-8 mb-1">
                        <label class="form-label">Account Name *</label>
                        <input name="ACCOUNT_NAME" class="form-control" placeholder="Nama account" required>
                    </div>

                    <div class="col-md-12 mb-1">
                        <label class="form-label">Remark</label>
                        <input name="REMARK" class="form-control" placeholder="">
                    </div>
                </div>

                <hr>

                <h6 class="mt-2">Checks (toggle Y/N)</h6>
                <div class="row">
                    <!-- We'll put toggles in a grid -->
                    <?php
                    $toggles = [
                        'CHECK_IN','CHECK_DC','CHECK_DEPT','CHECK_VENDOR','CHECK_BANK','CHECK_GOODS',
                        'CHECK_REMAIN','CHECK_BUDGET','CHECK_CURRENCY','CHECK_BANK_ACCOUNT','CHECK_EMP',
                        'CHECK_DATE1','CHECK_DATE2','CHECK_QTY','CHECK_QTY_UNIT','CHECK_RATE','CHECK_AMT1',
                        'CHECK_AMT2','CHECK_OTHNO','CHECK_INVEST'
                    ];
                    $labelMap = [
                        'CHECK_IN'=>'In','CHECK_DC'=>'DC','CHECK_DEPT'=>'Dept','CHECK_VENDOR'=>'Vendor','CHECK_BANK'=>'Bank','CHECK_GOODS'=>'Goods',
                        'CHECK_REMAIN'=>'Remain','CHECK_BUDGET'=>'Budget','CHECK_CURRENCY'=>'Currency','CHECK_BANK_ACCOUNT'=>'Bank Account','CHECK_EMP'=>'Emp',
                        'CHECK_DATE1'=>'Date1','CHECK_DATE2'=>'Date2','CHECK_QTY'=>'Qty','CHECK_QTY_UNIT'=>'Qty Unit','CHECK_RATE'=>'Rate','CHECK_AMT1'=>'Amt1',
                        'CHECK_AMT2'=>'Amt2','CHECK_OTHNO'=>'OthNo','CHECK_INVEST'=>'Invest'
                    ];
                    foreach ($toggles as $t) : ?>
                        <div class="col-md-3 mb-2">
                            <label class="form-label"><?= $labelMap[$t] ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input toggle-add" type="checkbox" id="<?= $t ?>_Add">
                                <label class="form-check-label" id="<?= $t ?>_LabelAdd" style="margin-top:4px; margin-left:10px;">NON-AKTIF</label>
                            </div>
                            <input type="hidden" name="<?= $t ?>" id="<?= $t ?>_ValueAdd" value="N">
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <div class="row mt-2">
                    <div class="col-md-3"><label class="form-label">Level No</label><input name="LEVEL_NO" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">BS Group 1</label><input name="BS_GROUP1" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">BS Group 2</label><input name="BS_GROUP2" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Account Type</label><input name="ACCOUNT_TYPE" class="form-control"></div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6"><label class="form-label">MOC Group 1</label><input name="MOC_GROUP1" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">MOC Group 2</label><input name="MOC_GROUP2" class="form-control"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="formEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Account - Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="orig_account" />
                <div class="row g-2">
                    <div class="col-md-4 mb-1">
                        <label class="form-label">Account *</label>
                        <input name="ACCOUNT" class="form-control" required readonly>
                    </div>

                    <div class="col-md-8 mb-1">
                        <label class="form-label">Account Name *</label>
                        <input name="ACCOUNT_NAME" class="form-control" required>
                    </div>

                    <div class="col-md-12 mb-1">
                        <label class="form-label">Remark</label>
                        <input name="REMARK" class="form-control">
                    </div>
                </div>

                <hr>

                <h6 class="mt-2">Checks (toggle Y/N)</h6>
                <div class="row">
                    <?php foreach ($toggles as $t) : ?>
                        <div class="col-md-3 mb-2">
                            <label class="form-label"><?= $labelMap[$t] ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input toggle-edit" type="checkbox" id="<?= $t ?>_Edit">
                                <label class="form-check-label" id="<?= $t ?>_LabelEdit" style="margin-top:4px; margin-left:10px;">NON-AKTIF</label>
                            </div>
                            <input type="hidden" name="<?= $t ?>" id="<?= $t ?>_ValueEdit" value="N">
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <div class="row mt-2">
                    <div class="col-md-3"><label class="form-label">Level No</label><input name="LEVEL_NO" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">BS Group 1</label><input name="BS_GROUP1" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">BS Group 2</label><input name="BS_GROUP2" class="form-control"></div>
                    <div class="col-md-3"><label class="form-label">Account Type</label><input name="ACCOUNT_TYPE" class="form-control"></div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6"><label class="form-label">MOC Group 1</label><input name="MOC_GROUP1" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">MOC Group 2</label><input name="MOC_GROUP2" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
        </form>
    </div>
</div>

<script>
    var state = {
        page: 1,
        limit: 10,
        order: 'ACCOUNT',
        dir: 'ASC',
        search: ''
    };

    $(function(){
        // debounce search input
        let timer = null;
        $('#search').on('keyup', function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                state.search = $('#search').val();
                state.page = 1;
                loadPage(1);
            }, 350);
        });

        // initial load
        loadPage(1);

        // init toggle handlers for ADD
        $('.toggle-add').on('change', function(){
            const id = $(this).attr('id'); // e.g. CHECK_IN_Add
            const key = id.replace('_Add','');
            const checked = $(this).is(':checked');
            $('#'+key+'_ValueAdd').val(checked ? 'Y' : 'N');
            $('#'+key+'_LabelAdd').text(checked ? 'AKTIF' : 'NON-AKTIF');
        });

        // init toggle handlers for EDIT
        $('.toggle-edit').on('change', function(){
            const id = $(this).attr('id'); // e.g. CHECK_IN_Edit
            const key = id.replace('_Edit','');
            const checked = $(this).is(':checked');
            $('#'+key+'_ValueEdit').val(checked ? 'Y' : 'N');
            $('#'+key+'_LabelEdit').text(checked ? 'AKTIF' : 'NON-AKTIF');
        });

        // submit add
        $('#formAdd').on('submit', function(e){
            e.preventDefault();

            // collect form data
            var formData = $(this).serializeArray();
            // ensure toggles included (they are hidden inputs)
            $.post("<?= base_url('account/create'); ?>", formData)
            .done(function(resp){
                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
                showToast(r.status ? 'success' : 'error', r.message);
                if (r.status) {
                    $('#modalAdd').modal('hide');
                    $('#formAdd')[0].reset();
                    // reset hidden toggle values to N and labels
                    $('.toggle-add').prop('checked', false).trigger('change');
                    loadPage(1);
                }
            }).fail(function(xhr){
                showToast('error','Server error');
                console.log(xhr.responseText);
            });
        });

        // submit edit
        $('#formEdit').on('submit', function(e){
            e.preventDefault();
            var formData = $(this).serializeArray();
            $.post("<?= base_url('account/update'); ?>", formData)
            .done(function(resp){
                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
                showToast(r.status ? 'success' : 'error', r.message);
                if (r.status) {
                    $('#modalEdit').modal('hide');
                    loadPage(state.page);
                }
            }).fail(function(xhr){
                showToast('error','Server error');
                console.log(xhr.responseText);
            });
        });

    });

    // load table page
    function loadPage(page = 1) {
        state.page = page;
        $('#table-body').html('<tr><td colspan="8" style="text-align:center">Loading...</td></tr>');

        $.get("<?= base_url('account/load_data'); ?>", {
            page: state.page,
            limit: state.limit,
            search: state.search,
            order: state.order,
            dir: state.dir
        })
        .done(function(res) {
            // Pastikan JSON
            let data = (typeof res === 'string') ? JSON.parse(res) : res;

            if(data.error){
                $('#table-body').html('<tr><td colspan="8" style="text-align:center;color:red;">'+data.error+'</td></tr>');
                $('#pagination').html('');
                $('#info').text('');
                return;
            }

            renderTable(data.rows);
            $('#pagination').html(data.pagination);
            $('#info').text('Menampilkan '+ data.rows.length + ' dari '+ data.total);

            // Update sort icons
            $('#mainTable thead th[data-order]').each(function(){
                let col = $(this).data('order');
                $(this).find('.sort-icon').text(col === state.order ? (state.dir === 'ASC' ? ' ▲' : ' ▼') : '');
            });
        })
        .fail(function(xhr, status, error){
            console.error('AJAX ERROR:', xhr.responseText);
            $('#table-body').html('<tr><td colspan="8" style="text-align:center;color:red;">Terjadi kesalahan server!</td></tr>');
            $('#pagination').html('');
            $('#info').text('');
        });
    }

    function renderTable(rows){
        let html = '';
        if(!rows || rows.length === 0){
            html = '<tr><td colspan="8" style="text-align:center">Tidak ada data</td></tr>';
        } else {
            rows.forEach(r => {
                html += '<tr>';
                html += '<td style="text-align: center; vertical-align: middle;">'+escapeHtml(r.ACCOUNT || '')+'</td>';
                html += '<td style="text-align: center; vertical-align: middle;">'+escapeHtml(r.ACCOUNT_NAME || '')+'</td>';
                html += '<td style="text-align: center; vertical-align: middle;">'+escapeHtml(r.REMARK || '')+'</td>';
                html += '<td style="text-align:center">'+escapeHtml(r.CHECK_IN || '')+'</td>';
                html += '<td style="text-align:center">'+escapeHtml(r.CHECK_DC || '')+'</td>';
                html += '<td style="text-align:center">'+escapeHtml(r.CHECK_VENDOR || '')+'</td>';
                html += '<td style="text-align: center; vertical-align: middle;">'+escapeHtml(r.ACCOUNT_TYPE || '')+'</td>';
                html += '<td style="text-align:center">';
                html += '<button class="btn btn-sm btn-warning me-1" onclick="showEdit(\''+r.ACCOUNT+'\')">Edit</button>';
                html += '<button class="btn btn-sm btn-danger" onclick="doDelete(\''+r.ACCOUNT+'\')">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        $('#table-body').html(html);
    }

    function showDetail(account){
        $('#detailBody').html('Loading...');
        $('#modalDetail').modal('show');
        $.get("<?= base_url('account/edit'); ?>", { account: account }, function(res){
            var r = res;
            if (typeof res === 'string') r = JSON.parse(res);
            if (!r.status) { $('#detailBody').html('Error'); return; }
            var d = r.data;
            var html = '<table class="table table-bordered">';
            for (var k in d) {
                html += '<tr><th style="width:30%">'+k+'</th><td>'+escapeHtml(d[k]||'')+'</td></tr>';
            }
            html += '</table>';
            $('#detailBody').html(html);
        }, 'json');
    }

    window.showEdit = function(account){
        $.get("<?= base_url('account/edit'); ?>", { account: account }, function(res){
            var r = res;
            if (typeof res === 'string') r = JSON.parse(res);
            if (!r.status) { alert('Error'); return; }

            var d = r.data;
            var f = $('#formEdit');

            f.find('input[name="orig_account"]').val(d.ACCOUNT);
            f.find('input[name="ACCOUNT"]').val(d.ACCOUNT).prop('readonly', true);
            f.find('input[name="ACCOUNT_NAME"]').val(d.ACCOUNT_NAME);
            f.find('input[name="REMARK"]').val(d.REMARK);

            // text fields
            f.find('input[name="LEVEL_NO"]').val(d.LEVEL_NO);
            f.find('input[name="BS_GROUP1"]').val(d.BS_GROUP1);
            f.find('input[name="BS_GROUP2"]').val(d.BS_GROUP2);
            f.find('input[name="ACCOUNT_TYPE"]').val(d.ACCOUNT_TYPE);
            f.find('input[name="MOC_GROUP1"]').val(d.MOC_GROUP1);
            f.find('input[name="MOC_GROUP2"]').val(d.MOC_GROUP2);

            // toggles: set each checkbox + hidden value + label
            var toggleKeys = [
                'CHECK_IN','CHECK_DC','CHECK_DEPT','CHECK_VENDOR','CHECK_BANK','CHECK_GOODS',
                'CHECK_REMAIN','CHECK_BUDGET','CHECK_CURRENCY','CHECK_BANK_ACCOUNT','CHECK_EMP',
                'CHECK_DATE1','CHECK_DATE2','CHECK_QTY','CHECK_QTY_UNIT','CHECK_RATE','CHECK_AMT1',
                'CHECK_AMT2','CHECK_OTHNO','CHECK_INVEST'
            ];
            toggleKeys.forEach(function(key){
                var val = (d[key] && d[key] === 'Y') ? 'Y' : 'N';
                var checked = val === 'Y';
                $('#'+key+'_Edit').prop('checked', checked);
                $('#'+key+'_ValueEdit').val(val);
                $('#'+key+'_LabelEdit').text(checked ? 'AKTIF' : 'NON-AKTIF');
            });

            $('#modalEdit').modal('show');
        }, 'json');
    };

    function doDelete(account) {
        if (!confirm('Yakin ingin dihapus?')) return;
        $.post("<?= base_url('account/remove'); ?>", { account: account }, function(res){
            var r = res;
            if (typeof res === 'string') r = JSON.parse(res);
            showToast(r.status ? 'success' : 'error', r.message);
            if (r.status) loadPage(state.page);
        }, 'json');
    }

    // helper
    function escapeHtml(text){
        if(!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon: type, title: message });
    }

    // attach generic listeners for runtime-created toggles inside modals (safety)
    $(document).on('change', '.toggle-add', function(){
        const id = $(this).attr('id'); const key = id.replace('_Add','');
        $('#'+key+'_ValueAdd').val($(this).is(':checked') ? 'Y' : 'N');
        $('#'+key+'_LabelAdd').text($(this).is(':checked') ? 'AKTIF' : 'NON-AKTIF');
    });
    $(document).on('change', '.toggle-edit', function(){
        const id = $(this).attr('id'); const key = id.replace('_Edit','');
        $('#'+key+'_ValueEdit').val($(this).is(':checked') ? 'Y' : 'N');
        $('#'+key+'_LabelEdit').text($(this).is(':checked') ? 'AKTIF' : 'NON-AKTIF');
    });
</script>
