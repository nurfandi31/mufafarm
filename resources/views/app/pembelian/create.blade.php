@extends('app.layouts.app')

@section('content')
    <form action="/app/pembelian" method="post" id="FormPembelian"> @csrf <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Tambah Data Pembelian</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="tanggal" class="form-label">Tanggal</label> <input type="text"
                                id="tanggal" name="tanggal" class="form-control dob-picker" placeholder="YYYY-MM-DD"
                                value="{{ date('Y-m-d') }}"> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="jenis" class="form-label">Jenis Barang</label> <select
                                id="jenis" name="jenis" class="form-control select2">
                                <option value="">-- Pilih Jenis Barang --</option>
                                <option value="bibit">Bibit</option>
                                <option value="perlengkapan">Perlengkapan</option>
                                <option value="pakan">Pakan</option>
                                <option value="babon">Babon</option>
                                <option value="lain-lain">Lain-lain</option>
                            </select> </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="nama_barang" class="form-label">Nama Barang</label> <input
                                type="text" id="nama_barang" name="nama_barang" class="form-control"
                                placeholder="Masukkan nama barang"> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="jumlah" class="form-label">Jumlah satuan</label> <input
                                type="number" step="0.01" id="jumlah" name="jumlah" class="form-control"
                                placeholder="Masukkan jumlah">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="harga_satuan" class="form-label">Harga Satuan</label> <input
                                type="text" id="harga_satuan" name="harga_satuan" class="form-control"
                                placeholder="Masukkan harga satuan"> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6"> <label for="total" class="form-label">Total</label> <input type="text"
                                id="total" name="total" class="form-control" placeholder="Total harga" readonly>
                        </div>
                    </div>
                </div>
                <div class="mb-6"> <label for="supplier" class="form-label">Supplier</label> <input type="text"
                        id="supplier" name="supplier" class="form-control" placeholder="Masukkan nama supplier"> </div>
                <div class="mb-0 d-flex justify-content-between"> <a href="/app/pembelian"
                        class="btn btn-outline-secondary">Kembali</a> <button type="button" id="simpanPembelian"
                        class="btn btn-primary">Simpan</button> </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(".dob-picker").flatpickr({
            monthSelectorType: "static"
        });
        $("#harga_satuan").on("input", function() {
            let value = $(this).val().replace(/\D/g, "");
            $(this).val(new Intl.NumberFormat("id-ID").format(value));
        });
        $('#jumlah, #harga_satuan').on('input', function() {
            let jumlah = parseFloat($('#jumlah').val().replace(/\D/g, "")) || 0;
            let harga = parseFloat($('#harga_satuan').val().replace(/\D/g, "")) || 0;
            let total = jumlah * harga;
            $('#total').val(total.toLocaleString('id-ID', {
                minimumFractionDigits: 0
            }));
        });
        $(document).on('click', '#simpanPembelian', function(e) {
            e.preventDefault();
            let f = $('#FormPembelian')[0],
                fd = new FormData(f),
                url = f.action;
            $.ajax({
                type: 'POST',
                url: url,
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Sukses', r.msg, 'success').then(() => window.location.href =
                            '/app/pembelian');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                }
            });
        });
    </script>
@endsection
