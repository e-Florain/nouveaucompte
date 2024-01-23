<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Mailer\Mailer;

class CheckAdhCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $datas = array();
        $florapi = $this->fetchTable('Florapi');
        $adhs = $florapi->getAllAdh();
        foreach ($adhs as $adh) {
            if ($adh['account_cyclos']) {
                if (!$this->checkMollie($adh['email'])) {
                    $datas[] = $adh['email'];
                }
            }
        }
        $contactsadmin = array(
            "virginie@florain.fr",
            "julie@florain.fr",
            "groche@guigeek.org"
        );
        
        foreach ($contactsadmin as $contact) {
            $this->sendEmailMonitor($contact, $datas);
        }
        return static::CODE_SUCCESS;
    }

    public function checkMollie($email)
    {
        $boolchange = False;
        $booladh = False;
        $mollie = $this->fetchTable('Mollie');
        $subs = $mollie->get_subscriptions($email);
        foreach ($subs as $sub) {
            if ($sub['description'] == "Change Florain") {
                $boolchange = True;
            }
            if (($sub['description'] == "Adhésion Florain Annuelle") or ($sub['description'] == "Adhésion Florain Mensuelle")) {
                $booladh = True;
            }
        }
        if ((!$boolchange) or (!$booladh)){
            return False;
        } else {
            return True;
        }
    }

    public function sendEmailMonitor($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Florain Vérification des adhérents')
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars(array("datas" => $datas))
            ->viewBuilder()
            ->setTemplate('monitor')
            ->setLayout('default');
        $mailer->deliver();
    }
}

?>