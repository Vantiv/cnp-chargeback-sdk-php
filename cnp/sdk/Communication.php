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
    const CONTENT_TYPE_HEADER ="Content-type: application/com.vantivcnp.services-v2+xml";
    const ACCEPT_HEADER = "Accept: application/com.vantivcnp.services-v2+xml";

    public function httpGetRequest($request_url, $hash_config = NULL, $useSimpleXml = false)
    {
        $headers = array(self::CONTENT_TYPE_HEADER, self::ACCEPT_HEADER);
        return $this->execHttpRequest($request_url, "PUT", $headers, NULL, NULL, $hash_config, $useSimpleXml);

    }

    public function httpPutRequest($request_url, $request_body, $hash_config = NULL, $useSimpleXml = false)
    {
        $headers = array(self::CONTENT_TYPE_HEADER, self::ACCEPT_HEADER);
        return $this->execHttpRequest($request_url, "PUT", $headers, $request_body, NULL, $hash_config, $useSimpleXml);
    }

    public function httpGetDocumentRequest($request_url, $path, $hash_config = NULL, $useSimpleXml = false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];
        $proxy = $config['proxy'];
        $timeout = $config['timeout'];
        $headers = array('Content-type: text/xml; charset=UTF-8', 'Expect: ', $this->generateAuthHeader($username, $password));
        
        $file = fopen($path, 'w+');
        $ch = $this->generateBaseCurlHandler($request_url, "GET", $headers, $proxy, $timeout);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!$output) {
            fclose($file);
            throw new \Exception (curl_error($ch));
        } else {
            fclose($file);
            curl_close($ch);
            if ((int)$config['print_xml']) {
                echo $output;
            }
        }

    }

    public function httpDeleteRequest($request_url, $hash_config = NULL, $useSimpleXml = false)
    {
        return $this->execHttpRequest($request_url, "DELETE", array(), NULL, NULL, $hash_config, $useSimpleXml);
    }

    public function httpPostRequest($request_url, $filepath, $hash_config = NULL, $useSimpleXml = false)
    {
        return $this->execHttpRequest($request_url, "POST", array(), NULL, $filepath, $hash_config, $useSimpleXml);

    }

    public function httpPutDocumentRequest($request_url, $filepath, $hash_config = NULL, $useSimpleXml = false)
    {
        return $this->execHttpRequest($request_url, "PUT", array(), NULL, $filepath, $hash_config, $useSimpleXml);

    }

    private function execHttpRequest($request_url, $type, $headers = array(), $request_body = NULL, $filepath = NULL, $hash_config = array(), $useSimpleXml = false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];
        $proxy = $config['proxy'];
        $timeout = $config['timeout'];
        $auth_header = $this->generateAuthHeader($username, $password);
        array_push($headers, $auth_header);


        $ch = $this->generateBaseCurlHandler($request_url, $type, $headers, $proxy, $timeout);
        if($request_body != NULL){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        }
        if($filepath != NULL){
            $file = fopen($filepath, 'r');
            curl_setopt($ch, CURLOPT_INFILE, $file);
        }

        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!$output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int)$config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }
    }

    private function generateBaseCurlHandler($request_url, $type, $headers, $proxy, $timeout){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        return $ch;

    }

    public function generateAuthHeader($username, $password)
    {
        return "Authorization: Basic " . base64_encode($username . ":" . $password);
    }
}
