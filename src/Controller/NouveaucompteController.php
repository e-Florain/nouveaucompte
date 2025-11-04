<?php
// src/Controller/BdcsController.php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\I18n\DateTime;


class NouveaucompteController extends AppController
{

    private $nbsteps = 9;
    private $translates = array(
        'firstname' => 'Prénom',
        'lastname' => 'Nom',
        'email' => 'Email',
        'street' => 'Adresse',
        'zip' => 'Code postal',
        'city' => 'Ville',
        'phone' => 'Téléphone',
        'ref' => 'Numéro d\'adhérent',
        'orga_choice' => 'Nom de l\'association choisie',
        'membership_state' => 'Adhésion',
        'membership_stop' => 'Date de fin d\'adhésion',
        'accept_newsletter' => 'Accepte les newsletters',
        'changeeuros' => 'Change Euros Mensuel',
        'nbeurosadhmensuel' => 'Adhésion mensuelle',
        'nbeurosadhannuel' => 'Adhésion annuelle'
    );

    private $search; 
    private $replace;

    public function initialize(): void
    {
        parent::initialize();
        $this->search = explode(",", "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
        $this->replace = explode(",", "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    }
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['index', 'index2', 'infos', 'dejacompte', 'get', 'confirmationemail', 'infossup', 'uploadid', 'validid', 
        'adh', 'choosechange', 'editiban', 'chooseasso', 'fin', 'activate', 'updateadh', 'updateadhfordebug', 'test']);
    }

    public function index()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $session->destroy();
    }

