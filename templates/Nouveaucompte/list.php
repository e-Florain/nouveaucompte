<!-- File: templates/Nouveaucompte/list.php -->
<br>
<br>
<h3>Demandes de compte</h3>
<table class="table">
    <thead>
        <tr>
        <th>Date</th>
        <th>Ref</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Cyclos</th>
        <th>A valider</th>
        <th>Fait</th>
        <th>App.</th>
        <th>Supp.</th>
    </thead>
    <tbody>
    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php 
    //var_dump($bdcs);
    foreach ($nvocomptes as $nvocompte): 
    if ($nvocompte->email != NULL) {
    ?>
    <tr>
        <td>
            <?= $nvocompte->modified ?>
        </td>
        <td>
            <?= $nvocompte->ref ?>
        </td>
        <td>
            <?= $nvocompte->lastname ?>
        </td>
        <td>
            <?= $nvocompte->firstname ?>
        </td>
        <td>
            <?= $nvocompte->email ?>
        </td>
        <td>
            <?php if($nvocompte->account_cyclos) {
                echo '<i class="bi bi-check"></i>';
            }
            ?>
        </td>
        <td>
            <?php if($nvocompte->action_needed) {
                echo '<i class="bi bi-check"></i>';
            }
            ?>
        </td>
        <td>
            <?php if($nvocompte->done) {
                echo '<i class="bi bi-check"></i>';
            }
            ?>
        </td>
        <td>
        </td>
        <td>
        <?php
            echo '<a href="/nouveaucompte/deletedemande/'.$nvocompte->uuid.'" <i class="bi bi-trash"></i></a>';
        ?>
        </td>
    </tr>
    <?php } endforeach; ?>
    </tbody>
</table>
