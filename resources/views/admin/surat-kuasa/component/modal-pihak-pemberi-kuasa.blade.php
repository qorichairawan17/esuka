 <div class="modal fade" id="pemberi-surat-kuasa" tabindex="-1" aria-labelledby="pemberi-surat-kuasa-title" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content rounded shadow border-0">
             <div class="modal-header border-bottom">
                 <h5 class="modal-title" id="pemberi-surat-kuasa-title">Tambah Pihak Pemberi Kuasa</h5>
                 <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>
             </div>
             <div class="modal-body">
                 <form id="form-pemberi-kuasa">
                     <div class="form-group mb-2">
                         <label for="pemberi_nama">
                             Nama <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="pemberi_nama" class="form-control" id="pemberi_nama" required>
                     </div>
                     <div class="form-group mb-2">
                         <label for="pemberi_nik">
                             NIK <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="pemberi_nik" class="form-control" id="pemberi_nik" required>
                     </div>
                     <div class="form-group mb-2">
                         <label for="pemberi_pekerjaan">
                             Pekerjaan <span class="text-danger">*</span>
                         </label>
                         <input type="text" name="pemberi_pekerjaan" class="form-control" id="pemberi_pekerjaan" required>
                     </div>
                     <div class="form-group mb-2">
                         <label for="pemberi_alamat">
                             Alamat <span class="text-danger">*</span>
                         </label>
                         <textarea class="form-control" name="pemberi_alamat" id="pemberi_alamat" required></textarea>
                     </div>
                     <div class="modal-footer">
                         <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
