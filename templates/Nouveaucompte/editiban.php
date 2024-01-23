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
        <h4>IBAN</h4>
    </div>
</div>

<div class="row">
    <div class="col-sm-8 offset-m2">

        <p>
            En renseignant votre IBAN, vous signez un mandat qui autorise l’association Le Florain à prélever votre
            compte :
        <ul class="list-group">
            <li class="list-group-item">pour votre adhésion</li>
            <?php if ($account_cyclos) { ?>
                <li class="list-group-item">pour votre change mensuel</li>
            <?php } ?>
        </ul>
        <br /><br />Vous devez détenir ce compte, ou être autorisé par le détenteur à agir pour son compte.
        <br /><br />Vos données personnelles feront l’objet d’un traitement par Mollie, notre partenaire pour des <a
            href="https://www.mollie.com/fr/features/security" target="_blank"> paiements sécurisés</a>.
        </p>

        <form method="post" action="/nouveaucompte/chooseasso" onSubmit='return testFormIBAN();'>
            <p>
                <b>*Coordonnées du compte à débiter (IBAN)</b>
            </p>
            <div class="row">
                <div class="col-sm-6">
                    <input type="text" id="iban" name="iban" required="required" class="form-control"
                        placeholder="____ ____ ____ ____ ____ ____ ___" maxlength="33">
                    <div id="iban-feedback" class="invalid-feedback"></div>
                </div>
            </div>
            <br>
            <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
                data-bcup-haslogintext="no">Suivant
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z" />
                    <path fill-rule="evenodd"
                        d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </button>
        </form>

    </div>
</div>