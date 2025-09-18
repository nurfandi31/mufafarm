@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <a href="/app/penjualan/create" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Penjualan Baru
                </a>
            </div>
        </div>
        <div class="card-datatable">
            <table id="penjualan" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Panen</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Jumlah Ekor</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusPenjualan" method="post">
        @method('DELETE')
        @csrf

    </form>
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#penjualan");
        let cl;

        if (tb) {
            cl = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/penjualan",
                },
                columns: [{
                        data: 'panen.bibit.nama',
                        name: 'panen.bibit.nama',
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        render: function(data, type, row) {
                            const kapasitas_bibit = row.panen.bibit.kolam ? row.panen.bibit.kolam
                                .kapasitas_bibit : '';
                            return data + (kapasitas_bibit ? ' ' + kapasitas_bibit : '');
                        }
                    }, {
                        data: 'jumlah_ekor',
                        name: 'jumlah_ekor',
                    }, {
                        data: 'harga_satuan',
                        name: 'harga_satuan',
                        render: function(data, type, row) {
                            if (data === null) return '';
                            return Number(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data, type, row) {
                            if (data === null) return '';
                            return Number(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="d-inline-flex gap-1">
                                <a href="/app/penjualan/${data.id}/edit" class="btn btn-sm btn-primary" title="Edit">
                                    Edit
                                </a>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}" title="Hapus">
                                    Hapus
                                </button>
                            </div>`;
                        }
                    }
                ]
            })
        }

        // delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Penjualan akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapuspaPenjualan');
                    form.attr('action', `/app/penjualan/${id}`);
                    form.off('submit').on('submit',
                        function(e) {
                            e.preventDefault();
                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: form.serialize(),
                                success: function(r) {
                                    Swal.fire("Berhasil!", r.message, "success").then(
                                        () => {
                                            cl.ajax.reload();
                                        });
                                },
                                error: function(xhr) {
                                    let msg = xhr.responseJSON?.message ||
                                        "Terjadi kesalahan pada server.";
                                    Swal.fire("Gagal!", msg, "error");
                                }
                            });
                        });
                    form.trigger('submit');
                }
            });
        });
    </script>
@endsection
