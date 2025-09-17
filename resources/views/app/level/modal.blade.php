    <div class="modal fade" id="LevelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-simple modal-add-new-address">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-6">
                        <h4 class="mb-2" id="formTitle"></h4>
                        <p>“Masukkan Level Jabatan”</p>
                    </div>
                    <form action="" method="post" id="FormLevel" autocomplete="off">
                        @csrf
                        <input type="hidden" id="id_Level" name="id_Level">

                        <div class="row">
                            <div class="col-12 col-md-12 ">
                                <label class="form-label" for="nama">Nama</label>
                                <input type="text" id="nama" name="nama" class="form-control"
                                    placeholder="Nama Level" />
                                <small id="msg_nama" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 mb-0">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="simpanLevel" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
