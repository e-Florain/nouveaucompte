<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN</h2>
</div>
<div class="row">
    <h3><b>A quoi ça sert ?</b><h3>
    <div class="col-md-8 offset-md-1">
        <h5>Le compte Florain numérique permet de faire des paiements dématérialisés en Florains, grâce à un compte
        personnel en ligne utilisable sur téléphone ou ordinateur.</h5>
    </div>
</div>

<div class="row">
    <h3><b>Est-ce obligatoire ?</b></h3>
    <div class="col-md-8 offset-md-1">
        <h5>Non, il n'est pas obligatoire d'utiliser la monnaie numérique pour payer en Florain. Les billets continuent à
        exister, et il sera possible d'utiliser les deux moyens de paiement, ou bien un seul (billets ou numérique) si
        vous le préférez.
        <br><br>
        Si vous souhaitez (ré)adhérer par prélèvement automatique annuel mais sans compte numérique et en continuant à utiliser uniquement les billets,
        <!--Si vous souhaitez adhérer et utiliser uniquement des florains en version papier, rendez vous dans un bureau de change ou -->

        <button type="button" class="btn btn-primary">
            <?php echo $this->Html->link("Cliquez ici", [
                'controller' => 'nouveaucompte',
                'action' => 'infos',
                '?' => ['comptecyclos' => "false"]
            ]); ?>
        </button>
        </h5>
    </div>
</div>

<br>

<div class="row">
    <h3><b>Quelles sont les conditions pour ouvrir un compte Florain numérique ?</b></h3>
    <div class="col-md-8 offset-md-1">
        <div class="ul-fl">
            <li><h5>Être un particulier (Pour les professionnels, nous contacter : <a
                    href="https://www.monnaielocalenancy.fr/nous-rejoindre/professionnels/" target="_blank">
                    http://www.florain.fr/nous-rejoindre/professionnels/ </a></h5></li>
            <li><h5>Être majeur</h5></li>
            <li><h5>Mettre en place une <b>adhésion</b> annuelle par prélèvement automatique (Si vous êtes déjà adhérent,
                elle prendra effet à la fin de votre période d'adhésion en cours)</h5></li>
            <li><h5>Mettre en place un <b>change</b> mensuel automatique (d’un minimum de 20€). Pratique pour avoir des
                florains sur votre compte, sans passer par un comptoir de change.</h5></li>
            <li><h5>Signer un mandat de <b> prélèvement SEPA </b> (pour l’adhésion et le change mensuel)</h5></li>
            <li><h5>Fournir une <b>pièce d’identité</b></h5></li>
        </div>
    </div>
</div>

<br>

<div class="row">
    <h3><b>Vous êtes prêt ? (Durée moyenne de la procédure : 4 minutes)</b></h3>
    <div class="col-md-8 offset-md-1">
        <h5>Pour débuter la création de votre compte, précisez nous si vous êtes déjà adhérent ou non.</h5>
    </div>
</div>

<br>

<div class="row">
    <h3><b>Adhérer au Florain</b></h3>
            <div class="input-field col s12">
                <button type="button" class="btn btn-primary">
                <?php echo $this->Html->link("Cliquez ici", [
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
            <h3><b>Je suis déjà adhérent, ouvrir mon compte en ligne</b></h3>
            <div class="input-field col s12">
                <button type="button" class="btn btn-primary">
                <?php echo $this->Html->link("Cliquez ici", [
                    'controller' => 'nouveaucompte',
                    'action' => 'dejacompte'
                ]); ?>
                </button>
            </div>
        </div>
    </div>
</div>