<h3>
    Dons HelloAsso
</h3>

<div id="results">
<table class="table-striped table">
    <tr>
        <th>Date</th>
        <th>Payeur</th>
        <th>Email</th>
        <th>Montant</th>
        <th></th>
    </tr>

    <?php foreach ($list_dons['data'] as $don): ?>
    <tr>
        <!--<td>
            <?php echo $don['id']; ?>
        </td>-->
        <td>
            <?php echo $don['date2']; ?>
        </td>
        <td>
            <?php echo $don['payer']['lastName']." ".$don['payer']['firstName']; ?>
        </td>
        <td>
            <?php echo $don['payer']['email']; ?>
        </td>
        <td>
            <?php echo $don['amount']/100; ?>
        </td>
        <td>
            <?php 
                //echo $list_customers[$list_payments[$chargeback['paymentId']]['customerId']]['name'];
                //echo $list_customers[$chargeback['customerId']]['name'];
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>