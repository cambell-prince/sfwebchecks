<?php

use models\mapper\ArrayOf;
use models\mapper\Id;
use models\mapper\rapuma\RapumaDecoder;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once('RapumaMapperTestEnvironment.php');

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
	
	function testArrayReference() {
		$array1 = array('11', '12', '13');
		$array2 = array('21', '22', '23');
		$stack = array();
		
		$stack[] =& $array1;
		$stack[] =& $array2;
		
		var_dump($array1);
		var_dump($array2);
		for ($l = 0, $m = count($stack); $l < $m; $l++) {
			$current =& $stack[$l];
			for ($i = 0, $c = count($current); $i < $c; $i++) {
				$current[$i] += 100;
			}
		}
		array_pop($stack);
		
		var_dump($stack);
		
		var_dump($array1);
		var_dump($array2);
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
        [[[Third Level]]]
        levelThree = 3
        
    [[usfm_Xetex]]
        draftBackground = linesWatermark, draftWatermark
        freezeTexSettings = False

EOT;
		$projectModel = new RapumaMapperMockProject();
		
		$model = new RapumaTestModel($projectModel);
		RapumaDecoder::decode($model, explode("\n", $values));
// 		var_dump($model);
		$this->assertEqual('0.6.r808', $model->projectInfo->projectCreatorVersion);
		$this->assertEqual('no quotes', $model->projectInfo->languageCode);
		$this->assertEqual(2, $model->managers->count());
		
		// usfm_text
		$item1 = $model->managers->data[0];
		$this->assertIsA($item1, 'RapumaUsfmText');
		$this->assertEqual('usfm_Text', $item1->id->asString());
		$this->assertEqual('utf8', $item1->sourceEncode);
		$this->assertEqual('utf8', $item1->workEncode);
		
		// usfm_xetex
		$item2 = $model->managers->data[1];
		$this->assertIsA($item2, 'RapumaUsfmXetex');
		$this->assertEqual('usfm_Xetex', $item2->id->asString());
		$this->assertEqual('linesWatermark, draftWatermark', $item2->draftBackground);
		$this->assertEqual('False', $item2->freezeTexSettings);
		
	}
}

?>