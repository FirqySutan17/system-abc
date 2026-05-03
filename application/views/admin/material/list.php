<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">CD MATERIAL - BASE DATA</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari material..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button>
                        <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#mmaterialAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable" style="border-collapse: collapse; border-radius: 10px; overflow: hidden;">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th class="border-bottom-0 cursor-pointer" data-order="material" style="text-align: center; vertical-align: middle;">Material <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="material_name" style="text-align: center; vertical-align: middle;">Nama <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="material_class" style="text-align: center; vertical-align: middle;">Class <span class="sort-icon"></span></th>
                            <th class="border-bottom-0 cursor-pointer" data-order="grade" style="text-align: center; vertical-align: middle;">Grade <span class="sort-icon"></span></th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;">Stock</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;">HTC</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;">Spec</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;">Unit</th>
                            <th class="border-bottom-0 cursor-pointer" data-order="unit_price" style="text-align: center; vertical-align: middle;">Unit Price <span class="sort-icon"></span></th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;">Remark</th>
                            <th class="border-bottom-0" style="text-align: center; vertical-align: middle;"></th>
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
<div class="modal fade" id="mmaterialAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="materialAdd">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Material - Tambah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-md-6">
                        <label class="form-label">Material *</label>
                        <input name="material" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Material Name *</label>
                        <input name="material_name" class="form-control" placeholder="Input disini.." required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select id="classAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddenclassAdd" name="class">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <select id="gradeAdd" class="form-control" required></select>
                        <input type="hidden" id="hiddengradeAdd" name="grade">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">HTC</label>
                        <input name="htc" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Spec</label>
                        <input name="mat_spec" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Unit</label>
                        <input name="mat_unit" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Unit Price</label>
                        <input name="unit_price" type="number" step="0.01" class="form-control" placeholder="Input disini..">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Remark</label>
                        <input name="remark" class="form-control" placeholder="Input disini..">
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
<div class="modal fade" id="mmaterialEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="materialEdit">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">CD Material - Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="orig_material">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Material *</label>
                        <input name="material" class="form-control" placeholder="Input disini.." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Material Name *</label>
                        <input name="material_name" class="form-control" placeholder="Input disini.." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select id="classEdit" class="form-control" required></select>
                        <input type="hidden" id="hiddenclassEdit" name="class">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <select id="gradeEdit" class="form-control" required></select>
                        <input type="hidden" id="hiddengradeEdit" name="grade">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Stock</label>
                        <input name="stock" class="form-control" placeholder="Input disini..">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">HTC</label>
                        <input name="htc" class="form-control" placeholder="Input disini..">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Spec</label>
                        <input name="mat_spec" class="form-control" placeholder="Input disini..">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit</label>
                        <input name="mat_unit" class="form-control" placeholder="Input disini..">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Unit Price</label>
                        <input name="unit_price" type="number" step="0.01" class="form-control" placeholder="Input disini..">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Remark</label>
                        <input name="remark" class="form-control" placeholder="Input disini..">
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


<script>
/* ========================== STATE ========================== */
var state = {
    page: 1,
    limit: 10,
    order: 'material',
    dir: 'ASC',
    search: ''
};

