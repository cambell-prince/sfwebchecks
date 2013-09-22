<?php
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

class RapumaTestModel
{

	public function __construct() {
		$this->projectInfo = new RapumaProjectInfo();
		$this->managers = new ArrayOf(ArrayOf::OBJECT, function($data) {
			return self::createManager($data);
		});
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

class RapumaMapperTestEnvironment extends TemporaryDirectory
{
	
	public function __construct() {
		parent::__construct('rapuma_mapper_test');
	}
	
	
}

?>