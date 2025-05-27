<?php
// src/Controller/MandatesController.php

namespace App\Controller;

class MandatesController extends AppController
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
                'add',
                'edit',
                'view',
                'delete'
            ),
            'admin' => array(
                'index',
                'add',
                'edit',
                'view',
                'delete'
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

    public function index()
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->set('role', $role);
        $mollie = $this->fetchTable('Mollie');
        $list_customers = array();
        $customers = $mollie->get_all_customers();
        $list_mandates = array();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
            $mandates = $mollie->get_mandates($customer['id']);
            foreach ($mandates as $mandate) {
                $list_mandates[$mandate['id']] = $mandate;
            }
        }
        $nbmandates = count($list_mandates);
        $this->set(compact('nbmandates'));
        $this->set(compact('list_customers'));
        $this->set(compact('list_mandates'));
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $customers = $mollie->get_all_customers();
        $this->set(compact('customers'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $customers = $mollie->get_customer($data['client_id']);
            $customerid = $customers[0]['id'];
            $consumerName = $customers[0]['name'];
            $email = $customers[0]['email'];
            $infos = $mollie->create_mandate($customerid, $data['iban'], $consumerName, $email);
            //var_dump($infos);
            if ($infos['status'] == 422) {
                $this->Flash->error(__('Erreur : Le mandat n\'a pas pu être créé. ' . $infos['detail']));
            } else {
                $this->Flash->success(__('Le mandat a été créé.'));
            }
            //$listmandates = $mollie->get_mandates($customerid);
            //$mandateid=$listmandates[0]['id'];

            //return $this->redirect('/mandates/index');
        }
    }

    public function edit($customerid, $mandateid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $mandate = $mollie->get_mandate($customerid, $mandateid);
        $this->set(compact('mandate'));
        $list_customers = array();
        $customers = $mollie->get_all_customers();
        /*foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }*/
        $customer = $mollie->get_customer_by_id($customerid);
        $subscriptions = $mollie->get_subscriptions($customerid)['_embedded']['subscriptions'];
        $subs_to_create = array();
        $sub = array();
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                if (preg_match('/Change/', $subscription['description'])) {
                    $sub['description'] = $subscription['description'];
                    $sub['amount'] = $subscription['amount']['value'];
                    $sub['interval'] = $subscription['interval'];
                    $sub['startDate'] = $subscription['nextPaymentDate'];
                    $subs_to_create[] = $sub;
                }
                if (preg_match('/Adhésion/', $subscription['description'])) {
                    $sub['description'] = $subscription['description'];
                    $sub['amount'] = $subscription['amount']['value'];
                    $sub['interval'] = $subscription['interval'];
                    $sub['startDate'] = $subscription['nextPaymentDate'];
                    $subs_to_create[] = $sub;
                }
            }
        }
        $mandates = $mollie->get_mandates($customerid);
        $mandateusr = array();
        foreach ($mandates as $mandate) {
            if ($mandate['status'] == 'valid') {
                $mandateusr['iban'] = $mandate['details']['consumerAccount'];
                $mandateusr['signatureDate'] = $mandate['signatureDate'];
                $mandateusr['id'] = $mandate['id'];
            }
        }
        //var_dump($subs_to_create);
        $this->set(compact('list_customers'));
        $this->set(compact('customers'));
        if ($this->request->is('post')) {  
            $data = $this->request->getData();
            $infos = $mollie->create_mandate($customerid, $data['iban'], $customer['name'], $customer['email']);
            if (isset($infos['id'])) {
                $mandate = $infos['id'];
                if (isset($mandateusr['id'])) {
                    $revok = $mollie->revoke_mandate($customerid, $mandateusr['id']);
                    var_dump($revok);
                }
                foreach ($subs_to_create as $sub) {
                    $res = $mollie->create_subscription($sub['amount'], $customerid, $mandate, $sub['description'], $sub['interval'], $sub['startDate'], 0);
                }
                $this->Flash->success(__('Le mandat a été modifié.'));
                return $this->redirect('/mandates/index');
            }

            $this->Flash->error(__('Le mandat n\'a pas été modifié.'));
            //
        }
    }

    public function delete($customerid, $mandateid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        //$subscription_id = $this->request->getQuery('mandate_id');
        //$customer_id = $this->request->getQuery('customer_id');
        $mollie = $this->fetchTable('Mollie');
        $mollie->revoke_mandate($customerid, $mandateid);
        $this->Flash->success(__('Le mandat a été supprimé.'));
        return $this->redirect('/mandates/index');
    }


}