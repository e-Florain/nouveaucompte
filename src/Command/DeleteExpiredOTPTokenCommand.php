<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\DateTime;

class DeleteExpiredOTPTokenCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->deleteExpiredOTPTokenUsers();
        //$this->deleteUuidNouveaucompte();
        return 0;
    }

    /*public function deleteUuidNouveaucompte()
    {
        $nouveaucompteTable = $this->fetchTable(('Nouveaucompte'));
        $nouveaucomptes = $nouveaucompteTable->find('all');
        Debug($nouveaucomptes);
        $now = DateTime::now();
        foreach ($nouveaucomptes as $nouveaucompte) {
            $newTime = $nouveaucompte->modified->modify('+15 min');
            if ($newTime < $now) {
                $datas['uuid'] = NULL;
                $user = $nouveaucompteTable->patchEntity($nouveaucompte, $datas);
                if (!$nouveaucompteTable->save($nouveaucompte)) {
                    Debug("Error");
                }
            }
        }
    }*/

    public function deleteExpiredOTPTokenUsers()
    {
        $userTable = $this->fetchTable('Users');
        $users = $userTable->find('all');
        $now = DateTime::now();
        foreach ($users as $user) {
            $newTime = $user->modified->modify('+15 min');
            if ($newTime < $now) {
                $datas['otp'] = "";
                $datas['token'] = "";
                $user = $userTable->patchEntity($user, $datas);
                if (!$userTable->save($user)) {
                    Debug("Error");
                }
            }
        }
    }
}

?>