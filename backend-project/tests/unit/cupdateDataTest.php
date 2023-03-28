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
     * updateUserData takes array as a parameter
     * with fields mentioned as following
     * @return void
     */
    public function testUpdateData()
    {
        
        $userControllerObject = $this->createMock(UserController::class);

        $userControllerObject
        ->expects($this->once())
        ->method("updateUserData")
        ->with(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE'])
        ->will($this->returnValue(true));


        $result = $userControllerObject->updateUserData(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE']);

        $this->assertTrue($result);
    }
    
    /**
     * testUpdateDataFail
     * If Id is not passed in array to updateUserData
     * it will not update into the csv file
     * @return void
     */
    public function testUpdateDataFail()
    {
        $userControllerObject = $this->createMock(UserController::class);

        $userControllerObject
        ->expects($this->once())
        ->method("updateUserData")
        ->with(["name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE'])
        ->will($this->returnValue(false));


        $result = $userControllerObject->updateUserData(["name" => 'Vivek', "state" => "Karnataka","zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE']);

        $this->assertFalse($result);
    }
}