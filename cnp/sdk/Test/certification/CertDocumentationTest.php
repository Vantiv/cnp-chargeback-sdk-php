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

namespace cnp\sdk\Test\certification;

use cnp\sdk\ChargebackDocument;
use cnp\sdk\Test\unit\ChargebackDocumentTest;
use cnp\sdk\Utils;
use cnp\sdk\XmlParser;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

define('PRELIVE_URL', 'https://services.vantivprelive.com/services/chargebacks/');

class CertDocumentationTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackDocument;
    private $merchantId;
    private $documentToUpload1;
    private $documentToUpload2;
    private $documentToUpload3;
    private $documentToUpload4;

    public function setUp()
    {
        $config = Utils::getConfig();
        $this->merchantId = $config['merchantId'];
        $this->chargebackDocument = new ChargebackDocument(array("url" => PRELIVE_URL));
        $this->documentToUpload1 = getcwd() . "/test.jpg";
        ChargebackDocumentTest::createTestFile($this->documentToUpload1);
        $this->documentToUpload2 = getcwd() . "/test.gif";
        ChargebackDocumentTest::createTestFile($this->documentToUpload2);
        $this->documentToUpload3 = getcwd() . "/test.pdf";
        ChargebackDocumentTest::createTestFile($this->documentToUpload3);
        $this->documentToUpload4 = getcwd() . "/test.tiff";
        ChargebackDocumentTest::createTestFile($this->documentToUpload4);
    }

    public function tearDown()
    {
        unlink($this->documentToUpload1);
        unlink($this->documentToUpload2);
        unlink($this->documentToUpload3);
        unlink($this->documentToUpload4);
    }

    public function test1()
    {
        $caseId = $this->merchantId . "001";

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload1);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload2);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload3);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);

        $response = $this->chargebackDocument->listDocuments($caseId);
        $documentId = XmlParser::getValueListByTagName($response, "documentId");
        $this->assertContains("test.jpg", $documentId);
        $this->assertContains("test.gif", $documentId);
        $this->assertContains("test.pdf", $documentId);


        $documentToRetrieve = getcwd() . "test1.tiff";
        $this->chargebackDocument->retrieveDocument($caseId, "test.jpg", $documentToRetrieve);
        $this->assertTrue(file_exists($documentToRetrieve));
        unlink($documentToRetrieve);

        $this->chargebackDocument->retrieveDocument($caseId, "test.jpg", $documentToRetrieve);
        $this->assertTrue(file_exists($documentToRetrieve));
        unlink($documentToRetrieve);

        $this->chargebackDocument->retrieveDocument($caseId, "test.jpg", $documentToRetrieve);
        $this->assertTrue(file_exists($documentToRetrieve));
        unlink($documentToRetrieve);

        $response = $this->chargebackDocument->replaceDocument($caseId, "test.jpg", $this->documentToUpload4);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);

        $this->chargebackDocument->retrieveDocument($caseId, "test.tiff", $documentToRetrieve);
        $this->assertTrue(file_exists($documentToRetrieve));
        unlink($documentToRetrieve);

        $response = $this->chargebackDocument->deleteDocument($caseId, "test.gif");
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);


        $response = $this->chargebackDocument->listDocuments($caseId);
        $documentId = XmlParser::getValueListByTagName($response, "documentId");
        $this->assertContains("test.pdf", $documentId);
        $this->assertContains("test.tiff", $documentId);
    }

    public function test2()
    {
        $caseId = $this->merchantId . "002";

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload1);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('010', $responseCode);
        $this->assertEquals('Case Not In Valid Cycle', $responseMessage);
    }

    public function test3()
    {
        $caseId = $this->merchantId . "003";

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload1);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('004', $responseCode);
        $this->assertEquals('Case Not In Merchant Queue', $responseMessage);

    }

    public function test4()
    {
        $caseId = $this->merchantId . "004";

        $documentMaxSize = getcwd() . "maxsize.tif";
        ChargebackDocumentTest::createTestFile($documentMaxSize, 1024);

        $response = $this->chargebackDocument->uploadDocument($caseId, $documentMaxSize);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('005', $responseCode);
        $this->assertEquals('Document already exists', $responseMessage);

        ChargebackDocumentTest::createTestFile($documentMaxSize, 2100000);
        $response = $this->chargebackDocument->uploadDocument($caseId, $documentMaxSize);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('012', $responseCode);
        $this->assertEquals('Filesize exceeds limit of 1MB', $responseMessage);

        $response = $this->chargebackDocument->uploadDocument($caseId, $this->documentToUpload1);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('008', $responseCode);
        $this->assertEquals('Max Document Limit Per Case Reached', $responseMessage);
    }
}
