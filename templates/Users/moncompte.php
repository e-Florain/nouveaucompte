<h4>MON COMPTE</h4>
<br>
<h5>Obtenir des florains (par CB)</h5>
<form class="col-sm-12" method="post" action="/users/paymentCB">
    <div class="row">
        <div class="col-sm-4">
            <select id="amount" name="amount" class="form-select" required>
                <option value="" disabled selected>Choisisser votre montant</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
    </div>
    <br>
    <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
        data-bcup-haslogintext="no">Valider</button>
</form>
<br>

<h5>Modifier le change mensuel (par prélèvement)</h5>
<table class="table">
    <thead>
        <th>Description</th>
        <th>Montant</th>
        <th>Type</th>
        <th>Prochain prélèvement</th>
    </thead>
    <tbody>

        <tr>
            <td>
                <?php echo '<a href="/users/subscription/'.$subid.'">'.$description.'</a>'; ?>
            </td>
            <td>
                <?= $amount ?>
            </td>
            <td>
                <?= $interval ?>
            </td>
            <td>
                <?= $nextdate ?>
            </td>
        </tr>
    </tbody>
</table>
<br>
<h5>Association soutenue</h5>
<table class="table">
    <thead>
        <th>Nom</th>
    </thead>
    <tbody>
        <tr>
            <td>
                <?= $assoname ?>
            </td>
        </tr>
    </tbody>
</table>
<br>
<h5>Modifier les coordonnées bancaires</h5>
<table class="table">
    <thead>
        <th>IBAN</th>
        <th>Date de création</th>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php echo '<a href="/users/mandate/'.$mandateusr['id'].'">'.$mandateusr['iban'].'</a>'; ?>
            </td>
            <td>
                <?= $mandateusr['signatureDate'] ?>
            </td>
        </tr>
    </tbody>
</table>