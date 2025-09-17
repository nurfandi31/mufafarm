<div class="modal fade" id="ModalPakan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2" id="formTitle"></h4>
                    <p>Masukkan nama Pakan, misal: Pelet</p>
                </div>
                <form id="FormPakan" autocomplete="off">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="nama">Nama Pakan</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                placeholder="Pelet" />
                            <small id="msg_nama" class="text-danger"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="stok">Stok Pakan</label>
                            <input type="text" id="stok" name="stok" class="form-control"
                                placeholder="Stok" />
                            <small id="msg_stok" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="satuan">Satuan</label>
                            <select id="satuan" name="satuan" class="form-control select2">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="kg">Kg</option>
                                <option value="ons">Ons</option>
                                <option value="gram">Gram</option>
                                <option value="ton">Ton</option>
                                <option value="liter">Liter</option>
                                <option value="ml">mL</option>
                                <option value="ekor">Ekor</option>
                                <option value="biji">Biji</option>
                                <option value="karung">Karung</option>
                                <option value="ember">Ember</option>
                                <option value="unit">Unit</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <small id="msg_satuan" class="text-danger"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="harga">Harga</label>
                            <input type="text" id="harga" name="harga" class="form-control"
                                placeholder="Harga Jual" />
                            <small id="msg_harga" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="SimpanPakan" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
