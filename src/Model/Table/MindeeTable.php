<?php
// src/Model/Table/MindeeTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\ORM\Locator;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;

class MindeeTable extends Table
{
    use LocatorAwareTrait;

    private $mindee = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->mindee['url'] = Configure::read('Mindee.url');
        $this->mindee['key'] = Configure::read('Mindee.key');
    }


    public function checkIdorPassport($file)
    {
        $results = array();

        $res = $this->checkIDCard($file);
        if ($res['api_request']['status'] == 'failure') {
            $results['result'] = False;
            $results['error'] = 'Document invalide';
            return $results;
        }
        if (isset($res['document']['inference']['prediction']['surname']['value'])) {
            return $res;
        }
        $res = $this->checkPassport($file);
        
        if ($res['api_request']['status'] == 'failure') {
            $results['result'] = False;
            $results['error'] = 'Document invalide';
            return $results;
        }
        if (isset($res['document']['inference']['prediction']['surname']['value'])) {
            return $res;
        }
        $results['result'] = False;
        $results['error'] = 'Document non reconnu';
        return $results;
    }
    

    public function checkIdentity($file, $lastname, $firstname)
    {
        $results = array();
        
        /*$res = $this->checkPassport($file);
        if ($res['api_request']['status'] == 'failure') {
            $results['result'] = False;
            $results['error'] = 'mindee';
            return $results;
        }*/
        $res = $this->checkIdorPassport($file);
        
        if (isset($res['result'])) {
            if ($res['result'] == False) {
                return $res;
            }
        }
        //var_dump($res['document']['inference']['pages'][0]['prediction']['alternate_name']['value']);
        $regex = "/(";
        if (isset($res['document']['inference']['prediction']['alternate_name']['value'])) {
            $epouseName = $res['document']['inference']['prediction']['alternate_name']['value'];
            if (preg_match('/Epouse\s+(.*)/', $epouseName, $matches)) {
                $usageName = $matches[1];
                $regex .= $matches[1];
            }
        }
        if ($res['document']['inference']['prediction']['surname']['value'] != NULL) {
            $regex .= "|" . $res['document']['inference']['prediction']['surname']['value'] . ")";
        } else {
            $regex .= ")";
            $results['result'] = False;
            $results['error'] = 'Non reconnu comme une pièce d\'identité';
            return $results;
        }
        if (isset($res['document']['inference']['prediction']['given_names'][0])) {
            if ($res['document']['inference']['prediction']['given_names'][0]['value'] != NULL) {
                $regex .= "\s+" . $res['document']['inference']['prediction']['given_names'][0]['value'];
            }
        }
        $regex .= "/i";
        if ($res['document']['inference']['prediction']['birth_date']['value'] != NULL) {
            $results['birth_date'] = $res['document']['inference']['prediction']['birth_date']['value'];
        }
        if (preg_match($regex, $lastname . " " . $firstname)) {
            $results['result'] = True;
        } else {
            $results['result'] = False;
            $results['error'] = "La pièce d'identité ne correspond pas.";
            $results['name'] = $regex;
        }
        return $results;
    }

    public function checkIDCard($file)
    {
        $ch = curl_init();
        try {
            $ACCOUNT = 'mindee';
            $VERSION = '2';
            $ENDPOINT = 'idcard_fr';
            $newfile = curl_file_create($file, mime_content_type($file), substr($file, strrpos($file, "/") + 1));
            $datas = array("document" => $newfile);
            $json = json_encode($datas);
            $url = $this->mindee['url'] . "/$ACCOUNT/$ENDPOINT/v$VERSION/predict";
            $headers = array(
                "Authorization: Token ".$this->mindee['key']
            );
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $datas,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array(
                $ch,
                $options
            );
            $json = curl_exec($ch);
            curl_close($ch);
            $results = json_decode($json, true);
            return $results;
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function checkPassport($file)
    {
        $ch = curl_init();
        try {
            $ACCOUNT = 'mindee';
            $VERSION = '1';
            $ENDPOINT = 'passport';
            $newfile = curl_file_create($file, mime_content_type($file), substr($file, strrpos($file, "/") + 1));
            $datas = array("document" => $newfile);
            $json = json_encode($datas);
            $url = $this->mindee['url'] . "/$ACCOUNT/$ENDPOINT/v$VERSION/predict";
            $headers = array(
                "Authorization: Token ".$this->mindee['key']
            );
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $datas,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array(
                $ch,
                $options
            );
            $json = curl_exec($ch);
            curl_close($ch);
            $results = json_decode($json, true);
            return $results;
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function isMajor($datestr)
    {
        $d = FrozenTime::createFromFormat('Y-m-d', $datestr, 'Europe/Paris');
        $now = new FrozenTime('NOW');
        $newDate = $d->modify('+18 years');
        //$interval = FrozenTime::createfromdatestring('+18 years');
        //$d->add($interval);
        return ($now > $newDate);
    }
}