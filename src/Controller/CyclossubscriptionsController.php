<?php
// src/Controller/CyclossubscriptionsController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\DateTime;
use Cake\Http\Client;

class CyclossubscriptionsController extends AppController
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
                'findSub',
                'add',
                'view',
                'delete'
            ),
            'admin' => array(
                'index',
                'findSub',
                'add',
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
        $order = $this->request->getQuery('orderby') ?? "created";
        $sort = $this->request->getQuery('sort') ?? "ASC";
        $subscriptions = $this->Cyclossubscriptions->find()->order([$order => $sort]);
        foreach ($subscriptions as $sub) {
            if ($sub['startdate'] != NULL) {
                $startdate = new DateTime($sub['startdate']);
                $sub['startdate'] = $startdate->i18nFormat('yyyy-MM-dd');   
            }
            if ($sub['nextpaymentdate'] != NULL) {
                $nextpaymentdate = new DateTime($sub['nextpaymentdate']);
                $sub['nextpaymentdate'] = $nextpaymentdate->i18nFormat('yyyy-MM-dd');
            }
        }
        $this->set(compact('subscriptions'));
        $nbsubscriptions = $this->Cyclossubscriptions->find()->count();
        $this->set('nbsubscriptions', $nbsubscriptions);
    }

    public function findSub($email)
    {
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        
        $user = $this->Cyclossubscriptions->find()
            ->select(['id', 'account_cyclos', 'sub_interval', 'amount', 'nextpaymentdate', 'startdate', 'created', 'modified'])
            ->where(['account_cyclos' => $email])
            ->first();
        return $user;
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

        $this->viewBuilder()->setLayout($this->getLayout($role));
        $now = DateTime::now();
        $cyclos = $this->fetchTable('Cyclos');
        $pros = $cyclos->getUsers('professionnels');
        $this->set(compact('pros'));
        //asort($pros);
        //Debug($pros);
        $startdate = $now->i18nFormat('yyyy-MM-dd');
        $this->set(compact('startdate'));
        $sub = $this->Cyclossubscriptions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $startdate = new DateTime($data['startdate']);
            $data['startdate'] = $startdate;
            $data['nextpaymentdate'] = $startdate;
            $sub = $this->Cyclossubscriptions->patchEntity($sub, $data);
            if ($this->Cyclossubscriptions->save($sub)) {
                $this->Flash->success(__('Le prélèvement a été ajouté.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erreur : Impossible d\'ajouter le prélèvement.'));
        }
    }

    public function delete($subid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }

        $this->viewBuilder()->setLayout($this->getLayout($role));
        $sub = $this->Cyclossubscriptions->get($subid);
        if ($this->Cyclossubscriptions->delete($sub)) {
            $this->Flash->success(__('Le prélèvement a été supprimé.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function edit($subid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }

        $this->viewBuilder()->setLayout($this->getLayout($role));
        $cyclos = $this->fetchTable('Cyclos');
        $pros = $cyclos->getUsers('professionnels');
        $this->set(compact('pros'));
        $sub = $this->Cyclossubscriptions->get($subid);
        $startdate = new DateTime($sub['startdate']);
        $sub['startdate'] = $startdate->i18nFormat('yyyy-MM-dd'); 
        $this->set(compact('sub'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $sub = $this->Cyclossubscriptions->patchEntity($sub, $data);
            $sub['id'] = $subid;
            if ($this->Cyclossubscriptions->save($sub)) {
                $this->Flash->success(__('Le prélèvement a été modifié.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Erreur : Impossible de modifier le prélèvement.'));
        }
    }

    public function view($subid)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }

        $this->viewBuilder()->setLayout($this->getLayout($role));
        $cyclos = $this->fetchTable('Cyclos');
        $pros = $cyclos->getUsers('professionnels');
        $this->set(compact('pros'));
        $subscription = $this->Cyclossubscriptions->get($subid);
        $startdate = new DateTime($subscription['startdate']);
        $subscription['startdate'] = $startdate->i18nFormat('yyyy-MM-dd'); 
        $this->set(compact('subscription'));
        $transactions = array();
        $results = $cyclos->searchPayments($subscription['account_cyclos_src'], $subscription['account_cyclos_dst'], $subscription['description']);
        foreach ($results as $result) {
            $datetransac = new DateTime($result['date']);
            $transaction = $result;
            $transaction['date'] = $datetransac->i18nFormat('yyyy-MM-dd hh:mm');
            $transactions[] = $transaction;
        }
        $this->set(compact('transactions'));
    }
}