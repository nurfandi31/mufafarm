@extends('app.layouts.app')

@section('content')
    <form action="/app/pembelian/{{ $pembelian->id }}" method="post" id="FormPembelianEdit">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Edit Data Pembelian</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control dob-picker"
                                placeholder="YYYY-MM-DD" value="{{ $pembelian->tanggal }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="jenis" class="form-label">Jenis Barang</label>
                            <select id="jenis" name="jenis" class="form-control select2">
                                <option value="">-- Pilih Jenis Barang --</option>
                                <option value="bibit" {{ $pembelian->jenis == 'bibit' ? 'selected' : '' }}>Bibit</option>
                                <option value="perlengkapan" {{ $pembelian->jenis == 'perlengkapan' ? 'selected' : '' }}>
                                    Perlengkapan</option>
                                <option value="pakan" {{ $pembelian->jenis == 'pakan' ? 'selected' : '' }}>Pakan</option>
                                <option value="babon" {{ $pembelian->jenis == 'babon' ? 'selected' : '' }}>Babon</option>
                                <option value="lain-lain" {{ $pembelian->jenis == 'lain-lain' ? 'selected' : '' }}>Lain-lain
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control"
                                placeholder="Masukkan nama barang" value="{{ $pembelian->nama_barang }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="jumlah" class="form-label">Jumlah satuan</label>
                            <input type="number" step="0.01" id="jumlah" name="jumlah" class="form-control"
                                placeholder="Masukkan jumlah" value="{{ $pembelian->jumlah }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <input type="text" id="harga_satuan" name="harga_satuan" class="form-control"
                                placeholder="Masukkan harga satuan"
                                value="{{ number_format($pembelian->harga_satuan, 0, ',', '.') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" id="total" name="total" class="form-control"
                                placeholder="Total harga" readonly
                                value="{{ number_format($pembelian->total, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" id="supplier" name="supplier" class="form-control"
                        placeholder="Masukkan nama supplier" value="{{ $pembelian->supplier }}">
                </div>
                <div class="mb-0 d-flex justify-content-between">
                    <a href="/app/pembelian" class="btn btn-outline-secondary">Kembali</a>
                    <button type="button" id="updatePembelian" class="btn btn-primary">Update</button>
                </div>
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

        $(document).on('click', '#updatePembelian', function(e) {
            e.preventDefault();
            let f = $('#FormPembelianEdit')[0],
                fd = new FormData(f),
                url = f.action;
            $.ajax({
                type: 'POST', // tetap POST, method override PUT
                url: url,
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        // Menggunakan Toast
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

                        Toast.fire({
                            icon: 'success',
                            title: r.msg
                        }).then(() => {
                            window.location.href = '/app/pembelian';
                        });
                    }
                },
                error: function(r) {
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                }
            });
        });
    </script>
@endsection
