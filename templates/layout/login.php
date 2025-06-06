<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'Nouveaucompte';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    
    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('my.css') ?>
    <?= $this->Html->css('bdc.css'); ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>



<body class="d-flex flex-column h-100">
    <header>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md fixed-top">
        <div class="spinner-border invisible" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>

        <img src="/img/logo-monnaie.svg" height="64"><a href="/nouveaucompte" class="brand-logo">Florain</a>        
        <div class="container-fluid">
        <!--<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>-->
        <div class="collapse navbar-collapse" id="navbarCollapse">
        </div>
        </div>
    </nav>
    </header>

    <main class="flex-shrink-0">
        <div class="container">
            <br>
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 ">
        <div class="container">
            
        </div>
    </footer>
    <?= $this->Html->script('jquery-3.7.1.min'); ?>
    <?= $this->Html->script('bootstrap.bundle.min'); ?>
    <?= $this->Html->script('my'); ?>
</body>
</html>
