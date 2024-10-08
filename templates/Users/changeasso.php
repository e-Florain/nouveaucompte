<br><br>
<h3>Changer l'association soutenue</h3>
<div id="liveAlertPlaceholder">
</div>
<form method="post" action="/users/changeasso" onSubmit='return testForm();'>
    <div class="form-floating col-sm-4">
        <select id="orgachoice" name="orgachoice" class="form-select" required aria-label="Default select example">
            <option value="" disabled>Choix de l'assocation soutenue</option>
            <?php
            foreach ($assos as $asso) {
                echo '<option value="'.$asso['id'].'" >'.$asso['name'].'</option>';
            }
            ?>
        </select>
    </div>
    <br>
    <button class="btn btn-primary" type="submit">Valider</button>
    <a href="/users/moncompte" class="btn btn-primary">Annuler</a>
</form>