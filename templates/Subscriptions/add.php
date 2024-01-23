<br>
  <h3>Ajouter un prélèvement</h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="client_id" class="form-label">Client</label>
          <select class="form-select" name="client_id" >
            <option value="" disabled selected>Choisir un client</option>
              <?php foreach ($customers as $customer) {
              echo '<option value="'.$customer['email'].'" >'.$customer['name'].' - '.$customer['email'].'</option>';
              }
              ?>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="description" class="form-label">Fréquence</label>
          <select class="form-select" name="interval" >
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
          <label for="times" class="form-label">Nombre de prélèvements</label>
          <input class="form-control" name="times" id="times" type="text" required class="validate" placeholder="Nombre de prélèvements">
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
        