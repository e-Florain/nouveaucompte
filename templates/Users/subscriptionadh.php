<br>
<div id="liveAlertAdh"></div>

<form method="post" action="/users/subscriptionadh/<?php echo $subid; ?>" onSubmit='return testForm();'>
    <div class="form-floating col-sm-4">
        <input type="text" class="form-control" id="description" name="description" placeholder="Description" disabled value="<?php echo $description; ?>">
        <label for="floatingDescription">Description</label>
    </div>
    <div class="form-floating col-sm-4">
        <select id="adhchoicemoncompte" name="adhchoicemoncompte" class="form-select" required aria-label="Default select example">
            <option value="" disabled>Choix de l'adhésion</option>
            <option value="annuel"
            <?php if ($interval == "1 year") {
                 echo 'selected';
            } ?>
            >Prélèvement annuel</option>';
            <option value="mensuel"
            <?php if ($interval == "1 month") {
                 echo 'selected';
            } ?>
            >Prélèvement mensuel</option>
        </select>
    </div>
    <!--<div class="form-floating col-sm-4">
        <input type="text" class="form-control" id="interval" name="interval" placeholder="Intervalle" value="<?php echo $interval; ?>">
        <label for="floatingAmount">Intervalle</label>
    </div>-->
    <div class="form-floating col-sm-4">
        <input type="text" class="form-control" id="montantadh" name="montantadh" placeholder="Montant" value="<?php echo $amount; ?>">
        <label for="floatingAmount">Montant</label>
    </div>
    <br>
    <button class="btn btn-primary" type="submit">Valider</button>
    <a href="/users/moncompte" class="btn btn-primary">Annuler</a>
</form>