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
    public function httpRequest($req,$hash_config=NULL)
    {
        $config = Obj2xml::getConfig($hash_config);

        if ((int) $config['print_xml']) {
            echo $req;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=UTF-8','Expect: '));
        curl_setopt($ch, CURLOPT_URL, $config['url']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }

            return $output;
        }

    }

    public function httpGetRequest($request_url, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];
        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: '.$this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }

    }

    public function httpPutRequest($request_url, $request_body, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];


        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: ' . $this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }

    }

    public function httpGetDocumentRequest($request_url, $path, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];
        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: '.$this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $file = fopen ($path, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_FILE, $file);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            fclose($file);
            throw new \Exception (curl_error($ch));
        } else {
            fclose($file);
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
        }

    }

    public function httpDeleteRequest($request_url, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];
        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: '.$this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }

    }

    public function httpPostRequest($request_url, $filepath, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];


        $file = fopen($filepath, 'r');

        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: ' . $this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_INFILE, $file);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }

    }

    public function httpPutDocumentRequest($request_url, $filepath, $hash_config=NULL, $useSimpleXml=false)
    {
        $config = Obj2xml::getConfig($hash_config);
        $username = $config['username'];
        $password = $config['password'];


        $file = fopen($filepath, 'r');

        $headers = array('Content-type: text/xml; charset=UTF-8','Expect: ', 'Authorization: ' . $this->generateAuthCode($username, $password));

//        if ((int) $config['print_xml']) {
//            echo $req;
//        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $config['proxy']);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_INFILE, $file);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! $output) {
            throw new \Exception (curl_error($ch));
        } else {
            curl_close($ch);
            if ((int) $config['print_xml']) {
                echo $output;
            }
            $output = Utils::generateRetrievalResponse($output, $useSimpleXml);
            return $output;
        }

    }

    public function generateAuthCode($username, $password){
        return "Basic " . base64_encode($username) . ":" . base64_encode($password);
    }
}
