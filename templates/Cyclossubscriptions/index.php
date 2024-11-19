<!-- File: templates/subscriptionscyclos/index.php -->
<br>
<?php if ($role == "root") { ?>
    <h1><a href="/cyclossubscriptions/add"><i class="bi bi-plus-circle-fill"></i></a></h1>
<?php } ?>
<br>
<div class="mb-3">
    <input type="search" class="form-control" list="datalistOptions" id="search" placeholder="Chercher ...">
</div>
<h3>
    <div id='nbsubscriptions'>Prélèvements Cyclos (<?php echo $nbsubscriptions; ?>)</div>
</h3>
<div id="results"></div>
<table class="table-striped table" id="table-subscriptions">
    <tr>
        <!--<th>Id</th>-->
        <th>Compte Cyclos Source</th>
        <th>Compte Cyclos Destination</th>
        <th>Description</th>
        <th>Intervalle</th>
        <th>Montant</th>
        <th>Date de démarrage</th>
        <th>Date de prochain prélèvement</th>
        <th></th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($subscriptions as $subscription): ?>
    <tr>
        <td>
            <?php echo $subscription['account_cyclos_src']; ?>
        </td>
        <td>
            <?php echo $subscription['account_cyclos_dst']; ?>
        </td>
        <td>
            <?php echo $subscription['description']; ?>
        </td>
        <td>
            <?php echo $subscription['sub_interval']; ?>
        </td>
        <td>
            <?php echo $subscription['amount']; ?>
        </td>
        <td>
            <?php echo $subscription['startdate']; ?>
        </td>
        <td>
            <?php echo $subscription['nextpaymentdate']; ?>
        </td>
        <td>
            <?php echo '<a href="/cyclossubscriptions/edit/'.$subscription['id'].'"><i class="bi bi-pen"></i></a>'; ?>
            <?php echo '<a href="/cyclossubscriptions/delete/'.$subscription['id'].'" onclick="return delete_sub();"><i class="bi bi-trash"></i></a>'; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="fixe-table-pagination">
<ul class="pagination">
<?php
if (isset($prevfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/cyclossubscriptions/index/'.$prevfrom.'"><i class="bi bi-chevron-left"></i></a></li>';
}
if (isset($nextfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/cyclossubscriptions/index/'.$nextfrom.'"><i class="bi bi-chevron-right"></i></a></li>';
}
?>
</ul>
</div>