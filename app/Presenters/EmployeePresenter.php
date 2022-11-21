<?php

namespace App\Presenters;

use Nette;

use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

use App\Model\XmlWorker;
use App\Model\Employee;
use App\Constants\Constants;

final class EmployeePresenter extends Nette\Application\UI\Presenter
{
    private XmlWorker $xmlWorker;
    private Employee $employee;

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
        $form->addHidden('id','id');

        $form->onSuccess[] = [$this, 'editPersonFormSucceeded'];

        return $form;
    }

    public function addPersonFormSucceeded(\stdClass $data): void
    {

        FileSystem::createDir(Constants::XML_FOLDER_NAME);

        if(!file_exists(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME)){

            $this->xmlWorker->createXmlFile(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME, $data);

        }else{
            $this->xmlWorker->appendXmlFile(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME, $data);
        }

        $this->flashMessage('Zamestnanec bol úspešne pridaný', 'success');
        $this->redirect('this');
    }

    public function editPersonFormSucceeded(\stdClass $data): void
    {

        $this->xmlWorker->delete($data->id);
        $this->xmlWorker->appendXmlFile(Constants::XML_FOLDER_NAME.'/'.Constants::XML_FILE_NAME, $data);

        $this->flashMessage('Záznam bol upravený', 'success');
        $this->redirect('Employee:showAllEmployees');
    }

    public function renderShowAllEmployees()
	{
		$this->template->employees = $this->employee->getAllDataFromEmployeeList();
        $this->template->addFunction('getSexInSlovak', function ($sex) {
            return $this->employee->getSexInSlovak($sex);
        });
        $this->template->addFunction('getDMYDateFormat', function ($date) {
            return $this->employee->getDMYDateFormat($date);
        });
	}

    public function renderageGraph()
	{
		$this->template->employeesNames = $this->employee->getAllNames();
        $this->template->employeesAges  = $this->employee->getAllAges();

	}

    public function actionDelete($id)
	{
		$this->xmlWorker->delete($id);

        $this->flashMessage('Zamestnanec bol úspešne odstránený', 'success');
        $this->redirect('Employee:showAllEmployees');
        
	}
    public function actionEdit(int $id) {
        $employee = $this->xmlWorker->getDataForEdit($id);
        $editPersonForm = $this['editPersonForm'];
        $editPersonForm->setDefaults([
            'name' => $employee[0]->Name,
            'sex' => $employee[0]->Sex,
            'dateOfBirth' => $employee[0]->DateOfBirth,
            'id'    => $id,
        ]);
    }

    public function renderEdit($id)
	{
		$this->template->employee = $this->xmlWorker->getDataForEdit($id);
	}
}