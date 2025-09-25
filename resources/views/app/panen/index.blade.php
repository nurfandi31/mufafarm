@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <a href="/app/panen/create" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Panen Baru
                </a>
            </div>
        </div>
        <div class="card-datatable">
            <table id="panen" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th>Bibit</th>
                        <th>Tanggal Panen</th>
                        <th>Jumlah</th>
                        <th>Berat Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapuspanen" method="post">
        @method('DELETE')
        @csrf

    </form>
@endsection

@section('script')
    <script>
        const tb = document.querySelector("#panen");
        let cl;

        if (tb) {
            cl = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/panen",
                },
                columns: [{
                        data: 'bibit.nama',
                        name: 'bibit.nama',
                    },
                    {
                        data: 'tanggal_panen',
                        name: 'tanggal_panen'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'berat_total',
                        name: 'berat_total'
                    }, {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 'ready') {
                                return '<span class="badge bg-success">Ready</span>';
                            } else if (data === 'habis') {
                                return '<span class="badge bg-warning">Habis</span>';
                            } else {
                                return '<span class="badge bg-secondary">Belum Diketahui</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.jumlah_raw == 0 || row.berat_raw == 0) {
                                return `<span class="badge bg-secondary">Sold Out</span>`;
                            } else {
                                return `<div class="d-inline-flex gap-1">
                            <a href="/app/panen/${data.id}/edit" class="btn btn-sm btn-primary" title="Edit">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}" title="Hapus">Hapus</button>
                        </div>`;
                            }
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
                text: "Data Panen akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapuspanen');
                    form.attr('action', `/app/panen/${id}`);
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
