<?php
// src/Model/Table/HelloassoTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\ORM\Locator;
use Cake\ORM\Locator\LocatorAwareTrait;

class HelloassoTable extends Table
{
    use LocatorAwareTrait;

    private $ha = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->ha['url'] = Configure::read('Helloasso.url');
        $this->ha['urltoken'] = Configure::read('Helloasso.urltoken');
        $this->ha['user'] = Configure::read('Helloasso.user');
        $this->ha['password'] = Configure::read('Helloasso.password');
        $this->get_token();
        //Debug($this->ha['token']);
    }

    public function get_token()
    {
        $http = new Client();
        $url = $this->ha['urltoken'];
        $datas = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->ha['user'],
            'client_secret' => $this->ha['password']
        );
        $json = json_encode($datas);
        
        $response = $http->post($url, $datas, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        $infos = $response->getJson();
        if (isset($infos['error'])) {
            return -1;
        } else {
            $this->ha['token'] = $infos['access_token'];
            return $this->ha['token'];
        }
    }

    public function get_formulaires()
    {
        $http = new Client();
        $url = $this->ha['url'] . '/forms?pageSize=100';
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->ha['token'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }

    public function get_payments()
    {
        $http = new Client();
        //url = cfg.ha['url']+'/payments'+'?pageSize=100&from='+last_hour_date_time.strftime("%Y-%m-%dT%H:%M:%S")
        $url = $this->ha['url'] . '/payments?pageSize=100';
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->ha['token'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }

    public function get_dons()
    {
        $this->get_token();
        $http = new Client();
        $url = $this->ha['url'] . '/forms/Donation/1/payments?pageSize=100';
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->ha['token'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }
}

