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
        <h4>Adhésion automatique</h4>
    </div>
</div>
<?php if ($comptecyclos) {
?>
<form method="post" action="/nouveaucompte/choosechange" onSubmit='return testFormAdh();'>
<?php
} else {
?>
<form method="post" action="/nouveaucompte/editiban" onSubmit='return testFormAdh();'>
<?php
}
?>
    <div class="row">
        <div class="col-sm-8 offset-m6">
            Les adhésions sont valables de date à date, sur une année glissante.
            <br />
            <i>Par exemple : du 8 octobre 2022 au 7 octobre 2023.</i>
            <br /><br />
            Votre adhésion sera renouvelée automatiquement chaque année, et peut s’arrêter à tout moment si vous le
            souhaitez.
            <br /><br />
            <?php if ($comptecyclos) { ?>
                Si vous souhaitez faire un change dans un comptoir pour obtenir des billets, il suffit de présenter votre
                application mobile Florain : votre fiche profil mentionne la date de fin de votre cotisation et votre numéro
                d’adhérent-e-s.
            <?php } ?>
        </div>
    </div>
    <?php
    
    if ($nvocompte->todo == "update") {
        if ($nvocompte->membership_state == "paid") {
            if ($nvocompte->membership_stop != NULL) {
                ?>
                <div class="row">
                    <div class="col s10 m6 offset-m2">
                        <h6>Vous êtes adhérent.e jusqu'au
                            <?php echo $membershipstop; ?>, votre nouvelle adhésion commencera donc le
                            <?php echo $newdateadh; ?>.
                        </h6>
                    </div>
                </div>
                <?php
            }
        }
    }
    ?>
    <br>
    <div class="row">
        <div class="col-sm-10">
            <h6>Vous devez choisir entre un prélèvement <b><u>annuel</u></b> OU <b><u>mensuel</u></b>.</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <select id="adhchoice" name="adhchoice" class="form-select" required aria-label="Default select example">
                <option value="" disabled selected>Choix de l'adhésion</option>
                <option value="annuel">Prélèvement annuel</option>
                <option value="mensuel">Prélèvement mensuel</option>
            </select>
        </div>
    </div>
    <br>
    <div class="col-sm-8 adhannuel">
        <b>*Montant annuel</b>
        <div class="form-check">
            <input class="with-gap nbeurosadhannuel" id="nbeurosadhannuel" name="nbeurosadhannuel" type="radio" disabled
                checked value="24">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Tarif <b>NORMAL</b> &nbsp;&nbsp;&nbsp; : <b>24 € / an </b> (soit un équivalent de <b>2 € /
                    mois</b>)</span>
            </label>
        </div>
        <div class="form-check">
            <input class="with-gap nbeurosadhannuel" id="nbeurosadhannuel" name="nbeurosadhannuel" type="radio" disabled
                 value="72">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Tarif <b>SOUTIEN</b> &nbsp; &nbsp;: <b>72 € / an</b> (soit un équivalent de <b>6 € /
                        mois</b>)</span>
            </label>
        </div>
        <div class="form-check">
            <input class="with-gap nbeurosadhannuel" id="nbeurosadhannuel" name="nbeurosadhannuel" type="radio" disabled
                 value="6">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Tarif <b>SOLIDAIRE</b> : <b>6 € / an </b> (soit un équivalent de <b>0.5 € / mois</b>) <i>(Si
                        vous avez peu de moyens)</i></span>
            </label>
        </div>
        <div class="form-check">
            <input class="with-gap nbeurosadhannuel" id="nbeurosadhannuel" name="nbeurosadhannuel" type="radio" disabled
                 value="other">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Autre montant (> 6 € / an)</span>
            </label>
        </div>
        <div class="row">
            <div class="col-sm-4 offset-sm-1" id="divmontantannuel">
                <input class="form-control" id="montantannuel" name="montantannuel">
                <div id="montantannuel-feedback" class="invalid-feedback">
                    
                </div>
            </div>
            <!--<div class="input-field col s8">
                <input type="hidden" id="montantannuel" name="montantannuel" class="form-control" disabled>
                <span id="helpermontantannuel" style="display:none;" class="helper-text" data-error="wrong"
                    data-success="right">Montant</span>
            </div>-->
            <div id="msgerror"></div>
        </div>
        
        <div id="msgerror"></div>
    </div>
    <br>
    <div class="col-sm-6 offset-m2">
        <h3>OU</h3>
    </div>
    <br>
    <div class="col-sm-6 offset-m2 adhmensuel">
        <b>*Montant mensuel</b>
        <div class="form-check">
            <input class="with-gap nbeurosadhmensuel" id="nbeurosadhmensuel" name="nbeurosadhmensuel" type="radio" disabled
                checked value="6">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Tarif : <b>SOUTIEN 6 € / mois</b> (soit un total de 72 €/an)</span>
            </label>
        </div>
        <div class="form-check">
            <input class="with-gap nbeurosadhmensuel" id="nbeurosadhmensuel" name="nbeurosadhmensuel" type="radio" disabled
                 value="15">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Tarif : <b>SOUTIEN++ 15 € / mois</b> (soit un total de 180 €/an)</span>
            </label>
        </div>
        <div class="form-check">
            <input class="with-gap nbeurosadhmensuel" id="nbeurosadhmensuel" name="nbeurosadhmensuel" type="radio" disabled
                 value="other">
            <label class="form-check-label" for="flexRadioDefault1">
                <span>Autre montant (> 6 € / mois)</span>
            </label>
        </div>
        <div class="row">
            <div class="col-sm-4 offset-sm-1" id="divmontantmensuel">
                <input class="form-control" id="montantmensuel" name="montantmensuel">
                <div id="montantmensuel-feedback" class="invalid-feedback">
                    
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-10">
            Le prélèvement sur votre compte bancaire sera réalisé à partir d'aujourd'hui (si jour ouvrable) sauf si vous êtes deja à jour de cotisation et dans ce cas le prélèvement aura lieu lors de la date de fin de votre adhésion.
        </div>
    </div>

    <div class="row">
        <div class="col-sm-10">
            <br /><br />
            <i> Les personnes souhaitant changer des euros en Florain (papier ou numérique) doivent adhérer à
                l’association. Peut adhérer au Collectif, en tant qu’utilisateur, tout particulier et toute personne
                morale sous réserve de respect des valeurs défendues par <a
                    href="https://www.monnaielocalenancy.fr/wp-content/uploads/Charte-de-Valeurs.pdf" target="_blank">
                    la charte de valeurs</a>.</i>
            <br /><br />
        </div>
    </div>

    <button type="submit" class="btn-primary btn"
        data-bcup-haslogintext="no">Suivant
     </button>
</form>
