<?php
define('APPROOT', __DIR__ . '/../../'); // APPROOT contains the root path of the project

require __DIR__ . "/../../app.php"; // include the class for the unit test cases

/**
 * readDataTest
 */
class readDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $csvFilePath = 'tests/_data/data.csv';
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests    
    /**
     * testReadData
     *
     * @return void
     */
    public function testReadData()
    {
        $dataCOntrollerbject = new CsvOrderController($this->csvFilePath);

        //read data
        $result = $dataCOntrollerbject->getCsvOrderData($this->csvFilePath);

        $this->assertIsArray($result, "returns array on success");
        $this->assertGreaterThanOrEqual(2, count($result)); // compare with number of array in csv file
    }
    
    /**
     * testFileReadable
     * Check the whether the given csv file is 
     * readable or not
     * @return void
     */
    public function testFileReadable(){
        $this->assertFileIsReadable($this->csvFilePath);
    }
    
    /**
     * testCsvWrite
     * Check the whether the given csv file is 
     * writable or not
     * @return void
     */
    public function testCsvWrite(): void
    {
        $this->assertFileIsWritable($this->csvFilePath);
    }
}