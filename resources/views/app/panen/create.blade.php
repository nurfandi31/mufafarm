@extends('app.layouts.app')

@section('content')
    <form action="/app/panen" method="post" id="FormPanen">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Tambah Data Panen</p>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <!-- Select Bibit & Kolam -->
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="bibit_id" class="form-label">Bibit & Kolam</label>
                            <select id="bibit_id" name="bibit_id" class="select2 form-select">
                                <option value="">-- Pilih Bibit --</option>
                                @foreach ($bibit as $p)
                                    <option value="{{ $p->id }}" data-kapasitas="{{ $p->kolam->kapasitas_bibit }}">
                                        {{ $p->nama }} - ({{ $p->kolam->nama }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Panen -->
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="tanggal_panen" class="form-label">Tanggal Panen</label>
                            <input type="text" id="tanggal_panen" name="tanggal_panen" class="form-control dob-picker"
                                placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="jumlah" class="form-label">Jumlah Ekor Panen</label>
                            <input type="number" step="0.01" id="jumlah" name="jumlah" class="form-control"
                                placeholder="Masukkan berat total panen">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label class="form-label" for="berat_total"> Berat Total (kg)</label>
                            <div class="input-group">
                                <input type="number" id="berat_total" name="berat_total" class="form-control"
                                    placeholder="berat_total bibit" aria-label="berat_total bibit" />
                                <button class="btn btn-outline-secondary" type="button" id="kapasitas_bibit" disabled>
                                    0
                                </button>
                            </div>
                            <small id="msg_berat_total" class="text-muted">Kapasitas kolam</small>
                        </div>
                    </div>
                </div>

                <div class="mb-0 d-flex justify-content-between">
                    <a href="/app/panen" class="btn btn-outline-secondary">Kembali</a>
                    <button type="button" id="simpanPanen" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        // Flatpickr
        $(".dob-picker").flatpickr({
            monthSelectorType: "static"
        });

        // Saat pilih bibit, tampilkan kapasitas kolam
        $('#bibit_id').on('change', function() {
            let kapasitas = $(this).find(':selected').data('kapasitas') || 0;
            $('#kapasitas_bibit').text(kapasitas);
        });

        // AJAX Simpan Panen
        $(document).on('click', '#simpanPanen', function(e) {
            e.preventDefault();
            let f = $('#FormPanen')[0],
                fd = new FormData(f),
                url = $('#FormPanen').attr('action');

            $.ajax({
                type: 'POST',
                url: url,
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Sukses', r.msg, 'success').then(() => {
                            window.location.href = '/app/panen';
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
