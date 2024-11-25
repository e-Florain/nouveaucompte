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

    /*public function getUsers($group)
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
    }*/

    public function getUsers($group)
    {
        $http = new Client();
        /*$headers = array(
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
        );*/
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
        );
        $url = $this->cyclos['url']."/users?groups='.$group.'&orderBy=alphabeticallyAsc&pageSize=10000&statuses=active&statuses=blocked&statuses=pending";
        $response = $http->get($url, [], [
            'headers' => $headers
        ]);
        $infos = $response->getJson();
        return $infos;
    }

   /* public function setPaymentPro1toPro2($prosrcid, $prodstid, $amount, $description)
    {
        $ch = curl_init();
        try {
            $this->log('set Payment '.$prosrcid.' to '.$prodstid, 'debug');
            $url = $this->cyclos['url']."/".$prosrcid."/payments";
    
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Basic '. base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
            );
            $datas = array(
                'amount: '.$amount,
                

            );
            //data = {'amount': amount, 'subject': 'system', 'type': 'user.toDebit', 'description': description} 
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
    }*/

    public function setPaymentPro1toPro2($prosrcid, $prodstid, $amount, $description)
    {
        $http = new Client();
        //Log::write('set Payment '.$prosrcid.' to '.$prodstid, 'debug');
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
        );
        $datas = array(
            'amount' => $amount,
            'description' => $description,
            'subject' => $prodstid,
            'type' => 'comptePro.toPro'
        );
        $json = json_encode($datas);
        $url = $this->cyclos['url']."/".$prosrcid.'/payments';
        $response = $http->post($url, $json, [
                'headers' => $headers
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function searchPayments($prosrcid, $prodstid, $description)
    {
        $http = new Client();
        //Log::write('set Payment '.$prosrcid.' to '.$prodstid, 'debug');
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.base64_encode($this->cyclos['admin'].':'.$this->cyclos['password'])
        );
        
        $url = $this->cyclos['url']."/".urlencode($prosrcid).'/accounts/comptePro/history?user='.urlencode($prodstid).'&description='.urlencode($description);
        $response = $http->get($url, [], [
                'headers' => $headers
        ]);
        $infos = $response->getJson();
        return $infos;
    }
}
?>