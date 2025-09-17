@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <button id="btnTambah" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Tambah Level
                </button>
            </div>
        </div>
        <div class="card-datatable">
            <table id="LevelU" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusLevel" method="post">
        @method('DELETE')
        @csrf
    </form>

    @include('app.level.modal')
@endsection
@section('script')
    <script>
        let table = $('#LevelU').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/app/level"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: d => `
                    <div class="d-inline-flex gap-1">
                        <button class="btn btn-sm btn-primary btnEdit" data-id="${d.id}" data-nama="${d.nama}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${d.id}">Hapus</button>
                    </div>`
                }
            ],
        });

        $('#btnTambah').click(() => {
            const form = $('#FormLevel');
            form.trigger('reset');
            form.find('input[name="id_Level"]').val('');
            form.attr('action', `/app/level`);
            form.find('input[name="_method"]').remove();
            $('#formTitle').text("Tambah Level Baru").css('color', 'green');
            new bootstrap.Modal(document.getElementById('LevelModal')).show();
        });

        $(document).on('click', '.btnEdit', function() {
            let d = $(this).data();
            const form = $('#FormLevel');
            $('#id_Level').val(d.id);
            $('#nama').val(d.nama);
            form.attr('action', `/app/level/${d.id}`);
            form.find('input[name="_method"]').remove();
            form.append('<input type="hidden" name="_method" value="PUT">');
            $('#formTitle').text("Edit Level").css('color', 'goldenrod');
            new bootstrap.Modal(document.getElementById('LevelModal')).show();
        });

        $(document).on('click', '#simpanLevel', function(e) {
            e.preventDefault();
            const form = $('#FormLevel');
            $('small').empty();
            $('.is-invalid').removeClass('is-invalid');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: r => {
                    if (r.success) {
                        Swal.fire({
                            icon: 'success',
                            title: r.msg,
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        bootstrap.Modal.getInstance(document.getElementById('LevelModal')).hide();
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: r.msg || 'Terjadi kesalahan'
                        });
                    }
                },
                error: xhr => {
                    const res = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: res?.msg || 'Cek kembali input yang anda masukkan'
                    });
                    if (res && res.errors) {
                        $.each(res.errors, (field, messages) => {
                            $('#' + field).addClass('is-invalid');
                            $('#msg_' + field).html(messages[0]);
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusLevel');
                    form.attr('action', `/app/level/${id}`);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: r => {
                            Swal.fire("Berhasil!", r.message, "success");
                            table.ajax.reload();
                        },
                        error: xhr => {
                            Swal.fire("Gagal!", xhr.responseJSON?.message ||
                                "Terjadi kesalahan pada server.", "error");
                        }
                    });
                }
            });
        });
    </script>
@endsection
