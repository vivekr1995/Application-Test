<?php
//Avoiding CORS origin error
require_once 'header.php';

//Interface created for checking file exists
interface User {
	public function checkFile();
}

class Application implements User {
	//Declaring public properties
	public $filename = 'data.csv';
	public $defaultData = [
		['id', 'name', 'state', 'zip', 'amount', 'qty', 'item'],
	];

	//Function to get data from CSV file
	public function getData() {

		//Check file exists and creating with headers
		if(!$this->checkFile()) {
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'File created with headers';
			$res['code'] = 201;
			return $res;
		}
		
		//Reading data from CSV
		$f = fopen($this->filename, 'r');
		if ($f === FALSE) {
			//Read failed
			$res['users'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'Cannot open the file ' . $this->filename;
			$res['code'] = 404;
		} else {
			//Read successfully
			$i = 0;
			$res['users'] = [];
			//Getting data from file
			while (($row = fgetcsv($f)) !== FALSE) {
				if($i != 0) {
					$arr = array(
						'id' => $row[0],
						'name' => $row[1],
						'state' => $row[2],
						'zip' => $row[3],
						'amount' => $row[4],
						'qty' => $row[5],
						'item' => $row[6]
					);

					$res['users'][] = $arr;
				}
				$i++;
			}

			$res['status'] = TRUE;
			$res['message'] = '';
			$res['code'] = 200;
		}
		
		fclose($f);
		return $res;
	}

	public function addData() {

		//Check file exists and creating with headers
		if(!$this->checkFile()) {
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'File created with headers';
			$res['code'] = 201;
			return $res;
		}

		//Getting new data
		$source_data = file_get_contents('php://input');
		$data = json_decode($source_data);

		if(!empty($data)) {
			//Getting original data
			$i = 0;
			$last_id = 0;
			$newdata = [];

			//Reading file and getting data
			$handle = fopen($this->filename, 'r');
			while (($val = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$newdata[$i][] = $val[0];          
				$newdata[$i][] = $val[1];    
				$newdata[$i][] = $val[2];      
				$newdata[$i][] = $val[3];    
				$newdata[$i][] = $val[4];    
				$newdata[$i][] = $val[5];
				$newdata[$i][] = $val[6];
				$i++;

				$last_id = (int) $val[0] + 1;
			}

			//Assigning id for new data and adding with the existing data
			$data->id = $last_id;
			foreach($data as $key => $val) {
				if($key != 'isEdit' && $key != 'isSelected') {
					$newdata[$i][] = $val;
				}
			}

			//Writing file
			$res = $this->writeCSV($newdata);
		} else {
			//Data not found
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'Entered data not found';
			$res['code'] = 204;
		}
		return $res;
	}

	public function updateData() {

		//Check file exists and creating with headers
		if(!$this->checkFile()) {
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'File created with headers';
			$res['code'] = 201;
			return $res;
		}

		//Getting ID
		$url = $_SERVER['REQUEST_URI'];
		$path = parse_url($url, PHP_URL_PATH);
		$pathFragments = explode('/', $path);
		$row_id = end($pathFragments);

		//Getting updated data
		$source_data = file_get_contents('php://input');
		$data = json_decode($source_data);

		if(!empty($data)) {
			$update_data = array();
			foreach($data as $key => $val) {
				if($key != 'isEdit') {
					$update_data[] = $val;
				}
			}

			//Getting original data
			$i = 0;
			$newdata = [];

			//Reading file and getting data
			$handle = fopen($this->filename, 'r');
			while (($val = fgetcsv($handle, 1000, ",")) !== FALSE) { 
				//Changing updated data     
				if ($i == $row_id) {
					$newdata[$i][] = $update_data[0];          
					$newdata[$i][] = $update_data[1];
					$newdata[$i][] = $update_data[2];
					$newdata[$i][] = $update_data[3];
					$newdata[$i][] = $update_data[4];
					$newdata[$i][] = $update_data[5];
					$newdata[$i][] = $update_data[6];
					$i++;
					continue;
				}  
				$newdata[$i][] = $val[0];          
				$newdata[$i][] = $val[1];    
				$newdata[$i][] = $val[2];      
				$newdata[$i][] = $val[3];    
				$newdata[$i][] = $val[4];    
				$newdata[$i][] = $val[5];
				$newdata[$i][] = $val[6];
				$i++;    
			}

			//Writing file
			try {
				$res = $this->writeCSV($newdata, 'edit');
			}
			catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();
			  }
		} else {
			//Data not found
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'Entered data not found';
			$res['code'] = 404;
		}
		return $res;
	}

	public function deleteData() {

		//Check file exists and creating with headers
		if(!$this->checkFile()) {
			$res['data'] = [];
			$res['status'] = FALSE;
			$res['message'] = 'File created with headers';
			$res['code'] = 201;
			return $res;
		}

		//Getting ID
		$url = $_SERVER['REQUEST_URI'];
		$path = parse_url($url, PHP_URL_PATH);
		$pathFragments = explode('/', $path);
		$row_id = end($pathFragments);

		//Getting original data
		$i = 0;
		$last_id = 0;
		$newdata = [];

		//Reading file and getting data
		$handle = fopen($this->filename, 'r');
		while (($val = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($val[0] != $row_id) {
				$newdata[$i][] = $val[0];          
				$newdata[$i][] = $val[1];    
				$newdata[$i][] = $val[2];      
				$newdata[$i][] = $val[3];    
				$newdata[$i][] = $val[4];    
				$newdata[$i][] = $val[5];
				$newdata[$i][] = $val[6];
				$i++;
			}
		}

		//Writing file
		$res = $this->writeCSV($newdata);
		return $res;
	}

	public function writeCSV($data, $type='') {
		//Writing file
		$f = fopen($this->filename, 'w');
		if ($f === false) {
			//Getting error when writing
			$res['data'] = $data;
			$res['status'] = FALSE;
			$res['message'] = 'Error opening the file ' . $this->filename;
			$res['code'] = 404;

			if($type == 'edit') {
				throw new Exception("Error opening the file");
			} 
		} else {
			//Writing data in blank file
			foreach ($data as $row) {
				fputcsv($f, $row);
			}
			fclose($f);

			$res['data'] = $data;
			$res['status'] = TRUE;
			$res['message'] = '';
			$res['code'] = 200;
		}
		
		return $res;
	}

	public function checkFile() {
		//Checking file exists
		if(!file_exists("data.csv")) {
			//Creating new file with only headers
			$f = fopen($this->filename, 'w');
			foreach ($this->defaultData as $row) {
				fputcsv($f, $row);
			}
			fclose($f);
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

//Creating instance for the class
$obj = new Application();

//Calling respective functions for each method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$result = $obj->getData();
	$result['method'] = $_SERVER['REQUEST_METHOD'];
	echo json_encode($result);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$result = $obj->addData();
	$result['method'] = $_SERVER['REQUEST_METHOD'];
	echo json_encode($result);
} else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
	$result = $obj->updateData();
	$result['method'] = $_SERVER['REQUEST_METHOD'];
	echo json_encode($result);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	$result = $obj->deleteData();
	$result['method'] = $_SERVER['REQUEST_METHOD'];
	echo json_encode($result);
}


?>