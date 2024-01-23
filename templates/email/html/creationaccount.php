Bonjour
<?= $firstname . " " . $lastname; ?>,
<br /> Vous avez demandé l'ouverture d'un compte Florain
<?php if ($account_cyclos == 't') { ?> numérique
<?php } ?>, avec les informations suivantes :<br>
Nom :
<?php echo $lastname; ?><br>
Prénom :
<?php echo $firstname; ?><br>
Adresse :
<?php echo $street; ?><br>
Code postal : :
<?php echo $zip; ?><br>
Ville :
<?php echo $city; ?><br>
Tel :
<?php echo $phone; ?><br>
<?php if (isset($nbeurosadhannuel)) { ?> Adhésion annuelle :
    <?php echo $nbeurosadhannuel . " €<br>";
} ?>
<?php if (isset($nbeurosadhmensuel)) { ?> Adhésion mensuelle :
    <?php echo $nbeurosadhmensuel . " €<br>";
} ?>
<?php if (isset($changeeuros)) { ?> Change mensuel :
    <?php echo $changeeuros . " €";
} ?>
<br>
Nom de l'association choisie :
<?php echo $assoname; ?><br>

<br />
<br /> En cliquant sur ce lien (valable 15 minutes) pour finaliser la procédure de création de compte, vous certifiez
que vous souhaitez :
<ul>
    <?php if (isset($comptecyclos)) { ?>
        <li> Créer un compte numérique Florain </li>
    <?php } else { ?>
        <li> Créer un compte Florain </li>
    <?php } ?>

    <li> Mettre en place une adhésion automatique </li>
    <?php if (isset($comptecyclos)) { ?>
        <li> Mettre en place un change mensuel automatique </li>
    <?php } ?>
    <li>Signer un mandat de prélèvement SEPA qui autorise l'association Le Florain à prélever votre compte.</li>
</ul>

<br/> <div style='text-align: center;'><b><font size='4'><a href="<?php echo $this->Url->build([
    'controller' => 'nouveaucompte',
    'action' => 'activate',
    '?' => ['uuid'=> $uuid]],
    ['fullBase' => true]
); ?>"><b>Finaliser mon compte Florain </b></a></font></div>

<br /> Si vous n'êtes pas l'origine de cette demande, veuillez ignorer ce message.
<br /> Le Florain
<br />
<br /> <i>Vous recevez cet email car vous avez accepté de recevoir des informations du Florain, ou que vous avez utilisé un de nos services.
<br /> Vous disposez des droits d'opposition, d'accès, de rectification, d'oubli et de portabilité des données qui vous concernent, ainsi que de limitation des finalités.
<br /> Pour exercer ces droits, contactez-nous via la rubrique "Contact".
<br /> Retrouvez nous sur Florain.fr </i>