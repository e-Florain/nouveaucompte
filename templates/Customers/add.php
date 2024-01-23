<br>
  <h3>Ajouter un client</h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="name" class="form-label">Nom</label>
          <input name="name" id="name" type="text" required class="from-control validate">
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="email" class="form-label">Email</label>
          <input name="email" id="email" type="text" required class="from-control validate">
        </div>
      </div>
    <button class="btn btn-primary" type="submit" name="action">Ajouter</button>
    </form>
  </div>
        