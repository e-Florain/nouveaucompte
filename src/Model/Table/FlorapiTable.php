<?php
// src/Model/Table/FlorapiTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\ORM\Locator;
use Cake\ORM\Locator\LocatorAwareTrait;

class FlorapiTable extends Table
{
    use LocatorAwareTrait;

    private $florapi = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->florapi['url'] = Configure::read('Florapi.url');
        $this->florapi['key'] = Configure::read('Florapi.key');
    }

    public function isAdhExists($email)
    {
        $http = new Client();
        $url = $this->florapi['url'].'/getAdhs?email='.urlencode($email);
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        if (count($infos) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllAdh()
    {
        $http = new Client();
        $url = $this->florapi['url'].'/getAdhs';
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function getAdh($email)
    {
        $http = new Client();
        $url = $this->florapi['url'].'/getAdhs?email='.urlencode($email);
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function getAdhPro($email)
    {
        $http = new Client();
        $url = $this->florapi['url'].'/getAdhpros?email='.urlencode($email);
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function getOdooAssos()
    {
        $assos = array();
        $http = new Client();
        $url = $this->florapi['url'].'/getAssos';
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        foreach ($infos as $info) {
            if (($info['membership_state'] != 'old') and ($info['membership_state'] != 'none')) {
                $assos[] = $info;
            }
        }
        return $assos;
    }

    public function getFreeRef()
    {
        $http = new Client();
        $url = $this->florapi['url'].'/getFreeRef';
        $response = $http->get($url, [], [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function updateAdh($datas)
    {
        $http = new Client();
        $json = json_encode($datas);
        $url = $this->florapi['url'].'/putAdhs';
        $response = $http->post($url, $json, [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function postAdh($datas)
    {
        $http = new Client();
        $json = json_encode($datas);
        $url = $this->florapi['url'].'/postAdhs';
        $response = $http->post($url, $json, [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function postMembership($datas)
    {
        $http = new Client();
        $json = json_encode($datas);
        $url = $this->florapi['url'].'/postMembership';
        $response = $http->post($url, $json, [
            'headers' => [
                'x-api-key' => $this->florapi['key'],
                'Content-Type' => 'application/json'
                ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }
}

?>