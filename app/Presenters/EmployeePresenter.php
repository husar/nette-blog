<?php

namespace App\Presenters;

use Nette;

use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

use App\Model\XmlWorker;
use App\Model\Employee;

final class EmployeePresenter extends Nette\Application\UI\Presenter
{
    private XmlWorker $xmlWorker;
    private Employee $employee;
    private $folderName = 'Employees';
    private $xmlName = 'employees_list.xml';

    public function __construct(XmlWorker $xmlWorker, Employee $employee)
	{
		$this->xmlWorker    = $xmlWorker;
        $this->employee     = $employee;
	}

	protected function createComponentAddPersonForm(): Form
    {


        $form = new Form; // means Nette\Application\UI\Form

        $form->setHtmlAttribute('class','form-horizontal');
        
        $form->addContainer('class','form-body');

        $form->addText('name', 'Meno:')
            ->setRequired()->setHtmlAttribute('class','form-control')->addRule($form::MAX_LENGTH, 'Meno môže mať maximálne 30 znakov', 30);

        $form->addRadioList('sex','Pohlavie', [
            "man" => "Muž",
            "woman"  => "Žena",
            ])->setRequired();

        $form->addText('dateOfBirth', 'Dátum narodenia')
            ->setType('date')->setRequired()->setHtmlAttribute('class','form-control');

        $form->addSubmit('send', 'Pridať zamestnanca')->setHtmlAttribute('class','btn blue');

        $form->onSuccess[] = [$this, 'addPersonFormSucceeded'];

        return $form;
    }

    protected function createComponentEditPersonForm(): Form
    {


        $form = new Form; // means Nette\Application\UI\Form

        $form->setHtmlAttribute('class','form-horizontal');
        
        $form->addContainer('class','form-body');

        $form->addText('name', 'Meno:')
            ->setRequired()->setHtmlAttribute('class','form-control')->addRule($form::MAX_LENGTH, 'Meno môže mať maximálne 30 znakov', 30);

        $form->addRadioList('sex','Pohlavie', [
            "man" => "Muž",
            "woman"  => "Žena",
            ])->setRequired();

        $form->addText('dateOfBirth', 'Dátum narodenia')
            ->setType('date')->setRequired()->setHtmlAttribute('class','form-control');

        $form->addSubmit('send', 'Upraviť záznam')->setHtmlAttribute('class','btn blue');

        $form->onSuccess[] = [$this, 'addPersonFormSucceeded'];

        return $form;
    }

    public function addPersonFormSucceeded(\stdClass $data): void
    {

        FileSystem::createDir($this->folderName);

        if(!file_exists($this->folderName.'/'.$this->xmlName)){

            $this->xmlWorker->createXmlFile($this->folderName.'/'.$this->xmlName, $data);

        }else{
            $this->xmlWorker->appendXmlFile($this->folderName.'/'.$this->xmlName, $data);
        }

        $this->flashMessage('Zamestnanec bol úspešne pridaný', 'success');
        $this->redirect('this');
    }

    public function renderShowAllEmployees()
	{
		$this->template->employees = $this->employee->getAllDataFromEmployeeList();
	}
    public function actionDelete($id)
	{
		$this->xmlWorker->delete($id);

        $this->flashMessage('Zamestnanec bol úspešne odstránený', 'success');
        $this->redirect('Employee:showAllEmployees');
        
	}
    public function actionEdit(int $id) {
        //$recipe = recipe_obtaining_magic($id);
        $employee = $this->xmlWorker->getDataForEdit($id);
        $editPersonForm = $this['editPersonForm'];
        $editPersonForm->setDefaults([
            'name' => $employee[0]->Name,
            'sex' => $employee[0]->Sex,
            'dateOfBirth' => $employee[0]->DateOfBirth,
        ]);
        $editPersonForm->onSuccess[] = function($form, $data) use ($id) {
            recipe_updating_magic($id, $data);
        };
    }

    public function renderEdit($id)
	{
		$this->template->employee = $this->xmlWorker->getDataForEdit($id);
	}
}