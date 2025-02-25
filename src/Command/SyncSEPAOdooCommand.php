<?php
/* 
Ce script synchronise la checkbox SEPA dans odoo avec les réels prélèvements dans Mollie
*/

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

class SyncSEPAOdooCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $florapi = $this->fetchTable('Florapi');
        $this->sync_adhs();
        $this->sync_adhpros();
        return static::CODE_SUCCESS;
    }

    public function sync_adhs()
    {
        $florapi = $this->fetchTable('Florapi');
        $mollie = $this->fetchTable('Mollie');
        $adhs = $florapi->getAdhs(array());
        foreach ($adhs as $adh) {
            $subs = $mollie->get_subscriptions($adh['email']);
            if (count($subs) > 0) {
                $foundsubadh = False;
                foreach ($subs as $sub) {
                    if (preg_match('/Adh/', $sub['description'])) {
                        if ($sub['status'] == 'active') {
                            $foundsubadh = True;
                            //echo $adh['email']." Prélèvement OK\n";
                            $datas = array();
                            $datas['email'] = $adh['email'];
                            $datas['infos']['prvlt_sepa'] = 't';
                            if ($adh['prvlt_sepa'] != 't') {
                                $florapi->updateAdh($datas);
                            }
                        }
                    }
                }
                if ($foundsubadh == False) {
                    if ($adh['prvlt_sepa'] == 't') {
                        $datas = array();
                        $datas['email'] = $adh['email'];
                        $datas['infos']['prvlt_sepa'] = 'f';
                        $florapi->updateAdh($datas);
                        echo "Decoche ".$adh['email'].'\n';
                    }
                }
            } else {
                if ($adh['prvlt_sepa'] == 't') {
                    $datas = array();
                    $datas['email'] = $adh['email'];
                    $datas['infos']['prvlt_sepa'] = 'f';
                    $florapi->updateAdh($datas);
                    echo "Decoche ".$adh['email'].'\n';
                }
            }
        }
    }

    public function sync_adhpros()
    {
        $florapi = $this->fetchTable('Florapi');
        $mollie = $this->fetchTable('Mollie');
        $adhpros = $florapi->getAdhpros(array());
        foreach ($adhpros as $adhpro) {
            $subs = $mollie->get_subscriptions($adhpro['email']);
            if (count($subs) > 0) {
                $foundsubadh = False;
                foreach ($subs as $sub) {
                    if (preg_match('/PRO adh/', $sub['description'])) {
                        if ($sub['status'] == 'active') {
                            $foundsubadh = True;
                            //echo $adhpro['name'].' '.$adhpro['email']." Prélèvement OK\n";
                            $datas = array();
                            $datas['email'] = $adhpro['email'];
                            $datas['infos']['prvlt_sepa'] = 't';
                            if ($adhpro['prvlt_sepa'] != 't') {
                                //echo "Update Florapi\n";
                                $florapi->updateAdh($datas);
                            }
                        }
                    }
                }
                if ($foundsubadh == False) {
                    if ($adhpro['prvlt_sepa'] == 't') {
                        $datas = array();
                        $datas['email'] = $adhpro['email'];
                        $datas['infos']['prvlt_sepa'] = 'f';
                        $florapi->updateAdh($datas);
                        echo "Decoche ".$adhpro['email'].'\n';
                    }
                }
            } else {
                if ($adhpro['prvlt_sepa'] == 't') {
                    $datas = array();
                    $datas['email'] = $adhpro['email'];
                    $datas['infos']['prvlt_sepa'] = 'f';
                    $florapi->updateAdh($datas);
                    echo "Decoche ".$adhpro['email'].'\n';
                }
            }
        }
    }

}

?>
