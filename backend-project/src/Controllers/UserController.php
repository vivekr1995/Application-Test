<?php

//importing required files
require_once(APPROOT . 'interfaces/CsvUserData.php');
require_once(APPROOT . 'Traits/ApiResponse.php');

//Interfaces and Traits
use \interfaces\CsvUserData;
use \Traits\ApiResponse;

/**
 *  load Mongolog logger framework
 *  
 */
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class UserController implements CsvUserData {

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
         * Create object for StreamHandler to handle creating and writing logs into file
         */
        $this->logger = new Logger("info");
        $stream_handler = new StreamHandler(APPROOT . 'logs/app.log', Logger::DEBUG);
        $this->logger->pushHandler($stream_handler);
    }

	/**
     * processRequest based on GET, POST, PATCH and DELETE
     * this function acts as a route helps in directing to 
     * controller function based on actions.
     * @param  string $method
     * @param  string $action
     * @param  int $id
     * @return void
     */
    public function processRequest(string $method, ?int $id)
    {
        switch ($method) {
            case "GET":
				return $this->successResponse(
                    $this->arrayToassociativeArray($this->readUserData($this->csvFilePath)),
                     true, 200
                );
                break;
                
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->validateData($data);
                if (!empty($errors)) {
                    return $this->errorResponse($errors, false, 422);
                }

                return $this->addUserData($data);
                break;

            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->validateData($data);

                if (!empty($errors)) {
                    return $this->errorResponse(
                        $errors, false
                     , 422);
                }

                return $this->updateUserData($data);

                break;
            case 'DELETE':
                return $this->deleteUserData($id);
                break;
            
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

	/**
     * arrayToassociativeArray
     * combine header with associated data
     * creates new array with header as key
     * @param  array $data
     * @return array
     */
    public function arrayToassociativeArray(array $data): array
    {
        $header = array_shift($data); // get first row / header
        /*
        Iterate through array and combine 
        */
        $result = array_map(function ($line) use ($header) {
        $associativeArray = array_combine($header, $line); // combine both the array
        return $associativeArray;
        }, $data);
    
        return $result;
    }

	/**
	 * readUserData
	 * opens csv file in read mode
	 * reads all the rows of csv and push into an array ,
	 * then returns the array
	 * @param  string $path
	 * @return array
	 */
	public function readUserData(string $csvFilePath): array
	{
		try{
			$csv = [];
			// Open for reading only
			if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
				while (($data = fgetcsv($handle)) !== FALSE) {
					$csv[] = $data; // push each row into an array
				}
				fclose($handle);// after read is complete close the file
			}else{
				throw new Exception("Failed to open file in write mode");
			}
			return $csv; 
		}catch(Exception $e){
	
			$response = $e->getMessage();
			$this->logger->error($response);
	
			return $this->errorResponse(
				$response, false
			 , 304);  
		}
	}

	/**
     * addUserData
     * read data from csv file , compare row-id with request id
     * push new array data along with existing array and write into csv file
     * @param  array $data
     * @return json
     */
	public function addUserData(array $data) {
		try{

            if(empty($data)) throw new Exception("Order can not be empty"); // check if empty array

            //get the count of records in csv file including header
            $getAllData = $this->readUserData($this->csvFilePath);
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
    
            $result = $this->writeCSV($getAllData);

            if($result){
                return $this->successResponse($result, true, 200);
            }else{
                throw new Exception("Failed to add data"); 
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);    
        }
	}

	/**
     * updateUserData
     * read data from csv file , compare row-id with request id
     * re-write new array to selected row
     * @param  array $data
     * @return json
     */
	public function updateUserData(array $data) {
		try{
			//Getting ID
            $id = array_key_exists("id",$data) ? $data['id'] : 0 ;
            $getAllData = $this->readUserData($this->csvFilePath);
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
    
            $result = $this->writeCSV($getAllData);

            if($result){

                return $this->successResponse(
                    $result,
                     true, 200
                );
            }else{
                throw new Exception("Failed to update order data");
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);
 
            return $this->errorResponse(
                 $response, false
              , 304);   
        }
	}

	/**
     * deleteUserData
     * read data from csv file , compare row-id with request id
     * splice/remove the array from existing order data
     * @param  int $id
     * @return json
     */
    public function deleteUserData(int $id) {

		try{
            
            if ($id == 0) throw new Exception("Order delete Id should not be 0.");

            $getAllData = $this->readUserData($this->csvFilePath);
    
            $num = count($getAllData);

            // Avoiding header data
            for($i = 1; $i < $num; $i++){
    
                if (is_numeric($getAllData[$i][0]) && $getAllData[$i][0] == $id) {
                    array_splice($getAllData, $i, 1); // splice current index from array
					break;
                }
    
            }
    
            $result = $this->writeCSV($getAllData);

            if($result){
                return $this->successResponse($result, true, 200);
            }else{
                throw new Exception("Failed to delete order data");
            }

        }catch(Exception $e){

            $response = $e->getMessage();
            $this->logger->error($response);

            return $this->errorResponse(
                $response, false
             , 304);   
        }
	}

	/**
     * writeCSV
     * Opens csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return bool
     */
    public function writeCSV(array $data): bool
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

            return $this->errorResponse(
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

            return $this->errorResponse(
                $response, false
             , 304);
        }
    }

	/**
     * validateData
     * checks if any field is mandatory or not
     * validate characters and numbers for each fields
     * @param  array $data
     * @return array
     */
    public function validateData(array $data): array
    {
        $errors = []; // empty error array

        // destructuring array for fields
        ['id' => $id, 'name' => $name, 'state' => $state, 'zip' => $zip,
         'amount' => $amount, 'qty' => $qty, 'item' => $item] = $data;

        if (!empty($data)) {

            if (array_key_exists("id", $data)) {

                if (filter_var($id, FILTER_VALIDATE_INT) === false) {
                    $errors['id'] = "Id must be an integer";
                }
            }

            if (empty($name)) {
                $errors['name'] = "Name is required";
            }else{
                $name_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($name_regex, $name) ) { 
                    $errors['name'] = "Name must be in Letters";
                }
            }

            if (empty($state)) {
                    $errors['state'] = "State is required";
            }else{
                $state_regex = "/^[a-zA-Z ]+$/i";
                if (!preg_match ($state_regex, $name) ) { 
                    $errors['state'] = "State must be in Letters";
                }
            }

            if (empty($zip)) {
                $errors['zip'] = "ZipCode is required";
            } else {
                $zip_regex = "/^(?:\d{5,6})$/i"; 
                if (!preg_match($zip_regex, $zip)) {
                    $errors['zip'] = "ZipCode must be numbers and length must be 5 or 6 ";
                }
            }

            if (empty($amount)) {
                $errors['amount'] = "Amount is required";
            } else {
                if (filter_var($amount, FILTER_VALIDATE_FLOAT) === false) {
                    $errors['amount'] = "Amount must be in decimal format e.g - 10.00";
                }
            }

            if (empty($qty)) {
                $errors['qty'] = "Quantity is required";
            } else {
                if (filter_var($qty, FILTER_VALIDATE_INT) === false) {
                    $errors['qty'] = "Quantity must be in numbers";
                }
            }

            if (empty($item)) {
                $errors['item'] = "Item is required";
            }else{
                $item_regex = "/^[a-zA-Z0-9]{3,10}$/";
                if (!preg_match ($item_regex, $item) ) { 
                    $errors['item'] = "Item must contain letters and numbers. Minimum length must be 2";
                }
            }
        }
        return $errors;
    }
}

?>