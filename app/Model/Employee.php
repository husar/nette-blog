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

}