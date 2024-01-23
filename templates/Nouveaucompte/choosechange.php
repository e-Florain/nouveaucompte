<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE
        <?php echo $step . "/" . $nbsteps; ?>
    </h6>
</div>

<div class="row">
    <div class="col-sm-10 offset-s2">
        <h4>Change automatique mensuel</h4>
    </div>
</div>

<div class="row">
    <div class="col s10 m6 offset-m2 change">
        Avec le change automatique mensuel, pas besoin de penser à recharger manuellement votre compte, vous aurez
        toujours des florains disponibles pour vos dépenses dans le réseau ! Et plus les monnaies locales sont
        utilisées, plus elles sont bénéfiques au territoire.
    </div>
</div>

<br>

<div class="row">
    <div class="col-sm-10 offset-m2 change">
        <b>*Montant du change automatique mensuel</b>

        <form method="post" action="/nouveaucompte/editiban" onSubmit='return testFormChange();'>
            <div class="form-check">
                <input class="with-gap" id="nbflorains" name="nbflorains" type="radio" checked value="100">
                <label class="form-check-label" for="flexRadioDefault1">
                    <span>100 florains</span>
                </label>
            </div>
            <div class="form-check">
                <input class="with-gap" id="nbflorains" name="nbflorains" type="radio" value="60">
                <label class="form-check-label" for="flexRadioDefault1">
                    <span>60 florains</span>
                </label>
            </div>
            <div class="form-check">
                <input class="with-gap" id="nbflorains" name="nbflorains" type="radio" value="20">
                <label class="form-check-label" for="flexRadioDefault1">
                    <span>20 florains</span>
                </label>
            </div>
            <div class="form-check">
                <input class="with-gap" id="nbflorains" name="nbflorains" type="radio" value="other">
                <label class="form-check-label" for="flexRadioDefault1">
                    <span>Autre montant (> 20 Fl)</span>
                </label>
            </div>
            <div class="row">
                <div class="col-sm-4 offset-sm-1" id="divmontant">
                    <input class="form-control" id="montant" name="montant">
                    <div id="montant-feedback" class="invalid-feedback">
                    </div>
                </div>

            </div>
            <br>
            Le prélèvement sur votre compte bancaire sera réalisé quelques jours après votre création de compte, et
            votre compte Florain sera crédité du même montant sous 24h (hors week-end). Ce prélèvement sera renouvelé
            tous les mois.
            <br><br>
            <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
                data-bcup-haslogintext="no">Suivant    
            </button>
        </form>

    </div>
</div>