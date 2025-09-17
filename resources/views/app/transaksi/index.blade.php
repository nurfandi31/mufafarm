@extends('app.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="/app/transaksi" method="post" id="FormTransaksi">
                        @csrf

                        <input type="hidden" name="transaksi" id="transaksi" value="jurnal_umum">
                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="text" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" />
                            </div>
                            <div class="col-md-6 mb-6">
                                <label for="jenis" class="form-label">Jenis Transaksi</label>
                                <select id="jenis" name="jenis" class="select2 form-select form-select-lg">
                                    <option value="">-- Pilih Jenis Transaksi --</option>
                                    <option value="Pemaksukan">Pemaksukan</option>
                                    <option value="Pengeluaran">Pengeluaran</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <label for="sumber_dana" class="form-label">Keterangan</label>
                                <select id="sumber_dana" name="sumber_dana" class="select2 form-select form-select-lg">
                                    <option value="">Select Value</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-6">
                                <label for="disimpan_ke" class="form-label">Disimpan Ke</label>
                                <select id="disimpan_ke" name="disimpan_ke" class="select2 form-select form-select-lg">
                                    <option value="">Select Value</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#tanggal').flatpickr();

        $(document).on('change', '#tanggal', function() {
            var tanggal = $(this).val();

            ambilDaftarInventaris(tanggal);
        });
    </script>
@endsection
