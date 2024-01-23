<?php
// src/Model/Table/UsersTable.php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        return $validator
            ->notEmptyString('email', 'An email is required')
            ->notEmptyString('password', 'A password is required')
            ->notEmptyString('role', 'A role is required')
            ->add('role', 'inList', [
                'rule' => ['inList', ['admin', 'root', 'user']],
                'message' => 'Indiquez un rôle valide'
            ]);
    }

    public function getRole($email)
    {
        $nbusers = $this->findByEmail($email)->count();
        if ($nbusers > 0) {
            $user = $this->findByEmail($email)->firstOrFail();
            return $user->role;
        } else {
            return "none";
        }
    }
}
?>