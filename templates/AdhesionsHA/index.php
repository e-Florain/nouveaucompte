<h3>
    Adhésions HelloAsso
</h3>

<div id="results">
<table class="table-striped table">
    <tr>
        <th>Date</th>
        <th>Payeur</th>
        <th>Email</th>
        <th>Montant</th>
        <th>Odoo ?</th>
        <th></th>
    </tr>

    <?php foreach ($list_payments['data'] as $payment): ?>
    <tr>
        <!--<td>
            <?php echo $payment['id']; ?>
        </td>-->
        <td>
            <?php echo $payment['date2']; ?>
        </td>
        <td>
            <?php echo $payment['payer']['lastName']." ".$payment['payer']['firstName']; ?>
        </td>
        <td>
            <?php echo $payment['payer']['email']; ?>
        </td>
        <td>
            <?php echo $payment['amount']/100; ?>
        </td>
        <td>
            <?php if ($payment['inodoo']) {
                echo '<i class="bi bi-check"></i>';
            }
            
            ?>
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