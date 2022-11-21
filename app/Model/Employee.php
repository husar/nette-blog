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

    public function getAllDataFromEmployeeList(){
        return $this->xml->getAllEmployeesData();
    }

    public function getAllNames(){
        $xml=$this->xml->getAllEmployeesData();
        $names=array();
        if($xml){
            foreach($xml as $employeeData){
                array_push($names, $employeeData->Name);
            }
        }
        return $names;
    }

    public function getAllAges(){
        $xml=$this->xml->getAllEmployeesData();
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
}