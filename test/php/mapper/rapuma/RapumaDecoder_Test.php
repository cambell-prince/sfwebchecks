<?php

use models\mapper\Id;

use models\mapper\ArrayOf;

use models\mapper\rapuma\RapumaDecoder;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

class DecoderUsfmText
{
	public function __construct() {
		$this->id = new Id();
	}
	
	public $id;
	
	public $sourceEncode;
	
	public $workEncode;
	
}

class DecoderUsfmXetex
{
	public function __construct() {
		$this->id = new Id();
	}
	
	public $id;
	
	public $draftBackground;
	
	public $freezeTextSettings;
	
}

class DecoderProjectInfo
{
	public $projectCreateVersion;
	
	public $languageCode;
}

class DecoderTestModel
{
	
	public function __construct() {
		$this->projectInfo = new DecoderProjectInfo();
		$this->managers = new ArrayOf(ArrayOf::OBJECT, function($data) {
			return self::createManager($data);
		});
	}
	
	public static function createManager($data) {
		switch ($data) {
			case '':
				return new DecoderUsfmText();
			case '':
				return new DecoderUsfmXetex();
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

class TestRapumaDecoder extends UnitTestCase {

	function __construct() {
	}
	
	function test_ok() {
		$values = <<<EOT
# Comment line, should be ignored
[ProjectInfo]
    projectCreatorVersion = 0.6.r808
    languageCode = "no quotes"

[Managers]
    [[usfm_Text]]
        sourceEncode = utf8
        workEncode = utf8
    [[usfm_Xetex]]
        draftBackground = linesWatermark, draftWatermark
        freezeTexSettings = False

EOT;
		
		$model = new DecoderTestModel();
		RapumaDecoder::decode($model, $values);
		$this->assertEqual('0.6.r808', $model->projectInfo->projectCreateVersion);
		$this->assertEqual('no quotes', $model->projectInfo->languageCode);
		$this->assertEqual(2, $model->managers->count());
		
		// usfm_text
		$item1 = $model->managers->data[0];
		$this->assertIsA(item1, 'DecoderUsfmText');
		$this->assertEqual('usfm_Text', $item1->id->asString());
		$this->assertEqual('utf8', $item1->sourceEncode);
		$this->assertEqual('utf8', $item1->workEncode);
		
		// usfm_xetex
		$item2 = $model->managers->data[1];
		$this->assertIsA(item2, 'DecoderUsfmXetex');
		$this->assertEqual('usfm_Xetex', $item2->id->asString());
		$this->assertEqual('linesWatermark, draftWatermark', $item2->draftBackground);
		$this->assertEqual('False', $item2->freezeTexSettings);
		
	}
}

?>