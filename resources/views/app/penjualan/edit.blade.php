@extends('app.layouts.app')

@section('content')
    <form action="/app/penjualan/{{ $penjualan->id }}" method="post" id="FormPenjualan">
        @csrf
        @method('PUT')
        <input type="hidden" id="jumlah_satuan_val" name="jumlah_satuan_val" value="">

        <div class="card">
            <div class="card-header">
                <div class="card-title mb-0">
                    <p class="card-subtitle">Edit Data Penjualan</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control dob-picker"
                                placeholder="YYYY-MM-DD" value="{{ $penjualan->tanggal }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="mb-3">
                            <label for="pembeli" class="form-label">Nama Pembeli</label>
                            <input type="text" id="pembeli" name="pembeli" class="form-control"
                                placeholder="Masukkan nama pembeli" value="{{ $penjualan->pembeli }}">
                        </div>
                    </div>
                </div>

                <div class="divider">
                    <div class="divider-text">Pilih Jenis Pembelian</div>
                </div>

                <div class="col-12 form-repeater">
                    <div data-repeater-list="pelaksana">
                        @forelse ($penjualan->items as $item)
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="col-lg-10 col-12 mb-6 pelaksana">
                                        <label class="form-label">Nama Produksi</label>
                                        <select name="item_id" class="select2 form-select pilih-item"
                                            data-allow-clear="true">
                                            <option value="">-- Pilih Panen / Kuliner --</option>
                                            @foreach ($panen as $p)
                                                <option value="panen-{{ $p->id }}"
                                                    {{ $item->item_type === 'panen' && $item->item_id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->bibit->nama }} ({{ $p->bibit->jenis }}) - {{ $p->tanggal_panen }}
                                                    @if ($p->status === 'ready')
                                                        (🟢 Ready)
                                                    @elseif ($p->status === 'habis')
                                                        (🔴 Habis)
                                                    @endif
                                                </option>
                                            @endforeach
                                            @foreach ($Kuliner as $k)
                                                <option value="kuliner-{{ $k->id }}"
                                                    {{ $item->item_type === 'kuliner' && $item->item_id == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama }} - {{ $k->tanggal_produksi }}
                                                    @if ($k->status === 'ready')
                                                        (🟢 Ready)
                                                    @elseif ($k->status === 'tidak_layak')
                                                        (🟡 Tidak Layak Jual)
                                                    @elseif ($k->status === 'habis')
                                                        (🔴 Habis)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12 d-flex align-items-end mb-6">
                                        <button type="button" class="btn btn-label-danger w-100" data-repeater-delete>
                                            <i class="icon-base bx bx-x me-1"></i>
                                        </button>
                                    </div>
                                </div>
                                @include('app.penjualan.form_panen', ['itemData' => $item])
                                @include('app.penjualan.form_kuliner', ['itemData' => $item])
                                <hr />
                            </div>
                        @empty
                            <div data-repeater-item>
                                <div class="row">
                                    <div class="col-lg-10 col-12 mb-6 pelaksana">
                                        <label class="form-label">Nama Produksi</label>
                                        <select name="item_id" id="item_id" class="select2 form-select form-select-lg"
                                            data-allow-clear="true">
                                            <option value="">-- Pilih Panen / Kuliner --</option>
                                            @foreach ($panen as $p)
                                                <option value="panen-{{ $p->id }}">
                                                    {{ $p->bibit->nama }} ({{ $p->bibit->jenis }}) -
                                                    {{ $p->tanggal_panen }}
                                                    @if ($p->status === 'ready')
                                                        (🟢 Ready)
                                                    @elseif ($p->status === 'habis')
                                                        (🔴 Habis)
                                                    @endif
                                                </option>
                                            @endforeach
                                            @foreach ($Kuliner as $k)
                                                <option value="kuliner-{{ $k->id }}">
                                                    {{ $k->nama }} - {{ $k->tanggal_produksi }}
                                                    @if ($k->status === 'ready')
                                                        (🟢 Ready)
                                                    @elseif ($k->status === 'tidak_layak')
                                                        (🟡 Tidak Layak Jual)
                                                    @elseif ($k->status === 'habis')
                                                        (🔴 Habis)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-12 d-flex align-items-end mb-6">
                                        <button type="button" class="btn btn-label-danger w-100" data-repeater-delete>
                                            <i class="icon-base bx bx-x me-1"></i>
                                        </button>
                                    </div>
                                </div>
                                @include('app.penjualan.form_panen')
                                @include('app.penjualan.form_kuliner')
                                <hr />
                            </div>
                        @endforelse
                    </div>

                    <div class="mb-0 d-flex justify-content-between align-items-center">
                        <a href="/app/penjualan" class="btn btn-outline-secondary">
                            <i class="icon-base bx bx-left-arrow-alt me-1"></i> Kembali
                        </a>
                        <div class="d-flex">
                            <button type="button" class="btn btn-outline-primary" data-repeater-create>
                                <i class="icon-base bx bx-plus me-1"></i> Tambahkan Jenis Pembelian
                            </button>
                            <button type="button" id="simpanPenjualan" class="btn btn-primary ms-2">
                                <i class="icon-base bx bx-cloud-upload me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    <script>
        function parseNum(val) {
            return val ? parseFloat(val.toString().replace(/\./g, '').replace(/[^\d]/g, '')) : 0;
        }

        function fmt(val) {
            return val ? new Intl.NumberFormat('id-ID').format(val) : '';
        }

        function initInput($wrapper) {
            $wrapper.find(
                '.harga_satuan,.jumlah,.total,.jumlah_satuan,.harga_satuan_panen,.jumlah_panen,.jumlah_satuan_panen,.total_panen,.harga_satuan_kuliner,.jumlah_kuliner,.total_kuliner'
            ).each(function() {
                let v = parseNum($(this).val());
                if (v) {
                    $(this).data('raw', v).val(fmt(v));
                } else {
                    $(this).data('raw', 0);
                }
            });
        }

        $(document).on('input',
            '.form-repeater .harga_satuan_panen,.form-repeater .harga_satuan_kuliner,.form-repeater .harga_satuan,.form-repeater .jumlah',
            function() {
                let val = $(this).val().replace(/\./g, '').replace(/[^\d]/g, '');
                $(this).data('raw', val ? parseFloat(val) : 0).val(val ? fmt(val) : '');
            });

        $(".dob-picker").flatpickr({
            monthSelectorType: "static",
            dateFormat: "Y-m-d"
        });

        const repeaterForm = $(".form-repeater");
        if (repeaterForm.length) {
            repeaterForm.repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('.select2').select2({
                        dropdownParent: $(this)
                    });
                    $(this).find('.form-panen,.form-kuliner').addClass('d-none');
                    initInput($(this));
                },
                hide: function(e) {
                    Swal.fire({
                        title: "Hapus input?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Hapus",
                    }).then((result) => {
                        if (result.isConfirmed) $(this).slideUp(e);
                    })
                },
            });
        }

        $(document).on('change', '.pelaksana select', function() {
            let val = $(this).val();
            let wrapper = $(this).closest('[data-repeater-item]');
            wrapper.find('.form-panen,.form-kuliner').addClass('d-none');
            if (!val) return;

            let [type, id] = val.split('-');
            $.get(type === 'panen' ? '/app/panen/detail/' + id : '/app/kuliner/detail/' + id, function(r) {
                if (!r.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: r.msg || 'Data tidak ditemukan'
                    });
                    return;
                }
                let data = r.data || {},
                    harga = parseNum(data.harga_jual);

                if (type === 'panen') {
                    wrapper.find('.form-panen').removeClass('d-none');
                    let totalKg = parseNum(data.berat_total),
                        totalEkor = parseNum(data.jumlah),
                        ekorPerKg = (totalKg && totalEkor) ? totalEkor / totalKg : 0;

                    let $jumlahKg = wrapper.find('.form-panen .jumlah_panen'),
                        $hargaInput = wrapper.find('.form-panen .harga_satuan_panen'),
                        $jumlahSatuan = wrapper.find('.form-panen .jumlah_satuan_panen'),
                        $totalInput = wrapper.find('.form-panen .total_panen');

                    if (!$jumlahKg.val()) $jumlahKg.val(1).data('raw', 1);
                    if (!$hargaInput.val()) $hargaInput.val(fmt(harga)).data('raw', harga);
                    $jumlahSatuan.prop('readonly', true);

                    let updatePanen = () => {
                        let j = parseNum($jumlahKg.data('raw'));
                        if (totalKg > 0 && j > totalKg) {
                            j = totalKg;
                            $jumlahKg.val(fmt(totalKg)).data('raw', totalKg);
                            Swal.fire('Peringatan', `Jumlah melebihi batas panen (${totalKg} kg)`,
                                'warning');
                        }
                        $jumlahSatuan.val(fmt(j * ekorPerKg) + ' ekor').data('raw', j * ekorPerKg);
                        $totalInput.val(fmt(j * parseNum($hargaInput.data('raw')))).data('raw', j *
                            parseNum($hargaInput.data('raw')));
                    };

                    $jumlahKg.off('input').on('input', () => {
                        $jumlahKg.data('raw', parseNum($jumlahKg.val()));
                        updatePanen();
                    });
                    $hargaInput.off('input').on('input', () => {
                        $hargaInput.data('raw', parseNum($hargaInput.val()));
                        updatePanen();
                    });
                    updatePanen();
                } else {
                    wrapper.find('.form-kuliner').removeClass('d-none');
                    let packing = typeof data.packing === 'string' ? JSON.parse(data.packing) : data
                        .packing || [],
                        stok = parseNum(packing?.[0]?.satuan || 0),
                        jenis = packing?.[0]?.jenis || 'cup';

                    let $jumlah = wrapper.find('.form-kuliner .jumlah_kuliner'),
                        $hargaInput = wrapper.find('.form-kuliner .harga_satuan_kuliner'),
                        $totalInput = wrapper.find('.form-kuliner .total_kuliner');

                    wrapper.find('.form-kuliner .satuan').text(jenis);
                    if (!$jumlah.val()) $jumlah.val(1).data('raw', 1);
                    if (!$hargaInput.val()) $hargaInput.val(fmt(harga)).data('raw', harga);

                    let updateKuliner = () => {
                        let j = parseNum($jumlah.data('raw'));
                        if (stok > 0 && j > stok) {
                            j = stok;
                            $jumlah.val(fmt(stok)).data('raw', stok);
                            Swal.fire('Peringatan', `Jumlah melebihi stok (${stok} ${jenis})`,
                                'warning');
                        }
                        $totalInput.val(fmt(j * parseNum($hargaInput.data('raw')))).data('raw', j *
                            parseNum($hargaInput.data('raw')));
                    };

                    $jumlah.off('input').on('input', () => {
                        $jumlah.data('raw', parseNum($jumlah.val()));
                        updateKuliner();
                    });
                    $hargaInput.off('input').on('input', () => {
                        $hargaInput.data('raw', parseNum($hargaInput.val()));
                        updateKuliner();
                    });
                    updateKuliner();
                }
            });
        });

        repeaterForm.find('[data-repeater-item]').each(function() {
            $(this).find('.form-panen,.form-kuliner').addClass('d-none'); // sembunyikan awal
            initInput($(this));
            let select = $(this).find('select');
            if (select.val()) {
                select.trigger('change');
                setTimeout(() => {
                    let $jmlPanen = $(this).find('.jumlah_panen'),
                        $hargaPanen = $(this).find('.harga_satuan_panen'),
                        $jmlKuliner = $(this).find('.jumlah_kuliner'),
                        $hargaKuliner = $(this).find('.harga_satuan_kuliner');
                    if ($jmlPanen.val() || $hargaPanen.val()) {
                        $jmlPanen.trigger('input');
                        $hargaPanen.trigger('input');
                    }
                    if ($jmlKuliner.val() || $hargaKuliner.val()) {
                        $jmlKuliner.trigger('input');
                        $hargaKuliner.trigger('input');
                    }
                }, 300);
            }
        });


        $(document).on('click', '#simpanPenjualan', function(e) {
            e.preventDefault();
            let f = $('#FormPenjualan')[0];

            // bersihkan input di form yang hidden
            $(f).find('[data-repeater-item]').each(function() {
                if ($(this).find('.form-panen').hasClass('d-none')) {
                    $(this).find('.jumlah_panen,.harga_satuan_panen,.jumlah_satuan_panen,.total_panen')
                        .val(0).data('raw', 0);
                }
                if ($(this).find('.form-kuliner').hasClass('d-none')) {
                    $(this).find('.jumlah_kuliner,.harga_satuan_kuliner,.total_kuliner')
                        .val(0).data('raw', 0);
                }
            });

            // ubah semua ke raw number
            $(f).find(
                '.harga_satuan,.jumlah,.total,.jumlah_satuan,.harga_satuan_panen,.jumlah_panen,.jumlah_satuan_panen,.total_panen,.harga_satuan_kuliner,.jumlah_kuliner,.total_kuliner'
            ).each(function() {
                let raw = $(this).data('raw');
                if (raw !== undefined) {
                    $(this).val(raw);
                } else {
                    $(this).val(parseNum($(this).val()));
                }
            });

            let fd = new FormData(f);
            $.ajax({
                type: 'POST',
                url: f.action,
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        Swal.fire('Sukses', r.msg, 'success').then(() => window.location.href =
                            '/app/penjualan');
                    } else {
                        Swal.fire('Error', r.msg || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                }
            });
        });


        $('.form-repeater .select2').select2({
            dropdownParent: $('.form-repeater')
        });
    </script>
@endsection
