<br>
<?php echo $this->Form->create() ?>
<div class="mb-3 row">
    <div class="form-floating col-sm-4">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="floatingInput">Mot de passe</label>
    </div>
</div>
<div class="mb-3 row">
    <div class="form-floating col-sm-4">
        <input type="password" class="form-control" id="password2" name="password2" placeholder="Password" required>
        <label for="floatingPassword">Mot de passe (confirmation)</label>
    </div>
</div>
    <br>
    <button class="btn btn-primary" type="submit">Valider</button>

</form>