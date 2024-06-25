<!-- File: templates/payments/index.php -->
<br>
<h3>
    <div id='nbchargebacks'>Chargebacks (<?php echo $nbchargebacks; ?>)</div>
</h3>

<!--<form class="col s12">
    <div class="row">
        <div class="input-field col s6">
            <i class="material-icons prefix">search</i>
            <input type="text" id="filter_payments_text"></textarea>
            <label for="icon_prefix2"></label>
        </div>
    </div>
</form>-->
<div id="results">
<table class="table-striped table">
    <tr>
        <th>Date</th>
        <th>Description</th>
        <th>Montant</th>
        <th>Client</th>
        <th></th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($list_chargebacks as $chargeback): ?>
    <tr>
        <!--<td>
            <?php echo $chargeback['id']; ?>
        </td>-->
        <td>
            <?php echo $chargeback['createdAt']; ?>
        </td>
        <td>
            <?php echo $chargeback['reason']['description']; ?>
        </td>
        <td>
            <?php echo $chargeback['amount']['value']; ?>
        </td>
        <td>
            <?php 
                echo $list_customers[$list_payments[$chargeback['paymentId']]['customerId']]['name'];
                //echo $list_customers[$chargeback['customerId']]['name'];
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="fixe-table-pagination">
<div class="float-center pagination">
<ul class="pagination">
<?php
if (isset($prevfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/chargebacks/index/'.$prevfrom.'"><i class="bi bi-chevron-left"></i></a></li>';
}
if (isset($nextfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/chargebacks/index/'.$nextfrom.'"><i class="bi bi-chevron-right"></i></a></li>';
}
?>
</ul>
</div>
</div>