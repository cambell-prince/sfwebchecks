<?php
namespace models\typeset;

use libraries\palaso\CodeGuard;
use models\mapper\Id;
use models\mapper\MapperModel;
use models\mapper\rapuma\RapumaMapper;

/**
 * TypesetModel is the base class for all models persisted inside the typesetting environment.
 * i.e. In /var/lib/rapuma/work/<some project>/<some further path to config>
 * The full path to a TypesetModel file is made up of 3 parts:
 * 1) The base path.  RAPUMA_BASE_PATH, this is defined in sf_config.php (or TestConfig.php)
 * 2) The project code. This is obtained from the project model and is based on the project name.
 * 3) The 'id' of the model.  This is the remainder of the path relative to the project directory.
 *    e.g. config/project.conf
 */
class TypesetModel extends MapperModel
{

	protected static function mapper() {
		static $instance = null;
		if (null === $instance) {
			$instance = new RapumaMapper(RAPUMA_BASE_PATH);
		}
		return $instance;
	}

	/**
	 * @var ProjectModel
	 */
	private $_projectModel;
	
	/**
	 * @param ProjectModel $projectModel
	 * @param string $id
	 */
	public function __construct($projectModel, $id = '') {
		$this->_projectModel = $projectModel;
		$this->id = new Id();
		parent::__construct(self::mapper(), $id);
	}

	public function setId($id) {
		$this->id->id = $id;
	}

	protected function relativeId($id) {
		return $this->_projectModel->projectCode . '/' . $id;
	}
	
	protected function ensureProjectDirectoryExists() {
		$this->_mapper->ensureDirectoryExists($this->_projectModel->projectCode);
		
	}
	
	/**
	 * Reads the model from the mongo collection
	 * @param string $id
	 * @see MongoMapper::read()
	 */
	public function read($id) {
		$this->_mapper->read(
			function($data) {
				return $this;
			},
			$this->relativeId($id)
		);
		$this->setId($id);
	}
	
	public function readByProperty($property, $value) {
		throw new \Exception("This method is intentionally not implemented. Don't use it.");
	}
	
	/**
	 * Writes the model to the mongo collection
	 * @return string The unique id of the object written
	 * @see MongoMapper::write()
	 */
	public function write() {
		CodeGuard::checkTypeAndThrow($this->id, 'models\mapper\Id');
		CodeGuard::checkNullAndThrow($this->id->id, 'TypesetModel::id');
		$this->ensureProjectDirectoryExists();
		$this->_mapper->write($this, $this->relativeId($this->id->asString()));
		return $this->id->asString();
	}
	
	public function remove($id) {
		CodeGuard::checkTypeAndThrow($id, 'string');
		return $this->_mapper->remove($this->relativeId($this->id->asString()));
	}
	
	/**
	 * @var Id
	 */
	public $id;

}

?>