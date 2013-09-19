<?php

use models\mapper\ArrayOf;
use models\mapper\Id;
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
	
	public $freezeTexSettings;
	
}

class DecoderProjectInfo
{
	public $projectCreatorVersion;
	
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
			case 'usfm_Text':
				return new DecoderUsfmText();
			case 'usfm_Xetex':
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
	/*
	function testRegEx_Groups() {
		$string = "[[[Some Group]]]";
		$matches = array();

		$result = preg_match('/(\[+)([^\]]+)(\]+)/', $string, $matches);
		var_dump($result, $matches);
	}

	function testRegEx_Properties() {
		$string = "Property  = \" some value \"";
		$matches = array();

		$result = preg_match('/(\w+)\s*=\s*(.+)/', $string, $matches);
		var_dump($result, $matches);
	}

	function testRegEx_QuotedString() {
		// http://blog.stevenlevithan.com/archives/match-quoted-string
		// http://stackoverflow.com/questions/3568995/how-do-i-match-a-pattern-with-optional-surrounding-quotes
		$string = "Property  = \" some value \"";
		$matches = array();
		
		$result = preg_match('/(["\'])(?:\\?.)*?\1/', $string, $matches);
		var_dump($result, $matches);
		
	}
	*/
	
	/*
	function testParse_ok() {
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
[Other]
	something = value
EOT;
// 		$result = RapumaDecoder::parse(explode("\n", $values));
// 		var_dump($result);
	}
	*/
	
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
		RapumaDecoder::decode($model, explode("\n", $values));
		$this->assertEqual('0.6.r808', $model->projectInfo->projectCreatorVersion);
		$this->assertEqual('no quotes', $model->projectInfo->languageCode);
		$this->assertEqual(2, $model->managers->count());
		
		// usfm_text
		$item1 = $model->managers->data[0];
		$this->assertIsA($item1, 'DecoderUsfmText');
		$this->assertEqual('usfm_Text', $item1->id->asString());
		$this->assertEqual('utf8', $item1->sourceEncode);
		$this->assertEqual('utf8', $item1->workEncode);
		
		// usfm_xetex
		$item2 = $model->managers->data[1];
		$this->assertIsA($item2, 'DecoderUsfmXetex');
		$this->assertEqual('usfm_Xetex', $item2->id->asString());
		$this->assertEqual('linesWatermark, draftWatermark', $item2->draftBackground);
		$this->assertEqual('False', $item2->freezeTexSettings);
		
	}
}

?>