Salut<br>
une ou plusieurs réadhésions ont été effectuées :<br>
<?php foreach ($datas as $data) { ?>
    <?php echo "Nom : ".$data['lastname']." ".$data['firstname']; ?><br>
    <?php echo "Email : ".$data['email']; ?><br>
    <?php echo "Montant de l'adhésion : ".$data['amount']." €"; ?><br>
    <?php echo "Adresse : ".$data['street']; ?><br>
    <?php echo "Code postal : ".$data['zip']; ?><br>
    <?php echo "Ville : ".$data['city']; ?><br>
    <?php echo "Association soutenue : ".$data['orga_choice']; ?><br>
    <?php echo "Compte numérique : "; if ($data['account_cyclos']==1) {
         echo "Oui"; } else { echo "Non"; }
    ?><br>
<?php
echo "<hr><br>";
}
?>