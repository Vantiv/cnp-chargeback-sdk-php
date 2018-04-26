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

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackDocumentTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackDocument;
    private $documentToUpload;

    public function setUp()
    {
        $this->chargebackDocument = new ChargebackDocument();
        $this->documentToUpload = getcwd() . "/test.jpg";
        self::createTestFile($this->documentToUpload, 1024);
    }

    public function tearDown()
    {
        unlink($this->documentToUpload);
    }

    public function testChargebackUploadDocument()
    {
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

    public function testChargebackRetrieveDocumentAsBytes()
    {
        $response = $this->chargebackDocument->retrieveDocumentAsString(123000, "logo.tiff");
        $this->assertTrue(strlen($response) != NULL);
        $this->assertTrue(strlen($response) == 30128);
    }

    public function testChargebackRetrieveDocument()
    {
        $filename = "logo.tiff";
        $this->chargebackDocument->retrieveDocumentToPath(123000, $filename, getcwd());
        $this->assertTrue(file_exists(getcwd() . "/" . $filename));
        $this->assertTrue(filesize(getcwd() . "/" . $filename) != 0);
        unlink(getcwd() . "/" . $filename);
    }

    public function testChargebackReplaceDocument()
    {
        $response = $this->chargebackDocument->replaceDocument(123000, "doc.pdf", $this->documentToUpload);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $documentId = XmlParser::getValueByTagName($response, "documentId");
        $caseId = XmlParser::getValueByTagName($response, "caseId");
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('doc.pdf', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackDeleteDocument()
    {
        $response = $this->chargebackDocument->removeDocument(123000, "logo.tiff");
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

    public function testErrorResponse001()
    {
        $response = $this->chargebackDocument->uploadDocument(123001, $this->documentToUpload);
        $responseCode = XmlParser::getValueByTagName($response, "responseCode");
        $responseMessage = XmlParser::getValueByTagName($response, "responseMessage");
        $this->assertEquals('001', $responseCode);
        $this->assertEquals('Invalid Merchant', $responseMessage);
    }

    public function testErrorResponse009()
    {
        try {
            $response = $this->chargebackDocument->retrieveDocumentAsString("1234009", "logo.tiff");
            echo $response;
        } catch (\cnp\sdk\ChargebackException $e) {
            $this->assertEquals($e->getMessage(), "Document Not Found");
            $this->assertEquals($e->getCode(), "009");
        }
    }

    public function testErrorResponse404()
    {
        try {
            $response = $this->chargebackDocument->retrieveDocumentAsString("1234404", "logo.tiff");
        } catch (\cnp\sdk\ChargebackException $e) {
            $this->assertEquals($e->getMessage(), "Could not find requested object.");
            $this->assertEquals($e->getCode(), 404);
        }
    }

    public static function createTestFile($filepath, $bytes)
    {
        $file = fopen($filepath, "w");
        $data = str_repeat(rand(0, 9), $bytes);
        fwrite($file, $data);
        fclose($file);
    }
}
