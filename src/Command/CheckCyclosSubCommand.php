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
        //$cyclos = $this->fetchTable('Cyclos');
        $cyclossub = $this->fetchTable('Cyclossubscriptions');
        $subscriptions = $cyclossub->find('all')->where(['nextpaymentdate =' => new DateTime('now')]);
        foreach ($subscriptions as $sub) {
            $date = new DateTime($sub['nextpaymentdate']);
            echo $sub['account_cyclos'];
            echo $sub['amount'];
            // do cyclos payment from account_cyclos at amount to 
            //$cyclos.setPayment($sub['amount'],)
            // change nextpaymentdate
            if ($sub['sub_interval'] == 'monthly') {
                $newdate = $date->modify('+1 month');
                $newdatestr = $newdate->i18nFormat('yyyy-MM-dd');
                $sub['nextpaymentdate'] = $newdatestr;
                $cyclossub->save($sub);
            }
            if ($sub['sub_interval'] == 'annually') {
                $newdate = $date->modify('+1 year');
                $newdatestr = $newdate->i18nFormat('yyyy-MM-dd');
                $sub['nextpaymentdate'] = $newdatestr;
                $cyclossub->save($sub);
            }
        }
        return 0;
    }

}