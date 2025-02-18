<?php
// src/Model/Table/NextcloudTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\Http\Client\Request as ClientRequest;
use Cake\ORM\Locator;
use Cake\ORM\Locator\LocatorAwareTrait;

class NextcloudTable extends Table
{
    use LocatorAwareTrait;

    private $nextcloud = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->nextcloud['url'] = Configure::read('Nextcloud.url');
        $this->nextcloud['login'] = Configure::read('Nextcloud.login');
        $this->nextcloud['password'] = Configure::read('Nextcloud.password');
        $this->nextcloud['path'] = Configure::read('Nextcloud.path');
    }

    public function createFolder($name)
    {
        $ch = curl_init();
        try {
            $url = $this->nextcloud['url'].$this->nextcloud['path'].'/'.$name;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'MKCOL');
            curl_setopt($ch, CURLOPT_USERPWD, $this->nextcloud['login'] . ":" . $this->nextcloud['password']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                echo curl_error($ch);
                die();
            }
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200) or $http_code == intval(201) or $http_code == intval(405)){
                return 0;
            } else {
                return -1;
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function uploadFile($dirname, $file, $name)
    {
        $ch = curl_init();
        try {
            $fp = fopen($file, "rb");           
            $filecontent = fread($fp, filesize($file));
            fclose($fp);
            $name=str_replace(' ', '', $name);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $url = $this->nextcloud['url'].$this->nextcloud['path'].'/'.$dirname.'/'.$name;
            $options = array(
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_HEADER => $headers,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_URL => $url,
                CURLOPT_POSTFIELDS => $filecontent,
                CURLOPT_SSL_VERIFYPEER=> false,
                CURLOPT_RETURNTRANSFER=> 1,
                CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
                CURLOPT_USERPWD=> $this->nextcloud['login'] . ":" . $this->nextcloud['password'],
                CURLOPT_HTTPHEADER=>array('OCS-APIRequest: true')
            );
            curl_setopt_array($ch, $options);
            $response = curl_exec($ch);
            //Debug($response);
            //$res = curl_getinfo($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200) or $http_code == intval(201) or $http_code == intval(204)){
                //echo "Ressource valide";
                return 0;
            }
            else{
                echo "Ressource introuvable : " . $http_code;
                return -1;
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function isFolderExist($dirname)
    {
        $ch = curl_init();
        try {
            $url = $this->nextcloud['url'].$this->nextcloud['path'];
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PROPFIND');
            curl_setopt($ch, CURLOPT_USERPWD, $this->nextcloud['login'] . ":" . $this->nextcloud['password']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $regex = str_replace('/', '\/', $this->nextcloud['path']);
            if(preg_match("/$dirname/", $response)) {
                return True;
            } else {
                return False;
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            curl_close($ch);
        }
    }

    public function findgoodsize($hyp, $WaterMarkText)
    {
        $fontsize=10;
        list($lefttext,, $righttext) = imageftbbox( $fontsize, 0, WWW_ROOT.'font/Steelfish.ttf', $WaterMarkText);
        $widthtext = $righttext - $lefttext;
        $percent = $widthtext/$hyp;
        while ($percent < 0.75) {
            $fontsize+=10;
            list($lefttext,, $righttext) = imageftbbox( $fontsize, 0, WWW_ROOT.'font/Steelfish.ttf', $WaterMarkText);
            $widthtext = $righttext - $lefttext;
            $percent = $widthtext/$hyp;
        }
        return $fontsize;
    }

    /*
     * Ajouter un filigrane sur les pieces d'identité
     */
    public function addwatermark($imageName, $imageURL)
    {
        //Debug($imageURL);
        $path_parts = pathinfo($imageName);
        $targetpath = '/tmp/'.$path_parts['filename']."-masque.".$path_parts['extension'];
        $mimetype = mime_content_type($imageURL);        
        switch($mimetype){ 
            case 'image/jpeg': 
                list ($width, $height) = getimagesize($imageURL);
                $hyp = sqrt(pow($width,2) + pow($height,2));
                $imageProperties = imagecreatetruecolor($width, $height);
                $targetLayer = imagecreatefromjpeg($imageURL);
                $anglerad = atan($width/$height);
                $angle = 90-($anglerad*180/pi());
                $WaterMarkText = 'CONFIDENTIEL - UNIQUEMENT POUR LE FLORAIN';
                $fontsize = $this->findgoodsize($hyp, $WaterMarkText);
                imagecopyresampled($imageProperties, $targetLayer, 0, 0, 0, 0, $width, $height, $width, $height);
                $watermarkColor = imagecolorallocate($imageProperties, 0, 0, 0);
                imagettftext($imageProperties, $fontsize, $angle, imagesx($imageProperties)/10, imagesy($imageProperties)-imagesy($imageProperties)/10, $watermarkColor, WWW_ROOT.'font/Steelfish.ttf', $WaterMarkText);
                imagepng($imageProperties, $targetpath);
                imagedestroy($targetLayer);
                imagedestroy($imageProperties);
                break; 
            case 'image/png':
                list ($width, $height) = getimagesize($imageURL);
                $hyp = sqrt(pow($width,2) + pow($height,2));
                $imageProperties = imagecreatetruecolor($width, $height);
                $targetLayer = imagecreatefrompng($imageURL);
                $anglerad = atan($width/$height);
                $angle = 90-($anglerad*180/pi());
                $WaterMarkText = 'CONFIDENTIEL - UNIQUEMENT POUR LE FLORAIN';
                $fontsize = $this->findgoodsize($hyp, $WaterMarkText);
                imagecopyresampled($imageProperties, $targetLayer, 0, 0, 0, 0, $width, $height, $width, $height);
                $watermarkColor = imagecolorallocate($imageProperties, 0, 0, 0);
                imagettftext($imageProperties, $fontsize, $angle, imagesx($imageProperties)/10, imagesy($imageProperties)-imagesy($imageProperties)/10, $watermarkColor, WWW_ROOT.'font/Steelfish.ttf', $WaterMarkText);
                imagepng($imageProperties, $targetpath);
                imagedestroy($targetLayer);
                imagedestroy($imageProperties);
                break;
            case 'application/pdf':
                //$targetpath = $path_parts['dirname'].'/'.$path_parts['filename']."-masque.".$path_parts['extension'];
                $cmd = 'pdftk "'.$imageURL.'" stamp '.WWW_ROOT.'img/stamp.pdf output "'.$targetpath.'"';
                //Debug($cmd);
                //echo $cmd."\n";
                shell_exec($cmd);
                unlink($imageURL);
                break;            
            default:
                if (($path_parts['extension'] == "jpg") or ($path_parts['extension'] == "jpeg")) {
                    $targetLayer = imagecreatefromjpeg($imageURL);
                }
                if ($path_parts['extension'] == "png") {
                    $targetLayer = imagecreatefrompng($imageURL);
                }
        }
        //Debug($targetpath);
        return $targetpath;
    }
}