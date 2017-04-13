<?php

require_once('Mail.php');

class THtmlFeedbackForm extends THtmlForm {

	private $fsFields;
	private $fsButtons;
	private $divContainer;
	
	public function init() {
		
		$this->addClass('zing');
		$this->children[] = zing::create('THtmlStyle', array('href' => '/Zing/Assets/Styles/forms.css', 'type' => 'text/css', 'rel' => 'stylesheet'));
		$this->children[] = zing::create('THtmlDiv', array('id' => 'divNotify'));
		$this->divContainer = $this->children[] = zing::create('THtmlDiv');
		
		$this->fsFields = $this->divContainer->children[] = zing::create('THtmlDiv', array('tag' => 'fieldset'));
		if ($this->hasLegend()) {
			$this->fsFields->children[] = zing::create('THtmlDiv', array('tag' => 'legend', 'innerText' => $this->getLegend()));
		}
		
		foreach ($this->fields as $index => $field) {
			if (!isset($field['id'])) {
				$field['id'] = 'feedback_' . $index;
			}
			switch ($field['type']) {
			case 'text':
				$this->fsFields->children[] = zing::create('THtmlInputCombo', $field);
				break;
			case 'textarea':
				unset($field['type']);
				$this->fsFields->children[] = zing::create('THtmlTextareaCombo', $field);
				break;
			case 'select':
				unset($field['type']);
				$this->fsFields->children[] = zing::create('THtmlSelectCombo', $field);
				break;
			}
		}
		
		$this->fsButtons = $this->divContainer->children[] = zing::create('THtmlDiv', array('tag' => 'fieldset'));
		$this->fsButtons->children[] = zing::create('THtmlButton', array('id' => 'btnSubmitFeedback', 'onClick' => 'sendEmail', 'value' => $this->hasButtonText() ? $this->getButtonText() : 'Submit'));
		parent::init();
	}

	private	$email;
	
	public function setEmail($email) {
		$this->email = $email;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function hasEmail() {
		return isset($this->email);
	}

	private $legend;
	
	public function setLegend($legend) {
		$this->legend = $legend;
	}
	
	public function getLegend() {
		return $this->legend;
	}
	
	public function hasLegend() {
		return isset($this->legend);
	}

	private $buttonText;
	
	public function setButtonText($buttonText) {
		$this->buttonText = $buttonText;
	}
	
	public function getButtonText() {
		return $this->buttonText;
	}
	
	public function hasButtonText() {
		return isset($this->buttonText);
	}

	private $fields = array();
	
	public function __call($name, $args) {
		if (strncasecmp($name, 'setfield',8) == 0) {
			$field = (int)substr($name, 8);
			foreach (explode(',', $args[0]) as $index => $arg) {
				list($name, $value) = explode('=',$arg);
				$this->fields[$field][$name] = $value;
			}
		}
	}

	public function sendEmail($control, $params) {
		$sess = TSession::getInstance();
		
		$errors = 0;
		foreach ($this->fields as $index => $field) {
			$var = 'feedback_' . $index;
			if ($this->$var->getRequired() && empty($sess->app->post[$var])) {
				$this->$var->setError('You must enter a value');
				$errors++;
			}
			$message .= $field['label'] . ': ' . strip_tags($sess->app->post[$var]) . "\r\n";
		}
		
		if ($errors) {
			$this->divNotify->setClass('error');
			$this->divNotify->setInnerText('Your submission could not be sent.  There were errors in your submission');
			return;
		}
		
		$mail = Mail::factory($sess->parameters['feedback.mail.type'], $sess->parameters['feedback.mail.parameters']);
		
		if (PEAR::isError($mail)) {
			$this->divNotify->setClass('error');
			$this->divNotify->setInnerText('Your submission could not be sent.  The specific error message was \'' . $mail->getMessage() . '\'.');
		}
		
		$res = $mail->send($this->getEmail(),
						   array(	'From' => $sess->parameters['feedback.mail.parameters']['username'],
									'To' => $this->getEmail(),
									'Subject' => 'Feedback received via ' . $sess->app->server->http_host),
						   $message);
		
		if (PEAR::isError($res)) {
			$this->divNotify->setClass('error');
			$this->divNotify->setInnerText('Your submission could not be sent.  Please check your input and try again.  The specific error message was \'' . $res->getMessage() . '\'.');
		} else {
			$this->divContainer->setVisible(false);
			$this->divNotify->setClass('success');
			$this->divNotify->setInnerText('Your submission has been successfully sent, and we will respond as soon as possible');
		}
	}
}

?>