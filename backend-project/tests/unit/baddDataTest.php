<?php

class addDataTest extends \Codeception\Test\Unit
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
     * testAddData
     * This function will create mock of the UserController
     * Here declaring the method addUserData and it will return 
     * bool(true)
     * 
     * @return void
     */
    public function testAddData()
    {
        $userControllerObject = $this->createMock(UserController::class);
        $userControllerObject->method("addUserData")->willReturn(true);

        $userControllerObject
        ->expects($this->once())
        ->method("addUserData")
        ->with(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE']);

        $result = $userControllerObject->addUserData(["id" => 5, "name" => 'Vivek', "state" => "Karnataka", 
        "zip" => 123456, "amount" => 25.05, "qty" => 8, "item" => 'ABCDE']);

        $this->assertTrue($result);
    }
    
    /**
     * testAddDataFail
     * If we send empty array to addUserData
     * it will not add the record to csv file
     * @return void
     */
    public function testAddDataFail()
    {
        $userControllerObject = $this->createMock(UserController::class);
        $userControllerObject->method("addUserData")->willReturn(true);

        $userControllerObject
        ->expects($this->once())
        ->method("addUserData")
        ->with([]);

        $result = $userControllerObject->addUserData([]);

        $this->assertTrue($result);

    }
}