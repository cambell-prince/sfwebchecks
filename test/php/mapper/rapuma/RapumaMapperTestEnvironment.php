<?php
use models\typeset\TypesetModel;

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

class RapumaTestModel extends TypesetModel
{

	public function __construct($projectModel, $id = '') {
		$this->projectInfo = new RapumaProjectInfo();
		$this->managers = new ArrayOf(ArrayOf::OBJECT, function($data) {
			return self::createManager($data);
		});
		parent::__construct($projectModel, $id);
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

	/**
	 * @var DecoderProjectInfo
	 */
	public $projectInfo;

	/**
	 * @var ArrayOf
	 */
	public $managers;
}

class RapumaMapperMockProject {
	public $projectCode;

	public function __construct($projectCode = "test_project") {
		$this->projectCode = $projectCode;
	}
}


class RapumaMapperTestEnvironment extends TemporaryDirectory
{
	
	public function __construct() {
		parent::__construct(RAPUMA_BASE_PATH);
	}
	
	
}

?>