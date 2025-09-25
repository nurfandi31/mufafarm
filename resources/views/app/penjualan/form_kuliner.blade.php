@php
    $jumlah = $itemData->jumlah ?? 1;
    $harga = $itemData->harga_satuan ?? '';
    $total = $itemData->total ?? '';
    $jenis = $itemData->packing[0]['jenis'] ?? '';
    $show = isset($itemData) ? ($itemData->item_type === 'kuliner' ? '' : 'd-none') : 'd-none';
@endphp

<div class="row form-kuliner {{ $show }}">
    <div class="col-md-6 col-12">
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <div class="input-group">
                <input type="number" step="1" name="jumlah_kuliner" class="form-control jumlah_kuliner"
                    placeholder="Masukkan jumlah" value="{{ $jumlah }}">
                <span class="input-group-text satuan">{{ $jenis }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="mb-3">
            <label class="form-label">Harga Satuan</label>
            <input type="text" name="harga_satuan_kuliner" class="form-control harga_satuan_kuliner"
                placeholder="Masukkan harga satuan"
                value="{{ $harga !== '' ? number_format((float) $harga, 0, ',', '.') : '' }}"
                data-raw="{{ $harga ?? 0 }}">
        </div>
    </div>
    <div class="col-md-10 col-12">
        <div class="mb-3">
            <label class="form-label">Total Bayar</label>
            <input type="text" name="total_kuliner" class="form-control total_kuliner" placeholder="Total harga"
                readonly value="{{ $total !== '' ? number_format((float) $total, 0, ',', '.') : '' }}"
                data-raw="{{ $total ?? 0 }}">
        </div>
    </div>
</div>
