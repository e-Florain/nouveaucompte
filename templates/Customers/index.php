<!-- File: templates/customers/index.php -->
<br>
<?php if ($role == "root") { ?>
    <h1><a class="btn-floating btn-large waves-effect waves-light btn-pink" href="/customers/add"><i class="bi bi-person-add"></i></a></h1>
<?php } ?>
<div class="mb-3">
    <input type="search" class="form-control" list="datalistOptions" id="search" placeholder="Chercher ...">
</div>
<h3>
    <div id='nbcustomers'>Customers (<?php echo $nbcustomers; ?>)</div>
</h3>

<!--<form class="col s12">
    <div class="row">
        <div class="input-field col s6">
            <i class="material-icons prefix">search</i>
            <input type="text" id="filter_payments_text"></textarea>
            <label for="icon_prefix2"></label>
        </div>
    </div>
</form>-->
<div id="results"></div>
<table class="table-striped table" id="table-customers">
    <tr class="position-sticky">
        <th>Id</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Date</th>
        <th></th>
    </tr>
    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($customers as $customer): ?>
    <tr>
        <td>
            <?php echo $customer['id']; ?>
        </td>
        <td>
            <?php echo $customer['name']; ?>
        </td>
        <td>
            <?php echo $customer['email']; ?>
        </td>
        <td>
            <?php echo $customer['createdAt']; ?>
        </td>
        <td>
            <?php echo '<a href="/customers/edit/'.$customer['id'].'"><i class="bi bi-pen"></i></a>'; ?>
            <?php echo '<a href="/customers/delete/'.$customer['id'].'" onclick="return delete_customer();"><i class="bi bi-trash"></i></a>'; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<center>
<ul class="pagination">
<?php
if (isset($prevfrom)) {
    echo '<li class="waves-effect"><a href="/payments/index/'.$prevfrom.'"><i class="material-icons">chevron_left</i></a></li>';
}
if (isset($nextfrom)) {
    echo '<li class="waves-effect"><a href="/payments/index/'.$nextfrom.'"><i class="material-icons">chevron_right</i></a></li>';
}
?>
</ul>
</center>
