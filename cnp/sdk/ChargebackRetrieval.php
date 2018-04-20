<?php

/*
 * Copyright (c) 2011 Vantiv eCommerce Inc.
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace cnp\sdk;
require_once realpath(dirname(__FILE__)) . '/Chargeback.php';

class ChargebackRetrieval
{
    private $useSimpleXml = false;
    private $config;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->useSimpleXml = $treeResponse;
        $this->config = Utils::getConfig($overrides);
    }

    public function getChargebackByCaseId($case_id)
    {
        $request_url = $this->config['url'] . "/" . $case_id;
        return Communication::httpGetRequest($request_url, $this->config, $this->useSimpleXml);
    }

    public function getChargebacksByDate($date)
    {
        return $this->getRetrievalResponse(array('date' => $date));

    }

    public function getChargebacksByFinancialImpact($date, $impact)
    {
        return $this->getRetrievalResponse(array('date' => $date, 'financialOnly' => $impact));
    }

    public function getActionableChargebacks($actionable)
    {
        return $this->getRetrievalResponse(array('actionable' => $actionable));

    }

    public function getChargebacksbyToken($token)
    {
        return $this->getRetrievalResponse(array('token' => $token));
    }

    public function getChargebacksbyCardNumber($cardNumber, $expDate)
    {
        return $this->getRetrievalResponse(array('cardNumber' => $cardNumber, 'expirationDate' => $expDate));
    }

    public function getChargebacksbyArn($arn)
    {
        return $this->getRetrievalResponse(array('arn' => $arn));
    }

    private function getRetrievalResponse($parameters)
    {
        $request_url = $this->config['url'];
        $prefix = "?";
        foreach ($parameters as $key => $value) {
            $request_url .= $prefix . $key . "=" . $value;
            $prefix = "&";
        }
        return Communication::httpGetRequest($request_url, $this->config);
    }
}
