<?php
// src/Model/Table/MollieTable.php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Http\Client;
use Cake\ORM\Locator;
use Cake\ORM\Locator\LocatorAwareTrait;

class MollieTable extends Table
{
    use LocatorAwareTrait;

    private $mollie = array();

    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->mollie['url'] = Configure::read('Mollie.url');
        $this->mollie['key'] = Configure::read('Mollie.key');
        $this->mollie['callbackurl'] = Configure::read('CallbackUrl.url');
    }

    public function list_payments($from = "")
    {
        $http = new Client();
        if ($from == "") {
            $url = $this->mollie['url'] . '/payments?limit=250';
        } else {
            $url = $this->mollie['url'] . '/payments?from=' . $from . '&limit=250';
        }
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }

    public function get_all_payments()
    {
        $infos = $this->list_payments();
        $res = $infos['_embedded']['payments'];
        while ($infos['_links']['next'] != NULL) {
            if (preg_match('/from=(\w+)\&/', $infos['_links']['next']['href'], $matches)) {
                $infos = $this->list_payments($matches[1]);
                $res = array_merge($res, $infos['_embedded']['payments']);
            }
        }
        return $res;
    }

    public function list_chargebacks($from = "")
    {
        $http = new Client();
        if ($from == "") {
            $url = $this->mollie['url'] . '/chargebacks?limit=250';
        } else {
            $url = $this->mollie['url'] . '/chargebacks?from=' . $from . '&limit=250';
        }
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }

    public function get_all_chargebacks()
    {
        $infos = $this->list_chargebacks();
        $res = $infos['_embedded']['chargebacks'];
        while ($infos['_links']['next'] != NULL) {
            if (preg_match('/from=(\w+)\&/', $infos['_links']['next']['href'], $matches)) {
                $infos = $this->list_chargebacks($matches[1]);
                $res = array_merge($res, $infos['_embedded']['chargebacks']);
            }
        }
        return $res;
    }

    public function list_mandates($customer)
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/customers/'.$customer.'/mandates?limit=250';
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $results = $response->getJson();
        return $results;
    }

    public function get_all_customers()
    {
        $infos = $this->list_customers();
        $res = $infos['_embedded']['customers'];
        while ($infos['_links']['next'] != NULL) {
            if (preg_match('/from=(\w+)\&/', $infos['_links']['next']['href'], $matches)) {
                $infos = $this->list_customers($matches[1]);
                $res = array_merge($res, $infos['_embedded']['customers']);
            }
        }
        return $res;
    }

    public function list_customers($from = "")
    {
        $http = new Client();
        if ($from == "") {
            $url = $this->mollie['url'] . '/customers?limit=250';
        } else {
            $url = $this->mollie['url'] . '/customers?from=' . $from . '&limit=250';
        }
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);

        $infos = $response->getJson();
        return $infos;
    }

    public function get_customers()
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/customers?limit=250';
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        //$results = $response->getJson();
        $infos = $response->getJson();
        //var_dump($infos['_embedded']['customers']);
        return $infos['_embedded']['customers'];
    }

    public function get_customer($email)
    {
        $infos = $this->get_all_customers();
        $results = array();
        foreach ($infos as $info) {
            if ($info['email'] == $email) {
                $results[] = $info;
            }
        }
        return $results;
        //var_dump($infos['_embedded']['customers']);
        //return $infos['_embedded']['customers'];
    }

    public function get_mandates($customer)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/mandates";
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        //$results = $response->getJson();
        $infos = $response->getJson();
        return $infos['_embedded']['mandates'];
    }

    public function get_mandate($customer, $mandate)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/mandates/" . $mandate;
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        //$results = $response->getJson();
        $infos = $response->getJson();
        return $infos;
    }

    public function get_subscription_by_id($id)
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/customers/'.$id.'/subscriptions';
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function list_subscriptions($from = "")
    {
        $http = new Client();
        if ($from == "") {
            $url = $this->mollie['url'] . '/subscriptions?limit=250';
        } else {
            $url = $this->mollie['url'] . '/subscriptions?from=' . $from . '&limit=250';
        }
        //$url = $this->mollie['url'].'/subscriptions?limit=250';
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function get_all_subscriptions()
    {
        $infos = $this->list_subscriptions();
        $res = $infos['_embedded']['subscriptions'];
        while ($infos['_links']['next'] != NULL) {
            if (preg_match('/from=(\w+)\&/', $infos['_links']['next']['href'], $matches)) {
                $infos = $this->list_subscriptions($matches[1]);
                $res = array_merge($res, $infos['_embedded']['subscriptions']);
            }
        }
        return $res;
    }

    public function get_subscription($customerid, $subscriptionid)
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/customers/' . $customerid . '/subscriptions/' . $subscriptionid;
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ],
            'timeout' => 6000
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function get_subscriptions($email) {
        $customers =$this->get_customer($email);
        $results = array();
        foreach ($customers as $customer) {
            $subscriptions = $this->get_subscription_by_id($customer['id'])['_embedded']['subscriptions'];
            
            foreach ($subscriptions as $subscription) {
                $results[] = $subscription;
            }
        }
        return $results;
    }
    

    public function has_change_florain($email)
    {
        $subscriptions = $this->get_subscriptions($email);
        foreach ($subscriptions as $subscription) {
            if ($subscription['description'] == "Change Florain") {
                if (($subscription['status'] == "active") or ($subscription['status'] == "pending")) {
                    return True;
                }
            }
        }
        return False;
    }

    public function has_adh_florain($email)
    {
        $subscriptions = $this->get_subscriptions($email);
        foreach ($subscriptions as $subscription) {
            if ($subscription['description'] == "Adhésion Florain Annuelle") {
                if (($subscription['status'] == "active") or ($subscription['status'] == "pending")) {
                    return True;
                }
            }
            if ($subscription['description'] == "Adhésion Florain Mensuelle") {
                if (($subscription['status'] == "active") or ($subscription['status'] == "pending")) {
                    return True;
                }
            }
        }
        return False;
    }



    public function get_customer_by_id($id)
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/customers/' . $id;
        $found = false;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function onepercent()
    {
        $adhs = $this->getTableLocator()->get('Adhesions');
        $listpaymentsbyassos = array();
        $payments = $this->list_payments();
        //echo count($payments['_embedded']['payments']);
        foreach ($payments['_embedded']['payments'] as $payment) {
            //echo $payment['description']." ".$payment['status'];
            if (($payment['status'] == "paid") and (preg_match("/Change/", $payment['description']))) {
                $customer = $this->get_customer_by_id($payment['customerId']);
                //echo $payment['description'];
                if (isset($customer['email'])) {
                    //echo $customer['email'];
                    $params = array("email" => $customer['email']);
                    $adh = $adhs->getAdhs($params);
                    if (isset($adh[0])) {
                        $assoid = $adh[0]['orga_choice'];
                        if (isset($listpaymentsbyassos[$assoid])) {
                            $listpaymentsbyassos[$assoid] += floatval($payment['amount']['value']);
                        } else {
                            $listpaymentsbyassos[$assoid] = floatval($payment['amount']['value']);
                        }
                    } else {
                        $listpaymentsbyassos[1] += floatval($payment['amount']['value']);
                    }
                } else {
                    if (isset($listpaymentsbyassos[1])) {
                        $listpaymentsbyassos[1] += floatval($payment['amount']['value']);
                    } else {
                        $listpaymentsbyassos[1] = floatval($payment['amount']['value']);
                    }
                }
                //var_dump($customer['email']);
            }
        }
        return $listpaymentsbyassos;
    }

    public function create_customer($email, $name)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers";
        $datas = array(
            "email" => $email,
            "name" => $name
        );
        $json = json_encode($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function delete_customer($id)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/".$id;
        $response = $http->delete($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function create_mandate($customer, $iban, $consumerName, $email)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/mandates";
        $datas = array(
            "method" => "directdebit",
            "consumerName" => $consumerName,
            "consumerAccount" => $iban,
            "consumerEmail" => $email

        );
        $json = json_encode($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function create_payment($amountvalue, $description, $order_id, $customer)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/payments";
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );
        $method = array(
            "creditcard"
        );
        $datas = array(
            "amount" => $amount,
            "redirectUrl" => "https://moncompte.florain.fr/users/validpayment/".$order_id,
            "method" => $method,
            "metadata" => array(
                "order_id" => $order_id
            ),
            "customerId" => $customer,
            "description" => $description
        );
        $json = json_encode($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function get_payment($order_id) {
        $payments = $this->list_payments();
        foreach ($payments["_embedded"]["payments"] as $payment) {
            if (isset($payment["metadata"])) {
                if (isset($payment["metadata"]["order_id"])) {
                    if ($payment["metadata"]["order_id"] == $order_id) {
                        return $payment;
                    }
                }
            }
        }
        return false;
    }

    public function get_payment_by_id($idPayment)
    {
        $http = new Client();
        $url = $this->mollie['url'] . '/payments/'.$idPayment;
        $response = $http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $payment = $response->getJson();
        return $payment;
    }

    public function get_status_payment($order_id)
    {
        $payment = $this->get_payment($order_id);
        return $payment["status"];
    }

    public function create_subscription_monthly($amountvalue, $customer, $mandate, $description, $startdate, $times)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions";
        $found = false;
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );
        $datas = array(
            "amount" => $amount,
            "interval" => "1 month",
            "startDate" => $startdate,
            "mandateId" => $mandate,
            "webhookUrl" => "https://helloasso.florain.fr",
            "description" => $description

        );
        if (($times != "0") and (intval($times) != 0)) {
            $datas["times"] = intval($times);
        }
        $json = json_encode($datas);

        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function create_subscription_annually($amountvalue, $customer, $mandate, $description, $startdate)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions";
        $found = false;
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );
        $datas = array(
            "amount" => $amount,
            "interval" => "365 days",
            "mandateId" => $mandate,
            "startDate" => $startdate,
            "webhookUrl" => "https://helloasso.florain.fr",
            "description" => $description

        );
        $json = json_encode($datas);

        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        //var_dump($infos);
        return $infos;
    }

    public function create_subscription($amountvalue, $customer, $mandate, $description, $interval, $startdate)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions";
        $found = false;
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );
        $datas = array(
            "amount" => $amount,
            "interval" => $interval,
            "mandateId" => $mandate,
            "startDate" => $startdate,
            "webhookUrl" => "https://helloasso.florain.fr",
            "description" => $description

        );
        $json = json_encode($datas);

        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        //var_dump($infos);
        return $infos;
    }

    /*public function update_subscription($subscription, $customer, $amountvalue, $times)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions/" . $subscription;
        $found = false;
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );

        $datas = array(
            "amount" => $amount

        );
        if (($times != "0") and (intval($times) != 0)) {
            $datas["times"] = intval($times);
        }
        $json = json_encode($datas);

        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }*/

    public function update_subscription($subscription, $customer, $amountvalue, $times, $interval=0)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions/" . $subscription;
        $found = false;
        $amount = array(
            "currency" => "EUR",
            "value" => $amountvalue
        );

        $datas = array(
            "amount" => $amount
        );
        if ($interval != 0) {
            $datas["interval"] = $interval;
        }
        if (($times != "0") and (intval($times) != 0)) {
            $datas["times"] = intval($times);
        }
        $json = json_encode($datas);
        var_dump($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    /*public function update_subscription_date($subscription, $customer, $date)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions/" . $subscription;
        $found = false;
        $datas = array(
            "interval" => '1 month'
        );
        $json = json_encode($datas);
        var_dump($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }*/

    public function cancel_subscription($customer, $subscription)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/subscriptions/" . $subscription;
        $response = $http->delete($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key']
                //'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function revoke_mandate($customer, $mandate)
    {
        $http = new Client();
        $url = $this->mollie['url'] . "/customers/" . $customer . "/mandates/" . $mandate;
        $response = $http->delete($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->mollie['key'],
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }

    public function calculAmountChanges()
    {
        $total = 0;
        $listsubscriptions = $this->list_subscriptions("");
        $subscriptions = $listsubscriptions['_embedded']['subscriptions'];
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                if (preg_match('/Change/', $subscription['description'])) {
                    $total = $total + floatval($subscription['amount']['value']);
                }
            }
        }
        return $total;
    }

    public function calculAdhAnnuelle()
    {
        $total = 0;
        $listsubscriptions = $this->list_subscriptions("");
        $subscriptions = $listsubscriptions['_embedded']['subscriptions'];
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                if (preg_match('/Adhésion Florain Annuelle/', $subscription['description'])) {
                    $total = $total + floatval($subscription['amount']['value']);
                }
            }
        }
        return $total;
    }

    public function calculAdhMensuelle()
    {
        $total = 0;
        $listsubscriptions = $this->list_subscriptions("");
        $subscriptions = $listsubscriptions['_embedded']['subscriptions'];
        foreach ($subscriptions as $subscription) {
            if ($subscription['status'] == 'active') {
                if (preg_match('/Adhésion Florain Mensuelle/', $subscription['description'])) {
                    $total = $total + floatval($subscription['amount']['value']);
                }
            }
        }
        return $total;
    }

    public function postCallbackUrl() {
        $http = new Client();
        $url = $this->mollie['callbackurl'];
        $datas = array();
        $json = json_encode($datas);
        $response = $http->post($url, $json, [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $infos = $response->getJson();
        return $infos;
    }
}