<br>
<form method="post" action="/users/subscriptionchange/<?php echo $subid; ?>" onSubmit='return testForm();'>
    <div class="form-floating col-sm-4">
        <input type="text" class="form-control" id="description" name="description" placeholder="Description" disabled value="<?php echo $description; ?>">
        <label for="floatingDescription">Description</label>
    </div>
    <div class="form-floating col-sm-4">
        <input type="text" class="form-control" id="montant" name="montant" placeholder="Montant" value="<?php echo $amount; ?>">
        <label for="floatingAmount">Montant</label>
    </div>
    <br>
    <button class="btn btn-primary" type="submit">Valider</button>
    <a href="/users/moncompte" class="btn btn-primary">Annuler</a>
</form>