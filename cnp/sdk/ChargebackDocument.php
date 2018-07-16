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

    const SERVICE_ROUTE = "/services/chargebacks";

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
        $explodedParams = explode("/", $uploadFilepath);
        $documentId = end($explodedParams);
        $urlSuffix = self::SERVICE_ROUTE . "/upload/" . $caseId . "/" . $documentId;
        return $this->communication->httpPostDocumentRequest($urlSuffix, $uploadFilepath);
    }

    public function retrieveDocumentAsString($caseId, $documentId)
    {
        $urlSuffix = self::SERVICE_ROUTE . "/retrieve/" . $caseId . "/" . $documentId;
        return $this->communication->httpGetDocumentRequest($urlSuffix);
    }

    public function retrieveDocumentToPath($caseId, $documentId, $downloadDirectoryPath)
    {
        $urlSuffix = self::SERVICE_ROUTE . "/retrieve/" . $caseId . "/" . $documentId;
        $response = $this->communication->httpGetDocumentRequest($urlSuffix);
        $filepath = $downloadDirectoryPath . "/" . $documentId;
        file_put_contents($filepath, $response);
        Utils::printToConsole("\n Document saved at: ", $filepath, $this->config['printXml'], $this->config['neuterXml']);
    }

    public function replaceDocument($caseId, $documentId, $uploadFilepath)
    {
        $urlSuffix = self::SERVICE_ROUTE . "/replace/" . $caseId . "/" . $documentId;
        return $this->communication->httpPutDocumentRequest($urlSuffix, $uploadFilepath);
    }

    public function deleteDocument($caseId, $documentId)
    {
        $urlSuffix = self::SERVICE_ROUTE . "/delete/" . $caseId . "/" . $documentId;
        return $this->communication->httpDeleteDocumentRequest($urlSuffix);

    }

    public function listDocuments($caseId)
    {
        $urlSuffix = self::SERVICE_ROUTE . "/list/" . $caseId;
        return $this->communication->httpGetRequest($urlSuffix);
    }
}
