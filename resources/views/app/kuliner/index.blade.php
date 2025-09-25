@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-end">
                <a href="/app/kuliner/create" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Kuliner Baru
                </a>
            </div>
        </div>
        <div class="card-datatable">
            <table id="kuliner" class="dt-responsive-child table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tanggal TP</th>
                        <th>Tanggal EXP</th>
                        <th>Nama</th>
                        <th>Packing</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <form id="FormHapusKuliner" method="post">
        @method('DELETE')
        @csrf

    </form>
@endsection

@section('script')
    <script>
        const formatTanggal = (tgl) => {
            if (!tgl) return '';
            const date = new Date(tgl);
            if (isNaN(date)) return tgl;
            const d = String(date.getDate()).padStart(2, '0');
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const y = date.getFullYear();
            return `${d}-${m}-${y}`;
        };

        const tb = document.querySelector("#kuliner");
        let cl;
        if (tb) {
            cl = setDataTable(tb, {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/app/kuliner",
                },
                columns: [{
                        data: null
                    },
                    {
                        data: 'tanggal_produksi',
                        name: 'tanggal_produksi'
                    },
                    {
                        data: 'tanggal_kadaluarsa',
                        name: 'tanggal_kadaluarsa'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'packing',
                        name: 'packing'
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah',
                        render: function(data, type, row) {
                            const kapasitas_bibit = row.panen?.bibit?.kolam?.kapasitas_bibit ?? '';
                            return data + (kapasitas_bibit ? ' ' + kapasitas_bibit : '');
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            if (data === 'ready') {
                                return '<span class="badge bg-success">Ready</span>';
                            } else if (data === 'habis') {
                                return '<span class="badge bg-warning">Habis</span>';
                            } else if (data === 'tidak_layak') {
                                return '<span class="badge bg-danger">Tidak Layak</span>';
                            } else {
                                return '<span class="badge bg-secondary">Belum Diketahui</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                        <div class="d-inline-flex gap-1">
                            <a href="/app/kuliner/${data.id}/edit" class="btn btn-sm btn-primary" title="Edit">Edit</a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}" title="Hapus">Hapus</button>
                        </div>
                    `;
                        }
                    }
                ],
                columnDefs: [{
                    className: "dt-control",
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    defaultContent: ""
                }]
            });
            cl.on("click", "td.dt-control", e => {
                const row = cl.row(e.target.closest("tr")),
                    d = row.data();

                if (row.child.isShown()) {
                    return row.child.hide();
                }

                const renderJumlah = (jumlah, row) => {
                    const kapasitas_bibit = row.panen?.bibit?.kolam?.kapasitas_bibit ?? '';
                    return jumlah + (kapasitas_bibit ? ' ' + kapasitas_bibit : '');
                };

                const formatRupiah = (angka) => {
                    if (!angka) return '0';
                    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                };

                let html = `
                    <small class="fw-medium">Detail Kuliner</small>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row overflow-hidden">
                            <div class="col-12">
                                <ul class="timeline timeline-outline mt-3">
                                    <li class="timeline-item timeline-item-transparent border-dashed">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-3">
                                                <h6 class="mb-0">${d.nama ?? ''}</h6>
                                                <small class="text-body-secondary">${d.created_at ?? ''}</small>
                                            </div>
                                            <p class="mb-2">◉ Sumber: ${d.panen?.bibit?.kolam?.nama ?? ''} - ( ${d.panen?.bibit?.jenis ?? ''} )</p>
                                            <p class="mb-2">◉ Tanggal Produksi: ${formatTanggal(d.tanggal_produksi)}</p>
                                            <p class="mb-2">◉ Tanggal Expired: ${formatTanggal(d.tanggal_kadaluarsa)}</p>
                                            <p class="mb-2">◉ Packing: ${d.packing ?? ''}</p>
                                            <p class="mb-2">◉ Jumlah: ${renderJumlah(d.jumlah ?? '', d)}</p>
                                            <p class="mb-2">◉ Biaya Produksi: Rp ${formatRupiah(d.biaya_produksi ?? 0)}</p>
                                            <p class="mb-2">◉ Keterangan: ${d.keterangan ?? ''}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>`;
                row.child(html).show();
            });
        }

        // delete
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data Kuliner akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then(res => {
                if (res.isConfirmed) {
                    let form = $('#FormHapusKuliner');
                    form.attr('action', `/app/kuliner/${id}`);
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
