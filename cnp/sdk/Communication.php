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
class Communication
{
    private $config;
    private $url;
    private $useSimpleXml;
    private $printXml;
    private $neuterXml;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->useSimpleXml = $treeResponse;
        $this->config = Utils::getConfig($overrides);
        $this->url = $this->config['url'];
        $this->printXml = $this->config['printXml'];
        $this->neuterXml = $this->config['neuterXml'];
    }

    public function httpGetRequest($urlSuffix)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nGET request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        $headers = array("Content-Type: " . CNP_CONTENT_TYPE, "Accept: " . CNP_CONTENT_TYPE);
        return $this->getHttpResponse($requestUrl, "GET", $headers);

    }

    public function httpPutRequest($urlSuffix, $requestBody)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nPUT request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        $headers = array("Content-Type: " . CNP_CONTENT_TYPE, "Accept: " . CNP_CONTENT_TYPE);
        Utils::printToConsole("\nRequest body: ", $requestBody, $this->printXml, $this->neuterXml);
        $options = array(CURLOPT_POSTFIELDS => $requestBody);
        return $this->getHttpResponse($requestUrl, "PUT", $headers, $options);
    }

    public function httpGetDocumentRequest($urlSuffix)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nGET request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        $responseArray = $this->execHttpRequest($requestUrl, "GET");
        $response = $responseArray['response'];
        $statusCode = $responseArray['statusCode'];
        $contentType = $responseArray['contentType'];
        $this->validateDocumentResponse($response, $statusCode, $contentType);
        return $response;
    }

    public function httpDeleteDocumentRequest($urlSuffix)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nDELETE request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        return $this->getHttpResponse($requestUrl, "DELETE");
    }

    public function httpPostDocumentRequest($urlSuffix, $uploadFilepath)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nPOST request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        Utils::printToConsole("\nFile: ", $uploadFilepath, $this->printXml, $this->neuterXml);
        $headers = array("Content-Type: " . mime_content_type($uploadFilepath));
        $file = fopen($uploadFilepath, 'r');
        $options = array(CURLOPT_INFILE => $file);
        $response = $this->getHttpResponse($requestUrl, "POST", $headers, $options);
        fclose($file);
        return $response;
    }

    public function httpPutDocumentRequest($urlSuffix, $uploadFilepath)
    {
        $requestUrl = $this->url . $urlSuffix;
        Utils::printToConsole("\nPUT request to: ", $requestUrl, $this->printXml, $this->neuterXml);
        Utils::printToConsole("\nFile: ", $uploadFilepath, $this->printXml, $this->neuterXml);
        $headers = array("Content-Type: " . mime_content_type($uploadFilepath));
        $file = fopen($uploadFilepath, 'r');
        $options = array(CURLOPT_INFILE => $file);
        $response = $this->getHttpResponse($requestUrl, "PUT", $headers, $options);
        fclose($file);
        return $response;
    }

    private function getHttpResponse($requestUrl, $requestType, $headers = array(), $options = array())
    {
        $responseArray = $this->execHttpRequest($requestUrl, $requestType, $headers, $options);
        $response = $responseArray['response'];
        $statusCode = $responseArray['statusCode'];
        $contentType = $responseArray['contentType'];
        $this->validateResponse($response, $statusCode, $contentType);
        Utils::printToConsole("\nResponse: ", $response, $this->printXml, $this->neuterXml);
        return Utils::generateResponseObject($response, $this->useSimpleXml);
    }

    private function execHttpRequest($requestUrl, $requestType, $headers = array(), $options = array())
    {
        $ch = $this->generateBaseCurlHandler($requestUrl, $requestType, $headers, $options);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return array('response' => $response, 'statusCode' => $statusCode, 'contentType' => $contentType);
    }

    private function validateResponse($httpResponse, $statusCode, $contentType)
    {
        if (!$httpResponse) {
            throw new ChargebackWebException("There was an exception while fetching the response");
        } else if ($statusCode != 200 || $statusCode != "200") {
            Utils::printToConsole("\nError Response: ", $httpResponse, $this->printXml, $this->neuterXml);
            $errorMessage = $this->generateErrorMessage($httpResponse);
            throw new ChargebackWebException($errorMessage, $statusCode);
        }
    }

    private function validateDocumentResponse($httpResponse, $statusCode, $contentType)
    {
        if (!$httpResponse) {
            throw new ChargebackWebException("There was an exception while fetching the response");
        } else if ($statusCode != 200 || $statusCode != "200") {
            Utils::printToConsole("\nError Response: ", $httpResponse, $this->printXml, $this->neuterXml);
            $errorMessage = $this->generateErrorMessage($httpResponse);
            throw new ChargebackWebException($errorMessage, $statusCode);
        } else if (strpos($contentType, CNP_CONTENT_TYPE) !== false) {
            Utils::printToConsole("\nDocument Error Response: ", $httpResponse, $this->printXml, $this->neuterXml);
            $errorMessage = $this->generateDocumentErrorMessage($httpResponse);
            $errorCode = $this->getDocumentErrorCode($httpResponse);
            throw new ChargebackDocumentException($errorMessage, $errorCode);
        }
    }

    private function generateBaseCurlHandler($requestUrl, $type, $headers, $options = array())
    {
        $auth_header = $this->generateAuthHeader();
        array_push($headers, $auth_header);
        $proxy = $this->config['proxy'];
        $timeout = $this->config['timeout'];
        $ch = curl_init();
        $defaultOptions = array(
            CURLOPT_PROXY => $proxy,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $requestUrl,
            CURLOPT_HTTPPROXYTUNNEL => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSLVERSION => 6);

        curl_setopt_array($ch, $defaultOptions + $options);
        return $ch;
    }

    private function generateAuthHeader()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];
        return "Authorization: Basic " . base64_encode($username . ":" . $password);
    }

    private function generateErrorMessage($errorResponseXml)
    {
        $errorResponse = Utils::generateResponseObject($errorResponseXml, false);
        $errorMessageList = XmlParser::getValueListByTagName($errorResponse, 'error');
        $errorMessage = "";
        $prefix = "";
        foreach ($errorMessageList as $error){
            $errorMessage .= $prefix . $error;
            $prefix = "\n";
        }
        return $errorMessage;
    }

    private function generateDocumentErrorMessage($documentResponseXml)
    {
        $errorResponse = Utils::generateResponseObject($documentResponseXml, false);
        $errorMessage = XmlParser::getValueByTagName($errorResponse, 'responseMessage');
        return $errorMessage;
    }

    private function getDocumentErrorCode($documentResponseXml)
    {
        $errorResponse = Utils::generateResponseObject($documentResponseXml, false);
        $errorCode = XmlParser::getValueByTagName($errorResponse, 'responseCode');
        return $errorCode;
    }
}
