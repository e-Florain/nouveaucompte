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
    <?= $this->Html->css('bootstrap-icons.min.css'); ?>
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
                    <ul class="navbar-nav">
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/nouveaucompte/list">LISTE DES DEMANDES</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/users/moncompte" onclick="waiting();">MONCOMPTE</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/customers" onclick="waiting();">CLIENTS</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/payments" onclick="waiting();">PAIEMENTS</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/mandates" onclick="waiting();">MANDATS</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/subscriptions" onclick="waiting();">PRELEVEMENTS</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="/chargebacks" onclick="waiting();">CHARGEBACKS</a>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav me-auto mb-1 mb-lg-0">
                    <li class="nav-item"><a href="/users">Utilisateurs</a></li>
                </ul>
                <div class="btn-group" role="group">
                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <?php 
                            $session = $this->getRequest()->getSession(); 
                            echo $session->read('User.name');
                        ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/users/logout">Se déconnecter</a></li>
                    </ul>
                </div>     
            </div>

        </nav>

    </header>

    <main class="flex-shrink-0">
        <div class="container">
            <div class="alertcb alert"></div>
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