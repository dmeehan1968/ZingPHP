<?php

class THtmlFileList extends TModule {

	public function auth() {
		$this->setAuthPerms('CmsFileList');
		parent::auth();
	}

	public function preRender() {

		$sess = TSession::getInstance();
		$page = $sess->app->request->page;
		if ($page < 1) {
			$page = 1;
		}
		$path = $sess->parameters['cms.files.rootpath'] . '/' . $sess->app->request->dir;
		try {
			$dir = new TFileSystemIterator($path, '/^(?!\.).*' . (!empty($sess->app->request->dir) ? '|\.\.' : '') . '/', '/' . $sess->app->request->dir, $this->authManager);
		} catch (Exception $e) {
			$this->authManager->login($sess->app->server->request_uri, $e->getCode());

		}

		if (count($dir) < 1) {
			$this->children->deleteAll();
			$content = $this->children[] = zing::create('StaticOrNotFound');
			$content->doStatesUntil('loadComplete');
		} else {

			$this->setBoundObject($dir);

			$paths = explode('/', $this->resolvePath($path, $sess->parameters['cms.files.rootpath']));

			$this->pathTitle->setInnerText('Path: ');

			$this->pathTitle->children[] = zing::create('THtmlLink', array('module' => 'Zing/Web/CMS/THtmlFileList', 'innerText' => ' / '));

			if (empty($paths[0])) {
				array_pop($paths);
			}

			foreach($paths as $index => $path) {
				$stub = array_slice($paths, 0, $index+1);
				$stub = implode('/', $stub) . '/';
				if ($this->pathTitle->children->count() > 2) {
					$this->pathTitle->children[] = zing::create('THtmlInnerText', array('value' => ' / '));
				}
				$this->pathTitle->children[] = zing::create('THtmlLink', array('module' => 'Zing/Web/CMS/THtmlFileList', 'dir' => $stub , 'innerText' => $path));

			}

			$this->frmCmsFileDelete->setAction($this->frmCmsFileDelete->getAction() . '?page=' . $sess->app->request->page);
		}

		parent::preRender();
	}

	public function getIcon($file) {
		$iconPath = $file->getIconPath();
		return zing::create('THtmlImage', array('src' => $iconPath));
	}

	public function insertCheckbox($control, $params) {
		$file = $control->getBoundObject();
		$control->children->deleteAll();
		if ($file->filename != '<parent>') {
			$params = array(	'id' => 'filesToDelete[]',
								'value' => $file->filename,
								);
			$child = $control->children[] = zing::create('THtmlCheckbox', $params);
			$child->doStatesUntil('preRender');
		}
	}

	public function insertPreviewLink($control, $params) {
		$sess = TSession::getInstance();
		$file = $control->getBoundObject();
		$params = array();
		$params['module'] = 'Zing/Web/CMS/THtmlFileList';
		$limit = 1;
		$params['dir'] = $this->resolvePath($file->pathname, $sess->parameters['cms.files.rootpath']);
		if ($file->isDir() && !empty($params['dir'])) {
			$params['dir'] .= '/';
		}
		$control->children->deleteAll();
		$child = $control->children[] = zing::create('THtmlLink', $params);
		$child->children[] = $this->getIcon($file);
		$child->children[] = zing::create('THtmlInnerText', array('value' => $file->getFilename()));
		$child->doStatesUntil('preRender');
	}

	/**
	 * resolvePath
	 *
	 * Strip out any path traversals and make relative to rootpath
	 */
	public function resolvePath($path, $rootpath = null) {
		$input = explode('/', $path);
		$output = array();
		foreach ($input as $dir) {
			switch ($dir) {
			case '.':
				break;
			case '..':
				array_pop($output);
				break;
			default:
				$output[] = $dir;
			}
		}

		if (!is_null($rootpath)) {
			foreach (explode('/', $rootpath) as $index => $dir) {
				if ($dir == $output[0]) {
					array_shift($output);
				} else {
					return '';
				}
			}
		}

		if (empty($output[count($output)-1])) {
			array_pop($output);
		}
		$result = implode('/', $output);
		return $result;
	}

	public function addFile($control, $params) {
		$sess = TSession::getInstance();
		$errors = array();
		if ($sess->app->files->upload['error'] == 0) {
			$path = $this->resolvePath($sess->parameters['cms.files.rootpath'] . '/' . $sess->app->request->dir, null);

			$targetFile = $path . '/' . $sess->app->files->upload['name'];

			if (file_exists($targetFile)) {
				$errors[] = 'The uploaded file already exists, please rename, delete or try again';
			} else {

				if (is_writable($path)) {
					move_uploaded_file($sess->app->files->upload['tmp_name'], $targetFile);
					chmod($targetFile, 0644);
				} else {
					$errors[] = 'Unable to save the uploaded file';
				}

				if (file_exists($sess->app->files->upload['tmp_name'])) {
					@unlink($sess->app->files->upload['tmp_name']);
				}
			}
		} else if ($sess->app->files->upload['error'] == 4) {
			$errors[] = 'You must specify the file to be uploaded, use the browse button';
		} else {
			$errors[] = 'There was an error with the uploaded file, please try again';
		}

		if (count($errors)) {
			$this->divNotify->setInnerText(implode(', ', $errors));
			$this->divNotify->addClass('error');
		} else {
			$this->divNotify->setInnerText($sess->app->files->upload['name'] . ' Uploaded successfully');
			$this->divNotify->addClass('success');
		}
	}

	public function deleteFiles($control, $params) {
		$sess = TSession::getInstance();
		$errors = array();
		$cnt = 0;
		foreach ((array)$sess->app->request->filesToDelete as $filename) {
			$path = $this->resolvePath($sess->parameters['cms.files.rootpath'] . '/' . $sess->app->request->dir, null);
			$path .= '/' . $filename;
			if (is_dir($path)) {
				if (@rmdir($path)) {
					$cnt++;
				} else {
					$errors[] = 'Unable to delete directory "' . $filename.  '"';
				}
			} else {
				if (@unlink($path)) {
					$cnt++;
				} else {
					$errors[] = 'Unable to delete file ' . $filename;
				}
			}
		}
		$success = ($cnt == count($sess->app->request->filesToDelete)) && $cnt > 0;
		$this->divNotify->setInnerText($cnt . ' Files deleted' . ($success ? ' successfully' : (count($errors) ? ' - ' . implode(', ', $errors) : '')));
		$this->divNotify->addClass($success ? 'success' : 'error');
	}

	public function addFolder($control, $params) {
		$folder = trim($this->folder->getValue());
		if ( ! empty($folder)) {
			$sess = TSession::getInstance();
			$path = $this->resolvePath($sess->parameters['cms.files.rootpath'] . '/' . $sess->app->request->dir, null);
			$path .= '/' . $this->folder->getValue();
			if (is_dir($path)) {
				$errors[] = 'Unable to create directory "' . $this->folder->getValue() . '", it already exists';
			} else {
				if (! @mkdir($path)) {
					$errors[] = 'Unable to create directory';
				} else {
					chmod($path, 0755);
				}
			}
		} else {
			$errors[] = 'You must specify a folder name';
		}
		$this->divNotify->setInnerText(count($errors) ? implode(', ', $errors) : 'Folder created successfully');
		$this->divNotify->addClass(count($errors) ? 'error' : 'success');
		if (count($errors) == 0) {
			$this->folder->setValue('');
		}
	}

	public function formatTime($control, $params) {
		$control->setInnerText(gmdate('d/m/Y H:i', $control->getInnerText()));
	}
}



?>
