@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <button id="btnTambah" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Tambah Kolam
                </button>
            </div>
        </div>
        <div class="card-datatable">
            <table id="Kolam" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Kolam</th>
                        <th>Type</th>
                        <th>Kapasitas </th>
                        <th>Lokasi Kolam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusKolam" method="post">
        @method('DELETE')
        @csrf
    </form>

    @include('app.Kolam.modal')
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#Kolam");
        let table;

        if (tb) {
            table = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/kolam",
                },
                columns: [{
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'kapasitas_bibit',
                        name: 'kapasitas_bibit'
                    },
                    {
                        data: 'lokasi_kolam',
                        name: 'lokasi_kolam'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <div class="d-inline-flex gap-1">
                                    <button 
                                        class="btn btn-sm btn-primary btnEdit"
                                        data-id="${data.id}"
                                        data-nama="${data.nama}"
                                        data-type="${data.type}"
                                        data-kapasitas_bibit="${data.kapasitas_bibit}"
                                        data-lokasi_kolam="${data.lokasi_kolam}">
                                        Edit
                                    </button>

                                    <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                        Hapus
                                    </button>
                                </div>`;
                        }
                    }
                ],
            });
        }

        // tombol tambah
        $('#btnTambah').click(() => {
            const form = $('#FormKolam');
            form.trigger('reset');
            form.find('input[name="id_kolam"]').val('');

            form.attr('action', `/app/kolam`);
            form.find('input[name="_method"]').remove();

            $('#formTitle').text("Tambah Kolam").css('color', 'green');

            const modal = new bootstrap.Modal(document.getElementById('KolamModal'));
            modal.show();
        });

        // tombol edit
        $(document).on('click', '.btnEdit', function() {
            let d = $(this).data();
            const form = $('#FormKolam');

            $('#id_kolam').val(d.id);
            $('#nama').val(d.nama);
            $('#type').val(d.type).trigger('change');
            $('#kapasitas_bibit').val(d.kapasitas_bibit).trigger('change');
            $('#lokasi_kolam').val(d.lokasi_kolam);

            form.attr('action', `/app/kolam/${d.id}`);
            form.find('input[name="_method"]').remove();
            form.append('<input type="hidden" name="_method" value="PUT">');

            $('#formTitle').text("Edit Kolam").css('color', 'goldenrod');
            const modal = new bootstrap.Modal(document.getElementById('KolamModal'));
            modal.show();
        });


        // simpan (tambah / edit)
        $(document).on('click', '#SimpanKolam', function(e) {
            e.preventDefault();
            const form = $('#FormKolam');
            $('small').empty();
            $('.is-invalid').removeClass('is-invalid');
            const actionUrl = form.attr('action');
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Toast.fire({
                            icon: 'success',
                            title: result.msg
                        });
                        const modalEl = document.getElementById('KolamModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        modalInstance.hide();
                        if (table) table.ajax.reload(null, false);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: result.msg || 'Terjadi kesalahan'
                        });
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Toast.fire({
                        icon: 'error',
                        title: response?.msg || 'Cek kembali input yang anda masukkan'
                    });
                    if (response && response.errors) {
                        $.each(response.errors, function(field, messages) {
                            const input = $('#' + field);
                            input.addClass('is-invalid');
                            $('#msg_' + field).html(messages[0]);
                        });
                    }
                }
            });
        });

        // hapus
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Kolam akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusKolam');
                    form.attr('action', `/app/kolam/${id}`);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(r) {
                            Swal.fire("Berhasil!", r.message, "success").then(() => {
                                if (table) table.ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message ||
                                "Terjadi kesalahan pada server.";
                            Swal.fire("Gagal!", msg, "error");
                        }
                    });
                }
            });
        });
    </script>
@endsection
