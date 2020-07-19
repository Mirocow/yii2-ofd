<?php

namespace mirocow\ofd\api;

use mirocow\ofd\models\Settings;
use mirocow\ofd\exceptions\OfdException;
use mirocow\ofd\models\AuthToken;
use mirocow\ofd\models\Receipt;
use mirocow\ofd\models\ReceiptItem;
use mirocow\ofd\models\ReceiptStatus;
use mirocow\settings\helpers\SettingsHelper;
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

    // Получение денежных средств от покупателя
    const TYPE_INCOME = 'Income';

    // Возврат денежных средств, полученных от покупателя
    const TYPE_INCOME_RETURN = 'IncomeReturn';

    // Чек коррекции/приход
    const TYPE_INCOME_CORRECTION = 'IncomeCorrection';

    public $login;
    public $password;
    public $ofdFermaApiUri = 'https://ferma.ofd.ru/api/';

    private $debug = false;
    private $logPath = '';
    private $email = '';
    private $authToken;

    /** @var Settings */
    private $settings;

    public function init()
    {
        $this->getAuthToken($this->login, $this->password);

        $this->settings = new Settings();

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
     * @param string $type see: https://ofd.ru/razrabotchikam/ferma#%D1%82%D0%B8%D0%BF%D1%8B_%D1%84%D0%BE%D1%80%D0%BC%D0%B8%D1%80%D1%83%D0%B5%D0%BC%D1%8B%D1%85_%D1%87%D0%B5%D0%BA%D0%BE%D0%B2_%D0%BF%D0%BE%D0%BB%D0%B5_type
     *
     * @return mixed
     * @throws OfdException
     */
    public function addReceipt(Receipt $receipt, $type = self::TYPE_INCOME)
    {
        $invoiceId = $receipt->invoice .'-'. $type;

        $receipt->type = $type;

        if ($this->checkReceipt($receipt)){
            $this->logMessage("Чек {$type} для заказа {$invoiceId} уже существует в реестре");
        }

        $items = array();

        $vat = $this->settings->getTax();

        $paymentMethod = $this->settings->getPaymentMethod();

        /** @var ReceiptItem $receiptItem */
        foreach ($receipt->getItems() as $receiptItem) {
            $items[] = array(
                'Label' => $receiptItem->label,
                'Price' => $this->formatFloat($receiptItem->price),
                'Quantity' => $this->formatFloat($receiptItem->quantity),
                'Amount' => $this->formatFloat($receiptItem->price * $receiptItem->quantity),
                'Vat' => $receiptItem->vat ?? $vat,
                'PaymentMethod' => $receiptItem->payment_method ?? $paymentMethod,
            );
        }

        if (!count($items)) {
            $this->logMessage(Yii::t('app','Для заказа не передан список товаров'));
        }

        $customerReceipt = new \stdClass();
        $customerReceipt->TaxationSystem = $this->settings->getTaxSystem();
        if($email = $this->settings->getEmail()) {
            $customerReceipt->Email = $email;
        }
        if($phone = $this->settings->getPhone()) {
            $customerReceipt->Phone = $this->fermaFormatPhone($phone);
        }
        $customerReceipt->Items = $items;

        $created_at = $receipt->created_at ?? time();

        $request = new \stdClass();
        $request->Inn = $this->settings->getInn();
        $request->Type = $receipt->type;
        $request->InvoiceId = $invoiceId;
        $request->LocalDate = date('Y-m-d\TH:i:s', $created_at);
        $request->CustomerReceipt = $customerReceipt;

        $data = new \stdClass();
        $data->Request = $request;

        $result = $this->request( self::REQUEST_RECEIPT . '?' . http_build_query(['AuthToken' => $this->authToken->getToken()]), $data);

        if(!empty($result['ReceiptId'])){
            $this->updateReceiptStatus($result['ReceiptId'], $receipt);
        }

        return $result['ReceiptId'];
    }

    /**
     * @param $int
     * @param int $count
     *
     * @return false|float
     */
    private function formatFloat( $int, $count = 2)
    {
        return	round( (float)$int , $count);
    }

    /**
     * @param string $receiptId
     * @param Receipt $reciept
     *
     * @return bool
     * @throws OfdException
     */
    public function updateReceiptStatus(string $receiptId, Receipt $reciept)
    {
        $result = $this->getReceiptStatus($receiptId);

        $attributes = [
            'invoice' => $reciept->invoice,
            'type' => $reciept->type,
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
                'rnm' => (string) $result['Device']['RNM'],
                'zn' => (string) $result['Device']['ZN'],
                'fn' => (string) $result['Device']['FN'],
                'fdn' => (string) $result['Device']['FDN'],
                'fpd' => (string) $result['Device']['FPD'],
            ]);
        }

        $status = ReceiptStatus::findOne(['receiptId' => $receiptId]);
        if(!$status) {
            $status = new ReceiptStatus();
        }
        $status->load($attributes, '');
        return $status->save();
    }

    /**
     * @param string $receiptId
     *
     * @return mixed
     * @throws OfdException
     */
    public function getReceiptStatus(string $receiptId)
    {
        $result = $this->request(self::REQUEST_STATUS . '?' . http_build_query(['AuthToken' => $this->authToken->getToken()]), [
            'Request' => [
                'ReceiptId' => $receiptId,
            ],
        ]);

        if(!isset($result['StatusCode'])) {
            $this->logMessage('Неверный формат ответа');
        }

        return $result;
    }

    /**
     * @param Receipt $reciept
     *
     * @return bool
     */
    public function checkReceipt(Receipt $reciept)
    {
        return ReceiptStatus::find()
            ->where(['invoice' => $reciept->invoice, 'type' => $reciept->type])
            ->exists();
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