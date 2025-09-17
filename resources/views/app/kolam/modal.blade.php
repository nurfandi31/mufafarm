<div class="modal fade" id="KolamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2" id="formTitle"></h4>
                    <p>“Masukkan data kolam dengan benar”</p>
                </div>
                <form action="" method="post" id="FormKolam" autocomplete="off">
                    @csrf
                    <input type="hidden" id="id_kolam" name="id_kolam">

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="nama">Nama Kolam</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                placeholder="Kolam A / Kolam 1" />
                            <small id="msg_nama" class="text-danger"></small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="type">Type Kolam</label>
                            <select id="type" name="type" class="form-control select2">
                                <option value="">-- Pilih Type --</option>
                                <option value="tanah">Tanah</option>
                                <option value="terpal">Terpal</option>
                                <option value="beton">Beton</option>
                                <option value="kayu">Kayu</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <small id="msg_type" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="kapasitas_bibit">Kapasitas Bibit</label>
                            <select id="kapasitas_bibit" name="kapasitas_bibit" class="form-control select2">
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
                            <small id="msg_kapasitas_bibit" class="text-danger"></small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="lokasi_kolam">Lokasi Kolam</label>
                            <input type="text" id="lokasi_kolam" name="lokasi_kolam" class="form-control"
                                placeholder="Belakang rumah / Lahan 1" />
                            <small id="msg_lokasi_kolam" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="SimpanKolam" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
