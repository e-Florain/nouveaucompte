<?php
// src/Controller/CustomersController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\DateTime;
use Cake\Http\Client;

class CustomersController extends AppController
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

    public function index($from="")
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
        $nbcustomers = count($customers);
        $this->set(compact('nbcustomers'));
        $this->set(compact('customers'));
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
        $this->viewBuilder()->setLayout($this->getLayout($role));
        if ($role != "root") {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        $customers = $mollie->get_all_customers();
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
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $now = DateTime::now();
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
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $subscription = $mollie->get_subscription($customerid, $subscriptionid);
        $this->set(compact('subscription'));
        $list_customers = array();
        $customers = $mollie->get_all_customers();
        foreach ($customers as $customer) {
            $list_customers[$customer['id']] = $customer;
        }
        $this->set(compact('list_customers'));

    }

    public function delete($customerid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $mollie = $this->fetchTable('Mollie');
        //Debug($customerid);
        $mollie->delete_customer($customerid);
        $this->Flash->success(__('Le client a été effacé.'));
        return $this->redirect('/customers/index');
    }


}