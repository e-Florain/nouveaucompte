<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Cake\Core\Configure;

class CheckAdhMollieCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $datas = $this->checkPaiementAdhMollie();
        $contactsadmin = Configure::read('ContactsAdmin');
        if (count($datas) > 0) {
            foreach ($contactsadmin as $contact) {
                $this->sendEmailReAdhesions($contact, $datas);
            }
        }
        return static::CODE_SUCCESS;
    }

    public function checkPaiementAdhMollie()
    {
        $results = array();
        $resultsAdhMensuelle = array();
        $resultsAdhAnnuelle = array();
        $mollie = $this->fetchTable('Mollie');
        $payments = $mollie->get_all_payments();
        foreach ($payments as $payment) {
            if ($payment["method"] == "directdebit") {
                if ($payment['status'] == 'paid') {
                    if ($payment['description'] == 'Adhésion Florain Annuelle') {
                        if (isset($payment['customerId'])) {
                            $customer = $mollie->get_customer_by_id($payment['customerId']);
                            if (!array_key_exists($customer['email'], $resultsAdhAnnuelle)) {
                                $resultsAdhAnnuelle[$customer['email']] = array(
                                    'date' => $payment['paidAt'],
                                    'amount' => floatval($payment['amount']['value']),
                                    'orderdate' => $payment['createdAt'],
                                    'paidAt' => $payment['paidAt'],
                                    'orderid' => $payment['id'],
                                    'state' => $payment['status'],
                                    'description' => $payment['description']
                                );
                            }
                        }
                    }
                    elseif ($payment['description'] == 'Adhésion Florain Mensuelle') {
                        if (isset($payment['customerId'])) {
                            $customer = $mollie->get_customer_by_id($payment['customerId']);
                            if (!array_key_exists($customer['email'], $resultsAdhMensuelle)) {
                                $resultsAdhMensuelle[$customer['email']] = array(
                                    'date' => $payment['paidAt'],
                                    'amount' => floatval($payment['amount']['value']),
                                    'orderdate' => $payment['createdAt'],
                                    'paidAt' => $payment['paidAt'],
                                    'orderid' => $payment['id'],
                                    'state' => $payment['status'],
                                    'description' => $payment['description']
                                );
                            }
                        }
                    }
                }
            }
        }
        $results['AdhMensuelle'] = $resultsAdhMensuelle;
        $results['AdhAnnuelle'] = $resultsAdhAnnuelle;
        //var_dump($results);
        $datas = $this->checkOdooAdhExpires($results);
        return $datas;
    }

    public function checkOdooAdhExpires($resultsMollie)
    {
        $results = array();
        $today = new DateTime('today');
        $florapi = $this->fetchTable('Florapi');
        $assos = $florapi->getOdooAssos();
        $adhs = $florapi->getAllAdh();
        foreach ($adhs as $adh) {
            if (isset($adh['membership_stop']) and ($adh['membership_stop'] != "none")) {
                if (preg_match('(\d{2}\s+\w+\s+\d{4})', $adh['membership_stop'], $matches)) {
                    $datemembershipstop = new Datetime($matches[0]);
                    if ($datemembershipstop<$today) {
                        if (array_key_exists($adh['email'], $resultsMollie['AdhMensuelle'])) {
                            $date_adh_dec_1_month = $datemembershipstop;
                            $date_adh_dec_1_month = $date_adh_dec_1_month->modify("-1 month");
                            $datemollie = new DateTime($resultsMollie['AdhMensuelle'][$adh['email']]['paidAt']);
                            if ($datemollie > $date_adh_dec_1_month) {
                                if (($adh['membership_state'] == 'old') or ($adh['membership_state'] == NULL)) {
                                    echo "Mensuelle : ";
                                    echo $adh['lastname']." ";
                                    echo $adh['firstname']." ";
                                    echo $datemembershipstop->format('Y-m-d')." ";
                                    echo $datemollie->format('Y-m-d')."\n";
                                    $datas = array(
                                        'email' => $adh['email'],
                                        'name' => $adh['lastname']." ".$adh['firstname'],
                                        'amount' => strval($resultsMollie['AdhMensuelle'][$adh['email']]['amount'])
                                    );
                                    $florapi->postMembership($datas);
                                    $datas['lastname'] = $adh['lastname'];
                                    $datas['firstname'] = $adh['firstname'];
                                    $datas['street'] = $adh['street'];
                                    $datas['zip'] = $adh['zip'];
                                    $datas['city'] = $adh['city'];
                                    $datas['account_cyclos'] = $adh['account_cyclos'];
                                    if ($adh['orga_choice'] != null) {
                                        foreach ($assos as $asso) {
                                            if ($asso['id'] == $adh['orga_choice']) {
                                                $assochosen = $asso['name'];
                                                continue;
                                            }
                                        }
                                    }
                                    if (isset($assochosen)) {
                                        $datas['orga_choice'] = $assochosen;
                                    } else {
                                        $datas['orga_choice'] = "Non choisie";
                                    }
                                    $results[] = $datas;
                                }
                            }
                        }
                        elseif (array_key_exists($adh['email'], $resultsMollie['AdhAnnuelle'])) {
                            $today_dec_1_year = $today;
                            $today_dec_1_year = $today_dec_1_year->modify("-1 year");
                            $datemollie = new DateTime($resultsMollie['AdhAnnuelle'][$adh['email']]['paidAt']);
                            if ($datemollie > $today_dec_1_year) {
                                if (($adh['membership_state'] == 'old') or ($adh['membership_state'] == NULL)) {
                                    /* condition à virer */
                                    if ($adh['email'] != "eva.buchi@wanadoo.fr") {
                                        echo "Annuelle : ";
                                        echo $adh['lastname']." ";
                                        echo $adh['firstname']." ";
                                        echo $datemembershipstop->format('Y-m-d')." ";
                                        echo $datemollie->format('Y-m-d')."\n";
                                        $datas = array(
                                            'email' => $adh['email'],
                                            'name' => $adh['lastname']." ".$adh['firstname'],
                                            'amount' => strval($resultsMollie['AdhAnnuelle'][$adh['email']]['amount'])
                                        );
                                        $florapi->postMembership($datas);
                                        $datas['lastname'] = $adh['lastname'];
                                        $datas['firstname'] = $adh['firstname'];
                                        $datas['street'] = $adh['street'];
                                        $datas['zip'] = $adh['zip'];
                                        $datas['city'] = $adh['city'];
                                        $datas['account_cyclos'] = $adh['account_cyclos'];
                                        if ($adh['orga_choice'] != null) {
                                            foreach ($assos as $asso) {
                                                if ($asso['id'] == $adh['orga_choice']) {
                                                    $assochosen = $asso['name'];
                                                    continue;
                                                }
                                            }
                                        }
                                        if (isset($assochosen)) {
                                            $datas['orga_choice'] = $assochosen;
                                        } else {
                                            $datas['orga_choice'] = "Non choisie";
                                        }
                                        $results[] = $datas;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $results;
    }

    public function sendEmailReAdhesions($to, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Florain - réadhésion des adhérents')
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars(array("datas" => $datas))
            ->viewBuilder()
            ->setTemplate('readhesion')
            ->setLayout('default');
        $mailer->deliver();
    }

}