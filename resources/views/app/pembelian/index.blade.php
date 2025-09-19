@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <a href="/app/pembelian/create" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Pembelian Baru
                </a>
            </div>
        </div>
        <div class="card-datatable">
            <table id="pembelian" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Supplier</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusPembelian" method="post">
        @method('DELETE')
        @csrf
    </form>
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#pembelian");
        let cl;

        if (tb) {
            cl = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/pembelian",
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
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
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="d-inline-flex gap-1">
                                <a href="/app/pembelian/${data.id}/edit" class="btn btn-sm btn-primary" title="Edit">
                                    Edit
                                </a>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}" title="Hapus">
                                    Hapus
                                </button>
                            </div>`;
                        }
                    }
                ]
            });
        }

        // delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Pembelian akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusPembelian');
                    form.attr('action', `/app/pembelian/${id}`);
                    form.off('submit').on('submit', function(e) {
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
