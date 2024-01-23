<div class="row">
    <h2 class="mt-5">OUVERTURE DE VOTRE COMPTE FLORAIN <?php echo $titre; ?></h2>
</div>
<div class="row">
    <h6 class="mt-5">ETAPE
        <?php echo $step . "/" . $nbsteps; ?>
    </h6>
</div>

<div class="row">
    <div class="col-sm-10 offset-s2">
        <h4>Choix de l'association</h4>
    </div>
</div>

<div class="row">
    <div class="col s12 m6 offset-m2 change">
        Quand une personne adhère au Florain, elle choisit une association locale, membre du Florain, qu’elle souhaite
        soutenir. Ainsi, à chaque change d’euros en florains, un don équivalent à 1% de la somme changée sera versé à
        l'association retenue. Par exemple, en changeant 300 euros tout au long de l'année, je recevrai 300 florains, et
        l'association que j'ai choisie recevra 3 florains en fin d'année !
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-8 offset-m2">
        <form method="post" action="/nouveaucompte/fin" onSubmit='return testFormIBAN();'>

            <select class="form-select" id="orga_choice" name="orga_choice">
                <option value="" disabled selected>Choisir</option>
                <?php

                
                //$assos = json_decode($assosjson, true);
                foreach ($assosjson as $asso) {
                    if ($nvocompte->orga_choice != NULL) {
                        if ($nvocompte->orga_choice == $asso['id']) {
                            if ($asso['detailed_activity'] != "") {
                                echo '<option value="' . $asso['id'] . '" selected >' . $asso['name'] . ' / ' . $asso['detailed_activity'] . '</option>';
                            } else {
                                echo '<option value="' . $asso['id'] . '" selected >' . $asso['name'] . '</option>';
                            }

                        } else {
                            if ($asso['detailed_activity'] != "") {
                                echo '<option value="' . $asso['id'] . '">' . $asso['name'] . ' / ' . $asso['detailed_activity'] . '</option>';
                            } else {
                                echo '<option value="' . $asso['id'] . '">' . $asso['name'] . '</option>';
                            }
                        }
                    } else {
                        if ($asso['detailed_activity'] != "") {
                            echo '<option value="' . $asso['id'] . '">' . $asso['name'] . ' / ' . $asso['detailed_activity'] . '</option>';
                        } else {
                            echo '<option value="' . $asso['id'] . '">' . $asso['name'] . '</option>';
                        }
                    }
                }
                ?>
            </select>

            <br>

            <button type="submit" id="form_step1" name="form_step1" class="btn-primary btn"
                data-bcup-haslogintext="no">Suivant
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z" />
                    <path fill-rule="evenodd"
                        d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </button>
        </form>

    </div>
</div>