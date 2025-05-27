<h3>Utilisateurs</h3>
<br>
<p class="d-inline-flex gap-1">
  <a href="/odoo/adhpart" class="btn btn-primary active" role="button">Adh Part</a>
  <a href="/odoo/adhpro" class="btn btn-primary" role="button">Adh Pro</a>
</p>

<div class="mb-3">
    <input type="search" class="form-control" list="datalistOptions" id="search" placeholder="Chercher ...">
</div>

<table class="table" id="table-adhpro">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Adhésion valide</th>
        </tr>
    </thead>
    <tbody>
<?php 
    //var_dump($bdcs);
    foreach ($adhs as $adh): ?>
    <tr>
        <td>
            <?= $adh['name'] ?>
        </td>
        <td>
            <?= $adh['email'] ?>
        </td>
        <td>
            <?php
            if ($adh['membership_state'] == 'old') {
                echo '<i class="bi bi-x"></i>';
            } else {
                echo '<i class="bi bi-check2"></i>';
            }
            ?>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>