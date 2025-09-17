@extends('app.layouts.app')

@section('content')
    <form action="/app/panen/{{ $panen->id }}" method="post" id="FormPanenEdit">
        @csrf
        @method('PUT') {{-- Untuk update --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Edit Data Panen</p>
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
                                    <option value="{{ $p->id }}" data-kapasitas="{{ $p->kolam->kapasitas_bibit }}"
                                        {{ $p->id == $panen->bibit_id ? 'selected' : '' }}>
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
                                placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime($panen->tanggal_panen)) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label for="jumlah" class="form-label">Jumlah Ekor Panen</label>
                            <input type="number" step="0.01" id="jumlah" name="jumlah" class="form-control"
                                placeholder="Masukkan jumlah panen" value="{{ $panen->jumlah }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label class="form-label" for="berat_total">Berat Total (kg)</label>
                            <div class="input-group">
                                <input type="number" id="berat_total" name="berat_total" class="form-control"
                                    placeholder="Berat total panen" value="{{ $panen->berat_total }}" />
                                <button class="btn btn-outline-secondary" type="button" id="kapasitas_bibit" disabled>
                                    {{ $panen->bibit->kolam->kapasitas_bibit ?? 0 }}
                                </button>
                            </div>
                            <small id="msg_berat_total" class="text-muted">Kapasitas kolam</small>
                        </div>
                    </div>
                </div>

                <div class="mb-0 d-flex justify-content-between">
                    <a href="/app/panen" class="btn btn-outline-secondary">Kembali</a>
                    <button type="button" id="simpanPanenEdit" class="btn btn-primary">Simpan</button>
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

        // AJAX Update Panen
        $(document).on('click', '#simpanPanenEdit', function(e) {
            e.preventDefault();
            let f = $('#FormPanenEdit')[0],
                fd = new FormData(f),
                url = $('#FormPanenEdit').attr('action');

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
