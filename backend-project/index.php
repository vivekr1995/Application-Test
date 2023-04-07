<?php

require_once(__DIR__ . "/inc/config.php");

require APPROOT . "vendor/autoload.php"; // load packages installed from packages

//Avoiding CORS origin error
require_once APPROOT . 'header.php';

//Loading CsvOrderController controller in index
require APPROOT . "Controllers/API/CsvOrderController.php";

//Creating instance for the class
$CsvObj = new CsvOrderController(APPROOT . CSV_PATH);

/**
 * Function call based on GET, POST, PATCH and DELETE
 * this acts as a route helps in directing to contoller functions
 */
if (API_METHOD === 'GET') {
    /**
     * Get data from CSV file and response details
     */
    $csvData = $CsvObj->getCsvOrderData();

    /*
    creates new array with header as key
    */
    $header = array_shift($csvData); // get first row / header
    /*
    Iterate through array and combine 
    */
    $result = array_map(function ($line) use ($header) {
    $associativeArray = array_combine($header, $line); // combine both the array
    return $associativeArray;
    }, $csvData);

    return $CsvObj->getSuccessResponse(
        $result,
        true, 200
    );

} else if (API_METHOD === 'POST') {
    /**
     * Get input data from request
     */
    $data = (array) json_decode(file_get_contents("php://input"), true);

    /**
     * Validate imput data and gets response details
     */
    $errors = $CsvObj->validateInputData($data);
    if (!empty($errors)) {
        return $CsvObj->getErrorResponse($errors, false, 422);
    }

    /**
     * Add input data in CSV file
     */
    return $CsvObj->addCsvOrderData($data);
} else if (API_METHOD === 'PATCH') {
    /**
     * Get input data from request
     */
    $data = (array) json_decode(file_get_contents("php://input"), true);

    /**
     * Validate imput data and gets response details
     */
    $errors = $CsvObj->validateInputData($data);

    if (!empty($errors)) {
        return $CsvObj->getErrorResponse(
            $errors, false
            , 422);
    }

    /**
     * Update existing row in CSV file
     */
    return $CsvObj->editCsvOrderData($data);
} else if (API_METHOD === 'DELETE') {
    /**
     * Gets data id from request
     */
    $parts = explode("/", $_SERVER["REQUEST_URI"]);
    $id = $parts[3] ?? null;

    /**
     * Remove row from the CSV file
     */
    return $CsvObj->removeCsvOrderData(intval($id));
} else {
    /**
     * Default request case
     */
    http_response_code(405);
    header("Allow: GET, POST");
}
