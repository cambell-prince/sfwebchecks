<?php

use models\mapper\rapuma\RapumaEncoder;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once('RapumaMapperTestEnvironment.php');

class TestRapumaEncoder extends UnitTestCase {

	function testEncode_ok() {
		$values = <<<EOT
[ProjectInfo]
	projectCreatorVersion = 0.6.r808
    languageCode = en
[Managers]
    [[usfm_Text]]
        sourceEncode = utf8
        workEncode = utf8
    [[usfm_Xetex]]
        draftBackground = linesWatermark, draftWatermark
        freezeTexSettings = False
EOT;
		$expected = explode("\n", $values);
	
		$projectModel = new RapumaMapperMockProject();
		
		$model = new RapumaTestModel($projectModel);
		$model->projectInfo->projectCreatorVersion = '0.6.r808';
		$model->projectInfo->languageCode = 'en';
		
		$manager = new RapumaUsfmText();
		$manager->id->id = 'usfm_Text';
		$manager->sourceEncode = 'utf8';
		$manager->workEncode = 'utf8';
		$model->managers->append($manager);

		$manager = new RapumaUsfmXetex();
		$manager->id->id = 'usfm_Xetex';
		$manager->draftBackground = 'linesWatermark, draftWatermark';
		$manager->freezeTexSettings = 'False';
		$model->managers->append($manager);
		
		$result = RapumaEncoder::encode($model);
//   		var_dump($result);
 		
 		for ($i = 0, $c = count($result); $i < $c; $i++) {
 			$this->assertEqual(trim($expected[$i]), trim($result[$i]));
 		}
	
	}
}

?>