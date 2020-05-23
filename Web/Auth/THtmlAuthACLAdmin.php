<?php

class THtmlAuthACLAdmin extends TModule {

	public function preRender() {
		$sess = TSession::getInstance();

		$groups = AuthGroup::findAll($sess->parameters->pdo);
		$this->groups->setBoundObject($groups);
		$this->groups->setBoundProperty('id|name');

		$roles = AuthRole::findAll($sess->parameters->pdo);
		$this->roles->setBoundObject($roles);
		$this->roles->setBoundProperty('id|name');

		$permissions = AuthPerm::findAll($sess->parameters->pdo);
		$this->permissions->setBoundObject($permissions);
		$this->permissions->setBoundProperty('id|name');

		parent::preRender();
	}

	public function onAclAdminPost($control, $params) {
		$this->divNotify->setInnerText($params['message']);
		$this->divNotify->addClass($params['class']);
	}

	public function onRefreshSessionACL($control, $params) {
		$this->authManager->refreshSessionACL($this->session->parameters->pdo);
		$this->divNotify->setInnerText('Session ACL has been refreshed');
		$this->divNotify->setClass('success');
	}

}

class AclAdminGroup extends THtmlDiv {

	private $formExisting;
	private $fsExisting;
	private $legend;
	private $checkbox;
	private $formNew;
	private $fsNew;
	private $input;
	private $btnAdd;
	private $btnRemove;
	private $plural;
	private $singular;

	public function __construct($params = array()) {

		$this->formExisting = zing::create('THtmlForm', array('class' => 'zing'));
		$this->fsExisting = $this->formExisting->children[] = zing::create('THtmlDiv', array('tag' => 'fieldset'));
		$this->legend = $this->fsExisting->children[] = zing::create('THtmlDiv', array('tag' => 'legend'));
		$this->checkbox = $this->fsExisting->children[] = zing::create('THtmlCheckboxGroup', array('onItemRender' => 'insertItemLink'));
		$this->btnRemove = $this->fsExisting->children[] = zing::create('THtmlButton', array('onClick' => 'onRemove'));

		$this->formNew = zing::create('THtmlForm', array('class' => 'zing'));
		$this->fsNew = $this->formNew->children[] = zing::create('THtmlDiv', array('tag' => 'fieldset'));
		$this->input = $this->fsNew->children[] = zing::create('THtmlInput');
		$this->btnAdd = $this->fsNew->children[] = zing::create('THtmlButton', array('onClick' => 'onAdd'));

		parent::__construct($params);

		$this->children[] = $this->formExisting;
		$this->children[] = $this->formNew;
		$this->addClass('AclAdmin');
	}

	public function auth() {
		$this->formNew->setAuthPerms('Auth' . $this->singular . 'Create');
		$this->btnRemove->setAuthPerms('Auth' . $this->singular . 'Delete');
		$this->checkbox->control->setDisabled( ! $this->authManager->hasPerm('Auth' . $this->singular . 'Delete'));
		parent::auth();
	}
	public function setBoundProperty($property) {
		$this->checkbox->setBoundProperty($property);
	}

	public function setLegend($legend) {
		$this->legend->setInnerText($legend);
	}

	public function setPlural($plural) {
		$this->plural = $plural;
	}

	public function setSingular($singular) {
		$this->singular = $singular;
	}

	public function render() {
		$this->btnAdd->setValue('Add ' . $this->singular);
		$this->btnRemove->setValue('Remove Selected ' . $this->plural);

		parent::render();
	}

	public function setId($id) {
		parent::setId($id);
		$this->formExisting->setId('AclAdmin-Existing-' . $id);
		$this->formNew->setId('AclAdmin-New-' . $id);
		$this->btnAdd->setId('AclAdmin-Add-' . $id);
		$this->btnRemove->setId('AclAdmin-Remove-' . $id);
		$this->checkbox->setid('AclAdmin-Checkbox-' . $id);
		$this->input->setid('AclAdmin-Input-' . $id);
	}

	public function setObjectClass($class) {
		$this->objectClass = $class;
	}

	public function onAdd($control, $params) {
		$input = trim($params['AclAdmin-Input-'.$this->getId()]);
		$sess = TSession::getInstance();
		$class = $this->objectClass;
		$object = new $class($sess->parameters->pdo);
		$object->name = $input;
        $errors = $object->validate();
		if ($errors === true) {
			$errors = array();
		}
		try {
			if (count($errors) < 1) {
				$object->update();
			}
		} catch (Exception $e) {
			if ($e->getCode() == 23000) {
				$errors[] = 'Unable to add ' . $this->singular . ' "' . $input . '", entry must be unique';
			} else {
				throw $e;
			}
		}

		if (count($errors) < 1) {
			$this->input->setValue('');
		}
		$this->fireEvent('onAclAdminPost', $this, array('class' => count($errors) ? 'error' : 'success', 'message' => count($errors) ? implode(', ',$errors) : $this->singular . ' "' . $input . '" added successfully'));
	}

	public function onRemove($control, $params) {
		$ids = $params['AclAdmin-Checkbox-'.$this->getId()];
		$sess = TSession::getInstance();
		$count = 0;
		foreach ((array) $ids as $id) {
			$object = call_user_func(array($this->objectClass,'findById'), $sess->parameters->pdo, $id);
			if ($object) {
				$object->destroy(true);
				$count++;
			}
		}

		$this->fireEvent('onAclAdminPost', $this, array('class' => $count ? 'success' : 'error', 'message' => $count ? $count . ' ' . $this->plural . ' removed successfully' : 'Unable to remove selected ' . $this->plural));
	}

	private $module;

	public function setModule($module) {
		$this->module = $module;
	}

	public function hasModule() {
		return isset($this->module);
	}

	public function setProperty($property) {
		$this->property = $property;
	}

	public function setParameterName($param) {
		$this->parameterName = $param;
	}

	public function insertItemLink($control, $params) {
		if ($this->hasModule() && $this->authManager->hasPerm('Auth' . $this->singular . 'Edit')) {
			$labelText = $control->label->getInnerText();
			$control->label->children->deleteAll();
			$control->label->setInnerText($labelText . ' ');
			$object = $control->getBoundObject();
			$property = $this->property;
			$value = $object->$property;
			$control->label->children[] = zing::create('THtmlLink', array('innerText' => '(edit)', 'module' => $this->module, $this->parameterName => $value));
		}
	}

}
