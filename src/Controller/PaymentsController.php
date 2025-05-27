<?php
// src/Controller/PaymentsController.php

namespace App\Controller;

class PaymentsController extends AppController
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
                'index',
                'cb'
            ),
            'admin' => array(
                'index',
                'cb'
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
        //$this->set('role', $role);
        $payments = $mollie->list_payments($from);
        $list_customers = array();
        $customers = $mollie->get_all_customers();
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

    public function cb()
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
        $payments = $mollie->list_payments();
        $list_customers = array();
        $customers = $mollie->get_all_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $list_payments = array();
        foreach ($payments['_embedded']['payments'] as $payment) {
            $result = array();
            if (($payment['method'] == "creditcard") && ($payment['status'] == "paid")) {
                $list_payments[] = $payment;
            }
            
        }
        $nbpayments = count($list_payments);
        $this->set(compact('nbpayments'));
        $this->set(compact('list_payments'));
        $this->set(compact('list_customers'));
        //Debug($results);
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