@extends('app.layouts.app')

@section('content')
    <form action="/app/kuliner/{{ $kuliner->id }}" method="post" id="FormKuliner">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Data Penjualan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="panen_id" class="form-label">Pilih Panen</label>
                            <select id="panen_id" name="panen_id" class="form-select select2">
                                <option value="">-- Pilih Panen --</option>
                                @foreach ($panen as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $p->id == $kuliner->panen_id ? 'selected' : '' }}>
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

                    <div class="col-md-3 col-12">
                        <div class="mb-3">
                            <label for="tanggal_produksi" class="form-label">Tanggal Produksi</label>
                            <input type="text" id="tanggal_produksi" name="tanggal_produksi"
                                class="form-control dob-picker" value="{{ $kuliner->tanggal_produksi }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="mb-3">
                            <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluwarsa</label>
                            <input type="text" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa"
                                class="form-control dob-picker" value="{{ $kuliner->tanggal_kadaluarsa }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Produksi</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                placeholder="Masukkan nama" value="{{ $kuliner->nama }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Packing & Jenis Kemasan</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="number" id="packing" name="packing" class="form-control" min="1"
                                        value="{{ json_decode($kuliner->packing, true)[0]['satuan'] ?? 1 }}">
                                </div>
                                <div class="col-8">
                                    <select id="jenis" name="jenis" class="form-select select2">
                                        @php
                                            $packingJenis = json_decode($kuliner->packing, true)[0]['jenis'] ?? '';
                                        @endphp
                                        <option value="">-- Pilih Jenis Kemasan --</option>
                                        <option value="plastik" {{ $packingJenis == 'plastik' ? 'selected' : '' }}>Plastik
                                        </option>
                                        <option value="standing_pouch"
                                            {{ $packingJenis == 'standing_pouch' ? 'selected' : '' }}>Standing Pouch
                                        </option>
                                        <option value="botol" {{ $packingJenis == 'botol' ? 'selected' : '' }}>Botol
                                        </option>
                                        <option value="toples" {{ $packingJenis == 'toples' ? 'selected' : '' }}>Toples
                                        </option>
                                        <option value="cup" {{ $packingJenis == 'cup' ? 'selected' : '' }}>Cup / Gelas
                                        </option>
                                        <option value="jar" {{ $packingJenis == 'jar' ? 'selected' : '' }}>Jar</option>
                                        <option value="sachet" {{ $packingJenis == 'sachet' ? 'selected' : '' }}>Sachet
                                        </option>
                                        <option value="dus" {{ $packingJenis == 'dus' ? 'selected' : '' }}>Dus / Karton
                                        </option>
                                        <option value="tray" {{ $packingJenis == 'tray' ? 'selected' : '' }}>Tray
                                        </option>
                                        <option value="kaleng" {{ $packingJenis == 'kaleng' ? 'selected' : '' }}>Kaleng
                                        </option>
                                        <option value="vakum_pack" {{ $packingJenis == 'vakum_pack' ? 'selected' : '' }}>
                                            Vakum
                                            Pack</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Satuan</label>
                            <div class="input-group">
                                <input type="text" id="jumlah" name="jumlah" class="form-control"
                                    placeholder="Jumlah" value="{{ $kuliner->jumlah }}">
                                <span class="input-group-text" id="satuan"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="biaya_produksi" class="form-label">Total Biaya Produksi</label>
                            <input type="text" id="biaya_produksi" name="biaya_produksi" class="form-control"
                                placeholder="Masukkan biaya"
                                value="{{ number_format($kuliner->biaya_produksi, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-12">
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="3"
                                placeholder="Masukkan keterangan produksi">{{ $kuliner->keterangan }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/app/kuliner" class="btn btn-outline-secondary">Kembali</a>
                    <button type="button" id="simpanKuliner" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(function() {
            $(".dob-picker").flatpickr({
                dateFormat: "Y-m-d"
            });
            if ($.fn.select2) $('.select2').select2({
                width: '100%'
            });

            function formatNumber(v) {
                v = String(v || '').replace(/[^\d]/g, '');
                if (v === '') return '';
                return new Intl.NumberFormat('id-ID').format(parseInt(v, 10));
            }

            $('#biaya_produksi').on('input', function() {
                $(this).val(formatNumber($(this).val()));
            });

            var panenCache = {};
            var currentPanenData = null;

            function fetchPanen(id, cb) {
                if (!id) return cb(null);
                if (panenCache[id]) return cb(panenCache[id]);
                $.get('/app/kuliner/list/' + id)
                    .done(function(r) {
                        if (r && r.success) {
                            panenCache[id] = r.data;
                            cb(r.data);
                        } else cb(null);
                    })
                    .fail(function() {
                        cb(null);
                    });
            }

            function extractCapacity(data) {
                if (!data) return null;
                if (data.bibit && data.bibit.kolam && data.bibit.kolam.kapasitas_bibit !== undefined) return data
                    .bibit.kolam.kapasitas_bibit;
                if (data.kolam && data.kolam.kapasitas_bibit !== undefined) return data.kolam.kapasitas_bibit;
                if (data.bibit && data.bibit.kapasitas_bibit !== undefined) return data.bibit.kapasitas_bibit;
                if (data.kapasitas_bibit !== undefined) return data.kapasitas_bibit;
                return null;
            }

            function setSatuanFromPanen(id) {
                if (!id) {
                    $('#satuan').text('');
                    currentPanenData = null;
                    return;
                }
                fetchPanen(id, function(data) {
                    currentPanenData = data;
                    var cap = extractCapacity(data);
                    $('#satuan').text(cap !== null ? cap : '');
                });
            }

            $('#panen_id').on('change select2:select', function() {
                var id = $(this).val();
                setSatuanFromPanen(id);
            });

            setSatuanFromPanen($('#panen_id').val());

            $('#jumlah').on('input', function() {
                var val = parseFloat($(this).val() || 0);
                if (currentPanenData && currentPanenData.berat_total !== undefined) {
                    var max = parseFloat(currentPanenData.berat_total);
                    if (val > max) {
                        Swal.fire('Peringatan', 'Jumlah melebihi total panen (' + max + ')', 'warning');
                        $(this).val(max);
                    }
                }
            });

            $('#simpanKuliner').on('click', function(e) {
                e.preventDefault();
                var f = $('#FormKuliner')[0];
                var fd = new FormData(f);
                $.ajax({
                    type: 'POST',
                    url: f.action,
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(r) {
                        if (r && r.success) {
                            Swal.fire('Sukses', r.msg, 'success').then(function() {
                                window.location.href = "/app/kuliner";
                            });
                        } else {
                            Swal.fire('Error', (r && r.msg) || 'Terjadi kesalahan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                    }
                });
            });
        });
    </script>
@endsection
