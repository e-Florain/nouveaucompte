<?php
// src/Controller/CyclossubscriptionsController.php

namespace App\Controller;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\DateTime;
use Cake\Http\Client;

class CyclossubscriptionsController extends AppController
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
        $order = $this->request->getQuery('orderby') ?? "created";
        $sort = $this->request->getQuery('sort') ?? "ASC";
        $subscriptions = $this->Cyclossubscriptions->find()->order([$order => $sort]);
        foreach ($subscriptions as $sub) {
            $startdate = new DateTime($sub['startdate']);
            $sub['startdate'] = $startdate->i18nFormat('yyyy-MM-dd');
            $nextpaymentdate = new DateTime($sub['nextpaymentdate']);
            $sub['nextpaymentdate'] = $nextpaymentdate->i18nFormat('yyyy-MM-dd');
        }
        
        //echo 
        $this->set(compact('subscriptions'));
        $nbsubscriptions = $this->Cyclossubscriptions->find()->count();
        $this->set('nbsubscriptions', $nbsubscriptions);
    }

    public function findSub($email)
    {
        $user = $this->Cyclossubscriptions->find()
            ->select(['id', 'account_cyclos', 'sub_interval', 'amount', 'nextpaymentdate', 'startdate', 'created', 'modified'])
            ->where(['account_cyclos' => $email])
            ->first();
        return $user;
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
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
        $this->viewBuilder()->setLayout('bdc');
        $sub = $this->Cyclossubscriptions->get($subid);
        if ($this->Cyclossubscriptions->delete($sub)) {
            $this->Flash->success(__('Le prélèvement a été supprimé.'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function edit($subid)
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $cyclos = $this->fetchTable('Cyclos');
        $pros = $cyclos->getUsers('professionnels');
        $this->set(compact('pros'));
        $sub = $this->Cyclossubscriptions->get($subid);
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
}