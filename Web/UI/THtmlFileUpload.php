<?php

class THtmlFileUpload extends THtmlInput {

	public function __construct($params = array()) {
		$this->maxFileSize = zing::create('THtmlInput', array('type' => 'hidden', 'name' => 'MAX_FILE_SIZE', 'value' => 1024*1024*1));
		parent::__construct($params);
	}

	/**
	 * Value is specified as bytes literal, or with magnitude suffix.
	 * K = kilobytes
	 * M = megabytes
	 */	
	public function setMaxFileSize($max) {
		if (preg_match('/(?P<value>\d+)(?P<magnitude>[KM]?)/i', $max, $matches)) {
			$mags = array('K' => 1024, 'M' => 1024 * 1024);
			$this->maxFileSize->setValue($matches['value'] * $mags[strtoupper($matches['magnitude'])]);
		} else {
			throw new Exception('Invalid maxfilesize specification \''.$max.'\' for fileupload control');
		}
	}
	
	public function getMaxFileSize() {
		return $this->maxFileSize->getValue();
	}

	public function init() {
		parent::init();
		
		$this->setType('file');
	}
	
	public static $UPLOAD_ERR_MSGS = array(
		UPLOAD_ERR_OK => 'The file upload was successful',
		UPLOAD_ERR_INI_SIZE => 'The file exceeds the servers maximum permitted size',
		UPLOAD_ERR_FORM_SIZE => 'The file exceeds the form\'s maximum permitted size',
		UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded',
		UPLOAD_ERR_NO_FILE => 'No file was uploaded',
		UPLOAD_ERR_NO_TMP_DIR => 'There is no temporary upload directory specified on the server',
		UPLOAD_ERR_CANT_WRITE => 'Unable to write the upload file to the temporary server directory',
		UPLOAD_ERR_EXTENSION => 'The file cannot be accepted as it has the wrong file extension'
		);
		
	public function post() {
	
		if ($this->hasId()) {
			$sess = TSession::getInstance();
			$id = $this->getId();

			$file = $sess->app->files->$id;

			$this->file_name = $file['name'];
			$this->file_size = $file['size'];
			$this->file_type = $file['type'];
			$this->file_temp_path = $file['tmp_name'];
			$this->file_error = $file['error'];
			if (array_key_exists($this->file_error, self::$UPLOAD_ERR_MSGS)) {
				$this->file_error_msg = self::$UPLOAD_ERR_MSGS[$this->file_error];			
			} else {
				$this->file_error_msg = 'An unspecified error occured';
			}
		} else {
			parent::post();
		}
	}
	
	public function render() {
		$this->maxFileSize->doStatesUntil('preRender');
		$this->maxFileSize->render();
		$this->maxFileSize->setVisible(false);
		parent::render();
	}

}

?>