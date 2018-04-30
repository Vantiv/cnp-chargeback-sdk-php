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

namespace cnp\sdk\Test\functional;

use cnp\sdk\ChargebackUpdate;
use cnp\sdk\ChargebackWebException;
use cnp\sdk\XmlParser;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackUpdateTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackUpdate;

    public function setUp()
    {
        $this->chargebackUpdate = new ChargebackUpdate();
    }

    public function testAssignCaseToUser()
    {
        $response = $this->chargebackUpdate->assignCaseToUser("1234000", "User0", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testAddNoteToCase()
    {
        $response = $this->chargebackUpdate->addNoteToCase("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testAssumeLiability()
    {
        $response = $this->chargebackUpdate->assumeLiability("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRepresentCaseFull()
    {
        $response = $this->chargebackUpdate->representCase("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRepresentCase()
    {
        $response = $this->chargebackUpdate->representCase("1234000", "Note", 1000);
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRespondToRetrievalRequest()
    {
        $response = $this->chargebackUpdate->respondToRetrievalRequest("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRequestArbitration()
    {
        $response = $this->chargebackUpdate->requestArbitration("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testErrorResponse()
    {
        try {
            $this->chargebackUpdate->addNoteToCase("1234404", "Note");
            $this->fail("Expected Exception");
        } catch (ChargebackWebException $e) {
            $this->assertEquals($e->getMessage(), "Could not find requested object.");
            $this->assertEquals($e->getCode(), 404);
        }
    }
}