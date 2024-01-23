<br>
  <h3>Editer un mandat</h3>
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
      <div class="row">
        <div class="input-field col s6">
          <!--<input name="asso" id="asso" required type="text" class="validate">-->
          <select name="client_id" >
            <option value="" disabled selected>Choisir un client</option>
            <?php var_dump($mandate); ?>
              <?php foreach ($customers as $customer) {
              echo '<option value="'.$customer['email'].'" >'.$customer['name'].' - '.$customer['email'].'</option>';
              }
              ?>
          </select>
          <label for="client">Client</label>
        </div>
      </div>
      <div class="row">
        <div class="input-field col s6">
            <input type="text" id="iban" name="iban" required="required" class="form-control" placeholder="____ ____ ____ ____ ____ ____ ___" maxlength="33">
            <span id="helper-iban" class="helper-text" data-error="IBAN non valide" data-success="valide"></span>
            <label for="iban">IBAN</label>
        </div>
      </div>
    <button class="btn waves-effect waves-light" type="submit" name="action">Ajouter
    <i class="material-icons right">add</i>
    </button>
    </form>
  </div>
        