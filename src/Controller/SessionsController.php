<?php
// src/Controller/SessionsController.php
namespace App\Controller;
use Cake\Mailer\Mailer;
use Cake\I18n\DateTime;

class SessionsController extends AppController
{
    private $translates = array(
        'firstname' => 'Prénom',
        'lastname' => 'Nom',
        'email' => 'Email',
        'membership_state' => 'Adhésion',
        'membership_stop' => 'Date de fin d\'adhésion',
        'ref' => 'Numéro d\'adhérent',
        'orga_choice' => 'Nom de l\'association choisie',
    );
    
    public function index()
    {
        $sessions = $this->Sessions->find('all');
        foreach ($sessions as $session) {
            Debug($session->datas);
        }
        $this->set(compact('sessions'));
    }

    public function get($uuid="")
    {
        $parameters = $this->request->getAttribute('params');
        if (isset($parameters['?']['uuid'])) {
            $uuid = $parameters['?']['uuid'];
        }
        $sess = $this->request->getSession();
        $this->set('translates', $this->translates);
        $florapi = $this->fetchTable('Florapi');
        $sessions = $this->Sessions->findByUuid($uuid);
        foreach ($sessions as $session) {
            $this->nbsteps = 9;
            $this->set('nbsteps', $this->nbsteps);
            //$datas = $session->datas;
            //$infos = json_decode($datas, TRUE);
            $adh = $florapi->getAdh($session->email);
            $assos = $florapi->getOdooAssos();
            foreach($assos as $asso) {
                if ($asso['id'] == $adh[0]['orga_choice']) {
                    $assochosen = $asso['name'];
                    continue;
                }
            }
            $this->set('assochosen', $assochosen);
            $this->set('adh', $adh[0]);
            $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
            $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
            $sess->write('todo', 'update');
            $sess->write('nbsteps', $this->nbsteps);
            $data = array();
            $data['todo'] = 'update';
            foreach($adh[0] as $key=>$value) {
                if ($key=="lastname") {
                    $lastname = str_replace($search, $replace, $value);
                    //$sess->write("last_name",trim(strtoupper($lastname)));
                    $data['lastname'] = trim(strtoupper($lastname));
                } elseif ($key=="firstname") {
                    $firstname = str_replace($search, $replace, $value);
                    //$sess->write("first_name", trim(ucfirst($firstname)));
                    $data['firstname'] = trim(ucfirst($firstname));
                }
                else {
                    $data[$key] = $value;
                    //$sess->write($key, trim(strtolower($value)));
                }
            }
            //Debug($data);
            $expirdate = DateTime::createFromFormat('D, d M Y H:i:s T', $data['membership_stop']);
            $data['membership_stop'] = $expirdate->format("Y-m-d H:i:s");
            $this->update($uuid, $data);
            return;
        }
    }

    public function add($data)
    {
        $session = $this->Sessions->newEmptyEntity();
        //if ($this->request->is('post')) {
        $session = $this->Sessions->patchEntity($session, $data);
        if ($this->Sessions->save($session)) {
            $this->log('create session', 'debug');
        } else {
            //Debug($session->getErrors());
            $this->log('error to create session', 'error');
        }
    }

    public function update($uuid, $data)
    {
        $sessions = $this->Sessions->findByUuid($uuid);
        foreach ($sessions as $session) {
            $session->todo = $data['todo'];
            $session->lastname = $data['lastname'];
            $session->firstname = $data['firstname'];
            $session->accept_newsletter = intval($data['accept_newsletter']);
            $session->ref = intval($data['ref']);
            $session->orga_choice = $data['orga_choice'];
            $session->membership_state = $data['membership_state'];
            $session->membership_stop = $data['membership_stop'];
            if ($this->Sessions->save($session)) {
                $this->log('update session', 'debug');
            } else {
                //Debug($session->getErrors());
                $this->log('error to create update', 'error');
            }
        }
    }

    public function confirmationemail($email="")
    {
        $florapi = $this->fetchTable('Florapi');
        if ($this->request->is('post')) {
            $data =$this->request->getData();
            $adh = $florapi->getAdh($data['email']);
            if (count($adh) > 0) {
                $this->set('adh', $adh[0]);
                $existaccount=True;
                $subject = "Commencer la création de votre compte";
                $uuid = bin2hex(random_bytes(40));
                $datas = array(
                    'uuid' => $uuid,
                    'lastname' => $adh[0]['lastname'],
                    'firstname' => $adh[0]['firstname'],
                    'email' => $data['email']
                );
                $this->sendconfirmationmail($subject, $datas['email'], $datas);
                $this->add($datas);
            } else {
                $existaccount=False;
            }
            $this->set('existaccount', $existaccount);
        } else {
            $adh = $florapi->getAdh($email);
            if (count($adh) > 0) {
                $this->set('adh', $adh[0]);
                $existaccount=True;
                $uri = "";
                $url = "";
                $subject = "Commencer la création de votre compte";
                $uuid = bin2hex(random_bytes(40));
                $datas = array(
                    'uuid' => $uuid,
                    'lastname' => $adh[0]['lastname'],
                    'firstname' => $adh[0]['firstname'],
                    'email' => $email
                );
                $this->sendconfirmationmail($subject, $email, $datas);
                $this->add($datas);
            } else {
                $existaccount=False;
            }
        }
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
}