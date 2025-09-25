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
                        <th></th>
                        <th>Kode Penjualan</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
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
                        data: null
                    },
                    {
                        data: 'kode_penjualan',
                        name: 'kode_penjualan'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'pembeli',
                        name: 'pembeli'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="d-inline-flex gap-1">
                        <a href="/app/penjualan/struk/${data.id}" class="btn btn-sm btn-secondary" target="_blank" title="Cetak Struk">Cetak Struk</a>
                        <a href="/app/penjualan/${data.id}/edit" class="btn btn-sm btn-primary" title="Edit">Edit</a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}" title="Hapus">Hapus</button>
                    </div>`;
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
                const row = cl.row(e.target.closest("tr"));
                const d = row.data();

                if (row.child.isShown()) {
                    return row.child.hide();
                }

                const formatRupiah = (angka) => {
                    if (!angka) return '0';
                    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                };

                let html = `<small class="fw-medium">Detail Item Pembelian</small>
                    <table class="table table-sm table-bordered mt-2">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Jumlah</th>
                            <th>Jumlah Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

                let totalBayar = 0;
                d.items.forEach(item => {
                    let namaItem = '-';
                    let Satuan = '-';
                    let JenisSatuan = '-';

                    if (item.item_type === 'kuliner') {
                        namaItem = item.kuliner?.nama ?? '-';
                    } else if (item.item_type === 'panen') {
                        namaItem = item.panen.bibit?.nama ?? '-';
                    }

                    if (item.item_type === 'kuliner') {
                        let packingStr = item.kuliner?.packing || '[]';
                        packingStr = packingStr.replace(/&quot;/g, '"');

                        try {
                            const packing = JSON.parse(packingStr);
                            if (packing.length > 0) {
                                Satuan = packing[0].jenis ?? '-';
                            }
                        } catch (e) {
                            Satuan = '-';
                        }
                    } else if (item.item_type === 'panen') {
                        Satuan = item.panen.bibit.kolam?.kapasitas_bibit ?? '-';
                    }

                    if (item.item_type === 'kuliner') {
                        let packingStr = item.kuliner?.packing || '[]';
                        packingStr = packingStr.replace(/&quot;/g, '"');

                        try {
                            const packing = JSON.parse(packingStr);
                            if (packing.length > 0) {
                                JenisSatuan = packing[0].jenis ?? '-';
                            }
                        } catch (e) {
                            JenisSatuan = '-';
                        }
                    } else if (item.item_type === 'panen') {
                        JenisSatuan = 'ekor';
                    }

                    html += `<tr>
                        <td>${namaItem}</td>
                        <td>${item.jumlah} ${Satuan}</td>
                        <td>${item.jumlah_satuan} ${JenisSatuan}</td>
                        <td>Rp ${formatRupiah(item.harga_satuan)}</td>
                        <td>Rp ${formatRupiah(item.total)}</td>
                    </tr>`;

                    totalBayar += parseFloat(item.total ?? 0);
                });

                // Baris total bayar
                html += `<tr class="fw-bold">
                <td colspan="4" class="text-end text-success">Total Bayar</td>
                <td class="text-success">Rp ${formatRupiah(totalBayar)}</td>
            </tr>`;

                html += `</tbody></table>`;
                row.child(html).show();
            });
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
                    let form = $('#FormHapusPenjualan');
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
