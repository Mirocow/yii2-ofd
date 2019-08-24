<?php

namespace mirocow\ofd\api;

use mirocow\ofd\exceptions\OfdException;
use mirocow\ofd\models\AuthToken;
use mirocow\ofd\models\Receipt;
use mirocow\ofd\models\ReceiptItem;
use mirocow\ofd\models\ReceiptStatus;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @see https://ofd.ru/razrabotchikam/ferma
 * Class OfdFermaApi
 * @package mirocow\ofd\api
 */
class OfdFermaApi extends Component
{
    const REQUEST_RECEIPT = '/kkt/cloud/receipt';

    const REQUEST_STATUS = '/kkt/cloud/status';

    public $login;
    public $password;
    public $ofdFermaApiUri = 'https://ferma.ofd.ru/api/';

    private $debug = false;
    private $logPath = '';
    private $email = '';
    private $authToken;

    public function init()
    {
        $this->getAuthToken($this->login, $this->password);

        parent::init();
    }

    /**
     * @param $login
     * @param $password
     * @return OfdFermaApiAuthTokenModel
     * @throws publicException
     */
    private function getAuthToken($login, $password)
    {
        $result = $this->request('/Authorization/CreateAuthToken', [
            'Login' => $login,
            'Password' => $password,
        ]);

        if(!$result){
            throw new Exception();
        }

        if (!isset($result['AuthToken']) || !isset($result['ExpirationDateUtc'])) {
            $this->logMessage('Неверный формат ответа');
        }

        $this->authToken = new AuthToken($result['AuthToken'], strtotime($result['ExpirationDateUtc']));
    }

    /**
     * @param Receipt $receipt
     *
     * @return mixed
     * @throws OfdException
     */
    public function addReceipt(Receipt $receipt)
    {
        $items = array();

        /** @var ReceiptItem $receiptItem */
        foreach ($receipt->getItems()->all() as $receiptItem) {
            $items[] = array(
                'Label' => $receiptItem->label,
                'Price' => $receiptItem->price,
                'Quantity' => $receiptItem->quantity,
                'Amount' => $receiptItem->amount,
                'Vat' => $receiptItem->vat,
                'PaymentMethod' => $receiptItem->payment_method,
            );
        }

        if (!count($items)) {
            throw new OfdException(Yii::t('app','Для заказа не передан список товаров'));
        }

        $customerReceipt = new \stdClass();
        $customerReceipt->TaxationSystem = $receipt->taxation_system;
        if (!empty($mail)) {
            $customerReceipt->Email = $mail;
        }
        if (!empty($phone)) {
            $customerReceipt->Phone = $this->fermaFormatPhone($phone);
        }
        $customerReceipt->Items = $items;
        /*$receipt->PaymentItems = [
            'PaymentType' => 0,
            'Sum' => '0.0',
        ];*/

        $request = new \stdClass();
        $request->Inn = $receipt->inn;
        $request->Type = $receipt->type;
        $request->InvoiceId = $receipt->invoice . $receipt->type;
        $request->LocalDate = date('Y-m-d\TH:i:s', $receipt->create_at);
        $request->CustomerReceipt = $customerReceipt;

        $data = new \stdClass();
        $data->Request = $request;

        $result = $this->request( self::REQUEST_RECEIPT . '?' . http_build_query(['AuthToken' => $this->authToken->getToken()]), $data);

        if(!empty($result['ReceiptId'])){
            $this->getReceiptStatus($result['ReceiptId'], $receipt);
        }

        return $result['ReceiptId'];
    }

    /**
     * @param string $receiptId
     * @param Receipt $reciept
     *
     * @return bool
     * @throws OfdException
     */
    public function getReceiptStatus(string $receiptId, Receipt $reciept)
    {
        $result = $this->request(self::REQUEST_STATUS . '?' . http_build_query(['AuthToken' => $this->authToken->getToken()]), [
            'Request' => [
                'ReceiptId' => $receiptId,
            ],
        ]);

        if(!isset($result['StatusCode'])) {
            $this->logMessage('Неверный формат ответа');
        }

        $attributes = [
            'receipt_id' => $reciept->id,
            'receiptId' => $receiptId,
            'status_code' => (string) $result['StatusCode'],
            'status_name' => (string) $result['StatusName'],
            'status_message' => (string) $result['StatusMessage'],
            'modified_date_utc' => (string) $result['ModifiedDateUtc'],
            'receipt_date_utc' => (string) $result['ReceiptDateUtc'],
        ];

        if(!empty($result['Device'])){
            $attributes = ArrayHelper::merge($attributes, [
                'device_id' => (string) $result['Device']['DeviceId'],
                'rnv' => (string) $result['Device']['RNM'],
                'zn' => (string) $result['Device']['ZN'],
                'fn' => (string) $result['Device']['FN'],
                'fdn' => (string) $result['Device']['FDN'],
                'fpd' => (string) $result['Device']['FPD'],
            ]);
        }

        $status = new ReceiptStatus($attributes);
        return $status->save();
    }

    /**
     * @param $url
     * @param $data
     * @param string $method
     *
     * @return mixed
     */
    private function request($url, $data, $method = 'POST')
    {
        $url = rtrim($this->ofdFermaApiUri,'/') . '/api/' . ltrim($url, '/');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100000);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($data && $method != 'GET') {
            $encoded = json_encode($data);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($encoded),
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        }

        $result = curl_exec($ch);
        if (!$result) {
            $this->logMessage(curl_error($ch));
        }

        $return = Json::decode($result, true);
        if (!is_array($return) || !isset($return['Status'])) {
            $this->logMessage('Неверный формат ответа');
        }

        if ($return['Status'] == 'Failed') {
            $message = $return['Error']['Message'];
            if ($return['Error']['Code'] && $val = $this->getErrorByCode($return['Error']['Code'], $data)) {
                $message = $val;
            }
            $this->logMessage($message);
        } else {
            return $return['Data'];
        }
    }

    /**
     * @param $message
     */
    private function logMessage($message)
    {
        Yii::debug($message, "ofd");
        throw new OfdException(Yii::t('app',$message));
    }

    /**
     * @param $code_id
     * @param $data
     *
     * @return string
     */
    private function getErrorByCode($code_id, $data)
    {
        $msg = '';

        SWITCH ($code_id) {
            case 1019:
                $msg = "Идентификатор счета '{$data->Request->InvoiceId}' уже существует в ОФД";
            break;
        }

        return $msg;
    }

    /**
     * @param $str
     *
     * @return mixed
     */
    private function fermaFormatPhone($str)
    {
        $phone = str_replace([' ', '-', '(', ')'], '', $str);
        return str_replace('+7', '8', $phone);
    }

}