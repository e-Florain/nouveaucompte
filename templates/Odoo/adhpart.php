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
        <th><?= $this->Html->link("Num", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "ref"]
        ]); ?>
        </th>
        <th><?= $this->Html->link("Nom", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "lastname"]
        ]); ?>
        </th>
        <th><?= $this->Html->link("Prénom", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "firstname"]
        ]); ?>
        </th>
        <th><?= $this->Html->link("Email", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "email"]
        ]); ?>
        </th>
        <th><?= $this->Html->link("Date de fin d'adhésion", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "membership_stop"]
        ]); ?>
        <th><?= $this->Html->link("Adhésion valide", [
            'controller' => 'odoo',
            'action' => 'adhpart',
            '?' => ['orderby' => "membership_state"]
        ]); ?>
        </th>
    <!--</tr>
        <tr>
            <th>Num</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date de fin d'adhésion</th>
            <th>Adhésion valide</th>
        </tr>-->
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