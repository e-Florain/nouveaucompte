<br>
<?php echo $this->Form->create() ?>
<div class="mb-3 row">
    <div class="form-floating col-sm-4">
        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
        <label for="floatingInput">Email</label>
    </div>
</div>
<div class="mb-3 row">
    <div class="form-floating col-sm-4">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        <label for="floatingPassword">Mot de passe</label>
    </div>
</div>
    <br>
    <button class="btn btn-primary" type="submit">Se connecter</button>

</form>