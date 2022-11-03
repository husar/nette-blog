<?php
namespace App\Model;

use Nette;

final class Employee
{
	use Nette\SmartObject;

    private $xml;

    public function __construct()
	{
		if(file_exists('Employees/employees_list.xml')){
            $this->xml = simplexml_load_file('Employees/employees_list.xml');
        }
	}

    public static function getAllDataFromEmployeeList(){
        $xml=NULL;
        if(file_exists('Employees/employees_list.xml')){
            $xml=simplexml_load_file('Employees/employees_list.xml');
        }
        return $xml;
    }

    public static function getAllNames(){
        $xml=NULL;
        $names=array();
        if(file_exists('Employees/employees_list.xml')){
            $xml=simplexml_load_file('Employees/employees_list.xml');
        }
        if($xml){
            foreach($xml as $employeeData){
                array_push($names, $employeeData->Name);
            }
        }
        return $names;
    }

    public static function getAllAges(){
        $xml=NULL;
        $ages=array();
        if(file_exists('Employees/employees_list.xml')){
            $xml=simplexml_load_file('Employees/employees_list.xml');
        }
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