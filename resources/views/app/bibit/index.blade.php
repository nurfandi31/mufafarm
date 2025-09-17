@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <button id="btnTambah" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Tambah Bibit
                </button>
            </div>
        </div>
        <div class="card-datatable">
            <table id="Bibit" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Kolam</th>
                        <th>Nama Bibit</th>
                        <th>Jenis Bibit</th>
                        <th>Tanggal Datang</th>
                        <th>Jumlah Bibit</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusBibit" method="post">
        @method('DELETE')
        @csrf
    </form>

    @include('app.bibit.modal')
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#Bibit");
        let table;

        if (tb) {
            table = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/bibit",
                },
                columns: [{
                        data: 'kolam.nama',
                        name: 'kolam.nama'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'tanggal_datang',
                        name: 'tanggal_datang'
                    }, {
                        data: 'jumlah',
                        name: 'jumlah',
                        render: function(data, type, row) {
                            const satuan = row.kolam ? row.kolam.kapasitas_bibit : '';
                            return data + (satuan ? ' ' + satuan : '');
                        }
                    },
                    {
                        data: 'sumber',
                        name: 'sumber'
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
                                        data-kolam_id="${data.kolam_id}"
                                        data-nama="${data.nama}"
                                        data-jenis="${data.jenis}"
                                        data-tanggal_datang="${data.tanggal_datang}"
                                        data-jumlah="${data.jumlah}"
                                        data-sumber="${data.sumber}">
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

        $(".dob-picker").not("#tanggal_awal").flatpickr({
            monthSelectorType: "static",
            appendTo: document.body,
            onOpen: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.style.zIndex = 2000;
            }
        });

        $(document).ready(function() {
            const kolamSelect = $('#kolam_id'); // dropdown kolam
            const kapasitasButton = $('#kapasitas_bibit'); // tombol kapasitas

            $.getJSON('/app/bibit/list', function(data) {
                kolamSelect.empty();
                kolamSelect.append('<option value="">Pilih Kolam</option>');

                data.forEach(item => {
                    kolamSelect.append(`
                <option value="${item.id}" data-kapasitas="${item.kapasitas_bibit}">
                    ${item.nama}
                </option>
            `);
                });
            });

            kolamSelect.on('change', function() {
                const satuan = $(this).find('option:selected').data('kapasitas');
                kapasitasButton.text(satuan || '');
            });
        });

        $('#btnTambah').click(() => {
            const form = $('#FormBibit');
            form.trigger('reset');
            form.find('input[name="id_bibit"]').val('');
            form.attr('action', `/app/bibit`);
            form.find('input[name="_method"]').remove();
            $('#formTitle').text("Tambah Bibit Baru").css('color', 'green');
            const modal = new bootstrap.Modal(document.getElementById('BibitModal'));
            modal.show();
        });

        $(document).on('click', '.btnEdit', function() {
            let d = $(this).data();
            const form = $('#FormBibit');
            $('#id_bibit').val(d.id);
            $('#nama').val(d.nama);
            $('#jenis').val(d.jenis).trigger('change');
            $('#tanggal_datang').val(d.tanggal_datang).trigger('change');
            $('#jumlah').val(d.jumlah);
            $('#sumber').val(d.sumber);
            $('#kolam_id').val(d.kolam_id).trigger('change');
            form.attr('action', `/app/bibit/${d.id}`);
            form.find('input[name="_method"]').remove();
            form.append('<input type="hidden" name="_method" value="PUT">');
            $('#formTitle').text("Edit Bibit").css('color', 'goldenrod');
            const modal = new bootstrap.Modal(document.getElementById('BibitModal'));
            modal.show();
        });

        $(document).on('click', '#SimpanBibit', function(e) {
            e.preventDefault();
            const form = $('#FormBibit');
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
                        const modalEl = document.getElementById('BibitModal');
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

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Bibit akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusBibit');
                    form.attr('action', `/app/bibit/${id}`);
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
