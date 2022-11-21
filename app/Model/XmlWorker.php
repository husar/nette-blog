<?php
namespace App\Model;

use Nette;
use App\Constants\Constants;

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

        $root = $dom->createElement(Constants::MAIN_XML_ELEMENT);

        $employeeNode = $dom->createElement(Constants::EMPLOYEE_XML_ELEMENT);
        $attr_employee_id = new \DOMAttr(Constants::EMPLOYEE_ID_XML_ELEMENT, 1);
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
        $root = $dom->getElementsByTagName(Constants::MAIN_XML_ELEMENT)->item(0);

        $employeeNode = $dom->createElement(Constants::EMPLOYEE_XML_ELEMENT);
        $attr_employee_id = new \DOMAttr(Constants::EMPLOYEE_ID_XML_ELEMENT,$this->getLastEmployeeId($path));
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
        return $xml->employee[$xml->count()-1][Constants::EMPLOYEE_ID_XML_ELEMENT]+1;
    }

    public function delete($id){
        $dom = new \DOMDocument();
        $dom->load(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME);
        $xpath = new \DomXPath($dom);
        $toDelete = $xpath->query('//*[@'.Constants::EMPLOYEE_ID_XML_ELEMENT.'="' . $id . '"]');
        foreach ($toDelete as $item) {
            $item->remove();
        }
        $xml = new \SimpleXMLElement($dom->saveXml());
        $xml->asXML(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME);
    }
    
    public function getDataForedit($id){
        $xml = simplexml_load_file(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME);
        return $xml->xpath('//*[@'.Constants::EMPLOYEE_ID_XML_ELEMENT.'="' . $id . '"]');
    }

    public function getAllEmployeesData(){
        $xml=NULL;
        if(file_exists(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME)){
            $xml=simplexml_load_file(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME);
        }
        return $xml;
    }
}