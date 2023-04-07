<?php

class updateDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }
  
    /**
     * testUpdateData
     * editCsvOrderData takes array as a parameter
     * with fields mentioned as following
     * @return void
     */
    public function testUpdateData()
    {
        
        $csvOrderControllerObject = $this->createMock(CsvOrderController::class);

        $csvOrderControllerObject
        ->expects($this->once())
        ->method("editCsvOrderData")
        ->with(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "quantity" => 8, "item" => 'ABCDE'])
        ->will($this->returnValue(true));


        $result = $csvOrderControllerObject->editCsvOrderData(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "quantity" => 8, "item" => 'ABCDE']);

        $this->assertTrue($result);
    }
    
    /**
     * testUpdateDataFail
     * If Id is not passed in array to editCsvOrderData
     * it will not update into the csv file
     * @return void
     */
    public function testUpdateDataFail()
    {
        $csvOrderControllerObject = $this->createMock(CsvOrderController::class);

        $csvOrderControllerObject
        ->expects($this->once())
        ->method("editCsvOrderData")
        ->with(["name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "quantity" => 8, "item" => 'ABCDE'])
        ->will($this->returnValue(false));


        $result = $csvOrderControllerObject->editCsvOrderData(["name" => 'Vivek', "state" => "Karnataka","zip" => 123456, "amount" => 25.05, "quantity" => 8, "item" => 'ABCDE']);

        $this->assertFalse($result);
    }
}