<style>
    .cct-wrapper {
        display: grid;
        grid-template-columns: 2fr 6fr;
        gap: 20px;
    }

    .cct-panel {
        overflow: hidden;
        background: #fff;
    }

    .cct-panel-header {
        background: #007bff;
        color: #fff;
        font-weight: 700;
        padding: 10px;
        text-align: center;
    }

    .cct-panel table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .cct-panel th {
        text-align: center;
        position: sticky;
        top: 0;
        background: #f4f6f9;
        border: 1px solid #000;
    }

    .cct-panel td {
        padding: 8px !important;
        vertical-align: middle;
    }

    #headcode-body tr {
        cursor: pointer;
    }

    #headcode-body tr:hover {
        background: #e9f3ff;
    }

    .active-row {
        background: #f8971d !important;
    }

    .active-row td {
        color: #fff !important;
    }

    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
    /* responsive */
    @media(max-width: 768px){
        .cct-wrapper {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">GENERAL CODE - MASTER</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <!-- <input id="search" type="text" class="form-control" placeholder="Cari code..." /> -->
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group ">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button>
                        <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="cct-wrapper">
                <!-- LEFT : HEAD CODE -->
                <div class="cct-panel">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Head</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody id="headcode-body">
                            <!-- AUTO GENERATED -->
                        </tbody>
                    </table>
                </div>

                <!-- RIGHT : DETAIL -->
                <div class="cct-panel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap mb-0 align-middle" id="mainTable">
                            <thead>
                                <tr>
                                    <th data-order="code">Code</th>
                                    <th data-order="code_name">Code Name</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <!-- AJAX EXISTING -->
                            </tbody>
                        </table>
                    </div>
                </div>
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
                <h5 class="modal-title">CD Code - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6 mb-1">
                        <label class="form-label">General Code *</label>
                        <select id="headAddSelect" class="form-control" required></select>
                        <input type="hidden" id="hiddenHeadAdd" name="head_code">
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Code *</label>
                        <select id="codeAddSelect" class="form-control" required></select>
                        <input type="hidden" id="hiddenCodeAdd" name="code">
                    </div>

                    <div class="col-md-12 mb-1">
                        <label class="form-label">Nama *</label>
                        <input name="code_name" class="form-control" placeholder="Input disini.." required>
                    </div>
                </div>

                <div class="row mt-2 mb-1">
                    <div class="col-md-8">
                        <label class="form-label">Deskripsi</label>
                        <input name="desc1" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="useYnToggleAdd">
                            <label class="form-check-label" id="useYnLabelAdd" style="margin-top: 4px; margin-left: 10px;">NON-AKTIF</label>
                        </div>
                        <input type="hidden" name="use_yn" id="useYnValueAdd" value="N">
                    </div>
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
    <div class="modal-dialog modal-lg">
        <form id="formEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Code - Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="orig_head" />
                <input type="hidden" name="orig_code" />
                <div class="row g-2">
                    <div class="col-md-6 mb-1">
                        <label class="form-label">General Code *</label>
                        <select name="head_code" id="headEditSelect" class="form-control" disabled></select>
                        <input type="hidden" id="hiddenHeadEdit" name="head_code">
                    </div>

                    <div class="col-md-6 mb-1">
                        <label class="form-label">Code *</label>
                        <select name="code" id="codeEditSelect" class="form-control"></select>
                        <input type="hidden" id="hiddenCodeEdit" name="code">
                    </div>

                    <div class="col-md-12 mb-1">
                        <label class="form-label">Nama *</label>
                        <input name="code_name" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-2 mb-1">
                    <div class="col-md-8">
                        <label class="form-label">Deskripsi</label>
                        <input name="desc1" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="useYnToggleEdit">
                            <label class="form-check-label" id="useYnLabelEdit" style="margin-top: 4px; margin-left: 10px;">NON-AKTIF</label>
                        </div>
                        <input type="hidden" name="use_yn" id="useYnValueEdit">
                    </div>
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


<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Detail CD Code</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" ></button>
            </div>
            <div class="modal-body" id="detailBody">
                <!-- filled by AJAX -->
                Loading...
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function buildHeadCode(rows) {
        let html = '';
        rows.forEach(r => {
            html += `
                <tr class="headcode-row" data-head="${r.HEAD_CODE}">
                    <td class="text-center" style="vertical-align: center">${r.HEAD_CODE}</td>
                    <td class="text-center" style="vertical-align: center">${r.CODE_NAME}</td>
                </tr>
            `;
        });

        $('#headcode-body').html(html);
    }

    function loadHeadCodePanel() {
        $.get("<?= base_url('code/get_head_code_panel'); ?>", function(res){
            let data = res;
            if(typeof res === 'string') data = JSON.parse(res);
            buildHeadCode(data);

            // 🔹 AUTO SELECT baris pertama
            if(data.length > 0){
                const firstHead = data[0].HEAD_CODE;
                state.head = firstHead;

                // tandai row pertama aktif
                $('#headcode-body tr.headcode-row').first().addClass('active-row');

                // load panel kanan sesuai HEAD_CODE pertama
                loadPage(1);
            }

        }, 'json');
    }

    $(document).ready(function(){
        loadHeadCodePanel(); // load panel kiri saat page ready

        // klik row untuk filter tabel kanan
        $(document).on('click', '.headcode-row', function () {
            $('.headcode-row').removeClass('active-row');
            $(this).addClass('active-row');

            let head = $(this).data('head');
            state.search = head; // set search untuk tabel kanan
            state.page = 1;
            loadPage(1); // reload tabel kanan
        });
    });

    // $(document).on('click', '.headcode-row', function () {
    //     $('.headcode-row').removeClass('active-row');
    //     $(this).addClass('active-row');

    //     let head = $(this).data('head');

    //     // filter via existing mechanism
    //     state.search = head;
    //     state.page = 1;
    //     loadPage(1);
    // });
    
    var state = {
        page: 1,
        limit: 10,
        order: 'head_code',
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

        // INIT ADD SELECT2
        $('#headAddSelect').select2({
            tags: true,
            placeholder: 'Pilih atau Input GENERAL CODE',
            width: '100%',
            dropdownParent: $('#modalAdd'),
            ajax: {
                url: '<?= base_url("code/get_head_code") ?>',
                dataType: 'json',
                processResults: function(data){
                    return { results: data };
                }
            }
        }).on('select2:select', function(e){
            let val = e.params.data.text.toUpperCase();
            $('#hiddenHeadAdd').val(val);

            // Reset CODE
            $('#codeAddSelect').val(null).trigger('change');
            $('#hiddenCodeAdd').val('');
            $('#codeAddSelect').data('head-code', val);
        });

        $('#codeAddSelect').select2({
            tags: true,
            placeholder: 'Pilih GENERAL CODE terlebih dahulu..',
            width: '100%',
            dropdownParent: $('#modalAdd'),
            ajax: {
                url: '<?= base_url("code/get_code_by_head") ?>',
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return {
                        head: $('#codeAddSelect').data('head-code'),
                        q: params.term
                    };
                },
                processResults: function(data){
                    return { results: data };
                }
            }
        }).on('select2:select', function(e){
            $('#hiddenCodeAdd').val(e.params.data.text.toUpperCase());
        });

        // INIT EDIT SELECT2
        $('#headEditSelect').select2({
            tags: true,
            placeholder: 'Pilih HEAD CODE',
            width: '100%'
        });

        $('#codeEditSelect').select2({
            tags: true,
            placeholder: 'Pilih atau input CODE',
            width: '100%',
            dropdownParent: $('#modalEdit'),
        });


        // handle header sort click
        $('#mainTable thead').on('click', 'th[data-order]', function(){
            var col = $(this).data('order');
            if (state.order === col) {
                state.dir = state.dir === 'ASC' ? 'DESC' : 'ASC';
            } else {
                state.order = col;
                state.dir = 'ASC';
            }
            loadPage(1);
        });

        // submit add
        $('#formAdd').on('submit', function(e){
            e.preventDefault();

            var data = {
                head_code: $('#headAddSelect').select2('data')[0] ? $('#headAddSelect').select2('data')[0].text.toUpperCase() : '',
                code: $('#codeAddSelect').select2('data')[0] ? $('#codeAddSelect').select2('data')[0].text.toUpperCase() : '',
                code_name: $('input[name="code_name"]').val(),
                desc1: $('input[name="desc1"]').val(),
                use_yn: $('#useYnValueAdd').val()
            };

            if(!data.head_code || !data.code || !data.code_name){
                showToast('error', 'head_code, code dan code_name wajib diisi');
                return;
            }

            $.post("<?= base_url('code/create'); ?>", data)
            .done(function(resp){
                var r = typeof resp === 'string' ? JSON.parse(resp) : resp;

                if(r.status){
                    showToast('success', r.message);
                    $('#modalAdd').modal('hide');
                    $('#formAdd')[0].reset();
                    $('#headAddSelect, #codeAddSelect').val(null).trigger('change');

                    // 🔹 Refresh table
                    loadPage(1);

                    // 🔹 Highlight row baru (opsional)
                    setTimeout(() => {
                        $('#tableContainer tr').first().css('background-color', '#d4edda');
                        setTimeout(() => $('#tableContainer tr').first().css('background-color', ''), 2000);
                    }, 500); // tunggu table selesai load
                } else {
                    showToast('error', r.message);
                }

            })
            .fail(function(xhr){
                showToast('error', 'Terjadi kesalahan pada server!');
                console.log("AJAX Error", xhr.responseText);
            });
        });

        // submit edit
        $('#formEdit').on('submit', function(e){
            e.preventDefault();
            $.post("<?= base_url('code/update'); ?>", $(this).serialize(), function(resp){
                var r = resp;
                if (typeof resp === 'string') r = JSON.parse(resp);
                alert(r.message);
                if (r.status) {
                    $('#modalEdit').modal('hide');
                    loadPage(state.page);
                }
            });
        });

        // export
        $('#exportExcel').on('click', function(){
            var url = "<?= base_url('code/export_excel'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
            window.location = url;
        });
        $('#exportPdf').on('click', function(){
            var url = "<?= base_url('code/export_pdf'); ?>?search="+encodeURIComponent(state.search)+"&order="+state.order+"&dir="+state.dir;
            window.location = url;
        });

    });

    // global function used by pagination links
    function loadPage(page = 1) {
        state.page = page;
        $('#table-body').html('<tr><td colspan="5" style="text-align:center">Loading...</td></tr>');

        $.get("<?= base_url('code/get_codes_by_head'); ?>", {
            head: state.head,
            page: state.page,
            limit: state.limit,
            order: state.order,
            dir: state.dir,
            search: state.search
        }, function(res){
            let data = res;
            if(typeof res==='string') data = JSON.parse(res);

            renderTable(data.rows);
            $('#pagination').html(data.pagination);
            $('#info').text('Menampilkan '+ data.rows.length + ' dari '+ data.total);

            // update sort icons
            $('#mainTable thead th[data-order]').each(function(){
                var col = $(this).data('order');
                $(this).find('.sort-icon').text(col === state.order ? (state.dir==='ASC'?' ▲':' ▼') : '');
            });
        }, 'json');
    }

    $(document).on('click', '.headcode-row', function () {
        $('.headcode-row').removeClass('active-row');
        $(this).addClass('active-row');

        state.head = $(this).data('head'); // set HEAD_CODE
        state.page = 1;
        loadPage(1); // reload panel kanan sesuai HEAD_CODE
    });

    function loadCodeAdd(headCode){
        $('#codeAddSelect').empty().trigger('change');
        $('#codeAddSelect').select2({
            tags: true,
            placeholder: 'Pilih atau input CODE',
            dropdownParent: $('#modalAdd'),
            ajax: {
                url: '<?= base_url("code/get_code_by_head") ?>?head=' + headCode,
                dataType: 'json',
                processResults: function(data){ return { results: data }; }
            }
        }).on('select2:select', function(e){
            $('#hiddenCodeAdd').val(e.params.data.text.toUpperCase());
        });
    }

    function renderTable(rows){
        var html = '';
        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="6" style="text-align: center">Tidak ada data</td></tr>';
        } else {
            rows.forEach(function(r){
                html += '<tr>';
                html += '<td>'+escapeHtml(r.CODE)+'</td>';
                html += '<td>'+escapeHtml(r.CODE_NAME)+'</td>';
                html += '<td>'+escapeHtml(r.DESC1 || '')+'</td>';
                html += '<td style="text-align: center; vertical-align: middle">'+escapeHtml(r.USE_YN || '')+'</td>';
                html += '<td style="text-align: center">';
                html += '<button class="btn btn-sm btn-warning me-1" onclick="showEdit(\''+r.HEAD_CODE+'\',\''+r.CODE+'\')">Edit</button>';
                html += '<button class="btn btn-sm btn-danger" onclick="doDelete(\''+r.HEAD_CODE+'\',\''+r.CODE+'\')">Delete</button>';
                html += '</td>';
                html += '</tr>';
            });
        }
        $('#table-body').html(html);
    }

    function showDetail(head, code) {
        $('#detailBody').html('Loading...');
        $('#modalDetail').modal('show');
        $.get("<?= base_url('code/detail'); ?>", { head: head, code: code }, function(res){
            var r = res;
            if (typeof res === 'string') r = JSON.parse(res);
            if (!r.status) {
                $('#detailBody').html('Error: '+r.message);
                return;
            }
            var d = r.data;
            var html = '<table class="table table-bordered">';
            for (var k in d) {
                html += '<tr><th style="width:30%">'+k+'</th><td>'+escapeHtml(d[k]||'')+'</td></tr>';
            }
            html += '</table>';
            $('#detailBody').html(html);
        }, 'json');
    }

    window.showEdit = function(head, code){
        $.get("<?= base_url('code/edit'); ?>", { head, code }, function(res){
            if(typeof res==='string') res=JSON.parse(res);
            if(!res.status) { alert('Error'); return; }

            let d = res.data;

            // ===== HEAD_CODE =====
            let headOption = new Option(d.HEAD_CODE, d.HEAD_CODE, true, true);
            $('#headEditSelect').empty().append(headOption).trigger('change');
            $('#headEditSelect').prop('disabled', true);
            $('#hiddenHeadEdit').val(d.HEAD_CODE);

            // ===== CODE =====
            $('#codeEditSelect').empty().append(new Option(d.CODE, d.CODE, true, true)).trigger('change');
            $('#hiddenCodeEdit').val(d.CODE);

            // Init CODE select2 ajax for searching other codes if needed
            $('#codeEditSelect').select2({
                tags:true,
                placeholder:'Pilih atau input CODE',
                width:'100%',
                dropdownParent: $('#modalEdit'),
                ajax:{
                    url:'<?= base_url("code/get_code_by_head") ?>?head=' + d.HEAD_CODE,
                    dataType:'json',
                    processResults:function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenCodeEdit').val(e.params.data.text.toUpperCase());
            });

            // ===== OTHER FIELDS =====
            $('#formEdit input[name="orig_head"]').val(d.HEAD_CODE);
            $('#formEdit input[name="orig_code"]').val(d.CODE);
            $('#formEdit input[name="code_name"]').val(d.CODE_NAME);
            $('#formEdit input[name="desc1"]').val(d.DESC1);
            $('#formEdit input[name="use_yn"]').val(d.USE_YN);

            // ===== TOGGLE STATUS =====
            const toggle = $('#useYnToggleEdit');
            const hidden = $('#useYnValueEdit');
            const label  = $('#useYnLabelEdit');
            if(d.USE_YN==='Y'){ toggle.prop('checked',true); hidden.val('Y'); label.text('AKTIF'); }
            else{ toggle.prop('checked',false); hidden.val('N'); label.text('NON-AKTIF'); }

            $('#modalEdit').modal('show');

        }, 'json');
    };

    function loadCodeEdit(headCode){
        $('#codeEditSelect').select2({
            tags:true,
            placeholder:'Pilih atau input CODE',
            dropdownParent: $('#modalEdit'),
            ajax:{
                url:'<?= base_url("code/get_code_by_head") ?>?head=' + headCode,
                dataType:'json',
                processResults:function(data){ return { results: data }; }
            }
        }).on('select2:select', function(e){
            $('#hiddenCodeEdit').val(e.params.data.text.toUpperCase());
        });
    }

    function doDelete(head, code) {
        if (!confirm('Yakin ingin dihapus?')) return;
        $.post("<?= base_url('code/remove'); ?>", { head: head, code: code }, function(res){
            var r = res;
            if (typeof res === 'string') r = JSON.parse(res);
            alert(r.message);
            if (r.status) loadPage(state.page);
        }, 'json');
    }

    // helper
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return $('<div/>').text(text).html();
    }

    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
    }
</script>

<script>
    $('#useYnToggleAdd').on('change', function(){
        $('#useYnValueAdd').val(this.checked ? 'Y' : 'N');
        $('#useYnLabelAdd').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
    });

    $('#useYnToggleEdit').on('change', function(){
        $('#useYnValueEdit').val(this.checked ? 'Y' : 'N');
        $('#useYnLabelEdit').text(this.checked ? 'AKTIF' : 'NON-AKTIF');
    });
</script>