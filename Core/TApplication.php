<?php

if (defined('ZING_PROFILER') && ZING_PROFILER) {

	class ZingPDO {
		
		private $pdo;
		public $queries = array();
		public $queryCount = 0;
		
		public function __construct($dsn, $username = NULL, $password = NULL, $driver_options = array()) {
			foreach ($driver_options as $attr => $value) {
				switch ($attr) {
					case PDO::ATTR_PERSISTENT:
						$driver_options[$attr] = false;
						break;
					case PDO::ATTR_STATEMENT_CLASS:
						$driver_options[$attr] = array('ZingPDOStatement', array($this));
						break;
				}
			}
			if (!isset($driver_options[PDO::ATTR_STATEMENT_CLASS])) {
				$driver_options[PDO::ATTR_STATEMENT_CLASS] = array('ZingPDOStatement', array($this));
			}
			$this->pdo = new PDO($dsn, $username, $password, $driver_options);
		}
		
		public function beginTransaction() {
			return $this->pdo->beginTransaction();
		}
		
		public function commit() {
			return $this->pdo->commit();
		}
		
		public function errorCode() {
			return $this->pdo->errorCode();
		}
		
		public function errorInfo() {
			return $this->pdo->errorInfo();
		}
		
		public function exec($statement) {
			return $this->pdo->exec($statement);
		}
		
		public function getAttribute($attribute) {
			return $this->pdo->getAttribute($attribute);
		}
		
		public function getAvailableDrivers() {
			return $this->pdo->getAvailableDrivers();
		}
		
		public function lastInsertId($name = NULL) {
			return $this->pdo->lastInsertId($name);
		}
		
		public function prepare($statement, $driver_options = array()) {
			return $this->pdo->prepare($statement, $driver_options);
		}
		
		public function query($statement) {
			return $this->pdo->query($statement);
		}
		
		public function quote($string, $parameter_type = PDO::PARAM_STR) {
			return $this->pdo->quote($string, $parameter_type);
		}
		
		public function rollBack() {
			return $this->pdo->rollBack();
		}
		
		public function setAttribute($attribute, $value) {
			return $this->setAttribute($attribute, $value);
		}
	}
		
	class ZingPDOStatement extends PDOStatement {
		private $pdo;
		private $params = array();
		
		protected function __construct(ZingPDO $pdo) {
			$this->pdo = $pdo;
		}

		public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = NULL, $driver_options = array()) {
			$this->params[$parameter] = array('variable' => $variable, 'data_type' => $data_type, 'length' => $length, 'driver_options' => $driver_options);
			return parent::bindParam($parameter, $variable, $data_type, $length, $driver_options);
		}
		
		public function execute() {
			$sql = $this->queryString;
			foreach ($this->params as $parameter => $options) {
				switch ($options['data_type']) {
					case PDO::PARAM_STR:
						$variable = $this->pdo->quote($options['variable']);
						break;
					default:
						$variable = $options['variable'];
						break;
				}
				$sql = preg_replace('/'.$parameter.'/', $variable, $sql);
			}
			$start = PhpQuickProfiler::getMicroTime();
			$result = parent::execute();
			$this->pdo->queries[$this->pdo->queryCount++] = array('sql' => $sql, 'time' => (PhpQuickProfiler::getMicroTime() - $start) * 1000);
			return $result;
		}

	}
} else {
	class ZingPDO extends PDO {
	}
}

class TApplication implements IContainer {

	private $hasEnvironment = false;

	private $session;
	private $env;
	private $get;
	private $post;
	private $request;
	private $files;
	private $cookies;
	private $server;
	private $profiler;
	
	public $modules;
	public $page;
	
	function __construct() {
		$this->session = TSession::getInstance();
		$this->session->app = $this;
		$this->modules = new TRegistry;

		if (defined('ZING_PROFILER') && ZING_PROFILER) {
			$this->profiler = new PhpQuickProfiler(PhpQuickProfiler::getMicroTime(), '/Zing/PhpQuickProfiler/');
		}
		
		Console::logSpeed('Application Initialisation');
		Console::logMemory(NULL,'Startup');
	}

	function __destruct() {
		Console::logSpeed('Application Destruction');
		if (isset($this->profiler)) {
			$pdo = $this->session->parameters->pdo;
			$this->profiler->display(isset($pdo) ? $pdo : '');
		}
	}
	
