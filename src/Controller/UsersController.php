<?php
// src/Controller/UsersController.php

namespace App\Controller;

use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class UsersController extends AppController
{
    private $list_roles = array("User", "Admin", "Root", "Benevole");

    private $list_auth = array("cyclos", "SQL");

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login', 'otp', 'activate']);

    }

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
                'importCi',
                'moncompte',
                'add'
            ),
            'admin' => array(
                'index',
                'importCi',
                'moncompte',
                'add'
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
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $order = $this->request->getQuery('orderby') ?? "lastname";
        $sort = $this->request->getQuery('sort') ?? "ASC";
        $users = $this->Users->find()->order([$order => $sort]);
        $this->set(compact('users'));
        $this->viewBuilder()->setLayout($this->getLayout($role));
    }

    /*public function test()
    {
        $this->Authorization->skipAuthorization();
        $actions = get_class_methods($this->Authentication);
        Debug($actions);
    }*/

    public function authCyclos($email, $password)
    {
        $datas =  array();
        $cyclos = $this->fetchTable('Cyclos');
        $res = $cyclos->getAuth($email, $password);
        //Debug($res);
        /* Connexion réussie */
        if (isset($res['user'])) {
            $user = $this->findUser($email);
            if (!is_null($user)) {
                /* create token */
                $token = bin2hex(random_bytes(40));
                $datas['token'] = $token;
                $datas['displayname'] = $res['user']['display'];
                $user = $this->Users->patchEntity($user, $datas);
                $this->Users->save($user);
                return $this->redirect(['controller' => 'Users', 
                    'action' => 'otp',
                    '?' => [
                        'token' => $token
                    ]
                ]);
            } else {
                /* create user and token */
                $token = bin2hex(random_bytes(40));
                $datas['token'] = $token;
                $datas['email'] = $email;
                $datas['displayname'] = $res['user']['display'];
                $user = $this->Users->newEmptyEntity();
                $user = $this->Users->patchEntity($user, $datas);
                $this->Users->save($user);
                return $this->redirect(['controller' => 'Users', 
                    'action' => 'otp',
                    '?' => [
                        'token' => $token
                    ]
                ]);
            }    
            return $res;
        }
        /* Connexion error */
        else {
            if ($res == -2) {
                $this->Flash->error(__('Impossible de se connecter au serveur'));
            } else {
                $this->Flash->error(__('Email ou mot passe invalide'));
            }
            
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    public function login()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            /* Teste si le user existe dans la base */
            $user = $this->findUser($data['email']);
            /* Si ce n'est pas le cas auth cyclos */
            if (is_null($user)) {
                $this->authCyclos($data['email'], $data['password']);
            } else {
                /* S'il existe regarder quelle auth utiliser */
                if ($user['auth'] == 'cyclos') {
                    $this->authCyclos($data['email'], $data['password']);
                } else {
                    if ($this->Authentication->getResult()) {
                        /* create token */
                        $token = bin2hex(random_bytes(40));
                        $datas['token'] = $token;
                        $datas['displayname'] = $user['firstname']." ".$user['lastname'];
                        $user = $this->Users->patchEntity($user, $datas);
                        $this->Users->save($user);
                        return $this->redirect(['controller' => 'Users', 
                            'action' => 'otp',
                            '?' => [
                                'token' => $token
                            ]
                        ]);
                    } else {
                        $this->Flash->error(__('Email ou mot passe invalide'));
                        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                    }
                }
            }
        }
    }

    public function otp()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');
        //$session = $this->request->getSession();
        //$email = $session->read('User.email');
        //$role = $session->read('User.role');

        if ($this->request->is('post')) {
            $token = $this->request->getQuery('token');
            $user = $this->findUserWithToken($token);
            $data = $this->request->getData();
            if ($data['otp'] == $user['otp']) {
                $this->Authentication->setIdentity($user);
                $session = $this->request->getSession();
                $session->write('User.name', $user->displayname);
                $session->write('User.email', $user->email);
                $session->write('User.id', $user->id);
                $session->write('User.role', $user->role);
                $session->write('User.auth', $user->auth);
                $result = $this->Authentication->getResult();
                if (($user->role == 'root') or ($user->role == 'admin')) {
                    return $this->redirect(['controller' => 'Nouveaucompte', 'action' => 'list']);
                } else {
                    if ($user->auth == 'SQL') {
                        return $this->redirect(['controller' => 'Odoo', 'action' => 'index']);
                    } else {
                        return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
                    }
                }
            } else {
                $this->Flash->error(__('Code invalide'));
            }
            /*$users
            if (($user->role == 'root') or ($user->role == 'admin')) {
                return $this->redirect(['controller' => 'Nouveaucompte', 'action' => 'list']);
            }*/
        } else {
            $token = $this->request->getQuery('token');
            $datas['otp'] = rand(10000, 99999);
            $user = $this->findUserWithToken($token);
            if (isset($user)) {
                $user = $this->Users->patchEntity($user, $datas);
                $this->Users->save($user);
            } else {
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }
            $this->sendEmailOtp($user->email, $datas);
        }
    }

    // in src/Controller/UsersController.php
    public function logout()
    {
        $this->Authorization->skipAuthorization();
        $this->Authentication->logout();
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $this->set('list_roles', $this->list_roles);
        $this->set('list_auth', $this->list_auth);
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $datas = $this->request->getData();
            $token = bin2hex(random_bytes(40));
            $datas['token'] = $token;
            $password = bin2hex(random_bytes(40));
            $datas['password'] = $password;
            $user = $this->Users->patchEntity($user, $datas);
            if ($this->Users->save($user)) {
                if ($datas['auth'] == 'SQL') {
                    $this->sendEmailTokenCreation($datas['email'], $datas);
                }
                $this->Flash->success(__('L\'utilisateur a été ajouté.'));
                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('Erreur : Impossible d\'ajouter l\'utilisateur.'));
        }
        $this->set('user', $user);
        $this->viewBuilder()->setLayout($this->getLayout($role));
    }

    public function activate()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('login');
        if ($this->request->is('get')) {
            $parameters = $this->request->getAttribute('params');
            if (isset($parameters['?']['token'])) {
                $token = $parameters['?']['token'];
            } else {
                return $this->redirect(['action' => 'moncompte']);
            }
            $user = $this->Users->find()
                ->select(['id', 'firstname', 'lastname', 'displayname', 'email', 'password', 'auth', 'role', 'otp', 'token'])
                ->where(['token' => $token])
                ->first();
            if (!isset($user)) {
                return $this->redirect(['action' => 'moncompte']);
            }
        }
        if ($this->request->is('post')) {
            $parameters = $this->request->getAttribute('params');
            if (isset($parameters['?']['token'])) {
                $token = $parameters['?']['token'];
            } else {
                return $this->redirect(['action' => 'moncompte']);
            }
            $user = $this->Users->find()
                ->select(['id', 'firstname', 'lastname', 'displayname', 'email', 'password', 'auth', 'role', 'otp', 'token'])
                ->where(['token' => $token])
                ->first();
            if (!isset($user)) {
                return $this->redirect(['action' => 'moncompte']);
            } else {
                $datas = $this->request->getData();
                if ($datas['password'] != $datas['password2']) {
                    $this->Flash->error(__('Les mots de passe ne correspondent pas'));
                } else {
                    $datas['token'] = '';
                    $user = $this->Users->patchEntity($user, $datas);
                    if ($this->Users->save($user)) {
                        $this->Flash->success(__('Votre compte est validé.'));
                    }
                }
            }
        }
    }

    public function edit($id)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $user = $this->Users->get($id);
        $this->set('list_roles', $this->list_roles);
        $this->set(compact('user'));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data["role"] = strtolower($data["role"]);
            $user = $this->Users->patchEntity($user, $data);
            if ($user->getErrors()) {
                var_dump($user->getErrors());
            } else {
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('L\'utilisateur a été modifié.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Erreur : Impossible de modifier l\'utilisateur.'));
                    return $this->redirect('/users/index');
                }
            }
        }
    }

    public function delete($id)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $user = $this->Users->get($id);
        $result = $this->Users->delete($user);
        $this->Flash->success(__('L\'utilisateur a été effacé.'));
        return $this->redirect('/users/index');
    }

    public function resetPassword($id)
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        $user = $this->Users->get($id);
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Le mot de passe a été changé.'));
                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('Erreur : Impossible de changer le mot de passe.'));
        }
    }

    public function findUser($email)
    {
        $user = $this->Users->find()
            ->select(['id', 'firstname', 'lastname', 'displayname', 'email', 'password', 'auth', 'role', 'otp'])
            ->where(['email' => $email])
            ->first();
        return $user;
    }

    public function findUserWithToken($token)
    {
        $user = $this->Users->find()
            ->select(['id', 'firstname', 'lastname', 'displayname', 'email', 'password', 'auth', 'role', 'token', 'otp'])
            ->where(['token' => $token])
            ->first();
        return $user;
    }

    public function moncompte()
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $customer = $mollie->get_customer($email);

        /* Prélèvement */
        $subscriptions = $mollie->get_subscriptions($email);
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                if (preg_match('/Change/', $subscription['description'])) {
                    //echo "<tr>";
                    //echo "<td><a href='subscription.php?id=".$subscription['id']."'>".$subscription['description']."</a></td>";
                    $this->set('description', $subscription['description']);
                    $this->set('amount', $subscription['amount']['value']);
                    $this->set('subid', $subscription['id']);
                    if (preg_match("/(\d+)\s+(\w+)/", $subscription['interval'], $matches)) {
                        if ($matches[2] == "days") {
                            $interval = $matches[1] . " jours";
                        } elseif ($matches[2] == "month") {
                            $interval = $matches[1] . " mois";
                        }
                        $this->set('interval', $interval);
                    }
                    $datetime = new DateTime();
                    $newDate = $datetime->createFromFormat('Y-m-d', $subscription['nextPaymentDate']);
                    $this->set('nextdate', $newDate->format('d-m-Y'));

                }
                if (preg_match('/Adh/', $subscription['description'])) {
                    $this->set('description2', $subscription['description']);
                    $this->set('amount2', $subscription['amount']['value']);
                    $this->set('subid2', $subscription['id']);
                    if (preg_match("/(\d+)\s+(\w+)/", $subscription['interval'], $matches)) {
                        if ($matches[2] == "days") {
                            $interval = $matches[1] . " jours";
                        } elseif ($matches[2] == "month") {
                            $interval = $matches[1] . " mois";
                        }
                        $this->set('interval2', $interval);
                    }
                    $datetime = new DateTime();
                    $newDate = $datetime->createFromFormat('Y-m-d', $subscription['nextPaymentDate']);
                    $this->set('nextdate2', $newDate->format('d-m-Y'));
                }
            }
        }

        /* CB */


        /* Association soutenue */
        $adhs = $florapi->getAdh($email);
        $adh = $adhs[0];
        $assos = $florapi->getOdooAssos();
        //var_dump($assos);
        foreach ($assos as $asso) {
            if ($asso['id'] == $adh['orga_choice']) {
                $assoname = $asso['name'];
            }
        }
        $this->set('assoname', $assoname);

        /* Coordonnées bancaires */
        $mandates = $mollie->get_mandates($customer[0]['id']);
        $mandateusr = array();
        foreach ($mandates as $mandate) {
            if ($mandate['status'] == 'valid') {
                $mandateusr['iban'] = $mandate['details']['consumerAccount'];
                $mandateusr['signatureDate'] = $mandate['signatureDate'];
                $mandateusr['id'] = $mandate['id'];
            }
        }
        $this->set('mandateusr', $mandateusr);
    }

    public function changeasso()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $florapi = $this->fetchTable('Florapi');
        $assos = $florapi->getOdooAssos();
        $this->set('assos', $assos);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $datas['email'] = $email;
            $datas['infos']['orga_choice'] = $data['orgachoice'];
            $florapi->updateAdh($datas);
            $this->Flash->success(__('L\'association soutenue a été changée.'));
            return $this->redirect(['action' => 'moncompte']);
        }
    }

    public function subscriptionchange($subid)
    {
        $role = $this->whoami();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $customer = $mollie->get_customer($email);
        $subscription = $mollie->get_subscription($customer[0]['id'], $subid);
        //Debug($subscription);
        if ($subscription['status'] == 404) {
            return $this->redirect(['action' => 'moncompte']);
        }
        $this->set('description', $subscription['description']);
        $this->set('amount', $subscription['amount']['value']);
        $this->set('subid', $subscription['id']);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $amount = strval(number_format(floatval($data['montant']), 2, '.', ''));
            $infos = $mollie->update_subscription($subid, $customer[0]['id'], $amount, 0);
            $datas['email'] = $email;
            $datas['infos']['changeeuros'] = $amount;
            $florapi->updateAdh($datas);
            $this->Flash->success(__('Le montant a été changé.'));
            return $this->redirect(['action' => 'moncompte']);
        }
    }

    public function subscriptionadh($subid)
    {
        $role = $this->whoami();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $customer = $mollie->get_customer($email);
        $subscription = $mollie->get_subscription($customer[0]['id'], $subid);
        //Debug($subscription);
        if ($subscription['status'] == 404) {
            return $this->redirect(['action' => 'moncompte']);
        }

        /*if (preg_match("/(\d+)\s+(\w+)/", $subscription['interval'], $matches)) {
            if ($matches[2] == "days") {
                $interval = $matches[1] . " jours";
            } elseif ($matches[2] == "month") {
                $interval = $matches[1] . " mois";
            }
            $this->set('interval', $interval);
        }*/
        $this->set('interval', $subscription['interval']);
        $this->set('description', $subscription['description']);
        $this->set('amount', $subscription['amount']['value']);
        $this->set('subid', $subscription['id']);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //var_dump($data);
            if ($data['adhchoicemoncompte'] == 'annuel') {
                $interval = "365 days";
            }
            if ($data['adhchoicemoncompte'] == 'mensuel') {
                $interval = "1 month";
            }
            $amount = strval(number_format(floatval($data['montantadh']), 2, '.', ''));
            $infos = $mollie->update_subscription($subid, $customer[0]['id'], $amount, 0, $interval);
            //var_dump($infos);
            $datas['email'] = $email;
            $datas['infos']['changeeuros'] = $amount;
            $this->Flash->success(__('Le montant a été changé.'));
            return $this->redirect(['action' => 'moncompte']);
        }
    }

    public function mandate($mandateid)
    {
        $role = $this->whoami();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $role = $session->read('User.role');
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $customer = $mollie->get_customer($email);
        $mandate = $mollie->get_mandate($customer[0]['id'], $mandateid);
        //Debug($mandate);
        if ($mandate['status'] == 404) {
            return $this->redirect(['action' => 'moncompte']);
        }
        $this->set('iban', $mandate['details']['consumerAccount']);
        $this->set('mandateid', $mandate['id']);
        $this->set('startdate', $mandate['signatureDate']);
        $mandateusr = array();
        $mandates = $mollie->list_mandates($customer[0]['id'])['_embedded']['mandates'];
        $subscriptions = $mollie->get_subscriptions($email);
        foreach ($mandates as $mandate) {
            if ($mandate['status'] == 'valid') {
                $mandateusr['iban'] = $mandate['details']['consumerAccount'];
                $mandateusr['signatureDate'] = $mandate['signatureDate'];
                $mandateusr['id'] = $mandate['id'];
            }
        }
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
        //Debug($mandateusr);
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //Debug($data);
            $infos = $mollie->create_mandate($customer[0]['id'], $data['iban'], $customer[0]['name'], $email);
            if (isset($infos['id'])) {
                $mandate = $infos['id'];
                if (isset($mandateusr['id'])) {
                    $res1 = $mollie->revoke_mandate($customer[0]['id'], $mandateusr['id']);
                }
                foreach ($subs_to_create as $sub) {
                    $res2 = $mollie->create_subscription($sub['amount'], $customer[0]['id'], $mandate, $sub['description'], $sub['interval'], $sub['startDate']);
                }
                $this->Flash->success(__('L\'IBAN a été changé.'));
                return $this->redirect(['action' => 'moncompte']);
            } else {
                if ($infos['detail'] == "The bank account is invalid") {
                    $this->Flash->error(__('Le compte bancaire est invalide'));
                }
            }
        }
    }

    public function paymentCB()
    {
        $role = $this->whoami();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $mollie = $this->fetchTable('Mollie');
        $role = $session->read('User.role');
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $numberAsString = number_format($data['amount'], 2);
            $order_id = bin2hex(random_bytes(40));
            $customers = $mollie->get_customer($email);
            if (isset($customers[0]['id'])) {
                //Debug($data);
                $res = $mollie->create_payment($numberAsString, "Change CB ".$email, $order_id, $customers[0]["id"]);
                //Debug($res);
                if (is_numeric($res["status"])) {
                    $strmsg = "Erreur ".strval($res["status"]);
                    $this->Flash->error(__('Erreur : '.$strmsg));
                } else {
                    if (($res["status"] == "valid") or ($res["status"] == "open")) {
                        $url = $res["_links"]["checkout"]["href"];
                        $this->redirect($url);
                    }
                }
            } else {
                $this->Flash->error(__('Erreur'));
            }
            
        }
    }

    public function validpayment ($order_id)
    {
        $role = $this->whoami();
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $email = $session->read('User.email');
        $mollie = $this->fetchTable('Mollie');
        $role = $session->read('User.role');
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout($this->getLayout($role));
        $payment_status = $mollie->get_status_payment($order_id);
        //Debug($payment_status);
        if ($payment_status == "paid") {
            /*echo '<div class="row"><div class="col s10 offset-s2">';
            echo "<h4>Merci votre compte Florain numérique sera crédité sous les 24h (jours ouvrables)</h4>";
            echo '</div>';*/
            $mollie->postCallbackUrl();
        }
        $this->set('payment_status', $payment_status);
        /* else {
            echo '<div class="row"><div class="col s10 offset-s2">';
            echo "<h4>Désolé le paiement a échoué, merci de réessayer plus tard</h4>";
            echo '</div>';
        }*/
    }

    public function importCi()
    {
        $this->Authorization->skipAuthorization();
        $role = $this->whoami();
        $parameters = $this->request->getAttribute('params');
        if (!$this->iamauthorized($parameters['action'], $role)) {
            $this->Flash->error(__('Vous n\'êtes pas autorisé à accéder à cette page'));
            return $this->redirect(['controller' => 'Users', 'action' => 'moncompte']);
        }
        if ($this->request->is('post')) {
            $mindee = $this->fetchTable('Mindee');
            $nextcloud = $this->fetchTable('Nextcloud');
            $res = $mindee->checkIdorPassport($_FILES['uploadedFile']['tmp_name']);
            Debug($res);
            if ($res['api_request']['status'] == 'success') {
                $dirname = $res['document']['inference']['prediction']['surname']['value'];
                $dirname .= "-".$res['document']['inference']['prediction']['given_names'][0]['value'];
                $newpath = $nextcloud->addwatermark($_FILES['uploadedFile']['name'], $_FILES['uploadedFile']['tmp_name']);
                //$nextcloud->uploadFile($dirname, $_FILES['uploadedFile']['tmp_name'], $_FILES['uploadedFile']['name']);
                $path_parts = pathinfo($newpath);
                if (!$nextcloud->isFolderExist($dirname)) {
                    $infos = $nextcloud->createFolder($dirname);
                    $this->log('create folder to nextcloud', 'debug');
                }
                $nextcloud->uploadFile($dirname, $newpath, $path_parts['filename'].'.'.$path_parts['extension']);
            } else {
                return $this->redirect(['controller' => 'Users', 'action' => 'importCi']);
            }
        }
    }

    public function sendEmailOtp($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Florain - Code de vérification')
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('otp')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function sendEmailTokenCreation($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Florain - Activation de votre compte')
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('tokencreate')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function flashAmount($amount)
    {
        $this->Authorization->skipAuthorization();
        //$this->viewBuilder()->setLayout('ajax');
        $this->Flash->warning('My message. '.$amount);
    }
}
?>