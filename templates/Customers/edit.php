<br>
  <h3>Editer le client <?php echo $customer['id'];?></h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="name" class="form-label">Nom</label>
          <input name="name" id="name" type="text" value="<?php echo $customer['name']; ?>" required class="validate">          
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="email" class="form-label">Email</label>
          <input name="email" id="email" type="text" value="<?php echo $customer['email']; ?>" required class="validate">          
        </div>
      </div>
    <button class="btn btn-primary" type="submit" name="action">Modifier</button>
    <a class="btn btn-primary" href="/customers" >Annuler</a>
    </form>
  </div>
        