<?php
// src/Controller/CustomersController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\FrozenTime;
use Cake\Http\Client;

class CustomersController extends AppController
{
    public function index($from="")
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
        $this->set('role', $role);
        $mollie = $this->fetchTable('Mollie');
        $list_customers = array();
        $customers = $mollie->get_customers();
        $nbcustomers = count($customers);
        $this->set(compact('nbcustomers'));
        $this->set(compact('customers'));
        $this->viewBuilder()->setLayout('bdc');
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
        if ($role != "root") {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        $this->viewBuilder()->setLayout('bdc');
        $customers = $mollie->get_customers();
        $this->set(compact('customers'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $mollie->create_customer($data['email'], $data['name']);
            $this->Flash->success(__('Le client a été créé.'));
            return $this->redirect('/customers/index');
        }
    }

    public function edit($customerid)
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
        $now = FrozenTime::now();
        $startdate = $now->i18nFormat('yyyy-MM-dd');
        $this->set(compact('startdate'));
        $mollie = $this->fetchTable('Mollie');
        $customer = $mollie->get_customer_by_id($customerid);
        $this->set(compact('customer'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $infos = $mollie->update_customer($customerid, $data['email'], $data['name']);
            $this->Flash->success(__('Le client a été modifié.'));
            return $this->redirect('/customers/index');
        }
    }

    public function view($customerid, $subscriptionid)
    {
        $mollie = $this->fetchTable('Mollie');
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
        $subscription = $mollie->get_subscription($customerid, $subscriptionid);
        $this->set(compact('subscription'));
        $list_customers = array();
        $customers = $mollie->get_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $this->set(compact('list_customers'));

    }


}