<?php

namespace mirocow\ofd\api;

use mirocow\ofd\models\AuthToken;
use mirocow\ofd\models\Receipt;
use mirocow\ofd\models\ReceiptItem;
use mirocow\ofd\models\ReceiptStatus;
use Yii;
use yii\base\Component;

/**
 * @see https://ofd.ru/razrabotchikam/ferma
 * Class OfdFermaApi
 * @package mirocow\ofd\api
 */
class OfdFermaApi extends Component
{
    public const REQUEST_RECEIPT = '/kkt/cloud/receipt';

    public $login;
    public $password;

    private $ofdFermaApiUri = 'https://ferma.ofd.ru/api/';
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
            'Password' => $password
        ]);

        if (!isset($result['AuthToken']) || !isset($result['ExpirationDateUtc'])) {
            $this->logMessage('Неверный формат ответа');
        }

        $this->authToken = new AuthToken($result['AuthToken'], strtotime($result['ExpirationDateUtc']));
    }

    /**
     * @see https://ofd.ru/razrabotchikam/ferma#%D1%84%D0%BE%D1%80%D0%BC%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5_%D0%BA%D0%B0%D1%81%D1%81%D0%BE%D0%B2%D0%BE%D0%B3%D0%BE_%D1%87%D0%B5%D0%BA%D0%B0
     * @param Receipt $receipt
     *
     * @return mixed
     */
    public function addReceipt(Receipt $receipt)
    {
        $items = array();

        foreach ($receipt->getItems() as $receiptItem) {
            if (!$receiptItem instanceof ReceiptItem) {
                continue;
            }

            $items[] = array(
                'Label' => $receiptItem->getLabel(),
                'Price' => $receiptItem->getPrice(),
                'Quantity' => $receiptItem->getQuantity(),
                'Amount' => $receiptItem->getAmount(),
                'Vat' => $receiptItem->getVat()
            );
        }

        if (!count($items)) {
            throw new publicException('Для заказа не передан список товаров');
        }

        $reciept = new stdClass();
        $reciept->TaxationSystem = $receipt->tax;
        if (!empty($mail)) {
            $reciept->Email = $mail;
        }
        if (!empty($phone)) {
            $reciept->Phone = _ofd_ferma_format_phone($phone);
        }
        $reciept->Items = $items;

        $request = new stdClass();
        $request->Inn = $receipt->inn;
        $request->Type = $receipt->type;
        $request->InvoiceId = $receipt->invoice . $receipt->type;
        $request->LocalDate = date('Y-m-d\TH:i:s', $receipt->create_at);
        $request->CustomerReceipt = $reciept;

        $data = new stdClass();
        $data->Request = $request;

        $result = $this->request( self::REQUEST_RECEIPT . '?' . http_build_query(['AuthToken' => $this->authToken]), $data);

        if (!isset($result['ReceiptId'])) {
            $this->logMessage('Неверный формат ответа');
        }

        return $result['ReceiptId'];
    }

    /**
     * @see https://ofd.ru/razrabotchikam/ferma#%D0%BF%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%BA%D0%B0_%D1%81%D1%82%D0%B0%D1%82%D1%83%D1%81%D0%B0_%D0%BA%D0%B0%D1%81%D1%81%D0%BE%D0%B2%D0%BE%D0%B3%D0%BE_%D1%87%D0%B5%D0%BA%D0%B0
     * @param $receiptId
     *
     * @return OfdFermaApiReceiptStatusModel
     */
    public function getReceiptStatus($receiptId)
    {
        $result = $this->request('/kkt/cloud/status?' . http_build_query(['AuthToken' => $this->authToken]), [
            'Request' => [
                'ReceiptId' => $receiptId
            ]
        ]);

        if(!isset($result['StatusCode'])) {
            $this->logMessage('Неверный формат ответа');
        }

        $status = new ReceiptStatus();
        $status->setCode($result['StatusCode']);
        $status->setName($result['StatusName']);
        $status->setMessage($result['StatusMessage']);
        $status->setModifiedDate(strtotime($result['ModifiedDateUtc']));
        $status->setReceiptDate(strtotime($result['ReceiptDateUtc']));
        $status->setDeviceId($result['Device']['DeviceId']);
        $status->setRnm($result['Device']['RNM']);
        $status->setZn($result['Device']['ZN']);
        $status->setFn($result['Device']['FN']);
        $status->setFdn($result['Device']['FDN']);
        $status->setFpd($result['Device']['FPD']);

        return $status;
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
        $url = $this->ofdFermaApiUri . ltrim($url, '/');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100000);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($data && $method != 'GET') {
            $encoded = json_encode($data);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($encoded)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
        }

        $result = curl_exec($ch);
        if (!$result) {
            $this->logMessage(curl_error($ch));
        }

        $return = json_decode($result, true);
        if (!is_array($return) || !isset($return['Status'])) {
            $this->logMessage('Неверный формат ответа');
        }

        if ($return['Status'] == 'Failed') {
            $message = $return['Error']['Message'];
            if ($return['Error']['Code'] && $val = $this->getErrorByCode($return['Error']['Code'], $data)) {
                $message = $val;
            }

            $this->logMessage($message);
        }

        return $return['Data'];
    }

    private function logMessage($message)
    {
        Yii::debug($message, "ofd");
    }

    /**
     * @see https://ofd.ru/razrabotchikam/ferma#%D0%BA%D0%BE%D0%B4%D1%8B_%D0%BE%D1%88%D0%B8%D0%B1%D0%BE%D0%BA
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
                $msg = "Идентификатор счета '{$data['Request']['InvoiceId']}' уже существует в ОФД";
            break;
        }

        return $msg;
    }

}