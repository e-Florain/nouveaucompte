<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE 4/
        <?php echo $nbsteps; ?>
    </h6>
</div>

<?php if ($res['result']) { ?>
    <div class="row">
        <div class="col s10 offset-m2">
            <h4>Votre pièce d'identité a été validée.</h4>
        </div>
    </div>

<div class="row">
    <div class="col offset-s2">
        <a href="/nouveaucompte/adh"  class="btn-primary btn">Suivant</a>
    </div>
</div>

<?php } ?>