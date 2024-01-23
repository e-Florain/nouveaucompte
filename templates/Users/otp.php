<br>
<h5>Vérifiez vos emails, vous avez recu un code de vérification à entrer :</h5>
<?php echo $this->Form->create() ?>
    <div class="form-floating col-sm-2">
        <input type="password" class="form-control" id="otp" name="otp" autocomplete="off" placeholder="OTP">
        <label for="floatingPassword">Code</label>
    </div>
    <br>
    <button class="btn btn-primary" type="submit">Valider</button>
</form>