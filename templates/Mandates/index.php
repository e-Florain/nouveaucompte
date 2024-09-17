<!-- File: templates/subscriptions/index.php -->
<br>
<?php if ($role == "root") { ?>
    <h1><a class="btn-floating btn-large waves-effect waves-light btn-pink" href="/mandates/add"><i class="bi bi-plus-circle-fill"></i></a></h1>
<?php } ?>
<br>
<div class="mb-3">
    <input type="search" class="form-control" list="datalistOptions" id="search" placeholder="Chercher ...">
</div>
<h3>
    <div id='nbmandates'>Mandates (<?php echo $nbmandates; ?>)</div>
</h3>
<!--
<form class="col s12">
    <div class="row">
        <div class="input-field col s6">
            <i class="material-icons prefix">search</i>
            <input type="text" id="filter_subscriptions_text"></textarea>
            <label for="icon_prefix2"></label>
        </div>
    </div>
</form>-->
<div id="results"></div>
<table class="table-striped table" id="table-mandates">
    <tr>
        <th>Id</th>
        <th>Email</th>
        <th>Nom</th>
        <th>Compte</th>
        <th>Status</th>
        <th></th>
    </tr>

    <?php foreach ($list_mandates as $mandate): ?>
    <tr>
        <td>
            <?php echo $mandate['id']; ?>
        </td>
        <td>
            <?php echo $list_customers[$mandate['customerId']]['email']; ?>
        </td>
        <td>
            <?php echo $mandate['details']['consumerName']; ?>
        </td>
        <td>
            <?php echo $mandate['details']['consumerAccount']; ?>
        </td>
        <td>
            <?php if ($mandate['status'] == 'valid') {
                echo '<i class="bi bi-check2"></i>';
            } else {
               echo  '<i class="bi bi-x"></i>';
            } ?>
        </td>
        <td>
        <?php echo '<a href="/mandates/edit/'.$mandate['customerId'].'/'.$mandate['id'].'" ><i class="bi bi-pen"></i></a>'; ?>
        <?php echo '<a href="/mandates/delete/'.$mandate['customerId'].'/'.$mandate['id'].'"><i class="bi bi-trash"></i></a>'; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
