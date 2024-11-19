<br>
  <h3>Ajouter un prélèvement cyclos</h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="client_id" class="form-label">Compte Source</label>
          <select class="form-select" name="account_cyclos_src" >
            <option value="" disabled selected>Choisir un client</option>
              <?php foreach ($pros as $pro) {
              echo '<option value="'.$pro['email'].'" >'.$pro['name'].'</option>';
              }
              ?>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="client_id" class="form-label">Compte Destination</label>
          <select class="form-select" name="account_cyclos_dst" >
            <option value="" disabled selected>Choisir un client</option>
              <?php foreach ($pros as $pro) {
              echo '<option value="'.$pro['email'].'" >'.$pro['name'].'</option>';
              }
              ?>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="description" class="form-label">Fréquence</label>
          <select class="form-select" name="sub_interval" >
            <option value="" disabled selected>Choisir un fréquence</option>
              <?php 
                echo '<option value="monthly">Mensuel</option>';
                echo '<option value="annually">Annuel</option>';
              ?>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="description" class="form-label">Description</label>
          <input class="form-control" name="description" id="description" type="text" required class="validate" placeholder="Description">
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="startdate" class="form-label">Début du prélèvement</label>
          <input name="startdate" value="<?php echo $startdate; ?>" type="text" id="startdate" required class="form-control datepicker" placeholder="Début du prélèvement">
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="amount" class="form-label">Montant</label>
          <input class="form-control" name="amount" id="amount" type="text" required class="validate" placeholder="Montant">
        </div>
      </div>
    <button class="btn btn-primary" type="submit" name="action">Ajouter
    </button>
    </form>
  </div>
        