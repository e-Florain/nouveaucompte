<div class="row">
    <div class="col-sm-8">
        <h2 class="mt-5">
        <?php
        if ($account_cyclos) {
            echo "OUVERTURE DE VOTRE COMPTE FLORAIN";
        } else {
            echo "OUVERTURE DE VOTRE COMPTE NUMERIQUE FLORAIN";
        }
        ?>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-10">
        <h4>Merci, votre compte a été activé.</h4>
        <br />
        <?php
        if ($account_cyclos) {
            echo "Vous recevrez prochainement vos identifiants pour accéder à l'application sécurisée pour les paiements en Florain numérique : <b>Cyclos</b>";

            ?>
        </div>
        <div style="margin-top: 50px" class="text-center">
            <a href="https://play.google.com/store/apps/details?id=org.cyclos.mobile" target="_blank"><img
                    src="/img/GooglePlay.png"
                    style="font-size: 1rem; width: 179px; height: 60px; border-width: 0px; float: none;" /></a>
            <a href="https://apps.apple.com/fr/app/cyclos-4-mobile/id829007510#?platform=iphone" target="_blank"><img
                    src="/img/AppStore.png"
                    style="font-size: 1rem; width: 179px; height: 60px; border-width: 0px; float: none;" /></a>

        </div>
        <div class="col s6 offset-s2">
            A l'ouverture de l'Appli mobile, saisir l'URL : <a href="https://cyclos.florain.fr" target="_blank">
                https://cyclos.florain.fr </a>
        </div>
        <?php } ?>
    </div>
</div>