<?php
/* 
Ce script vérifie que l'association soutenue par l'adhérent existe encore
et dans le cas contraire, envoie une notification à l'adhérent de choisir un nouvelle association
*/

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Mailer\Mailer;
use Cake\Core\Configure;

class CheckAdhAssoCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $datas = array();
        $data_assos = array();
        $florapi = $this->fetchTable('Florapi');
        $params=array("membership_state" => 'paid');
        $adhs = $florapi->getAdhs($params);
        $params=array("membership_state" => 'waiting');
        $adhs2 = $florapi->getAdhs($params);
        $alladhs = array_merge($adhs, $adhs2);
        $assos = $florapi->getOdooAssos();
        foreach ($assos as $asso) {
            $data_assos[$asso['id']] = $asso;
        }
        $nb = 0;
        var_dump($data_assos);
        foreach ($alladhs as $adh) {
            if (!array_key_exists($adh['orga_choice'], $data_assos)) {
                $contact = trim($adh['email']);
                echo $adh['lastname']." ".$adh['firstname']." ".$contact." ".$adh['orga_choice']."\n";
                //$this->sendEmail($contact, $datas);
                $nb++;
            }
        }
        echo $nb."\n";
        return static::CODE_SUCCESS;
    }

    public function sendEmail($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Florain : mettre à jour votre association bénéficiaire du 1%')
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars(array("datas" => $datas))
            ->viewBuilder()
            ->setTemplate('chooseasso')
            ->setLayout('default');
        $mailer->deliver();
    }
}

?>