<!-- File: templates/subscriptions/index.php -->
<br>
<?php if ($role == "root") { ?>
    <h1><a href="/subscriptions/add"><i class="bi bi-plus-circle-fill"></i></a></h1>
<?php } ?>
<br>
<h3>
    <div id='nbsubscriptions'>Prélèvements (<?php echo $nbsubscriptions; ?>)</div>
</h3>
<!--
<form class="col s12">
    <div class="row">
        <div class="input-field col s6">
            <i class="material-icons prefix">search</i>
            <input type="text" id="filter_subscriptions_text"></textarea>
            <label for="icon_prefix2"></label>
        </div>
    </div>
</form>-->
<div id="results">
<table class="table-striped table">
    <tr>
        <th>Id</th>
        <th>Description</th>
        <th>Montant</th>
        <th>Intervale</th>
        <th>Status</th>
        <th>Nom</th>
        <th>Email</th>
        <th></th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($subscriptions as $subscription): ?>
    <tr>
        <td>
            <?php echo '<a href="/subscriptions/view/'.$subscription['customerId'].'/'.$subscription['id'].'" >'.$subscription['id'].'</a>'; ?>
        </td>
        <td>
            <?php echo $subscription['description']; ?>
        </td>
        <td>
            <?php echo $subscription['amount']['value']; ?>
        </td>
        <td>
            <?php echo $subscription['interval']; ?>
        </td>
        <td>
            <?php echo $subscription['status']; ?>
        </td>
        <td>
            <?php echo $list_customers[$subscription['customerId']]['name']; ?>
        </td>
        <td>
            <?php echo $list_customers[$subscription['customerId']]['email']; ?>
        </td>
        <td>
            <?php echo '<a href="/subscriptions/edit/'.$subscription['customerId'].'/'.$subscription['id'].'" ><i class="bi bi-pen"></i></a>'; ?>
            <?php echo '<a href="/subscriptions/delete?subscription_id='.$subscription['id'].'&customer_id='.$subscription['customerId'].'"><i class="bi bi-trash"></i></a>'; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="fixe-table-pagination">
<ul class="pagination">
<?php
if (isset($prevfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/subscriptions/index/'.$prevfrom.'"><i class="bi bi-chevron-left"></i></a></li>';
}
if (isset($nextfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/subscriptions/index/'.$nextfrom.'"><i class="bi bi-chevron-right"></i></a></li>';
}
?>
</ul>
</div>