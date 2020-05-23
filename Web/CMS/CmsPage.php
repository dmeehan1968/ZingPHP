<?php

class CmsPage extends TObjectPersistence {

	public $id;
	/** 
	 * @validate "You must supply a valid uri for the page" regexp /^\/[a-z0-9\/\-_]*$/
	 */
	public $uri;
	/** 
	 * @validate "You must supply a title for the page" minlen 1
	 */
	public $title;
	public $abstract;
	public $body;
	/** 
	 * @validate "You must enter a valid date/time" sql_datetime
	 * @type TDateTime
	 */
	public $created;
	/** 
	 * @validate "You must enter a valid date/time" sql_datetime
	 * @type TDateTime
	 */
	public $modified;
	/** 
	 * @validate "You must enter a valid date/time" sql_datetime
	 * @type TDateTime
	 */
	public $published;
	/** 
	 * @validate "You must enter a valid date/time" sql_datetime
	 * @type TDateTime
	 */
	public $expires;
        /**
         * @validate "Page weight should be a number between 1 and 65535" int
         */
    public $weight;
        
	public $draft = true;
	public $navigation = true;
	public $sitemap = true;
	public $search = true;
	
	public function __construct(ZingPDO $pdo, $params = array()) {
		$this->created = $this->modified = $this->getSqlDate();
		parent::__construct($pdo, $params);
	}

	public static function findOnePublishedByUri(ZingPDO $pdo, $uri) {
		$sql = 'select * from cmspages where uri = :uri and published < :published and expires > :expires and draft = false';
		$s = $pdo->prepare($sql);
		$s->bindParam(':uri', $uri, ZingPDO::PARAM_STR);
		$now = self::getSqlDate();
		$s->bindParam(':published', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':expires', $now, ZingPDO::PARAM_STR);

		return self::findOneByStatement($pdo, $s, 'CmsPage');
	}
			
	public static function findOneById(ZingPDO $pdo, $id) {
		$sql = 'select * from cmspages where id = :id';
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);

		return self::findOneByStatement($pdo, $s, 'CmsPage');
	}
			
	public static function findAll(ZingPDO $pdo) {
		$sql = 'select * from cmspages order by uri';
		$s = $pdo->prepare($sql);

		return self::findAllByStatement($pdo, $s, 'CmsPage');
	}
	
	private static function findAllPublishedWithConstraint(ZingPDO $pdo, $constraint = '', $params = array()) {
		$sql = 'select * from cmspages where published < :published and expires > :expires and draft = false ' . $constraint . ' order by uri';
		$s = $pdo->prepare($sql);
		$now = self::getSqlDate();
		$s->bindParam(':published', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':expires', $now, ZingPDO::PARAM_STR);
		foreach ($params as $key => $value) {
			$s->bindParam($key, $value);
		}

		return self::findAllByStatement($pdo, $s, 'CmsPage');
	}
	
	public static function findAllPublished(ZingPDO $pdo) {
		$sql = 'select * from cmspages where published < :published and expires > :expires and draft = false order by uri';
		$s = $pdo->prepare($sql);
		$now = self::getSqlDate();
		$s->bindParam(':published', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':expires', $now, ZingPDO::PARAM_STR);

		return self::findAllByStatement($pdo, $s, 'CmsPage');
	}
	
	public static function findAllPublishedForSitemap(ZingPDO $pdo) {
		return self::findAllPublishedWithConstraint($pdo, 'and sitemap = true');
	}
	
	private static function findAllPublishedByUri(ZingPDO $pdo, $match, $uri, $order = 'uri', $constraint = '', $params = array(), $offset = 0, $limit = 1000) {
		$sql = '	select *
					from cmspages
					where uri ' . $match . ' :uri and published < :published and expires > :expires and draft = false ' . $constraint . '
					order by ' . $order . ' limit :offset, :limit';
		$s = $pdo->prepare($sql);
		$s->bindParam(':uri', $uri, ZingPDO::PARAM_STR);
		$now = self::getSqlDate();
		$s->bindParam(':published', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':expires', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':offset', $offset, ZingPDO::PARAM_INT);
		$s->bindParam(':limit', $limit, ZingPDO::PARAM_INT);
		foreach ($params as $key => $value) {
			$s->bindParam($key, $value);
		}

		return self::findAllByStatement($pdo, $s, 'CmsPage');
	}
	
	public static function findAllPublishedForNavigation(ZingPDO $pdo, $uri, $order = 'uri', $offset = 0, $limit = 1000) {
		return self::findAllPublishedByUri($pdo, 'regexp', $uri, $order, ' and navigation = true', array(), $offset, $limit);
	}
	
	public static function findAllPublishedForSitemapByUri(ZingPDO $pdo, $match, $uri, $order = 'uri', $offset = 0, $limit = 1000) {
		return self::findAllPublishedByUri($pdo, $match, $uri, $order, 'and sitemap = true', array(), $offset, $limit);	
	}
	
	public static function findAllPublishedBySearch(ZingPDO $pdo, $query, $booleanMode = false, $queryExpansion = false, $offset = 0, $limit = 1000) {
		$mode = $booleanMode ? 'in boolean mode' : ($queryExpansion ? 'with query expansion' : '');
		$sql = 'select *, match(title,abstract,body) against (:q1 ' . $mode . ') as score
				from cmspages where (published < :published and expires > :expires and draft = false and search = true)
				and match(title,abstract,body) against (:q2 ' . $mode . ')
				order by score desc
				limit :offset, :limit';

		$s = $pdo->prepare($sql);
		$now = self::getSqlDate();
		$s->bindParam(':published', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':expires', $now, ZingPDO::PARAM_STR);
		$s->bindParam(':q1', $query, ZingPDO::PARAM_STR);
		$s->bindParam(':q2', $query, ZingPDO::PARAM_STR);
		$s->bindParam(':offset', $offset, ZingPDO::PARAM_INT);
		$s->bindParam(':limit', $limit, ZingPDO::PARAM_INT);
		return self::findAllByStatement($pdo, $s, 'CmsPageSearchResult');	
	}
	
	public function update() {
		$this->modified = $this->getSqlDate();
		parent::update();
	}

}

class CmsPageSearchResult extends CmsPage {
	public $score;
}


?>
