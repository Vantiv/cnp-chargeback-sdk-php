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

class ChargebackUpdate
{
    private $config;
    private $communication;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->config = Utils::getConfig($overrides);
        $this->communication = new Communication($treeResponse, $overrides);
    }

    public function assignCaseToUser($caseId, $userId, $note)
    {
        $hash = array('activityType' => 'ASSIGN_TO_USER',
            'assignedTo' => $userId,
            'note' => $note);

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    public function addNoteToCase($caseId, $note)
    {
        $hash = array('activityType' => 'ADD_NOTE',
            'note' => $note);

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    public function assumeLiability($caseId, $note)
    {
        $hash = array('activityType' => 'MERCHANT_ACCEPTS_LIABILITY',
            'note' => $note);

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    public function representCase($caseId, $note, $representmentAmount = NULL)
    {
        $hash = array('activityType' => 'MERCHANT_REPRESENT',
            'note' => $note);

        if ($representmentAmount != NULL) {
            $hash['representedAmount'] = $representmentAmount;
        }

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    public function respondToRetrievalRequest($caseId, $note)
    {
        $hash = array('activityType' => 'MERCHANT_RESPOND',
            'note' => $note);

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    public function requestArbitration($caseId, $note)
    {
        $hash = array('activityType' => 'MERCHANT_REQUESTS_ARBITRATION',
            'note' => $note);

        $requestBody = Utils::generateChargebackUpdateRequest($hash);
        return $this->getUpdateResponse($caseId, $requestBody);
    }

    private function getUpdateResponse($caseId, $requestBody)
    {
        $requestUrl = $this->config['url'] . "/" . $caseId;
        return $this->communication->httpPutRequest($requestUrl, $requestBody);
    }
}
