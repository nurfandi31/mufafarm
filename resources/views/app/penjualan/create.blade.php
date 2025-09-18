@extends('app.layouts.app')

@section('content')
    <form action="/app/penjualan" method="post" id="FormPenjualan">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Tambah Data Penjualan</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control dob-picker"
                                placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="panen_id" class="form-label">Pilih Panen</label>
                            <select id="panen_id" name="panen_id" class="form-control select2">
                                <option value="">-- Pilih Panen --</option>
                                @foreach ($panen as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->bibit->nama }} - {{ $p->tanggal_panen }}
                                        @if ($p->status === 'ready')
                                            (🟢 Ready)
                                        @elseif ($p->status === 'habis')
                                            (🔴 Habis)
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="pembeli" class="form-label">Nama Pembeli</label>
                            <input type="text" id="pembeli" name="pembeli" class="form-control"
                                placeholder="Masukkan nama pembeli">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (kg)</label>
                            <input type="number" step="0.01" id="jumlah" name="jumlah" class="form-control"
                                placeholder="Masukkan jumlah" value="1">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-12">
                        <div class="mb-3">
                            <label for="jumlah_ekor" class="form-label">Jumlah Ekor</label>
                            <input type="text" id="jumlah_ekor" name="jumlah_ekor" class="form-control"
                                placeholder="Jumlah Ekor" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="mb-3">
                            <label for="harga_satuan" class="form-label">Harga Satuan</label>
                            <input type="text" id="harga_satuan" name="harga_satuan" class="form-control"
                                value="{{ number_format($settings->harga_jual, 0, ',', '.') }}"
                                placeholder="Masukkan harga satuan">
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" id="total" name="total" class="form-control"
                                value="{{ number_format($settings->harga_jual, 0, ',', '.') }}" placeholder="Total harga"
                                readonly>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/app/penjualan" class="btn btn-outline-secondary">Kembali</a>
                    <button type="button" id="simpanPenjualan" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        // === Datepicker ===
        $(".dob-picker").flatpickr({
            monthSelectorType: "static",
            dateFormat: "Y-m-d"
        });

        // === Helper format ribuan ===
        function formatNumber(value) {
            value = value.toString().replace(/\D/g, "");
            return new Intl.NumberFormat("id-ID").format(value);
        }

        // === Hitung total ===
        function hitungTotal() {
            let jumlah = parseFloat($('#jumlah').val()) || 0;
            let harga = parseFloat($('#harga_satuan').val().replace(/\D/g, "")) || 0;
            let total = jumlah * harga;

            $('#total').val(total.toLocaleString('id-ID'));
        }

        // === Hitung jumlah ekor ===
        function hitungJumlahEkor() {
            let panenId = $('#panen_id').val();
            let jumlahKg = parseFloat($('#jumlah').val()) || 0;

            if (panenId && jumlahKg > 0) {
                $.ajax({
                    url: '/app/panen/detail/' + panenId,
                    type: 'GET',
                    success: function(r) {
                        if (r.success) {
                            let jumlah = Number(r.data.jumlah) || 0; // jumlah ekor total
                            let berat = Number(r.data.berat_total) || 0; // berat total kg

                            if (jumlahKg > berat) {
                                // tampilkan notifikasi jika jumlah melebihi berat_total
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Jumlah Melebihi Stok!',
                                    text: `Jumlah yang dimasukkan (${jumlahKg} kg) melebihi berat total panen (${berat} kg).`,
                                });
                                $('#jumlah_ekor').val('');
                                return;
                            }

                            if (jumlah > 0 && berat > 0) {
                                let ekorPerKg = jumlah / berat;
                                let jumlahEkor = Math.ceil(jumlahKg * ekorPerKg);
                                $('#jumlah_ekor').val(jumlahEkor.toLocaleString('id-ID'));
                            } else {
                                $('#jumlah_ekor').val('');
                            }
                        }
                    }
                });
            } else {
                $('#jumlah_ekor').val('');
            }
        }


        // === Event binding ===
        $('#harga_satuan').on('input', function() {
            $(this).val(formatNumber($(this).val()));
            hitungTotal();
        });

        $('#jumlah').on('input', function() {
            hitungTotal();
            hitungJumlahEkor();
        });

        $('#panen_id').on('change', function() {
            hitungJumlahEkor();
        });

        // === Simpan via Ajax ===
        $(document).on('click', '#simpanPenjualan', function(e) {
            e.preventDefault();
            let f = $('#FormPenjualan')[0];
            let fd = new FormData(f);
            let url = f.action;

            $.ajax({
                type: 'POST',
                url: url,
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Sukses', r.msg, 'success')
                            .then(() => window.location.href = '/app/penjualan');
                    } else {
                        Swal.fire('Error', r.msg || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                }
            });
        });
    </script>
@endsection
