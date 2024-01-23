<?php
// src/Model/Table/NouveaucompteTable.php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

class NouveaucompteTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }
}