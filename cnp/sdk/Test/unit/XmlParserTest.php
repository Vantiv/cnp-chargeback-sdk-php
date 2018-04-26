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

use cnp\sdk\XmlParser;

class XmlParserTest extends \PHPUnit_Framework_TestCase
{
    public function test_round_trip_xml_to_dom_and_back()
    {
        $orig = '<foo>bar</foo>';
        $dom = XmlParser::domParser($orig);
        $after = XmlParser::getDomDocumentAsString($dom);
        $cleanedUp = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $after);
        $cleanedUp = str_replace("\n", '', $cleanedUp);
        $this->assertEquals($orig, $cleanedUp);
    }

    public function test_getNodeByTagName()
    {
        $orig = '<foo>bar<test>test</test></foo>';
        $dom = XmlParser::domParser($orig);
        $domnode = XmlParser::getNodeByTagName($dom, 'test');
        $this->assertTrue($domnode instanceof \DOMNode);
        $this->assertEquals('test', $domnode->nodeValue);
    }

    public function test_getNodeListByTagName()
    {
        $orig = '<foo>bar<test>test1</test><test>test2</test></foo>';
        $dom = XmlParser::domParser($orig);
        $domnodelist = XmlParser::getNodeListByTagName($dom, 'test');
        $this->assertTrue($domnodelist instanceof \DOMNodeList);
        $this->assertEquals(2, $domnodelist->length);
        $this->assertEquals('test1', $domnodelist->item(0)->nodeValue);
        $this->assertEquals('test2', $domnodelist->item(1)->nodeValue);
    }

    public function test_getValueByTagName()
    {
        $orig = '<foo>bar<test>test</test></foo>';
        $dom = XmlParser::domParser($orig);
        $domvalue = XmlParser::getValueByTagName($dom, 'test');
        $this->assertTrue(is_string($domvalue));
        $this->assertEquals('test', $domvalue);
    }

    public function test_getValueListByTagName()
    {
        $orig = '<foo>bar<test>test1</test><test>test2</test></foo>';
        $dom = XmlParser::domParser($orig);
        $domvaluelist = XmlParser::getValueListByTagName($dom, 'test');
        $this->assertTrue(is_array($domvaluelist));
        $this->assertEquals(2, sizeof($domvaluelist));
        $this->assertContains('test1', $domvaluelist);
        $this->assertContains('test2', $domvaluelist);
    }
}
