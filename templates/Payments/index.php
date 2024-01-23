<!-- File: templates/payments/index.php -->
<br>
<h3>
    <div id='nbpayments'>Paiments (<?php echo $nbpayments; ?>)</div>
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
        <th>Status</th>
        <th>Client</th>
        <th></th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($list_payments as $payment): ?>
    <tr>
        <!--<td>
            <?php echo $payment['id']; ?>
        </td>-->
        <td>
            <?php echo $payment['createdAt']; ?>
        </td>
        <td>
            <?php echo $payment['description']; ?>
        </td>
        <td>
            <?php echo $payment['amount']['value']; ?>
        </td>
        <td>
            <?php echo $payment['status']; ?>
        </td>
        <td>
            <?php 
            if (isset($payment['customerId'])) {
                echo $list_customers[$payment['customerId']]['name'];
            } else {
                echo 'None';
            }
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
    echo '<li class="page-item"><a class="page-link" href="/payments/index/'.$prevfrom.'"><i class="bi bi-chevron-left"></i></a></li>';
}
if (isset($nextfrom)) {
    echo '<li class="page-item"><a class="page-link" href="/payments/index/'.$nextfrom.'"><i class="bi bi-chevron-right"></i></a></li>';
}
?>
</ul>
</div>
</div>