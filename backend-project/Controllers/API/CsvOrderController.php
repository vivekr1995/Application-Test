<?php

//importing required files
require_once(APPROOT . 'Interfaces/OrderData.php');
require_once(APPROOT . 'Traits/ApiResponse.php');

//Interfaces and Traits
use \Interfaces\OrderData;
use \Traits\ApiResponse;

/**
 *  load Mongolog logger framework
 *  
 */
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CsvOrderController implements OrderData {

	use ApiResponse;

    private $csvFilePath = '';
    private $logger;
    
    /**
     * __construct
     *
     * @param  string $filePath
     * @return void
     */
    public function __construct(string $csvFilePath)
    {
        $this->csvFilePath = $csvFilePath;

		//check if file exists , if not throws an exception
        $this->checkCsvFile($this->csvFilePath);
        
        /**
         * Create object of Logger with channel-name Info
         * To handle creating and writing logs into file
         */
        $this->logger = new Logger("info");
        $stream_handler = new StreamHandler(APPROOT . 'logs/app.log', Logger::DEBUG);
        $this->logger->pushHandler($stream_handler);
    }

	/**
	 * getCsvOrderData
	 * opens csv file in read mode and reads then returns an array
	 * @return array
	 */
	public function getCsvOrderData(): array
	{
		try{
			$csv = [];
			// Open for reading only
			if (($handle = fopen($this->csvFilePath, "r")) !== FALSE) {
				while (($data = fgetcsv($handle)) !== FALSE) {
					$csv[] = $data; // push each row into an array
				}
				fclose($handle);// after read is complete close the file
			}else{
				throw new Exception("Failed to open csv file");
			}
        
            return $csv;

		}catch(Exception $e){
	
			$response = $e->getMessage();
			$this->logger->error($response);
	
			return $this->getErrorResponse(
				$response, false
			 , 304);  
		}
	}

	/**
     * addCsvOrderData
     * read data from csv file , compare row-id with request id
     * push new array data along with existing array and write into csv file
     * @param  array $data
     * @return json
     */
	public function addCsvOrderData(array $data) {
		try{

            if(empty($data)) throw new Exception("Order can not be empty"); // check if empty array

            //get the count of records in csv file including header
            $getAllData = $this->getCsvOrderData();
            $num = count($getAllData);
    
            if(!array_key_exists("id", $data)){
                
               /* 
                Creating new Id
               */
               $data = array("id" => $num) + $data;
            }else{
                $data["id"] = $num;
            }
            
			//Removing unwanted elements from new data
            unset($data["isEdit"]);
            unset($data["isSelected"]);

            // Push new data to existing array
            array_push($getAllData, $data);
    
            $result = $this->writeCSVFile($getAllData);

            if($result){
                return $this->getSuccessResponse($result, true, 200);
            }else{
                throw new Exception("Failed to add data"); 
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->getErrorResponse(
                $response, false
             , 304);    
        }
	}

	/**
     * editCsvOrderData
     * read data from csv file , compare row-id with request id
     * re-write new array to selected row
     * @param  array $data
     * @return json
     */
	public function editCsvOrderData(array $data) {
		try{
			//Getting ID
            $id = array_key_exists("id",$data) ? $data['id'] : 0 ;
            $getAllData = $this->getCsvOrderData();
            $num = count($getAllData);

			//Removing unwanted elements from new data
            unset($data["isEdit"]);
            unset($data["isSelected"]);

            // Avoiding header data
            for($i = 1; $i < $num; $i++){
                // compare id of edited data with existing CSV file data
                // re-write array values to selected array
                if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                    $getAllData[$i] = array_values($data); // getting values from array
                    break;
                }
            }
    
            $result = $this->writeCSVFile($getAllData);

            if($result){

                return $this->getSuccessResponse(
                    $result,
                     true, 200
                );
            }else{
                throw new Exception("Failed to update order data");
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);
 
            return $this->getErrorResponse(
                 $response, false
              , 304);   
        }
	}

	/**
     * removeCsvOrderData
     * read data from csv file , compare row-id with request id and remove row data
     * @param  int $id
     * @return json
     */
    public function removeCsvOrderData($id) {

		try{
            
            if ($id == 0) throw new Exception("Order delete Id should not be 0.");

            $getAllData = $this->getCsvOrderData();
    
            $num = count($getAllData);

            // Avoiding header data
            for($i = 1; $i < $num; $i++){
    
                if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                    array_splice($getAllData, $i, 1); // splice current index from array
					break;
                }
    
            }
    
            $result = $this->writeCSVFile($getAllData);

            if($result){
                return $this->getSuccessResponse($result, true, 200);
            }else{
                throw new Exception("Failed to delete order data");
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->getErrorResponse(
                $response, false
             , 304);   
        }
	}

	/**
     * writeCSVFile
     * Opens csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return bool
     */
    public function writeCSVFile(array $data): bool
    {
        try { 

            if(count($data) == 0) throw new Exception("Length of array should not be 0.");

            // open file in write only mode
            if (($fhandle = fopen($this->csvFilePath, "w")) !== FALSE) {
                foreach ($data as $fields) {
                    fputcsv($fhandle, $fields);
                }
                return fclose($fhandle); // returns true or false
            }else{
                throw new Exception(" Failed to open file in write mode");
            } 

        }catch (Exception $e) {

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->getErrorResponse(
                $response, false
             , 304);            
        }

    }

	/**
     * checkCsvFile
     *
     * @param  string $csvFilePath
     * @return void
     */
    public function checkCsvFile(string $csvFilePath)
    {
        try {
            if(!file_exists($csvFilePath)){
                throw new Exception("File does not exists");
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->getErrorResponse(
                $response, false
             , 304);
        }
    }

	/**
     * validateInputData
     * checks if any field is mandatory or not
     * validate characters and numbers for each fields
     * @param  array $data
     * @return array
     */
    public function validateInputData(array $data): array
    {
        $errors = []; // empty error array

        // destructuring array for fields
        ['id' => $id, 'name' => $name, 'state' => $state, 'zip' => $zip,
         'amount' => $amount, 'quantity' => $quantity, 'item' => $item] = $data;

        if (!empty($data)) {

            if (empty($name)) {
                $errors['name'] = "Name is required";
            }else{
                $name_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($name_regex, $name) ) { 
                    $errors['name'] = "Name must be in Letters.";
                }
            }

            if (empty($state)) {
                    $errors['state'] = "State is required";
            }else{
                $state_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($state_regex, $name) ) { 
                    $errors['state'] = "State must be in Letters.";
                }
            }

            if (empty($zip)) {
                $errors['zip'] = "ZipCode is required";
            } else {
                $zip_regex = "/^(?:\d{5,6})$/i"; 
                if (!preg_match($zip_regex, $zip)) {
                    $errors['zip'] = "ZipCode must be 5 or 6 numbers.";
                }
            }

            if (empty($amount)) {
                $errors['amount'] = "Amount is required";
            } else {
                if (filter_var($amount, FILTER_VALIDATE_FLOAT) === false) {
                    $errors['amount'] = "Amount must be in decimal.";
                }
            }

            if (empty($quantity)) {
                $errors['quantity'] = "Quantity is required";
            } else {
                if (filter_var($quantity, FILTER_VALIDATE_INT) === false) {
                    $errors['quantity'] = "Quantity must be in numbers.";
                }
            }

            if (empty($item)) {
                $errors['item'] = "Item is required";
            }else{
                $item_regex = "/^[a-zA-Z0-9]{3,10}$/";
                if (!preg_match ($item_regex, $item) ) { 
                    $errors['item'] = "Item must contain letters and numbers.";
                }
            }
        }
        return $errors;
    }
}
