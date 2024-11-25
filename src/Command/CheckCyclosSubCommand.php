<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;
use Cake\Mailer\Mailer;
use Cake\Core\Configure;

class CheckCyclosSubCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $cyclos = $this->fetchTable('Cyclos');
        $cyclossub = $this->fetchTable('Cyclossubscriptions');
        $subscriptions = $cyclossub->find('all')->where(['nextpaymentdate =' => new DateTime('now')]);
        foreach ($subscriptions as $sub) {
            $date = new DateTime($sub['nextpaymentdate']);
            echo $sub['account_cyclos_src'];
            echo $sub['account_cyclos_dst'];
            echo $sub['amount'];
            // do cyclos payment from account_cyclos at amount to 
            //$cyclos.setPayment($sub['amount'],)
            $cyclos->setPaymentPro1toPro2($sub['account_cyclos_src'], $sub['account_cyclos_dst'], $sub['amount'], $sub['description']);
            // change nextpaymentdate
            if ($sub['sub_interval'] == 'monthly') {
                $newdate = $date->modify('+1 month');
                $newdatestr = $newdate->i18nFormat('yyyy-MM-dd');
                $sub['nextpaymentdate'] = $newdatestr;
                //$cyclossub->save($sub);
            }
            if ($sub['sub_interval'] == 'annually') {
                $newdate = $date->modify('+1 year');
                $newdatestr = $newdate->i18nFormat('yyyy-MM-dd');
                $sub['nextpaymentdate'] = $newdatestr;
                //$cyclossub->save($sub);
            }
        }
        return 0;
    }

}