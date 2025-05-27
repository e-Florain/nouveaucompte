<?php
// src/Controller/ChargebacksController.php

namespace App\Controller;

class ChargebacksController extends AppController
{
    public function whoami() 
    {
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        if (!isset($email)) {
            return $this->redirect(['action' => 'logout']);
        }
        $users = $this->fetchTable('Users');
        $role = $users->getRole($email);
        return $role;
    }

    public function iamauthorized($action, $role)
    {
        $authorizations = array(
            'root' => array(
                'index'
            ),
            'admin' => array(
                'index'
            ),
            'user' => array(
            )
        );
        return (in_array($action, $authorizations[$role]));
    }

    public function getLayout($role)
    {
        switch($role)
        {
            case 'root':
                return 'bdc';
            case 'admin':
                return 'bdc';
            case 'benevole':
                return 'benevole';
            default:
                return 'userstd';
        }

    }

    public function index($from="")
    {
        $this->Authorization->skipAuthorization();
        $mollie = $this->fetchTable('Mollie');
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->set('role', $role);
        $chargebacks = $mollie->list_chargebacks($from);
        $list_customers = array();
        $customers = $mollie->get_all_customers();
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