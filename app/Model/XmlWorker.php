<?php
namespace App\Model;

use Nette;

final class XmlWorker
{
	use Nette\SmartObject;

    private $xmlChildNames=['Name','Sex','DateOfBirth'];
    private $data;

	public function createXmlFile($path, $data)
	{
        $this->data = [$data->name, $data->sex, $data->dateOfBirth];

		$dom = new \DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;

        $root = $dom->createElement('Employees');

        $employeeNode = $dom->createElement('employee');
        $attr_employee_id = new \DOMAttr('employee_id', 1);
        $employeeNode->setAttributeNode($attr_employee_id);

        $dataId=0;

        foreach($this->xmlChildNames as $childName){

            $child_node = $dom->createElement($childName, $this->data[$dataId]);
            $employeeNode->appendChild($child_node);

            $dataId++;
        }

        $root->appendChild($employeeNode);
        $dom->appendChild($root);

        $dom->save($path);
	}

    public function appendXmlFile($path, $data)
	{
        $this->data = [$data->name, $data->sex, $data->dateOfBirth];

		$dom = new \DOMDocument();
        $dom->load($path);
        $root = $dom->getElementsByTagName('Employees')->item(0);

        $employeeNode = $dom->createElement('employee');
        $attr_employee_id = new \DOMAttr('employee_id',$this->getLastEmployeeId($path));
        $employeeNode->setAttributeNode($attr_employee_id);

        $dataId=0;

        foreach($this->xmlChildNames as $childName){

            $child_node = $dom->createElement($childName, $this->data[$dataId]);
            $employeeNode->appendChild($child_node);

            $dataId++;
        }

        $root->appendChild($employeeNode);
        $dom->appendChild($root);

        $dom->save($path);
	}

    private function getLastEmployeeId($path){
        $xml=simplexml_load_file($path) or die("Error: Cannot create object");
        return $xml->employee[$xml->count()-1]['employee_id']+1;
    }

    public function delete($id){
        $dom = new \DOMDocument();
        $dom->load('Employees/employees_list.xml');
        $xpath = new \DomXPath($dom);
        $toDelete = $xpath->query('//*[@employee_id="' . $id . '"]');
        foreach ($toDelete as $item) {
            $item->remove();
        }
        $xml = new \SimpleXMLElement($dom->saveXml());
        $xml->asXML('Employees/employees_list.xml');
    }
    
    public function getDataForedit($id){
        $xml = simplexml_load_file('Employees/employees_list.xml');
        return $xml->xpath('//*[@employee_id="' . $id . '"]');
    }
}