	public function getModuleMap($path) {
	
		foreach ($this->modules as $module) {
			if ($module->isMatch($path)) {
				return $module;
			}
		}
		
		return null;
	}
	
	public function getModuleUri($modulePath, $uriParams = array(), $queryParams = array(), $bookmark = null) {
		foreach($this->modules as $module) {
			$cmp = substr($module->getModulePath(), 0-strlen($modulePath));
			if (strcasecmp($cmp, $modulePath) == 0) {
				$uri = $module->getUri($uriParams);
				if (count($queryParams)) {
					$uri .= '?';
					$cnt = 0;
					foreach ($queryParams as $param => $value) {
						$uri .= ($cnt++ ? '&' : '') . urlencode($param) . '=' . urlencode($value);
					}
				}
				if (!is_null($bookmark)) {
					$uri .= '#' . $bookmark;
				}
				return $uri;
			}
		}
	}
	
	public function redirect($modulePath, $uriParams = array(), $queryParams = array()) {
		if (strpos($modulePath, '://') === false) {
			$uri = $this->getModuleUri($modulePath, $uriParams, $queryParams);
		} else {
			$uri = $modulePath;
		}
		if (! empty($uri)) {
			header('Location: ' . $uri);
			exit();
		}
	}
	
	public function run() {
	
		try {
			if (! $this->hasEnvironment) {
				$this->setEnvironment();
			}
			
			if (is_null($moduleMap = $this->getModuleMap($this->request->_modpath))) {
				throw new Exception('404 Not Found');
			}

			foreach ($moduleMap->parameters as $param => $value) {
				$this->request[$param] = $value;
			}
			
			if ($layoutMap = $moduleMap->getLayout()) {
				$this->page = $layoutMap->getModule();
				$this->page->setContent($moduleMap->getModule());
			} else {
				$this->page = $moduleMap->getModule();
			}

			$this->page->setContainer($this);

			$this->page->doStatesUntil('renderComplete');

		} catch (Exception $e) {
			header('HTTP/1.1 ' . $e->getMessage());
			?>
			<table style="font-family: monospace;">
				<thead>
					<tr><td colspan="2"><h1><?=$e->getMessage()?></h1></td></tr>
				</thead>
				<tbody style=" vertical-align: top;">
					<tr>
						<td>File</td>
						<td><?=$e->getFile()?></td>
					</tr>
					<tr>
						<td>Line</td>
						<td><?=$e->getLine()?></td>
					</tr>
					<tr>
						<td>Trace</td>
						<td><?=implode("<br />#",explode('#',$e->getTraceAsString()))?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
	}
	
	function getPage() {
		return $this->pageLoader->getPage();
	}
	
	function setEnvironment($vars = null) {
		if (is_null($vars)) {
			$vars = array(	'_SERVER' => $_SERVER,
								'_GET' => $_GET,
								'_POST' => $_POST,
								'_REQUEST' => $_REQUEST,
								'_FILES' => $_FILES,
								'_ENV' => $_ENV,
								'_COOKIE' => $_COOKIE
						);
		}
	
		foreach ($vars as $name => $var) {
			switch (strtolower($name)) {
			case '_env':
				$this->env = new TRegistry($var);
				break;
			case '_get':
				$this->get = new TRegistry($var,true);
				break;
			case '_post':
				$this->post = new TRegistry($var,true);
				break;
			case '_request':
				$this->request = new TRegistry($var,true);
				break;
			case '_files':
				$this->files = new TRegistry($var);
				break;
			case '_cookie':
				$this->cookies = new TRegistry($var);
				break;
			case '_server':
				$this->server = new TRegistry($var);
				break;
			}
		}
		
		$this->hasEnvironment = true;
	}

	function __get($name) {
		if (in_array($name, array('env','get','post','request','files','cookies','server'))) {
			return $this->$name;
		}
	}
	
	/* ============================= IContainer ============================ */

	public function getChildren() {
		return array($this->page);
	}
	
	public function getDescendantById($id) {
		if ($this->page->getId() == $id) {
			return $this->page;
		}
		return $this->page->getDescendantById($id);
	}
	
	public function getDescendantsByClass($class) {
		$array = array();
		
		if ($this->page instanceof $class) {
			$array[] = $this->page;
		}
		
		if ($this->page instanceof IContainer) {
			$array = array_merge($array, $this->page->getDescendantsByClass($class));
		}

		return $array;
	}
		
	
}

?>
