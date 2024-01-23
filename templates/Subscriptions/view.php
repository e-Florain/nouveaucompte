<h3>
    <?php echo 'Prélèvement '.$subscription['id']; ?>
</h3>

<table class="table">
    <tr>
        <td>Description</td>
        <td>
            <?php
                echo $subscription['description'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Nom du client</td>
        <td>
            <?php
                echo $list_customers[$subscription['customerId']]['name'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Email du client</td>
        <td>
            <?php
                echo $list_customers[$subscription['customerId']]['email'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Montant</td>
        <td>
            <?php
                echo $subscription['amount']['value'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Fréquence</td>
        <td>
            <?php
                echo $subscription['interval'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Date de création
        </td>
        <td>
            <?php
                echo $subscription['createdAt'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Date de début de prélèvement
        </td>
        <td>
            <?php
                echo $subscription['startDate'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Date du prochain prélèvement</td>
        <td>
            <?php
                echo $subscription['nextPaymentDate'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Nombre de prélèvements</td>
        <td>
            <?php
                echo $subscription['times'];
            ?>
        </td>
    </tr>
    <tr>
        <td>Id du mandat</td>
        <td>
            <?php
                echo $subscription['mandateId'];
            ?>
        </td>
    </tr>
</table>
