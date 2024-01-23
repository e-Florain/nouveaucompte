<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN</h2>
</div>

<div class="row">
    <div class="col-sm-6">
        <?php
        if (isset($adh)) {
            if ($adh['account_cyclos'] == 't') {
        ?>
            <h4>Vous avez déjà un compte florain numérique !</h4>
        <?php
            } else {
        ?>
            <h4>Nous venons de vous envoyer un mail, vous allez recevoir un lien qui va vous permettre de créer votre compte florain .</h4>
        <?php 
            } 
        } else {
            ?>
                <h4>Vous n'êtes pas encore adhérent ! </h4>
                <h4>Pour créer un compte Florain numérique, cliquez <a href="/nouveaucompte/infos" class="btn-primary btn">ICI</a></h4>
            <?php
        }
        ?>
    </div>    
</div>
