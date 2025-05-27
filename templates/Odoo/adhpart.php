<h3>Utilisateurs</h3>
<br>
<p class="d-inline-flex gap-1">
  <a href="/odoo/adhpart" class="btn btn-primary active" role="button">Adh Part</a>
  <a href="/odoo/adhpro" class="btn btn-primary" role="button">Adh Pro</a>
</p>

<div class="mb-3">
    <input type="search" class="form-control" list="datalistOptions" id="search" placeholder="Chercher ...">
</div>

<table class="table" id="table-adhpart">
    <thead>
        <tr>
            <th>Num</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date de fin d'adhésion</th>
            <th>Adhésion valide</th>
        </tr>
    </thead>
    <tbody>
<?php 
    //var_dump($bdcs);
    foreach ($listadhs as $adh): ?>
    <tr>
        <td>
            <?= $adh['ref'] ?>
        </td>
        <td>
            <?= $adh['lastname'] ?>
        </td>
        <td>
            <?= $adh['firstname'] ?>
        </td>
        <td>
            <?= $adh['email'] ?>
        </td>
        <td>
            <?= $adh['membership_stop'] ?>
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