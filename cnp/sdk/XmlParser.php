<?php

// Copyright (c) 2011 Vantiv eCommerce Inc.

// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.

// class and methods to parse a XML document into an object
namespace cnp\sdk;
class XmlParser
{
    public static function domParser($xml)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        return $doc;
    }

    // Returns the first DOMNode associated with the given tag name in the xml
    public static function getNodeByTagName($dom, $elementName)
    {
        return $dom->getElementsByTagName($elementName)->item(0);
    }

    // Returns the DOMNodeList associated with the given tag name in the xml
    public static function getNodeListByTagName($dom, $elementName)
    {
        return $dom->getElementsByTagName($elementName);
    }

    // Returns the value associated with the given tag name in the xml
    public static function getValueByTagName($dom, $elementName)
    {
        return $dom->getElementsByTagName($elementName)->item(0)->nodeValue;
    }

    // Returns the array of values associated with the given tag name in the xml
    public static function getValueListByTagName($dom, $elementName)
    {
        $nodeList = $dom->getElementsByTagName($elementName);
        $nodes = array();
        foreach($nodeList as $node) {
            $nodes[] = $node->nodeValue;
        }
        return $nodes;
    }

    // Returns the attribute by name, of the given tag name in the xml
    public static function getAttribute($dom, $elementName, $attributeName)
    {
        $attributes = $dom->getElementsByTagName($elementName)->item(0);
        return $attributes->getAttribute($attributeName);
    }

    public static function getDomDocumentAsString($dom)
    {
        return $dom->saveXML($dom);
    }
}
