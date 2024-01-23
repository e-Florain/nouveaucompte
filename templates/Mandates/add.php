<br>
  <h3>Ajouter un mandat</h3>
  <div class="row">
    <?php echo $this->Form->create(); ?>
    <?php
    echo $this->Form->create(null, [
        'url' => [
            'onSubmit' => 'return testFormEtape7();'
        ]
    ]);
    ?>
    <input name="id" id="id" type="hidden" class="validate">
      <div class="mb-3 row">
        <div class="col-sm-4">
          <label for="client_id" class="form-label">Client</label>
          <select class="form-control" id="client_id" name="client_id" >
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
            <label for="iban" class="form-label">IBAN</label>
            <input type="text" id="iban" name="iban" required="required" class="form-control" placeholder="____ ____ ____ ____ ____ ____ ___" maxlength="33">
            <span id="helper-iban" class="helper-text" data-error="IBAN non valide" data-success="valide"></span>
        </div>
      </div>

    <button class="btn btn-primary" type="submit" name="action">Ajouter
    </button>

    </form>
  </div>
        