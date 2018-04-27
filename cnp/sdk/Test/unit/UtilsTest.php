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

use cnp\sdk\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function test_generate_chargeback_update_request()
    {
        $hash = array("activityType" => "ADD_NOTE", "note" => "note");
        $xml = Utils::generateChargebackUpdateRequest($hash);
        $cleanedUp = str_replace("\n", '', $xml);
        $cleanedUp = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $cleanedUp);

        $expected = '<chargebackUpdateRequest xmlns="http://www.vantivcnp.com/chargebacks"><activityType>ADD_NOTE</activityType><note>note</note></chargebackUpdateRequest>';

        $this->assertEquals($expected, $cleanedUp);
    }

    public function test_neuter_string()
    {
        $str = "<cardNumberLast4>sensitivedata</cardNumberLast4><token>stuff</token>";
        $neuterStr = Utils::neuterString($str);

        $this->assertEquals("<cardNumberLast4>****</cardNumberLast4><token>****</token>", $neuterStr);
    }
}
