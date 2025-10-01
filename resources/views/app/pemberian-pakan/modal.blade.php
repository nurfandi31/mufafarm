<div class="modal fade" id="PP-Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2" id="formTitle"></h4>
                    <p>“Masukkan Pemberian Pakan”</p>
                </div>
                <form action="" method="post" id="FormPemberianPakan" autocomplete="off">
                    @csrf
                    <input type="hidden" id="id_PP" name="id_PP">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="bibit_id">Bibit</label>
                            <select id="bibit_id" name="bibit_id" class="form-control select2"></select>
                            <small id="msg_bibit_id" class="text-danger"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="pakan_id">Pakan</label>
                            <select id="pakan_id" name="pakan_id" class="form-control select2"></select>
                            <small id="msg_pakan_id" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tanggal_pemberian">Tanggal Pemberian</label>
                            <input type="text" id="tanggal_pemberian" name="tanggal_pemberian"
                                class="form-control dob-picker" placeholder="Tanggal Pemberian" />
                            <small id="msg_tanggal_pemberian" class="text-danger"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="jumlah">Jumlah</label>
                            <div class="input-group">
                                <input type="number" id="jumlah" name="jumlah" class="form-control"
                                    placeholder="Jumlah Pakan" />
                                <button class="btn btn-outline-secondary" type="button" id="satuanPakan"></button>
                            </div>
                            <small id="msg_jumlah" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="SimpanPemberianPakan" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
