<?php

namespace Icinga\Module\Director\Forms;

use Icinga\Module\Director\Web\Form\DirectorObjectForm;

class IcingaHostVarForm extends DirectorObjectForm
{
    public function setup()
    {

        $this->addElement('select', 'host_id', array(
            'label'       => $this->translate('Host'),
            'description' => $this->translate('The name of the host'),
            'required'    => true
        ));

        $this->addElement('text', 'varname', array(
            'label' => $this->translate('Name'),
            'description' => $this->translate('host var name')
        ));

        $this->addElement('textarea', 'varvalue', array(
            'label' => $this->translate('Value'),
            'description' => $this->translate('host var value')
        ));

        $this->addElement('text', 'format', array(
            'label' => $this->translate('Format'),
            'description' => $this->translate('value format')
        ));

        $this->addElement('submit', $this->translate('Store'));
    }
}