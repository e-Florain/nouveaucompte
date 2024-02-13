<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;

class CheckPaymentCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $datas = array();
        $yesterday = new DateTime('yesterday');
        $mollie = $this->fetchTable('Mollie');
        $payments = $mollie->list_payments();
        foreach ($payments['_embedded']['payments'] as $payment) {
            if ($payment["method"] == "directdebit") {
                if ($payment['status'] == 'paid') {
                    if (isset($payment['customerId'])) {
                        $subscription = $mollie->get_subscription($payment['customerId'], $payment['subscriptionId']);
                        $datepaid = new DateTime($payment['paidAt']);
                        if ($datepaid->format('Y-m-d') == $yesterday->format('Y-m-d') ) {
                            $customer = $mollie->get_customer_by_id($payment['customerId']);
                            $RECIPIENT_EMAIL=$customer['email'];
                            $nextdate = new DateTime($subscription["nextPaymentDate"]);
                            $nextdatestr = $nextdate->i18nFormat('dd MMM YYYY', 'Europe/Paris', 'fr-FR');
                            $datas['date'] = $nextdatestr;
                            $datas['name'] = $payment["details"]["consumerName"];
                            $datas['amount'] = $payment["amount"]["value"];
                            $this->sendEmailPayment($RECIPIENT_EMAIL, $payment['description'], $datas);
                        }
                    }
                }
            }
        }
        $subs = $mollie->get_all_subscriptions();
        $datein1month = new DateTime('1 month');
        $datein1monthstr = $datein1month->i18nFormat('YYYY-MM-dd');
        $nextdatestr = $datein1month->i18nFormat('dd MMM YYYY', 'Europe/Paris', 'fr-FR');
        foreach ($subs as $sub) {
            if (($sub['status'] == 'active') and ($sub['description'] == 'Adhésion Florain Annuelle')) {
                if ($sub['nextPaymentDate'] == $datein1monthstr) {
                    $customer = $mollie->get_customer_by_id($sub['customerId']);
                    $RECIPIENT_EMAIL=$customer['email'];
                    if (preg_match('/([A-Z-\s]+)([a-z]+)/', $customer['name'], $matches)) {
                        $name = substr($matches[1],-1).$matches[2]." ".substr($matches[1],0,-1);
                    }
                    $datas['date'] = $nextdatestr;
                    $datas['name'] = $name;
                    $datas['amount'] = $sub["amount"]["value"];
                    //Debug($datas);
                    $this->sendEmailFuturPayment($RECIPIENT_EMAIL, $sub['description'], $datas);
                }
            }
        }

        foreach ($subs as $sub) {
            if (($sub['status'] == 'active') and ($sub['description'] == 'PRO adhésion annuelle')) {
                if ($sub['nextPaymentDate'] <= $datein1monthstr) {
                    $customer = $mollie->get_customer_by_id($sub['customerId']);
                    $RECIPIENT_EMAIL=$customer['email'];
                    $datas['date'] = $nextdatestr;
                    $datas['name'] = $customer['name'];
                    $datas['amount'] = $sub["amount"]["value"];
                    //Debug($datas);
                    $this->sendEmailProFuturPayment($RECIPIENT_EMAIL, $sub['description'], $datas);
                }
            }
        }

        return static::CODE_SUCCESS;
    }

    public function sendEmailPayment($to, $subject, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Paiement Florain - '.$subject)
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('paiement')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function sendEmailProFuturPayment($to, $subject, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Paiement Florain - '.$subject)
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('futurpropayment')
            ->setLayout('default');
        $mailer->deliver();
    }

    public function sendEmailFuturPayment($to, $subject, $datas)
    {
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat('both')
            ->setTo($to)
            ->setSubject('Paiement Florain - '.$subject)
            ->setFrom(['noreply@florain.fr' => 'Le Florain Numérique'])
            ->setViewVars($datas)
            ->viewBuilder()
            ->setTemplate('futurpayment')
            ->setLayout('default');
        $mailer->deliver();
    }
}

?>