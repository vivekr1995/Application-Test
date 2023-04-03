<?php

namespace Interfaces;

interface CsvUserData
{
    /**
     * arrayToassociativeArray
     * combine header with associated data
     * creates new array with header as key
     * @param  array $data
     * @return void
     */
    public function arrayToassociativeArray(array $data);

    /**
     * readUserData
     * opens csv file in read mode
     * reads all the rows of csv and push into an array ,
     * then returns the array
     * @return void
     */
    public function readUserData();

    /**
     * addUserData
     * read data from csv file , compare row-id with request id
     * push new array data exiting array and write into csv file
     * @param  array $data
     * @return void
     */
    public function addUserData(array $data);

    /**
     * updateUserData
     * read order data from csv file , compare row-id with with request id
     * re-assign new array to selected row
     * @param  array $data
     * @return void
     */
    public function updateUserData(array $data);

    /**
     * deleteUserData
     * read data from csv file , compare row-id with request id
     * splice/remove the array from existing order data
     * @param  int $id
     * @return void
     */
    public function deleteUserData(int $id);
     
    /**
     * writeCSV
     * Operns csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return void
     */
    public function writeCSV(array $data);
    
    /**
     * validateData
     * checks if any field is mandatory or not
     * validate characters and numbers for each fields
     * @param  array $data
     * @return void
     */
    public function validateData(array $data);
}
