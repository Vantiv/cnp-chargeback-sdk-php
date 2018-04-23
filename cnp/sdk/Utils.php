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
require_once realpath(dirname(__FILE__)) . '/Chargeback.php';

class Utils
{
    public static function getConfig($data, $type = NULL)
    {
        $config_array = null;

        $ini_file = realpath(dirname(__FILE__)) . '/chargeback_SDK_config.ini';
        if (file_exists($ini_file)) {
            @$config_array = parse_ini_file('chargeback_SDK_config.ini');
        }

        if (empty($config_array)) {
            $config_array = array();
        }

        $names = explode(',', CNP_CONFIG_LIST);
        foreach ($names as $name) {
            if (isset($data[$name])) {
                $config[$name] = $data[$name];

            } else {
                if ($name == 'merchantId') {
                    $config['merchantId'] = $config_array['currency_merchant_map']['DEFAULT'];
                } elseif ($name == 'version') {
                    $config['version'] = isset($config_array['version']) ? $config_array['version'] : CURRENT_XML_VERSION;
                } elseif ($name == 'timeout') {
                    $config['timeout'] = isset($config_array['timeout']) ? $config_array['timeout'] : '65';
                } else {
                    if ((!isset($config_array[$name])) and ($name != 'proxy')) {
                        throw new \InvalidArgumentException("Missing Field /$name/");
                    }
                    $config[$name] = $config_array[$name];
                }
            }
        }

        return $config;
    }

    public static function generateResponseObject($data, $useSimpleXml)
    {
        if ($useSimpleXml) {
            $respObj = simplexml_load_string($data);
        } else {
            $respObj = XmlParser::domParser($data);
        }

        return $respObj;
    }

    public static function generateChargebackUpdateRequest($hash){
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><chargebackUpdateRequest />");
        $xml-> addAttribute('xmlns:xmlns','http://www.vantivcnp.com/chargebacks');
        foreach ($hash as $key => $value) {
            if (((is_string($value)) || is_numeric($value))) {
                $xml->addChild($key, str_replace('&','&amp;',$value));
            }
        }
        return $xml->asXML();
    }
}
