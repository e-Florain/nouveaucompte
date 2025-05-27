<center>
<img src="https://nouveaucompte.florain.fr/img/logo-monnaie.svg" height="64">
</center>
<br>
Bonjour <?php echo $firstname." ".$lastname; ?>,
<br>
Un compte <?php echo $role; ?> sur moncompte.florain.fr a été créé pour vous.
<br>
Pour l'activer, et configurer le compte, cliquez sur lien suivant qui ne sera valable que pendant 24h.<br>
<br>
<b><font size='4'><a href="<?php echo $this->Url->build([
    'controller' => 'users',
    'action' => 'activate',
    '?' => ['token'=> $token]],
    ['fullBase' => true]
); ?>"><b>Activer mon compte Florain </b></a></font></div>

</b>
<br>
<br>
<i>
Cet email vous a été envoyé par Le Florain, car vous avez créé un compte chez nous. Si vous pensez que cet email vous a été envoyé par erreur, merci de nous le signaler. 
</i>