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
require_once realpath(dirname(__FILE__)) . '/UrlMapper.php';

function writeConfig($line, $handle)
{
    foreach ($line as $keys => $values) {
        fwrite($handle, $keys . '');
        if (is_array($values)) {
            foreach ($values as $key2 => $value2) {
                fwrite($handle, "['" . $key2 . "'] =" . $value2 . PHP_EOL);
            }
        } else {
            fwrite($handle, ' =' . $values);
            fwrite($handle, PHP_EOL);
        }
    }
}

function initialize()
{
    $line = array();

    $handle = @fopen('./chargeback_SDK_config.ini', "w");
    if ($handle) {
        print "Welcome to Vantiv eCommerce PHP Chargeback SDK" . PHP_EOL;
        print "Please input your user name: ";
        $line['username'] = formatConfigValue(STDIN);
        print "Please input your password: ";
        $line['password'] = formatConfigValue(STDIN);
        print "Please input your merchantId: ";
        $line['currency_merchant_map ']['DEFAULT'] = formatConfigValue(STDIN);
        print "Please choose Cnp url from the following list (example: 'sandbox') or directly input another URL: \n" .
            "sandbox => https://www.testvantivcnp.com/sandbox/communicator/online \n" .
            "postlive => https://payments.vantivpostlive.com/vap/communicator/online \n" .
            "transact-postlive => https://transact.vantivpostlive.com/vap/communicator/online \n" .
            "production => https://payments.vantivcnp.com/vap/communicator/online \n" .
            "production-transact => https://transact.vantivcnp.com/vap/communicator/online \n" .
            "prelive => https://payments.vantivprelive.com/vap/communicator/online \n" .
            "transact-prelive => https://transact.vantivprelive.com/vap/communicator/online" . PHP_EOL;
        $url = UrlMapper::getUrl(trim(fgets(STDIN)));

        $line['url'] = $url;
        print "Please input the proxy, if no proxy hit enter key: ";
        $line['proxy'] = formatConfigValue(STDIN);

        $line['print_xml'] = '0';
        $line['neuter_xml'] = "0";
        writeConfig($line, $handle);
        #default http timeout set to 500 ms
        fwrite($handle, "timeout =  500" . PHP_EOL);
    }
    fclose($handle);
    print "The Vantiv eCommerce configuration file has been generated, " .
        "the file is located in the lib directory" . PHP_EOL;
}

function formatConfigValue($str)
{
    return "\"" . trim(fgets($str)) . "\"";
}

initialize();
