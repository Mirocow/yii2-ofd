<?php

namespace mirocow\ofd\models;

class AuthToken
{
    private $token;
    private $date;

    function __construct($token, $date)
    {
        $this->token = $token;
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
}