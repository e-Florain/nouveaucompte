<?php
// src/Model/Table/CyclosTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\ORM\Locator;
use Cake\ORM\Locator\LocatorAwareTrait;

class CyclosTable extends Table
{
    use LocatorAwareTrait;

    private $cyclos = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->cyclos['url'] = Configure::read('Cyclos.url');
        $this->cyclos['admin'] = Configure::read('Cyclos.admin');
        $this->cyclos['password'] = Configure::read('Cyclos.password');
    }

    public function getAuth($login, $password) 
    {
        $ch = curl_init();
        try {
            $url = $this->cyclos['url']."/auth";
    
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic '. base64_encode($login.':'.$password),
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
            $res = json_decode($response, true);
            if ($res == NULL) {
                return -2;
            }
            if (isset($res["code"])) {
                if ($res["code"] == "login") {
                    return -1;
                }
            } else {
                return $res;
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function getUsers($group)
    {
        $ch = curl_init();
        try {
            $url = $this->cyclos['url']."/users?groups='.$group.'&orderBy=alphabeticallyAsc&pageSize=10000&statuses=active&statuses=blocked&statuses=pending";
    
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic '. base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            $response = curl_exec($ch);
            $res = json_decode($response, true);
            //var_dump($res);
            if ($res == NULL) {
                return -2;
            }
            if (isset($res["code"])) {
                if ($res["code"] == "login") {
                    return -1;
                }
            } else {
                return $res;
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }
}
?>