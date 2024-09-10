<?php
// src/Controller/DashboardController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\DateTime;
use Cake\Http\Client;

class DashboardController extends AppController
{
    public function index($from="")
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $users = $this->fetchTable('Users');
        $role = $users->getRole($email);
        $this->set('role', $role);
        if (($role != "root") or ($role == "admin")) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        $totalchange = $mollie->calculAmountChanges();
        $this->set(compact('totalchange'));
        $totaladhmensuelle = $mollie->calculAdhMensuelle();
        $this->set(compact('totaladhmensuelle'));
        $totaladhannuelle = $mollie->calculAdhAnnuelle();
        $this->set(compact('totaladhannuelle'));

        $florapi = $this->fetchTable('Florapi');
        /* compte cyclos */
        $params=array("account_cyclos" => 't');
        $nbadhscyclos = count($florapi->getAdhs($params));
        $this->set(compact('nbadhscyclos'));
        /* compte adhérent */
        $params=array("membership_state" => 'paid');
        $nbadhspart = count($florapi->getAdhs($params));
        $this->set(compact('nbadhspart'));
        /* compte pros */
        $params=array("account_cyclos" => 't');
        $nbadhproscyclos = count($florapi->getAdhpros($params));
        $this->set(compact('nbadhproscyclos'));
        /* compte adhs */
        $params=array();
        $nbadhs = count($florapi->getAdhs($params));
        $this->set(compact('nbadhs'));
        /* compte adhpros */
        $nbadhpros = count($florapi->getAdhpros($params));
        $this->set(compact('nbadhpros'));

    }
}