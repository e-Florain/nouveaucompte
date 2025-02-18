<br><br>
<h3>Importer une carte d'identité</h3>
<div class="row">
    <form class="col s12 m6 offset-m2" method="post" action="/users/import_ci" enctype="multipart/form-data" onSubmit='return testForm();'>
            <div class="row">
                <div class="input-field col s12">
                    <div class="file-field input-field">
                        <div class="mb-3">
                            <label for="formFile" class="form-label"><b>Importer votre pièce d'identité (recto) / taille
                                    maximale 10Mo</b></label>
                            <input class="form-control" type="file" id="file-upload" name="uploadedFile"
                                class="validate" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col offset-s2">
                    <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
                     data-bcup-haslogintext="no">Importer</button>
                </div>
            </div>
        </form>
</div>