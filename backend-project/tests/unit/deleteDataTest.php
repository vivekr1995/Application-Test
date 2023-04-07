<?php

/**
 * deleteDataTest
 */
class deleteDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
        
    /**
     * _before
     *
     * @return void
     */
    protected function _before()
    {
    }
    
    /**
     * _after
     *
     * @return void
     */
    protected function _after()
    {
    }
    
    /**
     * testDeleteTest
     *
     * @return void
     */
    public function testDeleteTest()
    {

        $csvOrderControllerObject = $this->createMock(CsvOrderController::class);
        $csvOrderControllerObject->method("removeCsvOrderData")->willReturn(true);

        $csvOrderControllerObject
        ->expects($this->once())
        ->method("removeCsvOrderData")
        ->with(5);

        $result = $csvOrderControllerObject->removeCsvOrderData(5);

        $this->assertTrue($result);
    }
   
    /**
     * testDeleteFail
     * If 0 is passed to removeCsvOrderData funtion it will 
     * Delete user data from csv file
     * @return void
     */
    public function testDeleteFail()
    {
        $csvOrderControllerObject = $this->createMock(CsvOrderController::class);
        $csvOrderControllerObject->method("removeCsvOrderData")->willReturn(false);

        $csvOrderControllerObject
        ->expects($this->once())
        ->method("removeCsvOrderData")
        ->with(0);

        $result = $csvOrderControllerObject->removeCsvOrderData(0);

        $this->assertFalse($result);
    }
}