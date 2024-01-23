<br>
<form method="post" action="/users/mandate/<?php echo $mandateid; ?>" onSubmit='return testForm();'>
    <div class="mb-3 row">
        <div class="form-floating col-sm-4">
            <input type="text" class="form-control" id="iban" name="iban" placeholder="IBAN" value="<?php echo $iban; ?>">
            <label for="floatingAmount">IBAN</label>
        </div>
    </div>
    <div class="mb-3 row">
        <div class="form-floating col-sm-4">
            <input type="text" class="form-control" id="startdate" name="startdate" placeholder="startdate" disabled value="<?php echo $startdate; ?>">
            <label for="floatingAmount">Date de création</label>
        </div>
    </div>

    <button class="btn btn-primary" type="submit">Valider</button>
    <a href="/users/moncompte" class="btn btn-primary">Annuler</a>
</form>