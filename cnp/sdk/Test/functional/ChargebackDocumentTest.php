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
use cnp\sdk\Utils;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackDocumentTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackDocument;
    private $documentToUpload;

    public function setUp()
    {
        $this->chargebackDocument = new ChargebackDocument();
        $this->documentToUpload = getcwd() . "/test.jpg";
        self::createTestFile($this->documentToUpload);
    }

    public function tearDown()
    {
        unlink($this->documentToUpload);
    }

    public function testChargebackUploadDocument()
    {
        $response = $this->chargebackDocument->uploadDocument(123000, $this->documentToUpload);
        $responseCode = $response->getElementsByTagName("responseCode")->item(0)->nodeValue;
        $responseMessage = $response->getElementsByTagName("responseMessage")->item(0)->nodeValue;
        $documentId = $response->getElementsByTagName("documentId")->item(0)->nodeValue;
        $caseId = $response->getElementsByTagName("caseId")->item(0)->nodeValue;
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('test.jpg', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackRetrieveDocument()
    {
        $testFile = getcwd() . "/test.tiff";
        $response = $this->chargebackDocument->retrieveDocument(123000, "logo.tiff", $testFile);
        $this->assertTrue(file_exists($testFile));
        unlink($testFile);
    }

    public function testChargebackReplaceDocument()
    {
        $response = $this->chargebackDocument->replaceDocument(123000, "doc.pdf", $this->documentToUpload);
        $responseCode = $response->getElementsByTagName("responseCode")->item(0)->nodeValue;
        $responseMessage = $response->getElementsByTagName("responseMessage")->item(0)->nodeValue;
        $documentId = $response->getElementsByTagName("documentId")->item(0)->nodeValue;
        $caseId = $response->getElementsByTagName("caseId")->item(0)->nodeValue;
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('doc.pdf', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackDeleteDocument()
    {
        $response = $this->chargebackDocument->removeDocument(123000, "logo.tiff");
        $responseCode = $response->getElementsByTagName("responseCode")->item(0)->nodeValue;
        $responseMessage = $response->getElementsByTagName("responseMessage")->item(0)->nodeValue;
        $documentId = $response->getElementsByTagName("documentId")->item(0)->nodeValue;
        $caseId = $response->getElementsByTagName("caseId")->item(0)->nodeValue;
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('logo.tiff', $documentId);
        $this->assertEquals('123000', $caseId);
    }

    public function testChargebackListDocuments()
    {
        $response = $this->chargebackDocument->listDocuments(123000);
        $responseCode = $response->getElementsByTagName("responseCode")->item(0)->nodeValue;
        $responseMessage = $response->getElementsByTagName("responseMessage")->item(0)->nodeValue;
        $documentId = $response->getElementsByTagName("documentId")->item(0)->nodeValue;
        $documentId1 = $response->getElementsByTagName("documentId")->item(1)->nodeValue;
        $caseId = $response->getElementsByTagName("caseId")->item(0)->nodeValue;
        $this->assertEquals('000', $responseCode);
        $this->assertEquals('Success', $responseMessage);
        $this->assertEquals('123000', $caseId);
        $this->assertEquals("logo.tiff", $documentId);
        $this->assertEquals("doc.tiff", $documentId1);
    }

    public static function createTestFile($filepath)
    {
        $file = fopen($filepath, "w");
        fwrite($file, "test file");
        fclose($file);
    }
}
