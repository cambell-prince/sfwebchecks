<?php

namespace models\typeset;

use libraries\palaso\CodeGuard;
use models\mapper\Id;

require_once(APPPATH . '/models/ProjectModel.php');

class GroupModelMongoMapper extends \models\mapper\MongoMapper
{
	/**
	 * @var GroupModelMongoMapper[]
	 */
	private static $_pool = array();
	
	/**
	 * @param string $databaseName
	 * @return GroupModelMongoMapper
	 */
	public static function connect($databaseName) {
		if (!isset(static::$_pool[$databaseName])) {
			static::$_pool[$databaseName] = new GroupModelMongoMapper($databaseName, 'groups');
		}
		return static::$_pool[$databaseName];
	}
	
}

class GroupModel extends \models\mapper\MapperModel
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
		parent::__construct(GroupModelMongoMapper::connect($databaseName), $id);
	}

	public static function remove($databaseName, $id) {
		GroupModelMongoMapper::connect($databaseName)->remove($id);
	}
	
	public static function readd($projectModel, $id) {
		GroupModelMongoMapper::connect($databaseName)->read(
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
				throw new \Exception("Unsupported Group type '$type'");
		}
		
	}

	public $id;
	
	public $type;
	
	public $name;
	
}

class GroupListModel extends \models\mapper\MapperListModel
{

	public function __construct($projectModel)
	{
		parent::__construct(
			GroupModelMongoMapper::connect($projectModel->databaseName()),
			array(),
			array('type', 'name')
		);
	}
	
}

?>
