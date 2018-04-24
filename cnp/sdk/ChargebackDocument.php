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
    private $config;
    private $communication;

    public function __construct($treeResponse = false, $overrides = array())
    {
        $this->config = Utils::getConfig($overrides);
        $this->communication = new Communication($treeResponse, $overrides);
    }

    public function setCommunication($communication)
    {
        $this->communication = $communication;
    }

    ////////////////////////////////////////////////////////////////////
    //                    ChargebackDocument API:                     //
    ////////////////////////////////////////////////////////////////////

    public function uploadDocument($caseId, $uploadFilepath)
    {
        $documentId = end(explode("/", $uploadFilepath));
        $requestUrl = $this->config['url'] . "/upload/" . $caseId . "/" . $documentId;
        return $this->communication->httpPostDocumentRequest($requestUrl, $uploadFilepath);
    }

    public function retrieveDocument($caseId, $documentId, $downloadPath)
    {
        $requestUrl = $this->config['url'] . "/retrieve/" . $caseId . "/" . $documentId;
        $this->communication->httpGetDocumentRequest($requestUrl, $downloadPath);
    }

    public function replaceDocument($caseId, $documentId, $uploadFilepath)
    {
        $requestUrl = $this->config['url'] . "/replace/" . $caseId . "/" . $documentId;
        return $this->communication->httpPutDocumentRequest($requestUrl, $uploadFilepath);
    }

    public function removeDocument($caseId, $documentId)
    {
        $requestUrl = $this->config['url'] . "/remove/" . $caseId . "/" . $documentId;
        return $this->communication->httpDeleteDocumentRequest($requestUrl);

    }

    public function listDocuments($caseId)
    {
        $requestUrl = $this->config['url'] . "/list/" . $caseId;
        return $this->communication->httpGetRequest($requestUrl);
    }
}