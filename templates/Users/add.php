<br>
<h3>Ajouter un utilisateur</h3>
<div class="row">
    <?php echo $this->Form->create(); ?>
    <div class="mb-3 row">
        <div class="col-sm-4">
            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Prénom">
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-4">
            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Nom">
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-4">
            <input type="text" class="form-control" id="email" name="email"  placeholder="Email">
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-4">
            <select class="form-select " name="auth">
                <option value="" disabled selected>Choisir</option>
                <?php foreach ($list_auth as $auth) {
                    echo '<option value="' . $auth . '" >' . $auth . '</option>';
                }
                ?>
            </select>
        </div>  
    </div>
    <div class="mb-3 row">
        <div class="col-sm-4">
            <select class="form-select " name="role">
                <option value="" disabled selected>Choisir</option>
                <?php foreach ($list_roles as $role) {
                    echo '<option value="' . strtolower($role) . '" >' . $role . '</option>';
                }
                ?>
            </select>
        </div>  
    </div>

    <button class="btn btn-primary" type="submit" name="action">Ajouter
    </button>
    </form>
</div>