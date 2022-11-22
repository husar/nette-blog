<?php
namespace App\Model;

use Nette;
use App\Constants\Constants;
use Nette\Utils\FileSystem;

final class XmlWorker
{
	use Nette\SmartObject;

	public function createXmlFile($dbName, $tableName, $columnNames, $data, $mainElement=NULL, $firstElement=NULL, $idElement=NULL)
	{
        FileSystem::createDir($dbName);

		 $dom = new \DOMDocument();
         $dom->encoding = 'utf-8';
         $dom->xmlVersion = '1.0';
         $dom->formatOutput = true;

         $root = $dom->createElement($mainElement);

         $firstNode = $dom->createElement($firstElement);
         $attrId = new \DOMAttr($idElement, 1);
         $firstNode->setAttributeNode($attrId);

         $dataId=0;

         foreach($columnNames as $childName){

             $childNode = $dom->createElement($childName, $data[$dataId]);
             $firstNode->appendChild($childNode);

             $dataId++;
         }

         $root->appendChild($firstNode);
         $dom->appendChild($root);

         $dom->save($dbName.'/'.$tableName);
	}

    public function appendXmlFile($dbName, $tableName, $columnNames, $data, $mainElement=NULL, $firstElement=NULL, $idElement=NULL)
	{

		$dom = new \DOMDocument();
        $dom->load($dbName.'/'.$tableName);
        $root = $dom->getElementsByTagName($mainElement)->item(0);

        $firstNode = $dom->createElement($firstElement);
        $attrId = new \DOMAttr($idElement,$this->getLastId($dbName.'/'.$tableName, $idElement));
        $firstNode->setAttributeNode($attrId);

        $dataId=0;

        foreach($columnNames as $childName){

            $childNode = $dom->createElement($childName, $data[$dataId]);
            $firstNode->appendChild($childNode);

            $dataId++;
        }

        $root->appendChild($firstNode);
        $dom->appendChild($root);

        $dom->save($dbName.'/'.$tableName);
	}

    private function getLastId($path, $idElement){
        $xml=simplexml_load_file($path) or die("Error: Cannot create object");
        return $xml->employee[$xml->count()-1][$idElement]+1;
    }

    public function delete($dbName, $tableName, $columnName, $id){
        $dom = new \DOMDocument();
        $dom->load($dbName.'/'.$tableName);
        $xpath = new \DomXPath($dom);
        $toDelete = $xpath->query('//*[@'.$columnName.'="' . $id . '"]');
        foreach ($toDelete as $item) {
            $item->remove();
        }
        $xml = new \SimpleXMLElement($dom->saveXml());
        $xml->asXML($dbName.'/'.$tableName);
    }
    
    public function getOneRecordData($dbName, $tableName, $idElement, $id){
        $xml = simplexml_load_file($dbName.'/'.$tableName);
        return $xml->xpath('//*[@'.$idElement.'="' . $id . '"]');
    }

    public function getAllData($dbName, $tableName){
        $xml=NULL;
        if(file_exists($dbName.'/'.$tableName)){
            $xml=simplexml_load_file($dbName.'/'.$tableName);
        }
        return $xml;
    }
}