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

namespace cnp\sdk\Test\functional;

use cnp\sdk\ChargebackRetrieval;
use cnp\sdk\Utils;

require_once realpath(__DIR__) . '/../../../../vendor/autoload.php';

class ChargebackRetrievalTest extends \PHPUnit_Framework_TestCase
{
    private $chargebackRetrieval;

    public function setUp()
    {
        $this->chargebackRetrieval = new ChargebackRetrieval();
    }

    public function testChargebackByDate()
    {
        $response = $this->chargebackRetrieval->getChargebacksByDate("2018-01-01");
        //assert statements
    }

    public function testChargebackByCaseId()
    {
        $response = $this->chargebackRetrieval->getChargebackByCaseId("123");
        //assert statements
    }

    public function testChargebacksByFinancialImpact()
    {
        $response = $this->chargebackRetrieval->getChargebacksByFinancialImpact("2018-01-01", true);
        //assert statements
    }

    public function testChargebacksActionable()
    {
        $response = $this->chargebackRetrieval->getActionableChargebacks(true);
        //assert statements
    }

    public function testChargebacksbyToken()
    {
        $response = $this->chargebackRetrieval->getChargebacksByToken("100000");
        //assert statements
    }

    public function testChargebacksByCardNumber()
    {
        $response = $this->chargebackRetrieval->getChargebacksByCardNumber("1111000011110000", "1018");
        //assert statements
    }

    public function testChargebacksByArn()
    {
        $response = $this->chargebackRetrieval->getChargebacksByArn("1000000000");
        //assert statements
    }

    public function testErrorResponse()
    {
        $response = $this->chargebackRetrieval->getChargebackByCaseId("123");
        //expect exception
    }
}
