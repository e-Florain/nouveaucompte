Bonjour,
une nouvelle <?php if ($todo=="create") {echo "adhésion";} if ($todo=="update") {echo "re-adhésion";}  if (isset($account_cyclos)) { ?> avec un compte numérique <?php } ?> a été enregistrée :<br>
Nom : <?php echo $lastname; ?><br>
Prénom : <?php echo $firstname; ?><br>
Mail : <?php echo $email; ?><br>
Adresse : <?php echo $street; ?><br>
Code postal : <?php echo $zip; ?><br>
Ville : <?php echo $city; ?><br>
Montant de l'adhésion : <?php  if (isset($nbeurosadhannuel)) { echo $nbeurosadhannuel." par an"; }
    if (isset($nbeurosadhmensuel)) { echo $nbeurosadhmensuel." par mois"; } ?><br>
<?php if (isset($account_cyclos)) {
echo "Change mensuel : ".$changeeuros."<br>";    
}
?>