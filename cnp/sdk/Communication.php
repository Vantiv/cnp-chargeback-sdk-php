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
    const CONTENT_TYPE_HEADER = "Content-type: application/com.vantivcnp.services-v2+xml";
    const ACCEPT_HEADER = "Accept: application/com.vantivcnp.services-v2+xml";

    private $config;
    private $useSimpleXml;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->useSimpleXml = $treeResponse;
        $this->config = Utils::getConfig($overrides);
    }

    public function httpGetRequest($requestUrl)
    {
        $this->printToConsole("\nGET request to: ", $requestUrl);
        $headers = array(self::CONTENT_TYPE_HEADER, self::ACCEPT_HEADER);
        return $this->execHttpRequest($requestUrl, "GET", $headers);

    }

    public function httpPutRequest($requestUrl, $requestBody)
    {
        $this->printToConsole("\nPUT request to: ", $requestUrl);
        $headers = array(self::CONTENT_TYPE_HEADER, self::ACCEPT_HEADER);
        return $this->execHttpRequest($requestUrl, "PUT", $headers, $requestBody);
    }

    public function httpGetDocumentRequest($requestUrl, $downloadPath)
    {
        $this->printToConsole("\nGET request to: ", $requestUrl);
        $headers = array($this->generateAuthHeader());
        $file = fopen($downloadPath, 'w+');
        $ch = $this->generateBaseCurlHandler($requestUrl, "GET", $headers);
        $httpResponse = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->validateResponse($httpResponse, $responseCode);
        fclose($file);
        curl_close($ch);
        $this->printToConsole("\nDocument saved at: ", $downloadPath);
    }

    public function httpDeleteRequest($requestUrl)
    {
        $this->printToConsole("\nDELETE request to: ", $requestUrl);
        return $this->execHttpRequest($requestUrl, "DELETE");
    }

    public function httpPostRequest($requestUrl, $uploadFilepath)
    {
        $this->printToConsole("\nPOST request to: ", $requestUrl);
        $this->printToConsole("\nFile: ", $uploadFilepath);
        $headers = array("Content-Type: " . mime_content_type($uploadFilepath));
        return $this->execHttpRequest($requestUrl, "POST", $headers, NULL, $uploadFilepath);

    }

    public function httpPutDocumentRequest($requestUrl, $uploadFilepath)
    {
        $this->printToConsole("\nPUT request to: ", $requestUrl);
        $this->printToConsole("\nFile: ", $uploadFilepath);
        $headers = array("Content-Type: " . mime_content_type($uploadFilepath));
        return $this->execHttpRequest($requestUrl, "PUT", $headers, NULL, $uploadFilepath);
    }

    private function execHttpRequest($requestUrl, $requestType, $headers = array(), $requestBody = NULL, $uploadFilepath = NULL)
    {
        $auth_header = $this->generateAuthHeader();
        array_push($headers, $auth_header);

        $ch = $this->generateBaseCurlHandler($requestUrl, $requestType, $headers);
        if ($requestBody != NULL) {
            $this->printToConsole("\nRequest body: ", $requestBody);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        }
        if ($uploadFilepath != NULL) {
            $file = fopen($uploadFilepath, 'r');
            curl_setopt($ch, CURLOPT_INFILE, $file);
            fclose($file);
        }

        $httpResponse = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->validateResponse($httpResponse, $responseCode);
        curl_close($ch);
        $this->printToConsole("\nResponse: ", $httpResponse);
        return Utils::generateResponseObject($httpResponse, $this->useSimpleXml);
    }

    private function validateResponse($httpResponse, $statusCode)
    {
        if (!$httpResponse) {
            throw new ChargebackException("There was an exception while fetching the response.");
        } else if ($statusCode != 200 || $statusCode != "200") {
            $this->printToConsole("\nError: ", $httpResponse);
            $errorResponse = Utils::generateResponseObject($httpResponse, true);
            throw new ChargebackException($errorResponse->errors->error, $statusCode);
        }
    }

    private function generateBaseCurlHandler($requestUrl, $type, $headers)
    {
        $proxy = $this->config['proxy'];
        $timeout = $this->config['timeout'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        return $ch;

    }

    public function generateAuthHeader()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];
        return "Authorization: Basic " . base64_encode($username . ":" . $password);
    }

    private function printToConsole($prefixMessage, $message)
    {
        if ((int)$this->config['print_xml']) {
            echo "\n" . $prefixMessage . $message;
        }
    }
}
