<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE 3/
        <?php echo $nbsteps; ?>
    </h6>
</div>

<div class="row">
    <div class="col s10 offset-s2">
        <h4><b>Pièce d'identité</b></h4>
    </div>
</div>

<div class="row">
    <div class="col s10 offset-m2 cardid">
        Afin de valider votre compte, la réglementation bancaire nous impose de vérifier une pièce d'identité.<br>
        <br>

        Comment importer une pièce d'identité ? <br>
        <div class="ul-fl">
            <li>Le document ne doit pas être flou</li>
            <li>Votre photo doit apparaître sur le document (importer le recto <b>uniquement</b>)</li>
            <li>Vous pouvez choisir : <b>Carte d’identité ou Passeport</b></li>
            <li>Au format jpeg, png, pdf ou tiff</li>
            <li>Le document doit comporter le nom choisi pour la création du compte (nom usage / nom de naissance ;
                prénom composé etc ...)</li>
        </div>
        <center>
            <div style="margin-top: 50px" class="text-center">
                <img src="/img/idcard.png" width="100px" />
            </div>
        </center>
        <br>
    </div>
    <div class="row">
        <form class="col s12 m6 offset-m2" method="post" action="/nouveaucompte/validid" enctype="multipart/form-data" onSubmit='return testForm();'>
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
                     data-bcup-haslogintext="no">Valider</button>
                </div>
            </div>
        </form>
    </div>
    <br>
    <div class="row">
        <div class="col s10 offset-m2">
            <p>
                <i>Pour limiter le risque de fraude, nous utilisons l'outil sécurisé, fiable et instantané de notre
                    partenaire MINDEE.
                    <br />
                    En soumettant ce formulaire, vous acceptez que MINDEE utilise vos données personnelles dans les
                    conditions décrites dans sa <a href="https://www.mindee.com/privacy-policy" target="_blank">
                        Politique de confidentialité. </a></i>
            </p>
        </div>
    </div>
</div>