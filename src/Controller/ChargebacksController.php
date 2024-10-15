<?php
// src/Controller/ChargebacksController.php

namespace App\Controller;

class ChargebacksController extends AppController
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
        $chargebacks = $mollie->list_chargebacks($from);
        $list_customers = array();
        $customers = $mollie->get_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $list_chargebacks = $chargebacks['_embedded']['chargebacks'];
        if (isset($chargebacks['_links']['next'])) {
            $href= $chargebacks['_links']['next']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $nextfrom=$matches[1];
            }
            $this->set(compact('nextfrom'));
        }
        if (isset($chargebacks['_links']['previous'])) {
            $href= $chargebacks['_links']['previous']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $prevfrom=$matches[1];
            }
            $this->set(compact('prevfrom'));
        }
        $list_payments = array();
        foreach ($list_chargebacks as $chargeback) {
            $payment = $mollie->get_payment_by_id($chargeback['paymentId']);
            $list_payments[$payment['id']] = $payment;
        }
        /*$payments = $mollie->get_all_payments();
        foreach ($payments as $payment) {
            $list_payments[$payment['id']] = $payment;
        }*/
        $this->set(compact('list_payments'));
        $nbchargebacks = count($list_chargebacks);
        $this->set(compact('nbchargebacks'));
        $this->set(compact('list_chargebacks'));
        $this->set(compact('list_customers'));
    }
}