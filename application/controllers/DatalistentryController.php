<?php

use Icinga\Module\Director\Web\Controller\ActionController;

class Director_DatalistentryController extends ActionController
{
    public function addAction()
    {
        $this->forward('index', 'datalistentry', 'director');
    }

    public function editAction()
    {
        $this->forward('index', 'datalistentry', 'director', array('edit' => true));
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->getParam('edit')) {
            $edit = true;
        } else {
            $edit = false;
        }

        $listId = $this->params->get('list_id');
        $this->view->lastId = $listId;

        if($this->params->get('list_id') && $entryName = $this->params->get('entry_name')) {
            $edit = true;
        }

        if ($edit) {
            $this->view->title = $this->translate('Edit entry');
            $this->getTabs()->add('editentry', array(
                'url'       => 'director/datalistentry/edit' . '?list_id=' . $listId . '&entry_name=' . $entryName,
                'label'     => $this->view->title,
            ))->activate('editentry');
        } else {
            $this->view->title = $this->translate('Add entry');
            $this->getTabs()->add('addlistentry', array(
                'url'       => 'director/datalistentry/add' . '?list_id=' . $listId,
                'label'     => $this->view->title,
            ))->activate('addlistentry');
        }

        $form = $this->view->form = $this->loadForm('directorDatalistentry')
            ->setSuccessUrl('director/datalistentry' . '?list_id=' . $listId)
            ->setDb($this->db());

        if ($request->isPost()) {
            $listId = $request->getParam('list_id');
            $entryName = $request->getParam('entry_name');
        }

        if ($edit) {
            $form->loadObject(array('list_id' => $listId, 'entry_name' => $entryName));
            $form->getElement('entry_name')->setAttribs(array('readonly' => true));
        }

        $form->handleRequest();

        $this->render('object/form', null, true);
    }
}