<?php

namespace mirocow\ofd\models;

class ReceiptStatus
{
    private $code;
    private $name;
    private $message;
    private $modifiedDate;
    private $receiptDate;
    private $deviceId;
    private $rnm;
    private $zn;
    private $fn;
    private $fdn;
    private $fpd;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    /**
     * @param mixed $modifiedDate
     */
    public function setModifiedDate($modifiedDate)
    {
        $this->modifiedDate = $modifiedDate;
    }

    /**
     * @return mixed
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * @param mixed $receiptDate
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return mixed
     */
    public function getRnm()
    {
        return $this->rnm;
    }

    /**
     * @param mixed $rnm
     */
    public function setRnm($rnm)
    {
        $this->rnm = $rnm;
    }

    /**
     * @return mixed
     */
    public function getZn()
    {
        return $this->zn;
    }

    /**
     * @param mixed $zn
     */
    public function setZn($zn)
    {
        $this->zn = $zn;
    }

    /**
     * @return mixed
     */
    public function getFn()
    {
        return $this->fn;
    }

    /**
     * @param mixed $fn
     */
    public function setFn($fn)
    {
        $this->fn = $fn;
    }

    /**
     * @return mixed
     */
    public function getFdn()
    {
        return $this->fdn;
    }

    /**
     * @param mixed $fdn
     */
    public function setFdn($fdn)
    {
        $this->fdn = $fdn;
    }

    /**
     * @return mixed
     */
    public function getFpd()
    {
        return $this->fpd;
    }

    /**
     * @param mixed $fpd
     */
    public function setFpd($fpd)
    {
        $this->fpd = $fpd;
    }
}