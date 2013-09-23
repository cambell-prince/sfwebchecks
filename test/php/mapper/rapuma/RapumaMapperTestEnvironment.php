<?php
use models\mapper\rapuma\RapumaMapper;

use models\mapper\MapperModel;

use models\mapper\ArrayOf;
use models\mapper\Id;

require_once(TestPath . 'common/TemporaryDirectory.php');

class RapumaUsfmText
{
	public function __construct() {
		$this->id = new Id();
	}

	public $id;

	public $sourceEncode;

	public $workEncode;

}

class RapumaUsfmXetex
{
	public function __construct() {
		$this->id = new Id();
	}

	public $id;

	public $draftBackground;

	public $freezeTexSettings;

}

class RapumaProjectInfo
{
	public $projectCreatorVersion;

	public $languageCode;
}

class RapumaTestModel extends MapperModel
{

	protected static function mapper() {
		static $instance = null;
		if (null === $instance) {
			$instance = new RapumaMapper(RAPUMA_BASE_PATH);
		}
		return $instance;
	}
	
	
	public function __construct($id = '') {
		$this->id = new Id();
		$this->projectInfo = new RapumaProjectInfo();
		$this->managers = new ArrayOf(ArrayOf::OBJECT, function($data) {
			return self::createManager($data);
		});
		parent::__construct(self::mapper(), $id);
	}
	
	public function setId($id) {
		$this->id->id = $id;
	}

	public static function createManager($data) {
		switch ($data) {
			case 'usfm_Text':
				return new RapumaUsfmText();
			case 'usfm_Xetex':
				return new RapumaUsfmXetex();
			default:
				throw new \Exception("Unknown manager '$data'");
		}
	}

	public $id;
	
	/**
	 * @var DecoderProjectInfo
	 */
	public $projectInfo;

	/**
	 * @var ArrayOf
	 */
	public $managers;
}

class RapumaMapperTestEnvironment extends TemporaryDirectory
{
	
	public function __construct() {
		parent::__construct(RAPUMA_BASE_PATH);
	}
	
	
}

?>