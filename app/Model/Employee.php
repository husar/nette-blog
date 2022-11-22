<?php
namespace App\Model;

use Nette;
use App\Constants\Constants;
use App\Model\XmlWorker;

final class Employee
{
	use Nette\SmartObject;

    public function __construct(private XmlWorker $xml) {
    }

    public function addEmployee($data){
        $columnNames=['Name','Sex','DateOfBirth'];
        $data=[$data->name, $data->sex, $data->dateOfBirth];

        if(!file_exists(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME)){

            $this->xml->createXmlFile(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME, $columnNames, $data, 'Employees', 'employee', 'employee_id');
    
        }else{

            $this->xml->appendXmlFile(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME, $columnNames, $data, 'Employees', 'employee', 'employee_id');

        }
    }

    public function deleteEmployee($id){

        $this->xml->delete(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME, 'employee_id', $id);

    }

    public function getAllDataFromEmployeeList(){

        return $this->xml->getAllData(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME);

    }

    public function getAllEmployeeNames(){
        $xml=$this->xml->getAllData(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME);
        $names=array();
        if($xml){
            foreach($xml as $employeeData){
                array_push($names, $employeeData->Name);
            }
        }
        return $names;
    }

    public function getAllEmployeesAges(){
        $xml=$this->xml->getAllData(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME);
        $ages=array();
        if($xml){
            foreach($xml as $employeeData){
                $birthDate=new \DateTime($employeeData->DateOfBirth);
                $now = new \DateTime();
                array_push($ages, $now->diff($birthDate)->y);
            }
        }
        return $ages;
    }

    public function getSexInSlovak($sex){
        return $sex == 'man'?'Muž':'Žena';
    }
    public function getDMYDateFormat($date){
        return date("d.m.Y", strtotime($date));
    }

    public function getEmployeeData($id){
        return $this->xml->getOneRecordData(Constants::XML_FOLDER_NAME, Constants::XML_FILE_NAME, 'employee_id', $id);
    }
}