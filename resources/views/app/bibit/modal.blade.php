<div class="modal fade" id="BibitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2" id="formTitle"></h4>
                    <p>“Masukkan Bibit Kolam Baru”</p>
                </div>
                <form action="" method="post" id="FormBibit" autocomplete="off">
                    @csrf
                    <input type="hidden" id="id_bibit" name="id_bibit">

                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="kolam_id">Kolam Bibit</label>
                            <select id="kolam_id" name="kolam_id" class="form-control select2"></select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="nama">Nama</label>
                            <input type="text" id="nama" name="nama" class="form-control"
                                placeholder="Nama bibit" />
                            <small id="msg_nama" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="jenis">Jenis Bibit</label>
                            <input type="text" id="jenis" name="jenis" class="form-control"
                                placeholder="Jenis Bibit" />
                            <small id="msg_jenis" class="text-danger"></small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="tanggal_datang">Tanggal Datang</label>
                            <input type="text" id="tanggal_datang" name="tanggal_datang"
                                class="form-control dob-picker" placeholder="Tanggal Datang" />
                            <small id="msg_tanggal_datang" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="jumlah">Jumlah</label>
                            <div class="input-group">
                                <input type="number" id="jumlah" name="jumlah" class="form-control"
                                    placeholder="Jumlah bibit" aria-label="Jumlah bibit"
                                    aria-describedby="button-addon2" />
                                <button class="btn btn-outline-secondary" type="button" id="kapasitas_bibit"></button>
                            </div>
                            <small id="msg_jumlah" class="text-danger"></small>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="sumber">Sumber</label>
                            <input type="text" id="sumber" name="sumber" class="form-control"
                                placeholder="Sumber bibit" />
                            <small id="msg_sumber" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="SimpanBibit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
