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

namespace cnp\sdk\Test\unit;

use cnp\sdk\ChargebackDocument;
use cnp\sdk\XmlParser;
use cnp\sdk\Utils;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackDocumentUnitTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackDocument;
    private $documentToUpload;
    private $mock;

    public function setUp()
    {
        $this->chargebackDocument = new ChargebackDocument();
        $this->documentToUpload = getcwd() . "/test.jpg";
        self::createTestFile($this->documentToUpload);
        $this->mock = $this->getMock('cnp\sdk\Communication');
    }

    public function tearDown()
    {
        unlink($this->documentToUpload);
    }

    public function testChargebackUploadDocument()
    {
        $expectedResponseXml = '<chargebackDocumentUploadResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <merchantId>999</merchantId>
                                  <caseId>123000</caseId>
                                  <documentId>test.jpg</documentId>
                                  <responseCode>000</responseCode>
                                  <responseMessage>Success</responseMessage>
                                </chargebackDocumentUploadResponse>';
        $expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
        $this->mock->expects($this->once())
            ->method('httpPostDocumentRequest')
            ->with($this->stringEndsWith("/services/chargebacks/upload/123000/test.jpg"), $this->stringEndsWith("/test.jpg"))
            ->will($this->returnValue($expectedResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $response = $this->chargebackDocument->uploadDocument(123000, $this->documentToUpload);

        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $documentId = XmlParser::getValueByTagName($response, "documentId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('test.jpg', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackRetrieveDocumentAsString()
    {
        $mockResponse = str_repeat(rand(0, 9), 1024);
        $this->mock->expects($this->once())
            ->method('httpGetDocumentRequest')
            ->with($this->stringEndsWith("/services/chargebacks/retrieve/123000/logo.tiff"))
            ->will($this->returnValue($mockResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $response = $this->chargebackDocument->retrieveDocumentAsString(123000, "logo.tiff");

        $this->assertTrue($response != NULL);
        $this->assertTrue(strlen($response) == 1024);
    }

    public function testChargebackRetrieveDocumentToPath()
    {
        $testFile = getcwd() . "/logo.tiff";
        $mockResponse = str_repeat(rand(0, 9), 1024);
        $this->mock->expects($this->once())
            ->method('httpGetDocumentRequest')
            ->with($this->stringEndsWith("/services/chargebacks/retrieve/123000/logo.tiff"))
            ->will($this->returnValue($mockResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $this->chargebackDocument->retrieveDocumentToPath(123000, "logo.tiff", getcwd());

        $this->assertTrue(file_exists($testFile));
        $this->assertTrue(filesize($testFile) == 1024);
        unlink($testFile);
    }

    public function testChargebackReplaceDocument()
    {
        $expectedResponseXml = '<chargebackDocumentUploadResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <merchantId>999</merchantId>
                                  <caseId>123000</caseId>
                                  <documentId>test.jpg</documentId>
                                  <responseCode>000</responseCode>
                                  <responseMessage>Success</responseMessage>
                                </chargebackDocumentUploadResponse>';
        $expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
        $this->mock->expects($this->once())
            ->method('httpPutDocumentRequest')
            ->with($this->stringEndsWith("/services/chargebacks/replace/123000/doc.pdf"), $this->stringEndsWith("/test.jpg"))
            ->will($this->returnValue($expectedResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $response = $this->chargebackDocument->replaceDocument(123000, "doc.pdf", $this->documentToUpload);

        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $documentId = XmlParser::getValueByTagName($response, "documentId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('test.jpg', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackDeleteDocument()
    {
        $expectedResponseXml = '<chargebackDocumentUploadResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <merchantId>999</merchantId>
                                  <caseId>123000</caseId>
                                  <documentId>logo.tiff</documentId>
                                  <responseCode>000</responseCode>
                                  <responseMessage>Success</responseMessage>
                                </chargebackDocumentUploadResponse>';
        $expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
        $this->mock->expects($this->once())
            ->method('httpDeleteDocumentRequest')
            ->with($this->stringEndsWith("/services/chargebacks/delete/123000/logo.tiff"))
            ->will($this->returnValue($expectedResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $response = $this->chargebackDocument->deleteDocument(123000, "logo.tiff");

        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $documentId = XmlParser::getValueByTagName($response, "documentId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('logo.tiff', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackListDocuments()
    {
        $expectedResponseXml = '<chargebackDocumentUploadResponse xmlns="http://www.vantivcnp.com/chargebacks">
                                  <merchantId>999</merchantId>
                                  <caseId>123000</caseId>
                                  <documentId>logo.tiff</documentId>
                                  <documentId>doc.tiff</documentId>
                                  <responseCode>000</responseCode>
                                  <responseMessage>Success</responseMessage>
                                </chargebackDocumentUploadResponse>';
        $expectedResponse = Utils::generateResponseObject($expectedResponseXml, false);
        $this->mock->expects($this->once())
            ->method('httpGetRequest')
            ->with($this->stringEndsWith("/services/chargebacks/list/123000"))
            ->will($this->returnValue($expectedResponse));
        $this->chargebackDocument->setCommunication($this->mock);
        $response = $this->chargebackDocument->listDocuments(123000);

        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $documentId = XmlParser::getValueListByTagName($response, "documentId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('123000', $caseId);
        $this->assertContains("logo.tiff", $documentId);
        $this->assertContains("doc.tiff", $documentId);
    }

    public static function createTestFile($filepath)
    {
        $file = fopen($filepath, "w");
        fwrite($file, "test file");
        fclose($file);
    }
}
