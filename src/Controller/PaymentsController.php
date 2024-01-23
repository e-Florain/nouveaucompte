<?php
// src/Controller/PaymentsController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\FrozenTime;
use Cake\Http\Client;
use Cake\Datasource\Paging\NumericPaginator;

class PaymentsController extends AppController
{

    public function index($from="")
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $mollie = $this->fetchTable('Mollie');
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        if (!isset($email)) {
            return $this->redirect(['action' => 'logout']);
        }
        $users = $this->fetchTable('Users');
        $role = $users->getRole($email);
        if (($role != "root") or ($role == "admin")) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->set('role', $role);
        $payments = $mollie->list_payments($from);
        $list_customers = array();
        $customers = $mollie->get_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $list_payments = $payments['_embedded']['payments'];
        if (isset($payments['_links']['next'])) {
            $href= $payments['_links']['next']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $nextfrom=$matches[1];
            }
            $this->set(compact('nextfrom'));
        }
        if (isset($payments['_links']['previous'])) {
            $href= $payments['_links']['previous']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $prevfrom=$matches[1];
            }
            $this->set(compact('prevfrom'));
        }
        $nbpayments = count($list_payments);
        $this->set(compact('nbpayments'));
        $this->set(compact('list_payments'));
        $this->set(compact('list_customers'));
        //var_dump($list_customers);
    }

    /*public function onepercent()
    {
        $mollie = $this->fetchTable('Mollie');
        $adh = $this->fetchTable('Adhesions');
        //$res = $mollie->onepercent();
        $listpaymentsbyassos = $mollie->onepercent();
        $assos = $adh->getOdooAssos();
        $listassos = array();
        foreach ($assos as $asso) {
            $listassos[$asso['id']] = $asso['name'];
        }
        $this->set(compact('listpaymentsbyassos'));
        $this->set(compact('listassos'));
    }*/
}