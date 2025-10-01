@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <button id="btnTambah" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Tambah Pemberian Pakan
                </button>
            </div>
        </div>
        <div class="card-datatable">
            <table id="Pb-Pakan" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Bibit</th>
                        <th>Pakan</th>
                        <th>Tanggal Pemberian</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusPemberianPakan" method="post">
        @method('DELETE')
        @csrf
    </form>

    @include('app.pemberian-pakan.modal')
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#Pb-Pakan");
        let table;

        if (tb) {
            table = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/pemberian-pakan",
                },
                columns: [{
                        data: 'bibit.nama',
                        name: 'bibit.nama'
                    },
                    {
                        data: 'pakan.nama',
                        name: 'pakan.nama'
                    },
                    {
                        data: 'tanggal_pemberian',
                        name: 'tanggal_pemberian'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        render: function(data, type, row) {
                            const satuan = row.pakan ? row.pakan.satuan : '';
                            return data + (satuan ? ' ' + satuan : '');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                        <div class="d-inline-flex gap-1">
                            <button class="btn btn-sm btn-primary btnEdit"
                                data-id="${data.id}"
                                data-bibit="${data.bibit ? data.bibit.id : ''}"
                                data-pakan="${data.pakan ? data.pakan.id : ''}"
                                data-tanggal_pemberian="${data.tanggal_pemberian}"
                                data-jumlah="${data.jumlah}">
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

        $('#PP-Modal').on('shown.bs.modal', function() {
            $("#tanggal_pemberian").flatpickr({
                dateFormat: "Y-m-d",
                appendTo: document.body,
                onOpen: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.style.zIndex = 2000;
                }
            });
        });

        $(document).ready(function() {
            const pakanSelect = $('#pakan_id');
            const satuanButton = $('#satuanPakan');
            const jumlahInput = $('#jumlah');
            let stokPakan = 0;

            $.getJSON('/app/pp-pakan/list', function(data) {
                pakanSelect.empty();
                pakanSelect.append('<option value="">Pilih Pakan</option>');
                data.forEach(item => {
                    pakanSelect.append(`
                <option value="${item.id}" 
                        data-satuan="${item.satuan}" 
                        data-stok="${item.stok}">
                    ${item.nama}
                </option>
            `);
                });
            });

            pakanSelect.on('change', function() {
                const selected = $(this).find('option:selected');
                const satuan = selected.data('satuan');
                stokPakan = selected.data('stok') || 0;

                satuanButton.text(satuan || '');
                jumlahInput.val('');
            });

            jumlahInput.on('input', function() {
                let val = parseFloat($(this).val());

                if (isNaN(val) || val <= 0) return;

                if (val > stokPakan) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stok Tidak Cukup',
                        text: `Stok tersedia hanya ${stokPakan}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $(this).val(stokPakan);
                }
            });
            const bibitSelect = $('#bibit_id');
            $.getJSON('/app/pp-bibit/list', function(data) {
                bibitSelect.empty();
                bibitSelect.append('<option value="">Pilih Bibit</option>');
                data.forEach(item => {
                    bibitSelect.append(`
                <option value="${item.id}">${item.text}</option>
            `);
                });
            });
        });


        $('#btnTambah').click(() => {
            const form = $('#FormPemberianPakan');
            form.trigger('reset');
            form.find('input[name="id_PP"]').val('');
            form.attr('action', `/app/pemberian-pakan`);
            form.find('input[name="_method"]').remove();
            $('#formTitle').text("Tambah Pemberian Pakan").css('color', 'green');
            const modal = new bootstrap.Modal(document.getElementById('PP-Modal'));
            modal.show();
        });

        $(document).on('click', '.btnEdit', function() {
            let d = $(this).data();
            const form = $('#FormPemberianPakan');
            $('#id_PP').val(d.id);
            $('#bibit_id').val(d.bibit).trigger('change');
            $('#pakan_id').val(d.pakan).trigger('change');
            $('#tanggal_pemberian').val(d.tanggal_pemberian).trigger('change');
            $('#jumlah').val(d.jumlah);
            form.attr('action', `/app/pemberian-pakan/${d.id}`);
            form.find('input[name="_method"]').remove();
            form.append('<input type="hidden" name="_method" value="PUT">');
            $('#formTitle').text("Edit Pemberian Pakan").css('color', 'goldenrod');
            const modal = new bootstrap.Modal(document.getElementById('PP-Modal'));
            modal.show();
        });

        $(document).on('click', '#SimpanPemberianPakan', function(e) {
            e.preventDefault();
            const form = $('#FormPemberianPakan');
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
                        const modalEl = document.getElementById('PP-Modal');
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
                text: "Data pemberian pakan akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusPemberianPakan');
                    form.attr('action', `/app/pemberian-pakan/${id}`);
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
