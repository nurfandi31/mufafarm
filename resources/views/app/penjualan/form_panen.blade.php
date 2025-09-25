@php
    $jumlahKg = $itemData->jumlah ?? 1;
    $harga = $itemData->harga_satuan ?? '';
    $jumlahSatuan = $itemData->jumlah_satuan ?? '';
    $total = $itemData->total ?? '';
    $satuan = $itemData->satuan ?? '';
    $show = isset($itemData) ? ($itemData->item_type === 'panen' ? '' : 'd-none') : 'd-none';
@endphp

<div class="row form-panen {{ $show }}">
    <div class="col-md-6 col-12">
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <div class="input-group">
                <input type="number" step="0.01" name="jumlah_panen" class="form-control jumlah_panen"
                    placeholder="Masukkan jumlah" value="{{ $jumlahKg }}">
                <span class="input-group-text satuan">{{ $satuan }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="mb-3">
            <label class="form-label">Harga Satuan</label>
            <input type="text" name="harga_satuan_panen" class="form-control harga_satuan_panen"
                placeholder="Masukkan harga satuan" data-raw="{{ $harga ?? 0 }}"
                value="{{ $harga !== '' ? number_format((float) $harga, 0, ',', '.') : '' }}">
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="mb-3">
            <label class="form-label">Jumlah Satuan</label>
            <input type="text" name="jumlah_satuan_panen" class="form-control jumlah_satuan_panen"
                placeholder="Jumlah Satuan" readonly value="{{ $jumlahSatuan }}">
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="mb-3">
            <label class="form-label">Total Bayar</label>
            <input type="text" name="total_panen" class="form-control total_panen" placeholder="Total harga" readonly
                value="{{ $total !== '' ? number_format((float) $total, 0, ',', '.') : '' }}"
                data-raw="{{ $total ?? 0 }}">
        </div>
    </div>
</div>
