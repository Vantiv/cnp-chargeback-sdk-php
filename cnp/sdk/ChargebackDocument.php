<?php
/**
 * Created by PhpStorm.
 * User: hvora
 * Date: 4/20/18
 * Time: 2:33 PM
 */

namespace cnp\sdk;


class ChargebackDocument
{
    private $useSimpleXml = false;
    private $config;
    private $comm;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->useSimpleXml = $treeResponse;
        $this->config = Utils::getConfig($overrides);
        $this->comm = new Communication();
    }

    public function uploadDocument($case_id, $file)
    {
        $filename = end(explode("/", $file));
        $request_url = $this->config['url'] . "/upload/" . $case_id . "/" . $filename;
        return $this->comm->httpPostRequest($request_url, $file, $this->config, $this->useSimpleXml);
    }

    public function retrieveDocument($case_id, $document_id, $path)
    {
        $request_url = $this->config['url'] . "/retrieve/" . $case_id . "/" . $document_id;
        return $this->comm->httpGetDocumentRequest($request_url, $path, $this->config, $this->useSimpleXml);
    }

    public function replaceDocument($case_id, $document_id, $file)
    {
        $request_url = $this->config['url'] . "/replace/" . $case_id . "/" . $document_id;
        return $this->comm->httpPutDocumentRequest($request_url, $file, $this->config, $this->useSimpleXml);
    }

    public function removeDocument($case_id, $document_id)
    {
        $request_url = $this->config['url'] . "/remove/" . $case_id . "/" . $document_id;
        return $this->comm->httpDeleteRequest($request_url);

    }

    public function listDocuments($case_id)
    {
        $request_url = $this->config['url'] . "/list/" . $case_id;
        return $this->comm->httpGetRequest($request_url);
    }
}