<br>
  <h3>Editer le prélèvement <?php echo $subscription['id']; ?></h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="row">
        <div class="form-floating mb-3 col-sm-4">
          <!--<input name="asso" id="asso" required type="text" class="validate">-->
          <select class="form-select" disabled name="client_id" >
            <option value="" disabled selected>Choisir un client</option>
              <?php foreach ($customers as $customer) {
                if ($list_customers[$subscription['customerId']]['email'] == $customer['email']) {
                    echo '<option selected value="'.$customer['email'].'" >'.$customer['name'].' - '.$customer['email'].'</option>';
                } else {
                    echo '<option value="'.$customer['email'].'" >'.$customer['name'].' - '.$customer['email'].'</option>';
                }
              }
              ?>
          </select>
          <label for="client">Client</label>
        </div>
      </div>
      <div class="row">
        <div class="form-floating mb-3 col-sm-4">
          <!--<input name="asso" id="asso" required type="text" class="validate">-->
          <select class="form-select" disabled name="interval" >
            <option value=""  selected>Choisir un fréquence</option>
              <?php 
              if ($subscription['interval'] == "1 month") {
                echo '<option value="monthly" selected >Mensuel</option>';
              } else {
                echo '<option value="monthly">Mensuel</option>';
              }
              if ($customer['interval'] == "365 days") {
                echo '<option value="monthly" selected>Annuel</option>';
              } else {
                echo '<option value="monthly">Annuel</option>';
              }
              ?>
          </select>
          <label for="client">Fréquence</label>
        </div>
      </div>
      <div class="row">
        <div class="form-floating mb-3 col-sm-4">
          <input class="form-control" disabled name="description" id="description" type="text" value="<?php echo $subscription['description']; ?>" required class="validate">
          <label for="description">Description</label>
        </div>
      </div>
      <div class="row">
        <div class="form-floating mb-3 col-sm-4">
          <input class="form-control" name="times" id="times" type="text" value="<?php echo $subscription['times'] ?? "0" ?>" required class="validate">
          <label for="times">Nombre de prélèvements</label>
        </div>
      </div>
      <div class="row">
        <div class="form-floating mb-3 col-sm-4">
          <input class="form-control" name="amount" id="amount" type="text" required value="<?php echo $subscription['amount']['value']; ?>" class="validate">
          <label for="amount">Montant</label>
        </div>
      </div>
    <button class="btn btn-primary" type="submit" name="action">Valider
    </button>
    </form>
  </div>
        