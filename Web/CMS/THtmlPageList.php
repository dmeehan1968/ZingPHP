<?php

class THtmlPageList extends TModule {

	public function auth() {
		$this->setAuthPerms('CmsPageList');
		parent::auth();
	}

	public function preRender() {
	
		$sess = TSession::getInstance();
	
		$pages = CmsPage::findAll($sess->parameters->pdo);
		$this->setBoundObject($pages);
	
		parent::preRender();
	}

	public function setPageState($control, $params) {
		$page = $control->getBoundObject();
		if ($page->draft) {
			$control->setClass('page-draft');
		} else {
			$now = gmdate('Y-m-d H:i:s',time());
			if ($now < $page->published) {
				$control->setClass('page-pending');
			} elseif ($now > $page->expires) {
				$control->setClass('page-expired');
			} else {
				$control->setClass('page-published');
			}
		}
	}
	
	public function insertCheckbox($control, $params) {
		$page = $control->getBoundObject();
		$params = array(	'id' => 'pagesToDelete[]',
								'value' => $page->id,
							);
		$control->children->deleteAll();
		$child = $control->children[] = zing::create('THtmlCheckbox', $params);
		$child->doStatesUntil('preRender');
	}
	
	public function insertEditLink($control, $params) {
		$page = $control->getBoundObject();
		$params = array(	'module' => 'Zing/Web/CMS/THtmlStandardPageEdit',
								'page_id' => $page->id,
								'innerText' => '(Edit)'
							);
		$control->children->deleteAll();
		$child = $control->children[] = zing::create('THtmlLink', $params);
		$child->doStatesUntil('preRender');
	}
	
	public function insertPreviewLink($control, $params) {
		$page = $control->getBoundObject();
		$params = array(	'module' => 'Zing/Web/CMS/THtmlStandardPage',
								'uri' => $page->uri,
								'innerText' => $control->getInnerText()
							);
		$control->children->deleteAll();
		$child = $control->children[] = zing::create('THtmlLink', $params);
		$child->doStatesUntil('preRender');
	}

	public function addPage($control, $params) {
		$sess = TSession::getInstance();
		$sess->app->redirect('Zing/Web/CMS/THtmlStandardPageEdit', array('page_id' => 'new'));	
	}
	
	public function deletePages($control, $params) {
		$sess = TSession::getInstance();
		$count = 0;
		foreach ((array)$sess->app->request->pagesToDelete as $page_id) {
			if ($page = CmsPage::findOneById($sess->parameters->pdo, $page_id)) {
				$page->destroy(true);
				$count++;
			}
		}
		
		$this->divNotify->setInnerText($count . ' pages deleted');
		$this->divNotify->setClass($count ? 'success' : 'error');
		$this->divNotify->setVisible(true);
	}
	
	public function insertDraftStatus($control, $params) {
		$page = $control->getBoundObject();
		$control->setInnerText($page->draft ? 'Yes' : 'No');
	}
}

?>