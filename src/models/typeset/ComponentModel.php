<?php

namespace models\typeset;

use libraries\palaso\CodeGuard;
use models\mapper\Id;

require_once(APPPATH . '/models/ProjectModel.php');

class ComponentModelMongoMapper extends \models\mapper\MongoMapper
{
	/**
	 * @var ComponentModelMongoMapper[]
	 */
	private static $_pool = array();
	
	/**
	 * @param string $databaseName
	 * @return ComponentModelMongoMapper
	 */
	public static function connect($databaseName) {
		if (!isset(static::$_pool[$databaseName])) {
			static::$_pool[$databaseName] = new ComponentModelMongoMapper($databaseName, 'components');
		}
		return static::$_pool[$databaseName];
	}
	
}

class ComponentModel extends \models\mapper\MapperModel
{
	
	const TYPE_BOOK  = 'book';
	const TYPE_COVER = 'cover';
	
	/**
	 * @var ProjectModel;
	 */
	private $_projectModel;
	
	public function __construct($projectModel, $id = '') {
		$this->id = new Id();
		$this->_projectModel = $projectModel;
		$databaseName = $projectModel->databaseName();
		parent::__construct(ComponentModelMongoMapper::connect($databaseName), $id);
	}

	public static function remove($databaseName, $id) {
		ComponentModelMongoMapper::connect($databaseName)->remove($id);
	}
	
	public static function readd($projectModel, $id) {
		ComponentModelMongoMapper::connect($databaseName)->read(
			function($data) use ($projectModel) {
				return self::create($projectModel, $data);
			},
			$id
		);
	}
	
	public static function create($projectModel, $data) {
		$type = $data['type'];
		CodeGuard::checkNullAndThrow($type, 'type');
		switch ($type) {
			case self::TYPE_BOOK:
				return new BookModel($projectModel);
			case self::TYPE_COVER:
				return new CoverModel($projectModel);
			default:
				throw new \Exception("Unsupported Component type '$type'");
		}
		
	}

	public $id;
	
	public $type;
	
	public $name;
	
}

class ComponentListModel extends \models\mapper\MapperListModel
{

	public function __construct($projectModel)
	{
		parent::__construct(
			ComponentModelMongoMapper::connect($projectModel->databaseName()),
			array(),
			array('type', 'name')
		);
	}
	
}

?>
