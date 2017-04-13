<?php

class THtmlGoogleAnalytics extends TCompositeControl {

	var $userAccount;
	var $multiDomain;
	
	public function setUserAccount($ua) {
		$this->userAccount = $ua;
	}
	
	public function getUserAccount() {
		if (!empty($this->userAccount)) {
			return $this->userAccount;
		}
		
		$sess = TSession::getInstance();
		$ua = $sess->parameters['google.analytics.account'];
		if (!empty($ua)) {
			return $ua;
		}
		
		return null;
	}
	
	public function setMultiDomain($md) {
		$this->multiDomain = $md;
	}
	
	public function getMultiDomain() {
		if (!empty($this->multiDomain)) {
			return $this->multiDomain;
		}
		
		$sess = TSession::getInstance();
		return $sess->parameters['google.analytics.multi-domain'] ? true : false;
	}
	
	public function setOneDomain($od) {
		$this->oneDomain = $od;
	}
	
	public function getOneDomain() {
		if (!empty($this->oneDomain)) {
			return $this->oneDomain;
		}
		
		$sess = TSession::getInstance();
		$domain = $sess->parameters['google.analytics.domain'];
		if (! empty($domain)) {
			return $domain;
		}
		
		return null;
	}
	
	public function hasOneDomain() {
		$domain = $this->getOneDomain();
		return !empty($domain);
	}
	
	public function hasUserAccount() {
		$ua = $this->getUserAccount();
		return !empty($ua);
	}
	
	public function render() {
		if ($this->getVisible() && $this->hasUserAccount()) {

			$this->children->deleteAll();

/*			$script = <<<EOT
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
EOT;

			echo $script;
			
			$script = <<<EOT
<script type="text/javascript">
	var pageTracker = _gat._getTracker("USER-ACCT");
	pageTracker._initData();
	MULTI-DOMAIN
	pageTracker._trackPageview();
</script>
EOT;
*/

			$script = <<<EOT
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'USER-ACCT']);
	ONE-DOMAIN
	MULTI-DOMAIN
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

</script>
EOT;

		$script = str_replace('USER-ACCT', $this->getUserAccount(), $script);
		$script = str_replace('ONE-DOMAIN', $this->hasOneDomain() ? '_gaq.push([\'_setDomainName\', \'' . $this->getOneDomain() . '\']);' : '', $script);
		$script = str_replace('MULTI-DOMAIN', $this->getMultiDomain() ? '_gaq.push([\'_setDomainName\',\'none\']); _gaq.push([\'_setAllowLinker\', true]);' : '', $script);
  
  echo $script;

		}
	}

}

?>
