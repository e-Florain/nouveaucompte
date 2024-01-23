<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE 1/<?php echo $nbsteps; ?></h6>
</div>

<div class="row">
    <div class="col s10 offset-s2">
        <h4><b>Identité</b></h4>
    </div>
</div>


<form class="col-sm-4" method="post" action="/nouveaucompte/infossup">
    <div class="mb-3">
        <label for="first_name" class="form-label">Prénom</label>
        <input type="text" class="form-control" id="first_name" name="first_name" class="validate" required>
    </div>

    <div class="mb-3">
        <label for="last_name" class="form-label">Nom</label>
        <input type="text" class="form-control" id="last_name" name="last_name" class="validate" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" class="validate" required aria-describedby="emailHelp">
    </div>
    
    <p>
        <label>
            <input type="checkbox" required />
            <span>Je certifie avoir plus de 18 ans</span>
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" required />
            <?php if (!$comptecyclos) {
                echo '<span>J\'ai lu et je valide la charte</b></span>';
            } else {
                echo '<span>J\'ai lu et je valide les <b><a href="https://www.monnaielocalenancy.fr/wp-content/uploads/CGU-Utilisateur-Florain-Oct-2022.pdf" target="_blank">CGU Particuliers</a></b></span>';
            }
            ?>
        </label>
    </p>
    <p>
        <label>
            <input type="checkbox" id="accept_newsletter" name="accept_newsletter" />
            <span>J'accepte de recevoir la newsletter du Florain</span>
        </label>
    </p>
    <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
        data-bcup-haslogintext="no">Suivant</button>
</form>