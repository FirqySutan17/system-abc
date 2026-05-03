<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">ROLE & PERMISSION - SETTINGS</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3" style="margin-top: 20px">
                <div class="col-md-8 col-sm-12">
                    <input id="search" type="text" class="form-control" placeholder="Cari nama role..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <button class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#roleAdd">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-nowrap mb-0 align-middle" id="mainTable">
                    <thead class="text-dark fs-3">
                        <tr>
                            <th class="border-bottom-0 cursor-pointer text-center" data-order="role_name">
                                Role <span class="sort-icon"></span>
                            </th>
                            <th class="border-bottom-0 text-center">Description</th>
                            <th style="width: 20%" class="border-bottom-0 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="fs-3">
                        <!-- AJAX -->
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

<!-- MODAL ADD ROLE -->
<div class="modal fade" id="roleAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="froleAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Role - Tambah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Role Name *</label>
                            <input name="role_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input name="description" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT ROLE -->
<div class="modal fade" id="roleEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="froleEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Role - Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="orig_id" id="orig_id">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Role Name *</label>
                            <input name="role_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input name="description" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PERMISSION -->
<div class="modal fade" id="rolePermission" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form id="frolePermission">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="orig_role_id" name="role_id">
                    <div class="row" id="permissionArea">
                        <!-- Checkbox menu via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
var state = {
    page: 1,
    limit: 10,
    order: 'role_name',
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

    loadPage(1);

    // handle sort
    $('#mainTable thead').on('click', 'th[data-order]', function(){
        var col = $(this).data('order');
        state.dir = (state.order === col) ? (state.dir === 'ASC' ? 'DESC' : 'ASC') : 'ASC';
        state.order = col;
        loadPage(1);
    });

    // ADD ROLE
    $('#froleAdd').on('submit', function(e){
        e.preventDefault();
        var data = {
            role_name: $('input[name="role_name"]').val(),
            description: $('input[name="description"]').val()
        };
        if(!data.role_name){ showToast('error','Role name wajib diisi'); return; }

        $.post("<?= base_url('roles/create'); ?>", data)
        .done(function(resp){
            var r = typeof resp==='string'?JSON.parse(resp):resp;
            if(r.status){
                showToast('success',r.message);
                $('#roleAdd').modal('hide');
                $('#froleAdd')[0].reset();
                loadPage(1);
            } else showToast('error',r.message);
        }).fail(function(xhr){
            showToast('error','Terjadi kesalahan pada server!');
            console.log(xhr.responseText);
        });
    });

    // EDIT ROLE
    $("#froleEdit").on("submit", function(e){
        e.preventDefault();
        $.post("<?= base_url('roles/update'); ?>", $(this).serialize())
        .done(function(resp){
            if(typeof resp==='string') resp=JSON.parse(resp);
            showToast(resp.status?'success':'error',resp.message);
            if(resp.status){ $("#roleEdit").modal("hide"); loadPage(state.page); }
        }).fail(function(xhr){ showToast('error','Server error'); console.log(xhr.responseText); });

    });

    // PERMISSION
    $('#frolePermission').on('submit', function(e){
        e.preventDefault();
        $.post("<?= base_url('roles/save_permission'); ?>", $(this).serialize())
        .done(function(resp){
            let r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
            showToast(r.status ? 'success' : 'error', r.message);
            if(r.status){
                $('#rolePermission').modal('hide');
                loadPage(state.page);
            }
        })
        .fail(function(xhr){
            showToast('error','Server error');
            console.log(xhr.responseText);
        });
    });

    // reset modals
    $('#roleAdd').on('hidden.bs.modal', function(){ $('#froleAdd')[0].reset(); });
    $('#roleEdit').on('hidden.bs.modal', function(){ $('#froleEdit')[0].reset(); });
    $('#rolePermission').on('hidden.bs.modal', function(){ $('#permissionArea').empty(); });
});

function loadPage(page){
    state.page=page;
    $('#table-body').html('<tr><td colspan="3" style="text-align:center">Loading...</td></tr>');
    $.get("<?= base_url('roles/load_data'); ?>", {
        page: page, limit: state.limit, search: state.search, order: state.order, dir: state.dir
    }, function(res){
        var data = typeof res==='string'?JSON.parse(res):res;
        renderTable(data.rows);
        $('#pagination').html(data.pagination);
        $('#info').text('Menampilkan '+ data.rows.length +' dari '+ data.total);
        $('#mainTable thead th[data-order]').each(function(){
            var col=$(this).data('order');
            $(this).find('.sort-icon').text(col===state.order?(state.dir==='ASC'?' ▲':' ▼'):'');
        });
    },'json');
}

function renderTable(rows){
    var html='';
    if(!rows||rows.length===0){
        html='<tr><td colspan="3" style="text-align:center">Tidak ada data</td></tr>';
    } else {
        rows.forEach(function(r){
            html+='<tr>';
            html+='<td class="text-center align-middle">'+escapeHtml(r.role_name)+'</td>';
            html+='<td class="text-center align-middle">'+escapeHtml(r.description||'')+'</td>';
            html+='<td class="text-center">';
            html+='<button class="btn btn-sm btn-primary me-1" onclick="showPermission('+r.id+')">Permission</button>';
            html+='<button class="btn btn-sm btn-warning me-1" onclick="showEdit('+r.id+')">Edit</button>';
            html+='<button class="btn btn-sm btn-danger" onclick="doDelete('+r.id+')">Delete</button>';
            html+='</td></tr>';
        });
    }
    $('#table-body').html(html);
}

window.showEdit=function(id){
    $.get("<?= base_url('roles/edit'); ?>",{id:id},function(res){
        if(typeof res==='string') res=JSON.parse(res);
        if(!res.status){ alert('Data tidak ditemukan!'); return; }
        let d=res.data;
        let f=$("#froleEdit");
        f.find("#orig_id").val(d.id);
        f.find('input[name="role_name"]').val(d.role_name);
        f.find('input[name="description"]').val(d.description||'');
        $("#roleEdit").modal("show");
    },'json');
};

window.showPermission = function(role_id){
    $.get("<?= base_url('roles/get_permission'); ?>", {id: role_id}, function(res){
        if(typeof res === 'string') res = JSON.parse(res);
        if(!res.status){ showToast('error', res.message); return; }

        let container = $('#permissionArea');
        container.empty();

        res.permissions.forEach(function(p){
            let checked = p.checked ? 'checked' : '';
            container.append(`
                <div class="form-check col-md-4" style="font-size: 17px; padding: 10px;">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="${p.menu_id}" id="perm_${p.menu_id}" ${checked}>
                    <label class="form-check-label" for="perm_${p.menu_id}">${p.menu_name}</label>
                </div>
            `);
        });

        $('#orig_role_id').val(role_id);
        $('#rolePermission').modal("show");
    }, 'json');
};


function doDelete(id){
    if(!confirm('Yakin ingin dihapus?')) return;
    $.post("<?= base_url('roles/remove'); ?>",{id:id},function(res){
        var r=typeof res==='string'?JSON.parse(res):res;
        showToast(r.status?'success':'error',r.message);
        if(r.status) loadPage(state.page);
    },'json');
}

function escapeHtml(text){ return text==null?'':$('<div/>').text(text).html(); }

function showToast(type,message){
    const Toast=Swal.mixin({ toast:true, position:"top-end", showConfirmButton:false, timer:3000, timerProgressBar:true });
    Toast.fire({ icon:type, title:message });
}
</script>
