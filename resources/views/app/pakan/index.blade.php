@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <button id="btnTambahPakan" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Tambah Pakan
                </button>
            </div>
        </div>
        <div class="card-datatable">
            <table id="Pakan" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Pakan</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusPakan" method="post">
        @method('DELETE')
        @csrf
    </form>

    @include('app.pakan.modal')
@endsection

@section('script')
    <script>
        const table = setDataTable(document.querySelector("#Pakan"), {
            processing: true,
            serverSide: true,
            ajax: "/app/pakan",
            columns: [{
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'stok',
                    name: 'stok'
                },
                {
                    data: 'satuan',
                    name: 'satuan'
                },
                {
                    data: 'harga',
                    name: 'harga',
                    render: d => new Intl.NumberFormat('id-ID').format(d)
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: d => `
            <div class="d-inline-flex gap-1">
                <button class="btn btn-sm btn-primary btnEditPakan"
                    data-id="${d.id}" data-nama="${d.nama}" data-stok="${d.stok}"
                    data-satuan="${d.satuan}" data-harga="${d.harga}">Edit</button>
                <button class="btn btn-sm btn-danger btn-delete-pakan" data-id="${d.id}">Hapus</button>
            </div>`
                }
            ]
        });

        $("#harga").on("input", function() {
            $(this).val(new Intl.NumberFormat("id-ID").format($(this).val().replace(/\D/g, "")));
        });

        $('#btnTambahPakan').click(() => {
            const form = $('#FormPakan').trigger('reset').find('input[name="_method"]').remove().end();
            $('#id').val('');
            form.attr('action', `/app/pakan`);
            $('#formTitle').text("Tambah Pakan").css('color', 'green');
            new bootstrap.Modal(document.getElementById('ModalPakan')).show();
        });

        $(document).on('click', '.btnEditPakan', function() {
            const d = $(this).data();
            const form = $('#FormPakan');

            form.find('input[name="_method"]').remove();

            $('#id').val(d.id);
            $('#nama').val(d.nama);
            $('#stok').val(d.stok);
            $('#satuan').val(d.satuan);
            $('#harga').val(d.harga);

            form.attr('action', `/app/pakan/${d.id}`)
                .append('<input type="hidden" name="_method" value="PUT">');

            $('#formTitle').text("Edit Pakan").css('color', 'goldenrod');
            new bootstrap.Modal(document.getElementById('ModalPakan')).show();
        });


        $(document).on('click', '#SimpanPakan', function(e) {
            e.preventDefault();
            const form = $('#FormPakan');
            $('small').empty();
            $('.is-invalid').removeClass('is-invalid');
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            $.post(form.attr('action'), form.serialize())
                .done(res => {
                    Toast.fire({
                        icon: 'success',
                        title: res.msg
                    });
                    bootstrap.Modal.getInstance(document.getElementById('ModalPakan')).hide();
                    table.ajax.reload(null, false);
                })
                .fail(xhr => {
                    const r = xhr.responseJSON;
                    Toast.fire({
                        icon: 'error',
                        title: r?.msg || 'Cek kembali input yang anda masukkan'
                    });
                    if (r?.errors) $.each(r.errors, (f, m) => {
                        $('#' + f).addClass('is-invalid');
                        $('#msg_' + f).html(m[0]);
                    });
                });
        });

        $(document).on('click', '.btn-delete-pakan', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Pakan akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal"
            }).then(res => {
                if (res.isConfirmed) {
                    $.post(`/app/pakan/${id}`, $('#FormHapusPakan').serialize())
                        .done(r => Swal.fire("Berhasil!", r.message, "success").then(() => table.ajax
                            .reload()))
                        .fail(xhr => Swal.fire("Gagal!", xhr.responseJSON?.message ||
                            "Terjadi kesalahan pada server.", "error"));
                }
            });
        });
    </script>
@endsection
