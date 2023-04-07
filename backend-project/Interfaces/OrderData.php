<?php

namespace Interfaces;

interface OrderData
{
    /**
     * getCsvOrderData
     * opens csv file in read mode
     * reads all the rows of csv and push into an array ,
     * then returns the array
     * @return void
     */
    public function getCsvOrderData();

    /**
     * addCsvOrderData
     * read data from csv file , compare row-id with request id
     * push new array data exiting array and write into csv file
     * @param  array $data
     * @return void
     */
    public function addCsvOrderData(array $data);

    /**
     * editCsvOrderData
     * read order data from csv file , compare row-id with with request id
     * re-assign new array to selected row
     * @param  array $data
     * @return void
     */
    public function editCsvOrderData(array $data);

    /**
     * removeCsvOrderData
     * read data from csv file , compare row-id with request id
     * splice/remove the array from existing order data
     * @param  int $id
     * @return void
     */
    public function removeCsvOrderData(int $id);
     
    /**
     * writeCSVFile
     * Operns csv file in write mode 
     * writes array of data into csv file
     * @param  array $data
     * @return void
     */
    public function writeCSVFile(array $data);
    
    /**
     * validateInputData
     * checks if any field is mandatory or not
     * validate characters and numbers for each fields
     * @param  array $data
     * @return void
     */
    public function validateInputData(array $data);
}
