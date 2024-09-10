<?php
// src/Controller/SubscriptionsController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\DateTime;
use Cake\Http\Client;

class SubscriptionsController extends AppController
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
        $list_customers = array();
        $customers = $mollie->get_customers();
        //var_dump($customers);
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        /*$listsubscriptions = $mollie->list_subscriptions($from);
        //var_dump($subscriptions);
        if (isset($listsubscriptions['_links']['next'])) {
            $href= $listsubscriptions['_links']['next']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $nextfrom=$matches[1];
            }
            $this->set(compact('nextfrom'));
        }
        if (isset($listsubscriptions['_links']['previous'])) {
            $href= $listsubscriptions['_links']['previous']['href'];
            if (preg_match('/from=(\w+)/', $href, $matches)) {
                $prevfrom=$matches[1];
            }
            $this->set(compact('prevfrom'));
        }
        $subscriptions = $listsubscriptions['_embedded']['subscriptions'];*/
        $subscriptions = $mollie->get_all_subscriptions();
        //var_dump($listsubscriptions);
        $nbsubscriptions = count($subscriptions);
        $this->set(compact('nbsubscriptions'));
        $this->set(compact('list_customers'));
        $this->set(compact('subscriptions'));
        /*$payments = $mollie->list_payments();
        //var_dump($payments['_embedded']['payments']);
        $list_payments = $payments['_embedded']['payments'];
        //var_dump($list_payments);
        //$list_payments = $this->Paginator->paginate($payments['_embedded']['payments']);
        $this->set(compact('list_payments'));*/
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();
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
        $this->viewBuilder()->setLayout('bdc');
        $mollie = $this->fetchTable('Mollie');
        $customers = $mollie->get_customers();
        $now = DateTime::now();
        $startdate = $now->i18nFormat('yyyy-MM-dd');
        $this->set(compact('startdate'));
        $this->set(compact('customers'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //var_dump($data);
            $customers = $mollie->get_customer($data['client_id']);
            $customerid = $customers[0]['id'];        
            $listmandates = $mollie->get_mandates($customerid);
            if (count($listmandates) == 0) {
                $this->Flash->error(__('Aucun mandata n\'a été trouvé.'));
            } else {
                $mandateid=$listmandates[0]['id'];            
                $amount = strval(number_format(floatval($data['amount']),2));
                if ($data['interval'] == "monthly") {
                    $infos = $mollie->create_subscription_monthly($amount, $customerid, $mandateid, $data['description'], $data['startdate'], $data['times']);
                    $this->Flash->success(__('Le prélèvement a été créé.'));
                }
                if ($data['interval'] == "annually") {
                    $infos = $mollie->create_subscription_annually($amount, $customerid, $mandateid, $data['description'], $data['startdate'], $data['times']);
                    $this->Flash->success(__('Le prélèvement a été créé.'));
                }
            }
            
        }
    }

    public function view($customerid, $subscriptionid)
    {
        $this->Authorization->skipAuthorization();
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
        $this->viewBuilder()->setLayout('bdc');
        $mollie = $this->fetchTable('Mollie');
        $subscription = $mollie->get_subscription($customerid, $subscriptionid);
        $this->set(compact('subscription'));
        $list_customers = array();
        $customers = $mollie->get_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $this->set(compact('list_customers'));
    }

    public function edit($customerid, $subscriptionid)
    {
        $this->Authorization->skipAuthorization();
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
        $this->viewBuilder()->setLayout('bdc');
        $now = DateTime::now();
        $startdate = $now->i18nFormat('yyyy-MM-dd');
        $this->set(compact('startdate'));
        $mollie = $this->fetchTable('Mollie');
        $subscription = $mollie->get_subscription($customerid, $subscriptionid);
        $this->set(compact('subscription'));
        $list_customers = array();
        $customers = $mollie->get_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $this->set(compact('list_customers'));
        $this->set(compact('customers'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $amount = strval(number_format(floatval($data['amount']),2));
            $infos = $mollie->update_subscription($subscriptionid, $customerid, $amount, $data['times']);
            $this->Flash->success(__('Le prélèvement a été modifié.'));
            return $this->redirect('/subscriptions/index');
        }
    }

    public function delete()
    {
        $this->Authorization->skipAuthorization();
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
        $this->viewBuilder()->setLayout('bdc');
        $subscription_id = $this->request->getQuery('subscription_id');
        $customer_id = $this->request->getQuery('customer_id');
        $mollie = $this->fetchTable('Mollie');
        $mollie->cancel_subscription($customer_id, $subscription_id);
        return $this->redirect('/subscriptions/index');
    }
}