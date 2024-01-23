<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE
        <?php echo $step."/".$nbsteps; ?>
    </h6>
</div>

<div class="row">
    <div class="col-sm-10 offset-m2">
        <h4>Résumé de votre demande de création :</h4>
        <?php
        foreach($results['infos'] as $key => $info) {
            echo "<div class='row'>";
            if($key != "account_cyclos") {
                echo "<div class='col-sm-4'><b>".$results['translates'][$key]."</b></div>";
                if($key == 'orga_choice') {
                    /*foreach($assos as $asso) {
                        if($asso['id'] == $nvocompte->$key) {*/
                    echo "<div class='col-sm-4'>".$assoname."</div>";
                    /*    }
                    }*/
                } elseif ($key == 'email') {
                    echo "<div class='col-sm-4'>".$results[$key]."</div>";
                } else {
                    if ($info == 't') {
                        echo "<div class='col-sm-4'>Oui</div>";
                    } elseif ($info == 'f') {
                        echo "<div class='col-sm-4'>Non</div>";
                    } else {
                        echo "<div class='col-sm-4'>".$results['infos'][$key]."</div>";
                    }
                    
                }
            }
            echo "</div>";
        }
        ?>
    </div>
</div>

<br>
<div class="row">
    <div class="col offset-s2">
        <?php if ($nvocompte->todo == 'update') { ?>
        <a href="/nouveaucompte/updateadh" class="btn-primary btn">Confirmer</a>
        <?php }  else {?>
            <h4>Un mail de confirmation vous a été envoyé. Pour activer votre compte, merci de cliquer sur le lien présent dans ce mail.</h4>
        <?php } ?>
    </div>
</div>