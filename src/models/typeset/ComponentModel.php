<?php

namespace models\typeset;

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
	/**
	 * @var ProjectModel;
	 */
	private $_projectModel;
	
	public function __construct($projectModel, $id = '')
	{
		$this->id = new Id();
		$this->_projectModel = $projectModel;
		$databaseName = $projectModel->databaseName();
		parent::__construct(ComponentModelMongoMapper::connect($databaseName), $id);
	}

	public static function remove($databaseName, $id) {
		ComponentModelMongoMapper::connect($databaseName)->remove($id);
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