    public function index2()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $session->destroy();
    }


    public function test()
    {
        $this->Authorization->skipAuthorization();
        //$mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $assos = $florapi->getOdooAssos();
        Debug($assos);
    }

    /*
     * Première étape d'informations (Nom, prénom, Email)
     */
    public function infos()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $parameters = $this->request->getAttribute('params');
        if (isset($parameters['?'])) {
            if ($parameters['?']['comptecyclos'] == "true") {
                $comptecyclos = True;
                $titre = "NUMERIQUE";
                $this->nbsteps = 9;
            } else {
                $comptecyclos = False;
                $titre = "(SANS COMPTE NUMERIQUE)";
                $this->nbsteps = 7;
            }
        } else {
            $comptecyclos = True;
            $this->nbsteps = 9;
            $titre = "NUMERIQUE";
        }
        $this->set('titre', $titre);
        $this->set('nbsteps', $this->nbsteps);
        $this->set('comptecyclos', $comptecyclos);
        $uuid = bin2hex(random_bytes(40));
        $data = array(
            'uuid' => $uuid,
            'account_cyclos' => $comptecyclos
        );
        $this->add($data);
        $session->write('uuid', $uuid);
        $session->write('nbsteps', $this->nbsteps);
        $this->log('infos '.$uuid, 'debug');
    }

    public function dejacompte()
    {
        $this->Authorization->skipAuthorization();
    }

    /*
     * Get nouveaucompte in SQL 
     */
    public function get($uuid = "")
    {
        $this->Authorization->skipAuthorization();
        $parameters = $this->request->getAttribute('params');
        if (isset($parameters['?']['uuid'])) {
            $uuid = $parameters['?']['uuid'];
        }
        if (isset($parameters['?']['account_cyclos'])) {
            $account_cyclos = $parameters['?']['account_cyclos'];
        }
        $session = $this->request->getSession();
        $this->set('translates', $this->translates);
        $florapi = $this->fetchTable('Florapi');
        $session->write('uuid', $uuid);
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        $datakeys = $nvocompte->toArray();
        $this->nbsteps = 9;
        $this->set('nbsteps', $this->nbsteps);
        if ($nvocompte->account_cyclos) {
            $titre = "(SANS COMPTE NUMERIQUE)";
        } else {
            $titre = "NUMERIQUE";
        }
        $this->set('titre', $titre);
        //$datas = $session->datas;
        //$infos = json_decode($datas, TRUE);
        $adh = $florapi->getAdh($nvocompte->email);
        $assos = $florapi->getOdooAssos();
        if ($adh[0]['orga_choice'] != null) {
            foreach ($assos as $asso) {
                if ($asso['id'] == $adh[0]['orga_choice']) {
                    $assochosen = $asso['name'];
                    continue;
                }
            }
            if (isset($assochosen)) {
                $this->set('assochosen', $assochosen);
            } else {
                $this->set('assochosen', 'Aucune');
            }
        } else {
            $this->set('assochosen', 'Aucune');
        }
        if ($adh[0]['membership_stop'] != null) {
            $expirdate = DateTime::createFromFormat('D, d M Y H:i:s T', $adh[0]['membership_stop']);
            $adh[0]['membership_stop'] = $expirdate->format("d m Y");
        }
        $this->set('adh', $adh[0]);
        //$sess->write('todo', 'update');
        $session->write('nbsteps', $this->nbsteps);
        $data = array();
        $data['todo'] = 'update';
        foreach ($adh[0] as $key => $value) {
            if ($key == "lastname") {
                $lastname = str_replace($this->search, $this->replace, $value);
                //$sess->write("last_name",trim(strtoupper($lastname)));
                $data['lastname'] = trim(strtoupper($lastname));
            } elseif ($key == "firstname") {
                $firstname = str_replace($this->search, $this->replace, $value);
                //$sess->write("first_name", trim(ucfirst($firstname)));
                $data['firstname'] = trim(ucfirst($firstname));
            } elseif (array_key_exists($key, $datakeys)) {
                if ($key != "id") {
                    $data[$key] = $value;
                }
            }
        }
        if ($data['membership_stop'] != null) {
            $data['membership_stop'] = $expirdate->format("Y-m-d H:i:s");
        }
        $this->set('account_cyclos', $account_cyclos);
        $this->update($uuid, $data);
        $this->log('get '.$uuid, 'debug');
        return;

    }

    /*
     * Add infos in SQL
     */
    public function add($data)
    {
        $this->Authorization->skipAuthorization();
        $nvocompte = $this->Nouveaucompte->newEmptyEntity();
        //if ($this->request->is('post')) {
        $nvocompte = $this->Nouveaucompte->patchEntity($nvocompte, $data);
        if ($this->Nouveaucompte->save($nvocompte)) {
            $this->log('createSQL nouveaucompte', 'debug');
        } else {
            //Debug($session->getErrors());
            $this->log('error to create nouveaucompte', 'error');
        }
    }

    /*
     * Update infos in SQL
     */
    public function update($uuid, $data)
    {
        $this->Authorization->skipAuthorization();
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        $this->Nouveaucompte->patchEntity($nvocompte, $data);
        if ($this->Nouveaucompte->save($nvocompte)) {
            $this->log('updateSQL nouveaucompte', 'debug');
            //Debug($nvocompte->getErrors());
        } else {
            Debug($nvocompte->getErrors());
            $this->log('error to update nouveaucompte', 'error');
        }
        //}
    }

    /*
     *  va chercher les infos de l'adhérent et vérifie que c'est bien lui (par mail)
     */
    public function confirmationemail($email = "", $account_cyclos=False)
    {
        $this->Authorization->skipAuthorization();
        $florapi = $this->fetchTable('Florapi');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $adh = $florapi->getAdh($data['email']);
            if ($adh !== null) {
                if (count($adh) > 0) {
                    $this->set('adh', $adh[0]);
                    if (!$adh[0]['account_cyclos']) {
                        $existaccount = True;
                        $subject = "Commencer la création de votre compte";
                        $uuid = bin2hex(random_bytes(40));
                        $datas = array(
                            'uuid' => $uuid,
                            'lastname' => $adh[0]['lastname'],
                            'firstname' => $adh[0]['firstname'],
                            'email' => $data['email'],
                            'account_cyclos' => $account_cyclos
                        );
                        $this->sendconfirmationmail($subject, $datas['email'], $datas);
                        $this->add($datas);
                        $this->log('confirmationemail '.$uuid, 'debug');
                    }
                } else {
                    $existaccount = False;
                }
                //$this->set('existaccount', $existaccount);
            } else {
                $this->Flash->error(__('Erreur ! Merci de de réessayer plus tard'));
                $this->log('pb avec florapi', 'error');
            }
        } else {
            $adh = $florapi->getAdh($email);
            if ($adh != null) {
                if (count($adh) > 0) {
                    $this->set('adh', $adh[0]);
                    if (!$adh[0]['account_cyclos']) {
                        $existaccount = True;
                        $uri = "";
                        $url = "";
                        $subject = "Commencer la création de votre compte";
                        $uuid = bin2hex(random_bytes(40));
                        $datas = array(
                            'uuid' => $uuid,
                            'lastname' => $adh[0]['lastname'],
                            'firstname' => $adh[0]['firstname'],
                            'email' => $email,
                            'account_cyclos' => $account_cyclos
                        );
                        $this->sendconfirmationmail($subject, $email, $datas);
                        $this->add($datas);
                        $this->log('confirmationemail '.$uuid, 'debug');
                    }
                } else {
                    $existaccount = False;
                }
            } else {
                $this->Flash->error(__('Erreur ! Merci de de réessayer plus tard'));
                $this->log('pb avec florapi', 'error');
            }
        }
    }

    /*
     * Etape d'informations supplémentaires (Adresse, Ville, Téléphone)
     */
    public function infossup()
    {
        $this->Authorization->skipAuthorization();
        $florapi = $this->fetchTable('Florapi');
        $session = $this->request->getSession();

        if ($session->read('uuid') != NULL) {
            $uuid = $session->read('uuid');
            if ($uuid == NULL) {
                $this->log('session expired', 'error');
                return $this->redirect('/nouveaucompte/index');
            }
            /* on récupère les infos stockées en SQL */
            $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
            $count = $this->Nouveaucompte->findByUuid($uuid)->count();
            if ($count > 0) {
                $nvocompte = $nvocomptes->firstOrFail();
            } else {
                $this->log('uuid not exist', 'error');
                return $this->redirect('/nouveaucompte/index');
            }
            $this->set('comptecyclos', $nvocompte->account_cyclos);

        } else {
            $this->log('uuid not exist : infossup redirect to index ', 'debug');
            return $this->redirect('/nouveaucompte/index');
        }
        /*if ($nvocompte->accept_newsletter == NULL) {
            Debug('ok');
        }*/
        $passedArgs = $this->request->getParam('pass');
        if ($nvocompte->account_cyclos) {
            $nbsteps = 9;
        } else {
            $nbsteps = 7;
        }
        $this->set('nbsteps', $nbsteps);
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $session->write('nbsteps', $nbsteps);
        /*if (!isset($passedArgs['accept_newsletter'])) {
            $session->write('accept_newsletter', False);
        } else {
            $session->write('accept_newsletter', True);
        }*/
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            //Debug($data);
            foreach ($data as $key => $value) {
                if ($key == "last_name") {
                    $lastname = str_replace($this->search, $this->replace, $value);
                    $data['lastname'] = trim(strtoupper($lastname));
                } elseif ($key == "first_name") {
                    $firstname = str_replace($this->search, $this->replace, $value);
                    $data['firstname'] = trim(ucfirst($firstname));
                } elseif ($key == "accept_newsletter") {
                    if ($value == "on") {
                        $data['accept_newsletter'] = True;
                    } else {
                        $data['accept_newsletter'] = False;
                    }
                }
            }
            if (!isset($data['accept_newsletter'])) {
                $data['accept_newsletter'] = False;
            }
            if (isset($data['form_step_update'])) {
                if (isset($data['account_cyclos'])) {
                    $data['account_cyclos'] = True;
                    $this->set('comptecyclos', True);
                    $titre = "NUMERIQUE";
                    $nbsteps = 9;
                } else {
                    $data['account_cyclos'] = False;
                    $this->set('comptecyclos', False);
                    $titre = "(SANS COMPTE NUMERIQUE)";
                    $nbsteps = 7;
                }
                $this->set('titre', $titre);
                $this->set('nbsteps', $nbsteps);
                $session->write('nbsteps', $nbsteps);
                $this->update($uuid, $data);
            } else {
                $adh = $florapi->getAdh($data['email']);
                if (count($adh) > 0) {
                    return $this->redirect('/nouveaucompte/confirmationemail/' . urlencode($adh[0]['email']));
                }
                $data['todo'] = 'create';
                //Debug($data);
                $this->update($uuid, $data);
                //session->write('todo', 'create');
            }
            $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
            $dirname = str_replace(' ', '', date("Ymd_H:i:s") . '_' . $nvocompte->lastname . '-' . $nvocompte->firstname);
            $session->write('dirname', $dirname);
            $nextcloud = $this->fetchTable('Nextcloud');
            if (!$nextcloud->isFolderExist($dirname)) {
                $infos = $nextcloud->createFolder($dirname);
                $this->log('create folder to nextcloud', 'debug');
            }
            $file = TMP . "/mail.txt";
            file_put_contents($file, $nvocompte->email);
            $nextcloud->uploadFile($dirname, $file, "mail.txt");
            $this->log('infossup '.$uuid, 'debug');
        }
    }

    /*
     * Etape d'upload de la pièce d'identité
     */
    public function uploadid()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['address'])) {
                $data['street'] = $data['address'];
            }
            $this->update($uuid, $data);
        }
        $this->set('nbsteps', $session->read('nbsteps'));
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $this->log('uploadid '.$uuid, 'debug');
    }

    

    /*
     * Etape de validation de la pièce d'identité
     */
    public function validid()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $nextcloud = $this->fetchTable('Nextcloud');
        $mindee = $this->fetchTable('Mindee');
        $uuid = $session->read('uuid');
        
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $this->set('nbsteps', $session->read('nbsteps'));
        if ($this->request->is('post')) {
            if ($session->read('dirname') == NULL) {
                $dirname = str_replace(' ', '', date("Ymd_H:i:s") . '_' . $nvocompte->lastname . '-' . $nvocompte->firstname);
                $session->write('dirname', $dirname);

            } else {
                $dirname = $session->read('dirname');
            }
            if (!$nextcloud->isFolderExist($dirname)) {
                $infos = $nextcloud->createFolder($dirname);
                $this->log('create folder to nextcloud', 'debug');
            }
            //Debug($_FILES);
            //$file = $this->request->getUploadedFiles();
            //Debug($this->request->getData());
            $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
            $this->log("dirname : " . $dirname . " tmp_name : " . $_FILES['uploadedFile']['tmp_name'] . " name : " . $_FILES['uploadedFile']['name'], 'debug');
            $res = $mindee->checkIdentity($_FILES['uploadedFile']['tmp_name'], $nvocompte->lastname, $nvocompte->firstname);
            /* FORCE VALIDATE CI */
            $res['result'] = 'OK';
            $res['birth_date'] = '1980-01-01';
            /* FIN */
            $this->set('res', $res);
            //Debug($res);
            $newpath = $nextcloud->addwatermark($_FILES['uploadedFile']['name'], $_FILES['uploadedFile']['tmp_name']);
            //$nextcloud->uploadFile($dirname, $_FILES['uploadedFile']['tmp_name'], $_FILES['uploadedFile']['name']);
            $path_parts = pathinfo($newpath);
            $nextcloud->uploadFile($dirname, $newpath, $path_parts['filename'].'.'.$path_parts['extension']);
            if ($res['result']) {
                if (!$mindee->isMajor($res['birth_date'])) {
                    $this->Flash->error(__('Vous devez être majeur.e'));
                    $this->log('error not major', 'error');
                } else {
                    $this->Flash->success(__('La pièce d\'identité a été validée.'));
                }
            } else {
                if (($res['error'] == 'Non reconnu comme une pièce d\'identité') or 
                ($res['error'] == 'Document non reconnu')) {
                    if ($nvocompte->recheckid >= 5) {
                        $this->Flash->warning(__('Votre pièce d\'identité devra être validée manuellement.'));
                        $data = array();
                        $data['action_needed'] = True;
                        $this->update($uuid, $data);
                        return $this->redirect('/nouveaucompte/adh');
                    }
                    $data = array();
                    $data['recheckid'] = $nvocompte->recheckid+1;
                    $this->update($uuid, $data);
                } 
                $this->Flash->error(__($res['error']));
                $this->log($res['error'], 'error');
                $this->log("Nom attendu : " . $res['error'], 'error');
                return $this->redirect('/nouveaucompte/uploadid');
            }

            //Debug($session->read('dirname'));
        }
        $this->log('validid '.$uuid, 'debug');
    }

    /*
     * Etape concernant les choix d'adhésion
     */
    public function adh()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['address'])) {
                $data['street'] = $data['address'];
            }
            $this->update($uuid, $data);
        }
        if ($nvocompte->account_cyclos) {
            $step = 5;
        } else {
            $step = 3;
        }
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        if ($nvocompte->lastname == NULL) {
            $this->log('try to access to adh without lastname', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->street == NULL) {
            $this->log('street not exist : adh redirect to index ', 'debug');
            return $this->redirect('/nouveaucompte/index');
        }
        $this->set('step', $step);
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }

        if ($nvocompte->membership_stop != NULL) {
            $membershipstop = new DateTime($nvocompte->membership_stop);
            $newdateadh = $membershipstop->modify('+1 day');
            $this->set('membershipstop', $membershipstop->format("d-m-Y"));
            $this->set('newdateadh', $newdateadh->format("d-m-Y"));
        }
        $this->set('titre', $titre);
        $this->set('nbsteps', $session->read('nbsteps'));
        $this->set('comptecyclos', $nvocompte->account_cyclos);
        $this->set('nvocompte', $nvocompte);
        $this->log('adh '.$uuid, 'debug');
    }

    /*
     * Etape pour les choix du change
     */
    public function choosechange()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['nbeurosadhmensuel'])) {
                if ($data['nbeurosadhmensuel'] == "other") {
                    $data['nbeurosadhmensuel'] = strval(number_format(floatval($data['montantmensuel']), 2));
                    $data['nbeurosadhannuel'] = NULL;
                } else {
                    $data['nbeurosadhmensuel'] = strval(number_format(floatval($data['nbeurosadhmensuel']), 2));
                    $data['nbeurosadhannuel'] = NULL;
                }
            }
            if (isset($data['nbeurosadhannuel'])) {
                if ($data['nbeurosadhannuel'] == "other") {
                    $data['nbeurosadhannuel'] = strval(number_format(floatval($data['montantannuel']), 2));
                    $data['nbeurosadhmensuel'] = NULL;
                } else {
                    $data['nbeurosadhannuel'] = strval(number_format(floatval($data['nbeurosadhannuel']), 2));
                    $data['nbeurosadhmensuel'] = NULL;
                }
            }
            $this->update($uuid, $data);
        }
        if ($nvocompte->account_cyclos) {
            $step = 6;
        } else {
            $step = 4;
        }
        if ($nvocompte->lastname == NULL) {
            $this->log('try to access to adh without lastname', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->street == NULL) {
            $this->log('steet not exist : adh redirect to index ', 'debug');
            return $this->redirect('/nouveaucompte/index');
        }
        $this->set('step', $step);
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $this->set('nbsteps', $session->read('nbsteps'));
        $this->set('comptecyclos', $nvocompte->account_cyclos);
        $this->log('choosechange '.$uuid, 'debug');
    }

    /*
     * Etape pour entrer l'IBAN
     */
    public function editiban()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (isset($data['nbflorains'])) {
                if ($data['nbflorains'] == "other") {
                    $data['nbflorains'] = strval(number_format(floatval($data['montant']), 2));
                } else {
                    $data['nbflorains'] = strval(number_format(floatval($data['nbflorains']), 2));
                }
            }
            if (isset($data['nbeurosadhmensuel'])) {
                if ($data['nbeurosadhmensuel'] == "other") {
                    $data['nbeurosadhmensuel'] = strval(number_format(floatval($data['montantmensuel']), 2));
                    $data['nbeurosadhannuel'] = NULL;
                } else {
                    $data['nbeurosadhmensuel'] = strval(number_format(floatval($data['nbeurosadhmensuel']), 2));
                    $data['nbeurosadhannuel'] = NULL;
                }
            }
            if (isset($data['nbeurosadhannuel'])) {
                if ($data['nbeurosadhannuel'] == "other") {
                    $data['nbeurosadhannuel'] = strval(number_format(floatval($data['montantannuel']), 2));
                    $data['nbeurosadhmensuel'] = NULL;
                } else {
                    $data['nbeurosadhannuel'] = strval(number_format(floatval($data['nbeurosadhannuel']), 2));
                    $data['nbeurosadhmensuel'] = NULL;
                }
            }
            $this->update($uuid, $data);
        }
        if ($nvocompte->account_cyclos) {
            $step = 7;
        } else {
            $step = 5;
        }
        $this->set('step', $step);
        $this->set('nbsteps', $session->read('nbsteps'));
        $this->set('account_cyclos', $nvocompte->account_cyclos);
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        if ($nvocompte->lastname == NULL) {
            $this->log('try to access to editiban without lastname', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $this->log('editiban '.$uuid, 'debug');
    }

    /*
     * Etape pour choisir l'association
     */
    public function chooseasso()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $florapi = $this->fetchTable('Florapi');
        $assosjson = $florapi->getOdooAssos();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['iban'] = trim(str_replace(' ', '', $data['iban']));
            $this->update($uuid, $data);
        }
        if ($nvocompte->account_cyclos) {
            $step = 8;
        } else {
            $step = 6;
        }
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        $this->set('assosjson', $assosjson);
        $this->set('nvocompte', $nvocompte);
        $this->set('step', $step);
        $this->set('nbsteps', $session->read('nbsteps'));
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $this->set('comptecyclos', $nvocompte->account_cyclos);
        if ($nvocompte->lastname == NULL) {
            $this->log('try to access to chooseasso without lastname', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->iban == NULL) {
            $this->log('try to access to chooseasso without iban', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $this->log('chooseasso '.$uuid, 'debug');
    }

    /*
     * Etape de fin / Confirmation des informations
     */
    public function fin()
    {
        $this->Authorization->skipAuthorization();
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $this->update($uuid, $data);
        }
        if ($nvocompte->account_cyclos) {
            $step = 9;
        } else {
            $step = 7;
        }      
        $this->set('step', $step);
        $this->set('nbsteps', $session->read('nbsteps'));
        if ($nvocompte->account_cyclos) {
            $titre = "NUMERIQUE";
        } else {
            $titre = "(SANS COMPTE NUMERIQUE)";
        }
        $this->set('titre', $titre);
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        $results = $this->create_infos_for_odoo($nvocompte);
        $assoname = $this->get_asso_name($nvocompte->orga_choice);
        $this->set('assoname', $assoname);
        $this->set('results', $results);
        $this->set('nvocompte', $nvocompte);
        if ($nvocompte->lastname == NULL) {
            $this->log('try to access to fin without lastname', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->orga_choice == NULL) {
            $this->log('try to access to fin without orga_choice', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if ($nvocompte->todo == 'create') {
            $datas = $results['infos'];
            $datas['assoname'] = $assoname;
            $datas['uuid'] = $uuid;
            /* Envoi email de confirmation */
            $this->sendcreationaccount('Florain : Finaliser la création de votre compte', $results['email'], $datas);
        }
        if ($nvocompte->todo == 'update') {
            $datas = $results['infos'];
            if ($nvocompte->membership_stop != NULL) {
                $contactsadmin = Configure::read('ContactsAdmin');
                $now = DateTime::now();
                if ($now > $nvocompte->membership_stop) {
                    $startdate_debit = $now;
                } else {
                    $startdate_debit = $nvocompte->membership_stop;
                }
                $this->update($uuid, array('startdate_debit' => $startdate_debit));
            }
        }
        $this->log('fin '.$uuid, 'debug');
    }

    public function create_infos_for_odoo($nvocompte)
    {
        $this->Authorization->skipAuthorization();
        $results = array();
        $results['translates'] = $this->translates;
        $results['email'] = $nvocompte->email;
        $infos = array(
            'firstname' => $nvocompte->firstname,
            'lastname' => $nvocompte->lastname,
            'street' => $nvocompte->street,
            'city' => $nvocompte->city,
            'zip' => $nvocompte->postcode,
            'phone' => $nvocompte->phone,
            'orga_choice' => $nvocompte->orga_choice
        );
        if ($nvocompte->accept_newsletter) {
            $infos['accept_newsletter'] = 't';
        } else {
            $infos['accept_newsletter'] = 'f';
        }
        
        if ($nvocompte->account_cyclos) {
            $infos['account_cyclos'] = 't';
            $infos['changeeuros'] = $nvocompte->nbflorains;
        } else {
            $infos['account_cyclos'] = 'f';
            $infos['changeeuros'] = '0';
        }
        $results['infos'] = $infos;
        return $results;
    }

    public function get_asso_name($asso_id)
    {
        $florapi = $this->fetchTable('Florapi');
        $assos = $florapi->getOdooAssos();
        foreach($assos as $asso) {
            if($asso['id'] == $asso_id) {
                return $asso['name'];
            }
        }
        return "Aucune";
    }

    public function create_odoo_adh($datas)
    {
        $florapi = $this->fetchTable('Florapi');
        $ref = $florapi->getFreeRef();
        $datas['infos']['ref'] = $ref;
        $res = $florapi->postAdh($datas);
        return $res;
    }

    public function update_odoo_adh($datas)
    {
        $florapi = $this->fetchTable('Florapi');
        $florapi->updateAdh($datas);
    }

    public function create_mollie_user($lastname, $firstname, $email)
    {
        $this->log('create_mollie_user '.$firstname, 'debug');
        $mollie = $this->fetchTable('Mollie');
        $name_whitoutspace = $lastname.$firstname;
        $results = $mollie->get_customer($email);
        if (count($results) > 0) {
            $this->log('mollie user already exists', 'error');
            return $results[0]['id'];
        }
        $infoscustomer = $mollie->create_customer($email, $name_whitoutspace);
        return $infoscustomer['id'];
    }

    public function create_mandate($nvocompte, $customerid)
    {
        $this->log('create_mandate '.$customerid, 'debug');
        $mollie = $this->fetchTable('Mollie');
        $name = $nvocompte->lastname." ".$nvocompte->firstname;
        $infosmandate = $mollie->create_mandate($customerid, $nvocompte->iban, $name, $nvocompte->email);
        if (is_numeric($infosmandate["status"])) {
            $strmsg = "ERROR Mollie".strval($infosmandate["status"]);
            $this->Flash->error(__('Une erreur s\'est produite, merci de contacter le support'));
            $this->log('error create mandate '.$customerid.' '.print_r($infosmandate, TRUE), 'error');
            return;
        }
        return $infosmandate;
    }

    public function create_mollie_adh($nvocompte, $customerid, $mandateId)
    {
        $this->log('create_mollie_adh '.$customerid, 'debug');
        $mollie = $this->fetchTable('Mollie');
        $florapi = $this->fetchTable('Florapi');
        $results = array();
        $name = $nvocompte->lastname." ".$nvocompte->firstname;
        $boolstartdatenow = False;
        if ($nvocompte->startdate_debit != NULL) {
            $startdate_debit = new DateTime($nvocompte->startdate_debit);
            $startdate = $startdate_debit->format("Y-m-d");
            $boolstartdatenow = False;
        } else {
            $now = new DateTime('NOW');
            $startdate = $now->format("Y-m-d");
            $boolstartdatenow = True;
        }
        if (!$mollie->has_adh_florain($nvocompte->email)) {
            if ($nvocompte->nbeurosadhannuel != NULL) {
                $results = $mollie->create_subscription_annually($nvocompte->nbeurosadhannuel, $customerid, $mandateId, "Adhésion Florain Annuelle", $startdate);
                if (!isset($results['id'])) {
                    $this->log('error create mollie subscription adh '.$nvocompte->nbeurosadhannuel.' '.$customerid.' '.$startdate.' '.print_r($results, TRUE), 'error');
                } else {
                    if ($boolstartdatenow) {
                        $datas = array(
                            'email' => $nvocompte->email,
                            'name' => $name,
                            'amount' => $nvocompte->nbeurosadhannuel
                        );
                        $florapi->postMembership($datas);
                    }
                }
            }
            if ($nvocompte->nbeurosadhmensuel != NULL) {
                $results = $mollie->create_subscription_monthly($nvocompte->nbeurosadhmensuel, $customerid, $mandateId, "Adhésion Florain Mensuelle", $startdate, 0);
                if (!isset($results['id'])) {
                    $this->log('error create mollie subscription adh '.$nvocompte->nbeurosadhmensuel.' '.$customerid.' '.$startdate.' '.print_r($results, TRUE), 'error');
                } else {
                    if ($boolstartdatenow) {
                        $datas = array(
                            'email' => $nvocompte->email,
                            'name' => $name,
                            'amount' => $nvocompte->nbeurosadhmensuel
                        );
                        $florapi->postMembership($datas);
                    }
                }
            }
        } else {
            $this->log('mollie mandate exists', 'error');
        }
        return $results;
    }

    public function create_mollie_change($nvocompte, $customerid, $mandateId)
    {
        $this->log('create_mollie_change '.$customerid, 'debug');
        $mollie = $this->fetchTable('Mollie');
        //$florapi = $this->fetchTable('Florapi');
        $now = new DateTime('NOW');
        $results = array();
        $startdate = $now->format("Y-m-d");
        if (!$mollie->has_change_florain($nvocompte->email)) {
            $results = $mollie->create_subscription_monthly($nvocompte->nbflorains, $customerid, $mandateId, "Change Florain", $startdate, 0);
            if (!isset($results['id'])) {
                $this->log('error create mollie  change '.$nvocompte->nbflorains.' '.$customerid.' '.$startdate.' '.print_r($results, TRUE), 'error');
            }
        }
    }

    /*
     * Activate account after email for new adh
     */
    public function activate($uuid = "")
    {
        $this->Authorization->skipAuthorization();
        $contactsadmin = Configure::read('ContactsAdmin');
        $parameters = $this->request->getAttribute('params');
        if (isset($parameters['?']['uuid'])) {
            $uuid = $parameters['?']['uuid'];
        } else {
            return ;
        }
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        $this->set('action_needed', $nvocompte->action_needed);
        $this->set('account_cyclos', $nvocompte->account_cyclos);
        
        if (!$nvocompte->action_needed) {
            if (!$nvocompte->done) {
                $resultsodoo = $this->create_infos_for_odoo($nvocompte);
                
                // create Odoo Adh
                /* Vérifier si le compte odoo existe ? */
                $this->create_odoo_adh($resultsodoo);
                    
                // create Mollie user if necessary 
                $customerid = $this->create_mollie_user($nvocompte->lastname, $nvocompte->firstname, $nvocompte->email);

                // create Mollie mandate 
                $infosmandate = $this->create_mandate($nvocompte, $customerid);

                // create Mollie subscription for adh 
                $results = $this->create_mollie_adh($nvocompte, $customerid, $infosmandate['id']);

                // create Mollie subscription for change
                if ($nvocompte->account_cyclos) {
                    $this->create_mollie_change($nvocompte, $customerid, $infosmandate['id']);
                }

                $resultsodoo['infos']['email'] = $nvocompte->email;
                $infosmails = $resultsodoo['infos'];
                if ($nvocompte->nbeurosadhmensuel != NULL) {
                    $infosmails['nbeurosadhmensuel'] = $nvocompte->nbeurosadhmensuel;
                }
                if ($nvocompte->nbeurosadhannuel != NULL) {
                    $infosmails['nbeurosadhannuel'] = $nvocompte->nbeurosadhannuel;
                }
                $infosmails['todo']= $nvocompte->todo;
                foreach ($contactsadmin as $contact) {
                    $this->sendnouvelleinscription($contact, $infosmails);
                }
                $data['done'] = True;
                $this->update($uuid, $data);
            }
        } else {
            $data['confirmed'] = True;
            $this->update($uuid, $data);
        }
        $this->log('activate '.$uuid, 'debug');
    }

    public function updateadh()
    {
        $this->Authorization->skipAuthorization();
        $contactsadmin = Configure::read('ContactsAdmin');
        $session = $this->request->getSession();
        $uuid = $session->read('uuid');
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
            $this->set('account_cyclos', $nvocompte->account_cyclos);
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if (!$nvocompte->done) {
            $results = $this->create_infos_for_odoo($nvocompte);
            $infos = $results['infos'];
            $infos['email'] = $results['email'];
            if ($nvocompte->nbeurosadhmensuel != NULL) {
                $infos['nbeurosadhmensuel'] = $nvocompte->nbeurosadhmensuel;
            }
            if ($nvocompte->nbeurosadhannuel != NULL) {
                $infos['nbeurosadhannuel'] = $nvocompte->nbeurosadhannuel;
            }
            $infos['todo']= $nvocompte->todo;
            foreach ($contactsadmin as $contact) {
                $this->sendnouvelleinscription($contact, $infos);
            }
            // create Odoo Adh
            $this->update_odoo_adh($results);
                
            // create Mollie user if necessary 
            $customerid = $this->create_mollie_user($nvocompte->lastname, $nvocompte->firstname, $nvocompte->email);

            // create Mollie mandate 
            $infosmandate = $this->create_mandate($nvocompte, $customerid);

            // create Mollie subscription for adh 
            $results = $this->create_mollie_adh($nvocompte, $customerid, $infosmandate['id']);

            // create Mollie subscription for change
            if ($nvocompte->account_cyclos) {
                $this->create_mollie_change($nvocompte, $customerid, $infosmandate['id']);
            }
            $data['done'] = True;
            $this->update($uuid, $data);
        }
        $this->log('updateadh '.$uuid, 'debug');
    }

    public function updateadhfordebug($uuid="")
    {
        Debug("test");
        $this->Authorization->skipAuthorization();
        $contactsadmin = Configure::read('ContactsAdmin');
        $session = $this->request->getSession();
        $parameters = $this->request->getAttribute('params');
        if (isset($parameters['?']['uuid'])) {
            $uuid = $parameters['?']['uuid'];
        } else {
            return ;
        }
        Debug($uuid);
        if ($uuid == NULL) {
            $this->log('session expired', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        $nvocomptes = $this->Nouveaucompte->findByUuid($uuid);
        $count = $this->Nouveaucompte->findByUuid($uuid)->count();
        if ($count > 0) {
            $nvocompte = $nvocomptes->firstOrFail();
            $this->set('account_cyclos', $nvocompte->account_cyclos);
        } else {
            $this->log('uuid not exist', 'error');
            return $this->redirect('/nouveaucompte/index');
        }
        if (!$nvocompte->done) {
            $results = $this->create_infos_for_odoo($nvocompte);
            $infos = $results['infos'];
            $infos['email'] = $results['email'];
            if ($nvocompte->nbeurosadhmensuel != NULL) {
                $infos['nbeurosadhmensuel'] = $nvocompte->nbeurosadhmensuel;
            }
            if ($nvocompte->nbeurosadhannuel != NULL) {
                $infos['nbeurosadhannuel'] = $nvocompte->nbeurosadhannuel;
            }
            $infos['todo']= $nvocompte->todo;
            foreach ($contactsadmin as $contact) {
                $this->sendnouvelleinscription($contact, $infos);
            }
            // create Odoo Adh
            $this->update_odoo_adh($results);
                
            // create Mollie user if necessary 
            $customerid = $this->create_mollie_user($nvocompte->lastname, $nvocompte->firstname, $nvocompte->email);

            // create Mollie mandate 
            $infosmandate = $this->create_mandate($nvocompte, $customerid);

            // create Mollie subscription for adh 
            $results = $this->create_mollie_adh($nvocompte, $customerid, $infosmandate['id']);

            // create Mollie subscription for change
            if ($nvocompte->account_cyclos) {
                $this->create_mollie_change($nvocompte, $customerid, $infosmandate['id']);
            }
            $data['done'] = True;
            $this->update($uuid, $data);
        }
        $this->log('updateadh '.$uuid, 'debug');
    }

    /* Envoie l'email à la derière étape (fin), afin de valider l'email et en attente du click pour l'activation
    */
    public function sendcreationaccount($subject, $to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject($subject)
            ->setFrom(['noreply@eflorain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('creationaccount')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function sendconfirmationmail($subject, $to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject($subject)
            ->setFrom(['noreply@eflorain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('confirmationemail')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function sendnouvelleinscription($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Nouvelle adhésion')
            ->setFrom(['noreply@eflorain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('nouvelleinscription')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function list()
    {
        $this->Authorization->skipAuthorization();
        $this->viewBuilder()->setLayout('bdc');
        $nvocomptes = $this->Nouveaucompte->find()->orderBy(['modified' => 'DESC']);
        $this->set('nvocomptes', $nvocomptes);
    }

    public function deletedemande($uuid)
    {
        $this->Authorization->skipAuthorization();
        $nvocompte = $this->Nouveaucompte->findByUuid($uuid)->firstOrFail();
        if ($this->Nouveaucompte->delete($nvocompte)) {
            $this->Flash->success(__('La demande a été supprimée'));
            $this->redirect('/nouveaucompte/list');
        }
    }
}
?>