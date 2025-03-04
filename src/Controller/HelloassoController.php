<?php
// src/Controller/HelloassoController.php

namespace App\Controller;
use Cake\I18n\DateTime;

class HelloassoController extends AppController
{
    public function index()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        if (!isset($email)) {
            return $this->redirect(['action' => 'logout']);
        }
        $users = $this->fetchTable('Users');
        $role = $users->getRole($email);
        $this->set('role', $role);
        if (($role != "root") or ($role == "admin")) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $helloasso = $this->fetchTable('Helloasso');
        $florapi = $this->fetchTable('Florapi');
        //$adhs = $florapi->getAdhs(array());
        //$adhpros = $florapi->getAdhpros(array());
        $list_payments = $helloasso->get_payments();
        foreach ($list_payments['data'] as $key=>$payment) {
            $datestr = new DateTime($payment['date']);
            $list_payments['data'][$key]['date2'] = $datestr->format("d-m-Y H:i:s");
            $adh = $florapi->getAdh($payment['payer']['email']);
            if (count($adh) == 0) {
                $adhpro = $florapi->getAdhpro($payment['payer']['email']);
                if (count($adhpro) == 0) {
                    $list_payments['data'][$key]['inodoo'] = False;
                } else {
                    $list_payments['data'][$key]['inodoo'] = True;
                }
            } else {
                $list_payments['data'][$key]['inodoo'] = True;
            }
        }
        $this->set(compact('list_payments'));
        //Debug($list_payments);
    }

    public function dons()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        if (!isset($email)) {
            return $this->redirect(['action' => 'logout']);
        }
        $users = $this->fetchTable('Users');
        $role = $users->getRole($email);
        $this->set('role', $role);
        if (($role != "root") or ($role == "admin")) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $helloasso = $this->fetchTable('Helloasso');

        $list_dons = $helloasso->get_dons();
        foreach ($list_dons['data'] as $key=>$don) {
            $datestr = new DateTime($don['date']);
            $list_dons['data'][$key]['date2'] = $datestr->format("d-m-Y H:i:s");
        }
        $this->set(compact('list_dons'));
        /*foreach ($list_payments['data'] as $key=>$payment) {
            Debug($payment['paymentReceiptUrl']);
            $datestr = new DateTime($payment['date']);
            $list_payments['data'][$key]['date2'] = $datestr->format("d-m-Y H:i:s");
        }
        $this->set(compact('list_payments'));*/
    }
}