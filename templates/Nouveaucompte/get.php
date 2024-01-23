<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN</h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE 1</h6>
</div>

<div class="row">
    <div class="col-sm-10 offset-s2">
        <h4><b>Identité</b></h4>
    </div>
</div>

<form class="col-sm-10" method="post" action="/nouveaucompte/infossup">
    <?php
    //Debug($adh);
    foreach ($translates as $key => $translate) {
        if (isset($adh[$key])) {
            echo "<div class='row'>";
            echo "<div class='col-sm-5'><b>" . $translate . "</b></div>";
            if ($key == "orga_choice") {
                echo "<div class='col-sm-4'>" . $assochosen . "</div>";
            } else {
                //Debug($adh[$key]);
                if ($adh[$key] == 'true') {
                    echo "<div class='col-sm-4'>Oui</div>";
                } elseif (($adh[$key] == "none") or ($adh[$key] == "old")) {
                    echo "<div class='col-sm-4'>Aucune</div>";
                } elseif ($adh[$key] == false) {
                    echo "<div class='col-sm-4'>Non</div>";
                } elseif ($adh[$key] == 'free') {
                    echo "<div class='col-sm-4'>Libre</div>";
                } elseif ($adh[$key] == "waiting") {
                    echo "<div class='col-sm-4'>En attente</div>";
                } elseif (($adh[$key] == "paid") or ($adh[$key] == "invoiced")) {
                    echo "<div class='col-sm-4'>A jour</div>";
                } else {
                    echo "<div class='col-sm-4'>" . $adh[$key] . "</div>";
                }
            }
            echo "</div>";
        }
    }
    ?>
    <div class="row">
        <div class='col-sm-5'><b>Souhaitez-vous un compte florain numérique ?</b></div>
        &nbsp;&nbsp;
        <div class="col-sm-4 form-check">
            <input class="form-check-input" type="checkbox" value="" id="account_cyclos" name="account_cyclos">
        </div>
    </div>

    <br>
    <button type="submit" id="form_step_update" name="form_step_update" class="btn-primary btn"
        data-bcup-haslogintext="no">Suivant</button>
</form>
