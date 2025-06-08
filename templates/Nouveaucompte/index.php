<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN</h2>
</div>

<h3><b>Je souhaite adhérer au Florain</b></h3>
<div class="row">
    <h2><b>Je souhaite utiliser des florains papiers ET numériques </b></h2>
    <div class="input-field col s12">
        <button type="button" class="btn btn-primary">
            <?php echo $this->Html->link("Adhérer + ouvrir mon compte en ligne (Cyclos)", [
                'controller' => 'nouveaucompte',
                'action' => 'infos',
                '?' => ['comptecyclos' => 'true']
            ]); ?>
        </button>
    </div>
</div>

<br>
<div class="row">
    <div class="col s12">
        <h5>OU</h5>
    </div>
</div>
<br>


<div class="row">
    <h2><b>Je souhaite utiliser des florains papiers UNIQUEMENT</b></h2>
    <div class="input-field col s12">
        <button type="button" class="btn btn-primary">
            <?php echo $this->Html->link("Adhérer par prélèvement SEPA", [
                'controller' => 'nouveaucompte',
                'action' => 'infos',
                '?' => ['comptecyclos' => "false"]
            ]); ?>
        </button>
    </div>
    <a href="https://florain.fr/adhesion" target="_blank"> Adhérer par paiement CB </a>
    <br>
    <a href="https://florain.fr/contact" target="_blank"> J'ai reçu une carte cadeau : contactez nous </a>
</div>


<h3><b>Je suis déjà adhérent au Florain</b></h3>
<div class="row">
    <h2><b>Je suis déjà adhérent, activer mon compte en ligne</b></h2>
    <div class="input-field col s12">
        <button type="button" class="btn btn-primary">
            <?php echo $this->Html->link("Activer mon compte en ligne (déjà adhérent)", [
                'controller' => 'nouveaucompte',
                'action' => 'dejacompte'
            ]); ?>
        </button>
    </div>
</div>



<div class="row">
    <h3><b>Quelles sont les conditions pour ouvrir un compte Florain numérique ?</b></h3>
    <div class="col-md-8 offset-md-1">
        <div class="ul-fl">
            <li>
                <h5>Être un particulier (Pour les professionnels, nous contacter : <a
                        href="https://www.monnaielocalenancy.fr/nous-rejoindre/professionnels/" target="_blank">
                        http://www.florain.fr/nous-rejoindre/professionnels/ </a></h5>
            </li>
            <li>
                <h5>Être majeur</h5>
            </li>
            <li>
                <h5>Mettre en place une <b>adhésion</b> annuelle par prélèvement automatique (Si vous êtes déjà adhérent,
                    elle prendra effet à la fin de votre période d'adhésion en cours)</h5>
            </li>
            <li>
                <h5>Mettre en place un <b>change</b> mensuel automatique (d’un minimum de 20€). Pratique pour avoir des
                    florains sur votre compte, sans passer par un comptoir de change.</h5>
            </li>
            <li>
                <h5>Signer un mandat de <b> prélèvement SEPA </b> (pour l’adhésion et le change mensuel)</h5>
            </li>
            <li>
                <h5>Fournir une <b>pièce d’identité</b></h5>
            </li>
        </div>
    </div>
</div>

<br>

