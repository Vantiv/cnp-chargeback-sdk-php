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
use cnp\sdk\Utils;
use cnp\sdk\XmlParser;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackUpdateUnitTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackUpdate;
    private $expectedResponse;

    public function setUp()
    {
        $this->chargebackUpdate = new ChargebackUpdate();
        $expectedResponseXml = '<chargebackUpdateResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <transactionId>28333613520922</transactionId>
                                </chargebackUpdateResponse>';

        $this->expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
    }

    public function testAssignCaseToUser()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->assignCaseToUser("1234000", "User0", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testAddNoteToCase()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->addNoteToCase("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testAssumeLiability()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->assumeLiability("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRepresentCaseFull()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->representCase("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRepresentCase()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->representCase("1234000", "Note", 1000);
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRespondToRetrievalRequest()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->respondToRetrievalRequest("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }

    public function testRequestArbitration()
    {
        $mock = $this->getMock('cnp\sdk\Communication');
        $mock->expects($this->once())
            ->method('httpPutRequest')->will($this->returnValue($this->expectedResponse));
        $this->chargebackUpdate->setCommunication($mock);
        $response = $this->chargebackUpdate->requestArbitration("1234000", "Note");
        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $this->assertRegExp('/\d+/', $transactionId);
    }
}
