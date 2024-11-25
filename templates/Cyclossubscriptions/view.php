<br>
  <h2>Visualiser un prélèvement cyclos</h2>
  <div class="row">
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
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->
    
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
    </tr>
  </table>

  <h3>Liste des paiements cyclos</h3>
  <table class="table-striped table" id="table-payments">
    <tr>
        <!--<th>Id</th>-->
        <th>Id</th>
        <th>TransactionNumber</th>
        <th>Date</th>
        <th>Montant</th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->
    <?php foreach ($transactions as $transaction): ?>
    <tr>
        <td>
            <?php echo $transaction['id']; ?>
        </td>
        <td>
            <?php echo $transaction['transactionNumber']; ?>
        </td>
        <td>
            <?php echo $transaction['date']; ?>
        </td>
        <td>
            <?php echo $transaction['amount']; ?>
        </td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>