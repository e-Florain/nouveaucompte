Bonjour <?= $firstname." ".$lastname; ?>,
<br /> Vous avez demandé l'ouverture d'un compte Florain, et vous êtes déjà adhérent à
l'association
<br /> Veuillez cliquer sur le lien ci-dessous pour vérifier vos informations et
continuer la procédure de création.


<br /> <a href="<?php echo $this->Url->build([
    'controller' => 'nouveaucompte',
    'action' => 'get',
    '?' => ['uuid'=> $uuid]],
    ['fullBase' => true]
); ?>"> Activer mon compte Florain </a>
<br /> Si vous n'êtes pas l'origine de cette demande, veuillez ignorer ce message.
<br /> Le Florain
<br />
<br /> <i>Vous recevez cet email car vous avez accepté de recevoir des informations du
    Florain, ou que vous avez utilisé un de nos services.
    <br /> Vous disposez des droits d'opposition, d'accès, de rectification, d'oubli et
    de portabilité des données qui vous concernent, ainsi que de limitation des finalités
    <br /> Pour exercer ces droits, contactez-nous via la rubrique "Contact"
    <br /> Retrouvez nous sur Florain.fr </i>