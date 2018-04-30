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

use cnp\sdk\ChargebackRetrieval;
use cnp\sdk\Utils;
use cnp\sdk\XmlParser;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackRetrievalUnitTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackRetrieval;
    private $expectedResponse;
    private $mock;

    public function setUp()
    {
        $this->chargebackRetrieval = new ChargebackRetrieval();
        $expectedResponseXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                                <chargebackRetrievalResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <transactionId>1234567890</transactionId>
                                  <chargebackCase>
                                    <caseId>1333078000</caseId>
                                    <merchantId>1234567</merchantId>
                                    <dayIssuedByBank>2018-01-01</dayIssuedByBank>
                                    <dateReceivedByVantivCnp>2018-01-01</dateReceivedByVantivCnp>
                                    <vantivCnpTxnId>21200000820903</vantivCnpTxnId>
                                    <cycle>First Chargeback</cycle>
                                    <orderId>TEST02.2</orderId>
                                    <cardNumberLast4>0000</cardNumberLast4>
                                    <cardType>MC</cardType>
                                    <chargebackAmount>2002</chargebackAmount>
                                    <chargebackCurrencyType>USD</chargebackCurrencyType>
                                    <originalTxnDay>2018-01-01</originalTxnDay>
                                    <chargebackType>D</chargebackType>
                                    <representedAmount>2002</representedAmount>
                                    <representedCurrencyType>USD</representedCurrencyType>
                                    <reasonCode>4837</reasonCode>
                                    <reasonCodeDescription>No Cardholder Authorization</reasonCodeDescription>
                                    <currentQueue>Network Assumed</currentQueue>
                                    <fraudNotificationStatus>AFTER</fraudNotificationStatus>
                                    <acquirerReferenceNumber>1111111111</acquirerReferenceNumber>
                                    <chargebackReferenceNumber>00143789</chargebackReferenceNumber>
                                    <merchantTxnId>600001</merchantTxnId>
                                    <fraudNotificationDate>2018-01-01</fraudNotificationDate>
                                    <bin>532499</bin>
                                    <token>1000000</token>
                                    <historicalWinPercentage>80</historicalWinPercentage>
                                    <customerId>123abc</customerId>
                                    <paymentAmount>3099</paymentAmount>
                                    <replyByDay>2018-01-01</replyByDay>
                                    <activity>
                                      <activityDate>2018-01-01</activityDate>
                                      <activityType>Assign To User</activityType>
                                      <fromQueue>Vantiv</fromQueue>
                                      <toQueue>Merchant</toQueue>
                                      <settlementAmount>2002</settlementAmount>
                                      <settlementCurrencyType>USD</settlementCurrencyType>
                                      <notes>notes on activity</notes>
                                    </activity>
                                  </chargebackCase>
                                </chargebackRetrievalResponse>';

        $this->expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
        $this->mock = $this->getMock('cnp\sdk\Communication');
    }

    public function testChargebackByDate()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?date=2018-01-01"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebacksByDate("2018-01-01");

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
    }

    public function testChargebacksByFinancialImpact()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?date=2018-01-01&financialOnly=true"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebacksByFinancialImpact("2018-01-01", true);

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
    }

    public function testChargebacksActionable()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?actionable=true"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getActionableChargebacks(true);

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
    }

    public function testChargebackByCaseId()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks/1333078000"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebackByCaseId("1333078000");

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
        $this->assertEquals('1333078000', $caseId);
    }

    public function testChargebacksbyToken()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?token=100000"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebacksByToken("100000");

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $token = XmlParser::getValueByTagName($response, "token");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
        $this->assertEquals('1000000', $token);
    }

    public function testChargebacksByCardNumber()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?cardNumber=1111000011110000&expirationDate=1018"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebacksByCardNumber("1111000011110000", "1018");

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $cardNumberLast4 = XmlParser::getValueByTagName($response, "cardNumberLast4");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
        $this->assertEquals('0000', $cardNumberLast4);
    }

    public function testChargebacksByArn()
    {
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/chargebacks?arn=1000000000"))
            ->will($this->returnValue($this->expectedResponse));
        $this->chargebackRetrieval->setCommunication($this->mock);
        $response = $this->chargebackRetrieval->getChargebacksByArn("1000000000");

        $transactionId = XmlParser::getValueByTagName($response, "transactionId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $acquirerReferenceNumber = XmlParser::getValueByTagName($response, "acquirerReferenceNumber");
        $this->assertRegExp('/\d+/', $transactionId);
        $this->assertRegExp('/\d+/', $caseId);
        $this->assertEquals('1111111111', $acquirerReferenceNumber);
    }
}
