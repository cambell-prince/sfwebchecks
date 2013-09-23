<?php

use models\mapper\rapuma\RapumaMapper;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once('RapumaMapperTestEnvironment.php');

class TestRapumaMapper extends UnitTestCase {

	function __construct() {
	}
	
	function testCRUD_Works() {
		$e = new RapumaMapperTestEnvironment();
		$e->clean();
		
		// Create
		$model = new RapumaTestModel();
		$model->projectInfo->languageCode = 'th';
		$model->projectInfo->projectCreatorVersion = '0.1.2';
		$model->setId('project.conf');
		$id = $model->write();
		
		$this->assertNotNull($id);
		$this->assertIsA($id, 'string');
		$this->assertEqual('project.conf', $id);
		$this->assertEqual('project.conf', $model->id->asString());
		
		// Read back
		$otherModel = new RapumaTestModel($id);
		$this->assertEqual($model->id, $otherModel->id);
		$this->assertEqual($model->projectInfo, $otherModel->projectInfo);
		
// 		var_dump($model, $otherModel);
		
		// Update
		$otherModel->projectInfo->languageCode = 'en';
		$otherModel->write('project.conf');
		
		// Read back
		$otherModel = new RapumaTestModel($id);
		$this->assertEqual('en', $otherModel->projectInfo->languageCode);
		
		// Delete
		$this->assertTrue(false, 'delete nyi');
		
		// Test not exist
		$filePath = $e->filePath('project.conf');
		$this->assertFalse(file_exists($filePath));
		
	}
	
}

?>