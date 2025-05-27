<?php
// src/Controller/DashboardController.php

namespace App\Controller;

class DashboardController extends AppController
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
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $this->set('role', $role);
        $mollie = $this->fetchTable('Mollie');
        $totalchange = $mollie->calculAmountChanges();
        $this->set(compact('totalchange'));
        $totaladhmensuelle = $mollie->calculAdhMensuelle();
        $this->set(compact('totaladhmensuelle'));
        $totaladhannuelle = $mollie->calculAdhAnnuelle();
        $this->set(compact('totaladhannuelle'));

        $florapi = $this->fetchTable('Florapi');
        /* compte cyclos */
        $params=array("account_cyclos" => 't');
        $nbadhscyclos = count($florapi->getAdhs($params));
        $this->set(compact('nbadhscyclos'));
        /* compte adhérent */
        $params=array("membership_state" => 'paid');
        $nbadhspart = count($florapi->getAdhs($params));
        $this->set(compact('nbadhspart'));
        /* compte pros */
        $params=array("account_cyclos" => 't');
        $nbadhproscyclos = count($florapi->getAdhpros($params));
        $this->set(compact('nbadhproscyclos'));
        /* compte adhs */
        $params=array();
        $nbadhs = count($florapi->getAdhs($params));
        $this->set(compact('nbadhs'));
        /* compte adhpros */
        $nbadhpros = count($florapi->getAdhpros($params));
        $this->set(compact('nbadhpros'));

    }
}