/* ========================== PAGE LOAD ========================== */
$(function(){
    let timer = null;
    $('#search').on('keyup', function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            state.search = $('#search').val();
            state.page = 1;
            loadPage(1);
        }, 350);
    });

    loadPage(1);

    $('#mainTable thead').on('click','th[data-order]', function(){
        var col = $(this).data('order');
        if (state.order === col) {
            state.dir = state.dir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            state.order = col;
            state.dir = 'ASC';
        }
        loadPage(1);
    });

    $('#mmaterialAdd').on('shown.bs.modal', function() {
        // SELECT2 Class
        if (!$('#classAdd').hasClass('select2-hidden-accessible')) {
            $('#classAdd').select2({
                tags: true,
                placeholder: '- PILIH CLASS -',
                width: '100%',
                dropdownParent: $('#mmaterialAdd'),
                ajax: {
                    url: '<?= base_url("material/get_class") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenclassAdd').val(e.params.data.id);
            });
        }

        // SELECT2 Grade
        if (!$('#gradeAdd').hasClass('select2-hidden-accessible')) {
            $('#gradeAdd').select2({
                tags: true,
                placeholder: '- PILIH GRADE -',
                width: '100%',
                dropdownParent: $('#mmaterialAdd'),
                ajax: {
                    url: '<?= base_url("material/get_grades") ?>',
                    dataType: 'json',
                    processResults: function(data){ return { results: data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddengradeAdd').val(e.params.data.text.toUpperCase());
            });
        }
    });

    $('#materialAdd').on('submit', function(e){
        e.preventDefault();

        var data = {
            material: $('input[name="material"]').val(),
            material_name: $('input[name="material_name"]').val(),
            class: $('#classAdd').select2('data')[0] ? $('#classAdd').select2('data')[0].text : '',
            grade: $('#gradeAdd').select2('data')[0] ? $('#gradeAdd').select2('data')[0].text : '',
            stock: $('input[name="stock"]').val(),
            htc: $('input[name="htc"]').val(),
            mat_spec: $('input[name="mat_spec"]').val(),
            mat_unit: $('input[name="mat_unit"]').val(),
            unit_price: $('input[name="unit_price"]').val(),
            remark: $('input[name="remark"]').val()
        };

        console.log('DATA SUBMIT:', data);

        $.post("<?= base_url('material/create'); ?>", data, function(resp){
            var r = typeof resp === 'string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success' : 'error', r.message);

            if(r.status){
                $('#mmaterialAdd').modal('hide');
                $('#materialAdd')[0].reset();
                $('#classAdd, #gradeAdd').val(null).trigger('change');
                loadPage(1);
            }
        }).fail(function(xhr){
            console.error('AJAX ERROR:', xhr.responseText);
            showToast('error', 'Terjadi kesalahan server!');
        });
    });

    // fungsi preload sama persis
    function preloadSelect2Value(selectId, hiddenId, value){
        if(!value) return;

        // Buat option agar Select2 mengenali value
        let option = new Option(value, value, true, true);
        $(selectId).append(option).trigger('change');

        // Isi hidden input agar form submit dapat value
        $(hiddenId).val(value);
    }

    $('#mmaterialEdit').on('shown.bs.modal', function() {
        if (!$('#classEdit').hasClass('select2-hidden-accessible')) {
            $('#classEdit').select2({
                tags:true,
                placeholder:'- PILIH CLASS -',
                width:'100%',
                dropdownParent: $('#mmaterialEdit'),
                ajax:{
                    url:'<?= base_url("material/get_class") ?>',
                    dataType:'json',
                    processResults:function(data){ return { results:data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddenclassEdit').val(e.params.data.id);
            });
        }

        if (!$('#gradeEdit').hasClass('select2-hidden-accessible')) {
            $('#gradeEdit').select2({
                tags:true,
                placeholder:'- PILIH GRADE -',
                width:'100%',
                dropdownParent: $('#mmaterialEdit'),
                ajax:{
                    url:'<?= base_url("material/get_grades") ?>',
                    dataType:'json',
                    processResults:function(data){ return { results:data }; }
                }
            }).on('select2:select', function(e){
                $('#hiddengradeEdit').val(e.params.data.text);
            });
        }
    });

    // SUBMIT EDIT MATERIAL
    $('#materialEdit').on('submit', function(e){
        e.preventDefault(); // cegah reload page

        // Ambil semua value input biasa
        var material = $('#materialEdit input[name="material"]').val().trim();
        var material_name = $('#materialEdit input[name="material_name"]').val().trim();
        var stock = $('#materialEdit input[name="stock"]').val().trim();
        var htc = $('#materialEdit input[name="htc"]').val().trim();
        var mat_spec = $('#materialEdit input[name="mat_spec"]').val().trim();
        var mat_unit = $('#materialEdit input[name="mat_unit"]').val().trim();
        var unit_price = $('#materialEdit input[name="unit_price"]').val().trim();
        var remark = $('#materialEdit input[name="remark"]').val().trim();
        var orig_material = $('#materialEdit input[name="orig_material"]').val();

        // Ambil value dari Select2 (hidden input)
        var material_class = $('#hiddenclassEdit').val();
        var grade = $('#hiddengradeEdit').val();

        var data = {
            orig_material: orig_material,
            material: material,
            material_name: material_name,
            class: material_class,
            grade: grade,
            stock: stock,
            htc: htc,
            mat_spec: mat_spec,
            mat_unit: mat_unit,
            unit_price: unit_price,
            remark: remark
        };

        console.log('DATA SUBMIT EDIT:', data);

        $.post("<?= base_url('material/update'); ?>", data)
        .done(function(resp){
            var r = typeof resp==='string' ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success':'error', r.message);
            if(r.status){
                $('#mmaterialEdit').modal('hide');
                $('#materialEdit')[0].reset();
                $('#classEdit, #gradeEdit').val(null).trigger('change');
                loadPage(1);
            }
        })
        .fail(function(xhr){
            console.error('AJAX ERROR:', xhr.responseText);
            showToast('error','Terjadi kesalahan server!');
        });
    });


    /* EXPORT */
    $('#exportExcel').on('click', function(){
        window.location = "<?= base_url('material/export_excel'); ?>?search="+state.search+"&order="+state.order+"&dir="+state.dir;
    });
    $('#exportPdf').on('click', function(){
        window.location = "<?= base_url('material/export_pdf'); ?>?search="+state.search+"&order="+state.order+"&dir="+state.dir;
    });
});

/* ========================== LOAD PAGE ========================== */
function loadPage(page){
    state.page = page;
    $('#table-body').html('<tr><td style="text-align: center" colspan="11">Loading...</td></tr>');
    $.get("<?= base_url('material/load_data'); ?>", state, function(res){
        let data = typeof res === 'string' ? JSON.parse(res) : res;
        renderTable(data.rows);
        $('#pagination').html(data.pagination);
        $('#info').text('Menampilkan '+data.rows.length+' dari '+data.total);
    }, 'json');
}

/* ========================== RENDER TABLE ========================== */
function renderTable(rows){
    let html = '';
    if (!rows || rows.length === 0) {
        html = '<tr><td style="text-align: center" colspan="11">Tidak ada data</td></tr>';
    } else {
        rows.forEach(r => {
            html += `
                <tr>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.material)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.material_name)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.material_class_name)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.grade)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.stock)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.htc)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.mat_spec)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.mat_unit)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.unit_price)}</td>
                    <td style="text-align: center; vertical-align: middle;">${escapeHtml(r.remark)}</td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button class="btn btn-sm btn-warning me-1" onclick="showEdit('${r.material}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="doDelete('${r.material}')">Delete</button>
                    </td>
                </tr>
            `;
        });
    }
    $('#table-body').html(html);
}

/* ========================== DETAIL ========================== */
function showDetail(material){
    $('#detailBody').html('Loading...');
    $('#modalDetail').modal('show');
    $.get("<?= base_url('material/detail'); ?>", {material}, function(res){
        let r = typeof res === 'string' ? JSON.parse(res) : res;
        if (!r.status) return $('#detailBody').html('Error');

        let d = r.data;
        let html = `<table class="table table-bordered">`;
        for (let k in d){
            html += `<tr><th>${k}</th><td>${escapeHtml(d[k]||'')}</td></tr>`;
        }
        html += `</table>`;
        $('#detailBody').html(html);
    });
}

function preloadSelect2Value(selectId, hiddenId, value){
    if(!value) return;

    // Buat option agar Select2 mengenali value
    let option = new Option(value, value, true, true);
    $(selectId).append(option).trigger('change');

    // Isi hidden input agar form submit dapat value
    $(hiddenId).val(value);
}

function showEdit(material){
    $.get("<?= base_url('material/edit'); ?>", { material: material }, function(res){
        if(typeof res==='string') res = JSON.parse(res);
        if(!res.status){ alert('Error'); return; }

        let d = res.data;
        let f = $('#materialEdit');

        // Isi input biasa
        f.find('input[name="orig_material"]').val(d.material);
        f.find('input[name="material"]').val(d.material);
        f.find('input[name="material_name"]').val(d.material_name);
        f.find('input[name="stock"]').val(d.stock);
        f.find('input[name="htc"]').val(d.htc);
        f.find('input[name="mat_spec"]').val(d.mat_spec);
        f.find('input[name="mat_unit"]').val(d.mat_unit);
        f.find('input[name="unit_price"]').val(d.unit_price);
        f.find('input[name="remark"]').val(d.remark);

        // Preload Select2 values
        preloadSelect2Value('#classEdit','#hiddenclassEdit', d.material_class);
        preloadSelect2Value('#gradeEdit','#hiddengradeEdit', d.grade);

        // Tampilkan modal
        $('#mmaterialEdit').modal('show');
    });
}


// /* ========================== EDIT ========================== */
// function showEdit(material){
//     $.get("<?= base_url('material/edit'); ?>", {material}, function(res){
//         let r = typeof res === 'string' ? JSON.parse(res) : res;
//         if (!r.status) return;

//         let d = r.data;
//         let f = $('#materialEdit');

//         for (let k in d){
//             f.find(`[name="${k}"]`).val(d[k]);
//         }

//         $('#mmaterialEdit').modal('show');
//     });
// }

/* ========================== DELETE ========================== */
function doDelete(material){
    if (!confirm("Yakin ingin menghapus data?")) return;
    $.post("<?= base_url('material/remove'); ?>", {material}, function(res){
        let r = typeof res === 'string' ? JSON.parse(res) : res;
        alert(r.message);
        if (r.status) loadPage(state.page);
    });
}

/* ========================== UTIL ========================== */
function escapeHtml(text){
    return $('<div/>').text(text).html();
}

function showToast(type, message){
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000
    });
    Toast.fire({ icon: type, title: message });
}
</script>
