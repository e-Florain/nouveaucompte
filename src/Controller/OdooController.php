<?php
// src/Controller/OdooController.php

namespace App\Controller;
use Cake\I18n\DateTime;

class OdooController extends AppController
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
                'adhpro',
                'adhpart'
            ),
            'admin' => array(
                'index',
                'adhpro',
                'adhpart'
            ),
            'benevole' => array(
                'index',
                'adhpro',
                'adhpart'
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
        $session = $this->request->getSession();
        $auth = $session->read('User.auth');
        $this->set(compact('auth'));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->viewBuilder()->setLayout($this->getLayout($role));
    }

    public function adhpart($filters=array())
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $session = $this->request->getSession();
        $auth = $session->read('User.auth');
        $this->set(compact('auth'));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $florapi = $this->fetchTable('Florapi');
        $adhs = $florapi->getAdhs($filters);
        $listadhs = array();
        if (isset($parameters['?']['orderby'])) {
            array_multisort(array_column($adhs, $parameters['?']['orderby']), SORT_ASC, $adhs); 
        } else {
            array_multisort(array_column($adhs, 'lastname'), SORT_ASC, $adhs); 
        }
        foreach ($adhs as $adh) {
            if ($adh['membership_stop'] != null) {
                $expirdate = DateTime::createFromFormat('D, d M Y H:i:s T', $adh['membership_stop']);
                $adh['membership_stop'] = $expirdate->format("d m Y");
                $listadhs[] = $adh;
            }
        }
        $this->set(compact('listadhs'));
    }

    public function adhpro($filters=array())
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $session = $this->request->getSession();
        $auth = $session->read('User.auth');
        $this->set(compact('auth'));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $florapi = $this->fetchTable('Florapi');
        $adhs = $florapi->getAdhpros($filters);
        if (isset($parameters['?']['orderby'])) {
            array_multisort(array_column($adhs, $parameters['?']['orderby']), SORT_ASC, $adhs); 
        } else {
            array_multisort(array_column($adhs, 'name'), SORT_ASC, $adhs); 
        }
        $this->set(compact('adhs'));
    }
}
?>