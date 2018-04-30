<?php
/**
 * Created by PhpStorm.
 * User: enunez
 * Date: 4/26/18
 * Time: 10:00 AM
 */

namespace cnp\sdk\Test\certification;

use cnp\sdk\ChargebackRetrieval;
use cnp\sdk\ChargebackUpdate;
use cnp\sdk\XmlParser;

class TestCert extends \PHPUnit_Framework_TestCase
{
    private $chargebackRetrieval;
    private $chargebackUpdate;

    // test responses
    const CYCLE_FIRST_CHARGEBACK = "First Chargeback";
    const CYCLE_PRE_ARB_CHARGBACK = "Pre-Arbitration";
    const CYCLE_ARBITRATION_CHARGEBACK = "VISA Pre-Arbitration/Arbitration";
    const CYCLE_ISSUER_DECLINE_PRESAB = "Issuer Declined Pre-Arbitration";
    const ACTIVITY_MERCHANT_REPRESENT = "Merchant Represent";
    const ACTIVITY_MERCHANT_ACCEPTS_LIABILITY = "Merchant Accepts Liability";
    const ACTIVITY_ADD_NOTE = "Add Note";

    public function setUp()
    {
        $this->chargebackRetrieval = new ChargebackRetrieval();
        $this->chargebackUpdate = new ChargebackUpdate();
    }

    public function test1()
    {
        $response = $this->chargebackRetrieval->getChargebacksByDate("2013-01-01");
        $cases = XmlParser::getNodeListByTagName($response, "chargebackCase");

        $this->testChargebackCase($cases->item(0), "1111111111", self::CYCLE_FIRST_CHARGEBACK);
        $this->testChargebackCase($cases->item(1), "2222222222", self::CYCLE_FIRST_CHARGEBACK);
        $this->testChargebackCase($cases->item(2), "3333333333", self::CYCLE_FIRST_CHARGEBACK);
        $this->testChargebackCase($cases->item(3), "4444444444", self::CYCLE_FIRST_CHARGEBACK);
        $this->testChargebackCase($cases->item(4), "5555555550", self::CYCLE_PRE_ARB_CHARGBACK);
        $this->testChargebackCase($cases->item(5), "5555555551", self::CYCLE_PRE_ARB_CHARGBACK);
        $this->testChargebackCase($cases->item(6), "5555555552", self::CYCLE_PRE_ARB_CHARGBACK);
        $this->testChargebackCase($cases->item(7), "6666666660", self::CYCLE_ARBITRATION_CHARGEBACK);
        $this->testChargebackCase($cases->item(8), "7777777770", self::CYCLE_ISSUER_DECLINE_PRESAB);
        $this->testChargebackCase($cases->item(9), "7777777771", self::CYCLE_ISSUER_DECLINE_PRESAB);
        $this->testChargebackCase($cases->item(10), "7777777772",self:: CYCLE_ISSUER_DECLINE_PRESAB);
    }

    function testChargebackCase($case, $arn, $cycle)
    {
        $this->assertEquals($arn, XmlParser::getValueByTagName($case, "acquirerReferenceNumber"));
        $this->assertEquals($cycle, XmlParser::getValueByTagName($case, "cycle"));
    }

    public function test2()
    {
        $caseId = $this->getCaseIdForArn("1111111111");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test2");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_ADD_NOTE, $activityType);
        $this->assertEquals("Cert test2", $activityNotes);
    }

    public function test3_1()
    {
        $caseId = $this->getCaseIdForArn("2222222222");
        $this->chargebackUpdate->representCase($caseId, "Cert test3_1");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals("Cert test3_1", $activityNotes);
    }

    public function test3_2()
    {
        $caseId = $this->getCaseIdForArn("3333333333");
        $this->chargebackUpdate->representCase($caseId, "Cert test3_2", 10027);
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $settlementAmount = XmlParser::getValueByTagName($activity, "settlementAmount");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals(10027, $settlementAmount);
        $this->assertEquals("Cert test3_2", $activityNotes);
    }

    public function test4_and_5_1()
    {
        $caseId = $this->getCaseIdForArn("4444444444");
        $this->chargebackUpdate->assumeLiability($caseId, "Cert test4");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_ACCEPTS_LIABILITY, $activityType);
        $this->assertEquals("Cert test4", $activityNotes);

        try {
            $this->chargebackUpdate->assumeLiability($caseId, "Cert test5_1");
            $this->fail("Expected Exception");
        } catch (ChargebackWebException $e) {
            $this->assertEquals($e->getMessage(), "Could not find requested object.");
            $this->assertEquals($e->getCode(), 404);
        }
    }

    public function test5_2()
    {
        try {
            $caseId = $this->getCaseIdForArn("1234");
            $this->fail("Expected Exception");
        } catch (ChargebackWebException $e) {
            $this->assertEquals($e->getMessage(), "Could not find requested object.");
            $this->assertEquals($e->getCode(), 404);
        }
    }

    public function test6_1()
    {
        $caseId = $this->getCaseIdForArn("5555555550");
        $this->chargebackUpdate->representCase($caseId, "Cert test6_1");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals("Cert test6_1", $activityNotes);
    }

    public function test6_2()
    {
        $caseId = $this->getCaseIdForArn("5555555551");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test6_2", 10051);
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");
        $settlementAmount = XmlParser::getValueByTagName($activity, "settlementAmount");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals("Cert test6_2", $activityNotes);
        $this->assertEquals(10051, $settlementAmount);
    }

    public function test7()
    {
        $caseId = $this->getCaseIdForArn("5555555552");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test7");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_ACCEPTS_LIABILITY, $activityType);
        $this->assertEquals("Cert test7", $activityNotes);
    }
    public function test8()
    {
        $caseId = $this->getCaseIdForArn("6666666660");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test8");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_ACCEPTS_LIABILITY, $activityType);
        $this->assertEquals("Cert test8", $activityNotes);
    }

    public function test9_1()
    {
        $caseId = $this->getCaseIdForArn("7777777770");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test9_1");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals("Cert test9_1", $activityNotes);
    }

    public function test9_2()
    {
        $caseId = $this->getCaseIdForArn("7777777771");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test9_2", 10071);
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");
        $settlementAmount = XmlParser::getValueByTagName($activity, "settlementAmount");

        $this->assertEquals(self::ACTIVITY_MERCHANT_REPRESENT, $activityType);
        $this->assertEquals("Cert test9_2", $activityNotes);
        $this->assertEquals(10071, $settlementAmount);
    }

    public function test10()
    {
        $caseId = $this->getCaseIdForArn("7777777772");
        $this->chargebackUpdate->addNoteToCase($caseId, "Cert test10");
        $activity = $this->getLastActivity($caseId);
        $activityType = XmlParser::getValueByTagName($activity, "activityType");
        $activityNotes = XmlParser::getValueByTagName($activity, "notes");

        $this->assertEquals(self::ACTIVITY_MERCHANT_ACCEPTS_LIABILITY, $activityType);
        $this->assertEquals("Cert test10", $activityNotes);
    }


    private function getCaseIdForArn($arn)
    {
        $response = $this->chargebackRetrieval->getChargebacksbyArn($arn);
        $case = XmlParser::getNodeByTagName($response, "chargebackCase");
        $caseId = XmlParser::getValueByTagName($case, "caseId");
        return $caseId;
    }

    private function getLastActivity($caseId)
    {
        $response = $this->chargebackRetrieval->getChargebackByCaseId($caseId);
        $case = XmlParser::getNodeByTagName($response, "chargebackCase");
        $activity = XmlParser::getNodeByTagName($case, "activity");
        return $activity;
    }
}




