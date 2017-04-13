<?php

class StaticOrNotFound extends TModule {

	public function render() {
		
		$sess = TSession::getInstance();
		
		$fullpath = substr(str_replace('\\', '/', dirname(dirname(__FILE__)) . '/'), 0, -1) . $sess->app->request->_modpath;
		
		if (is_file($fullpath)) {
			if (!($mime = getimagesize($fullpath))) {
				$ext = strtolower(substr(strrchr($fullpath, '.'), 1));
				if ($ext == 'txt') {
					$ext = 'plain';
				}
				$mime = 'text/' . $ext;
			} else {
				$mime = $mime['mime'];
			}

			if (isset($sess->app->server->http_if_modified_since)) {
				$clientLastModified = strtotime($sess->app->server->http_if_modified_since);
			}
			
			$modified = filemtime($fullpath);
			$etag = md5($modifiedStr = gmdate('D, d M Y H:i:s', filemtime($fullpath)) . ' GMT');
			
			$send = true;
			
			if (isset($clientLastModified) && $modified <= $clientLastModified) {
				$send = false;
			}
			
			if ($send) {
				$clientEtag = substr($sess->app->server->http_if_none_match,1,-1);
				if (isset($sess->app->server->http_if_none_match) && $clientEtag == $etag) {
					$send = false;
				}
			}
			
			if ($send) {
				header('Cache-Control:');
				header('Pragma:');
				header('Expires:');
				header('Content-Type:' . $mime);
				header('Last-Modified:' . $modifiedStr);
				header('ETag: "' . $etag . '"');
				echo file_get_contents($fullpath);
			} else {
				header('HTTP/1.1 304 Not Modified');
				header('Cache-Control:');
				header('Pragma:');
				header('Expires:');
				header('Last-Modified:' . $modifiedStr);
				header('ETag: "' . $etag . '"');

			}
		} else {
			header('HTTP/1.0 404 Not Found');
			parent::render();
		}
	}
}

?>