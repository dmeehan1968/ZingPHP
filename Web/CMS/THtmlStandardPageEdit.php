<?php

class THtmlStandardPageEdit extends TModule {

    public function auth() {
        $this->setAuthPerms('CmsPageEdit');
        parent::auth();
    }

    public function load() {

        $sess = TSession::getInstance();
        
        if ($sess->app->request->page_id == 'new') {
            $this->page = new CmsPage($sess->parameters->pdo);
            $this->page->published = gmdate('Y-m-d H:i:s', time());
            $this->page->expires = '9999-12-31 23:59:59';
            
            $this->pageTitle->setValue('Create Page');
			$this->pageLink->setVisible(false);
        } else {
            $this->page = CmsPage::findOneById($sess->parameters->pdo, $sess->app->request->page_id);
            if (empty($this->page)) {
                throw new Exception('404 Not Found');
            }
        }

		if ($sess->parameters['cms.standardpage.editor.richtext'] == false) {
			$this->yuiEditor->setVisible(false);
		} else {
 			if ($this->page->body == strip_tags($this->page->body)) {
				$formatter = zing::create('THtmlFormattedDiv', array('innerText' => $this->page->body, 'visible' => THtmlFormattedDiv::VIS_CHILDREN));
				ob_start();
				$formatter->doStatesUntil('renderComplete');
				$html = ob_get_contents();
				ob_end_clean();
				$this->page->body = $html;
			}
		}
		
        parent::load();
    }
	
    public function preRender() {

		if ($this->page->isStored()) {
			$this->pageTitle->setValue('Edit Page: "' . $this->page->title . '"');
			$this->pageLink->setHref($this->page->uri);
		}
		
		foreach (array('draft', 'navigation', 'sitemap', 'search') as $field) {
			$checkboxes = array();
	        $checkbox = new StdClass;
	        $checkbox->value = 1;
	        $checkbox->description = '';
	        $checkbox->selected = $this->page->$field;
	        $checkboxes[] = $checkbox;
	        $this->$field->setBoundObject($checkboxes);
	        $this->$field->setBoundProperty('value|description');
		}
       
        $this->setBoundObject($this->page);

        parent::preRender();
    }
	
    public function savePage($control, $params) {

        $sess = TSession::getInstance();
        $request = $sess->app->request;
        $errors = array();
        $newPage = ! $this->page->isStored();
                        
        try {
                
            $sess->parameters->pdo->beginTransaction();
    
            $this->page->uri = $request->uri;
            $this->page->title = $request->title;
            $this->page->weight = $request->weight;
            $this->page->published = $request->published;
            $this->page->expires = $request->expires;
            $this->page->abstract = $request->abstract;
            $this->page->body = $request->body;
            $this->page->draft = ($request->draft[1] == 1) ? true : false;
            $this->page->navigation = ($request->navigation[1] == 1) ? true : false;
            $this->page->sitemap = ($request->sitemap[1] == 1) ? true : false;
            $this->page->search = ($request->search[1] == 1) ? true : false;

            if (($e = $this->page->validate()) !== true) {
                $errors = array_merge($errors, $e);
                throw new Exception('There are '.count($errors).' problems with the information you have provided.  Please make the changes indicated to allow the information to be saved');
            }

            $this->page->update();
            
            $sess->parameters->pdo->commit();
            
            if ($newPage) {
                $sess->app->redirect('Zing/Web/CMS/THtmlStandardPageEdit', array('page_id' => $this->page->id));	
            }

            $this->divNotify->setInnerText('Changes to the page have been saved');
            $this->divNotify->setClass('success');

        } catch (Exception $e) {
        
            $sess->parameters->pdo->rollback();

            foreach ($errors as $property => $error) {
                $this->$property->setError($error);
            }
            $this->divNotify->setInnerText($e->getMessage());
            $this->divNotify->setClass('error');
        }
        
        $this->divNotify->setVisible(true);	
    }
}

?>