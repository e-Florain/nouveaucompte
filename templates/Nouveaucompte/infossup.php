<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE 2/<?php echo $nbsteps; ?></h6>
</div>

<div class="row">
    <div class="col s10 offset-s2">
        <h4><b>Coordonnées</b></h4>
    </div>
</div>


<?php
if ($comptecyclos) { ?>
<form class="col-sm-6" method="post" action="/nouveaucompte/uploadid" onSubmit='return testForm();'>
<?php  } else { ?>
    <form class="col-sm-6" method="post" action="/nouveaucompte/adh" onSubmit='return testForm();'>
<?php } ?>    
    <div class="mb-3">
        <label for="address" class="form-label">Adresse</label>
        <input type="text" class="form-control" id="address" name="address" class="validate" required>
        <ul class="address-feedback position-absolute list-group" style="z-index:1100;"></ul>
    </div>

    <div class="mb-3">
        <label for="postcode" class="form-label">Code postal</label>
        <input type="text" class="form-control" id="postcode" name="postcode" class="validate" required>
    </div>

    <div class="mb-3">
        <label for="city" class="form-label">Ville</label>
        <input type="text" class="form-control" id="city" name="city" class="validate" required>
    </div>
    
    <div class="mb-3">
        <label for="phone" class="form-label">Téléphone portable</label>
        <input type="text" class="form-control" id="phone" name="phone" class="validate" required>
    </div>

    <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
        data-bcup-haslogintext="no">Suivant<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
     </button>
</form>