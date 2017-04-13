<?php

class ImageView extends TControl {

	public function onRender() {
	
		$sess = TSession::getInstance();
		$cache = new TCache($sess->paths->cache, $sess->app->request->_modpath);

		if ($sess->app->server->http_cache_control == 'no-cache') {
			// force cache reload
			$cache->destroyCache();
		}

		if ($cache->isExpired()) {
			
			$image = File::findOneByFilename($sess->parameters->pdo, $sess->app->request->filename);

			$cache->startContent();
			
			header('Content-Type: '.$image->mimetype);
			header('Content-Length: '.$image->size);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
			echo $image->data;

			$cache->saveContent();
					
		}
		
		echo $cache->getContent();
	}
}

?>