<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 02-Nov-17
 * Time: 19:45
 */

namespace App\Http\Controllers;


class PaymentController
{
    private $urlGetPaymentKey    = "https://rest.goldenpay.az/web/service/merchant/getPaymentKey";
    private $urlGetPaymentResult = "https://rest.goldenpay.az/web/service/merchant/getPaymentResult";
    private $urlRedirect         = "https://rest.goldenpay.az/web/paypage?payment_key=";


    private $merchantName = "dreamcard";
    private $authKey      = "aa79a95f0cf246a056b43b15918dab7a";

    public function getPaymentKeyJSONRequest($amount, $lang, $cardType, $description) {
        $params = array(
            'merchantName' => $this->merchantName,
            'cardType' => $cardType,
            'amount' => $amount,
            'description' => $description
        );

        $params['hashCode'] = $this->getHashcCode($params);
        $params['lang'] = $lang;

        $request = json_encode($params);

        $json = json_decode($this->getJsonContent($this->urlGetPaymentKey, $request));

        $json->urlRedirect =($this->urlRedirect).($json->paymentKey);

        return $json;
    }


    public function getPaymentResult($payment_key) {
        $params = array(
            'payment_key' => $payment_key
        );
        $params['hash_code'] = $this->getHashcCode($params);

        $options = array(
            'http' => array(
                'header'  => "Accept: application/json\r\n",
                'method'  => 'GET'
            )
        );

        $context = stream_context_create($options);
        $json = file_get_contents($this->urlGetPaymentResult."?".
            http_build_query($params), false, $context);

        return json_decode($json);
    }


    private function getHashcCode($params) {
        return md5($this->authKey.implode($params));
    }

    private function getParamsClone() {
        return array_merge(array(), $params);
    }


    private function getJsonContent($url, $content) {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
                'method'  => 'POST',
                'content' => $content
            ),
        );
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}