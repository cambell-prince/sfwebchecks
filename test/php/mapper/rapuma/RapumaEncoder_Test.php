<?php

use models\mapper\Id;
use models\mapper\ArrayOf;
use models\mapper\MapOf;
use models\mapper\rapuma\RapumaEncoder;
use models\typeset\TypesetModel;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once('RapumaMapperTestEnvironment.php');

class RapumaTestBook
{
	/**
	 * @param string $id
	 */
	public function __construct($id) {
		$this->id = new Id($id);
	}

	public $id;

	public $cidList;
	
	public $cType;

}

class RapumaTestProjectInfo
{
	public $projectIDCode;

	public $languageCode;
}

class RapumaEncoderTestModel extends TypesetModel
{

	public function __construct($projectModel, $id = '') {
		$this->projectInfo = new RapumaTestProjectInfo();
		$this->groups = new MapOf(function($data) {
			return new RapumaTestBook();
		});
		parent::__construct($projectModel, $id);
	}

	/**
	 * @var DecoderProjectInfo
	 */
	public $projectInfo;

	/**
	 * @var ArrayOf
	 */
	public $groups;
}

class TestRapumaEncoder extends UnitTestCase {

	function testEncode_ok() {
		$values = <<<EOT
[ProjectInfo]
    isbnNumber = ""
    projectCreatorVersion = 0.6.r808
    languageCode = th
    projectName = The Book of James in English
    projectIDCode = ENG-LATN-JAS
    typesetters = ,
    translators = ,
    projectTitle = ""
    projectMediaIDCode = book
    finishDate = ""
    creatorID = default_user
    startDate = ""
    projectCreateDate = 2013-09-07 00:00:39
[Groups]
    [[james]]
        tocSectionTitle = ""
        useGrpStyOverride = False
        useMacros = False
        cidList = jas,
        compStyOverrideList = ""
        startPageNumber = 1
        cType = usfm
        precedingGroup = None
        useGrpTexOverride = False
        postprocessScripts = ""
        isLocked = True
        csid = mb
        useHyphenation = False
        tocInclude = False
        totalPages = 12
        useManualAdjustments = True
        useIllustrations = False
        usePreprocessScript = False
        compTexOverrideList = ""
        bindingOrder = 0
EOT;
		$expected = explode("\n", $values);
	
		$projectModel = new RapumaMapperMockProject();
		
		$model = new RapumaEncoderTestModel($projectModel);
		$model->projectInfo->projectIDCode = 'SOME-ID-CODE';
		$model->projectInfo->languageCode = 'en';
		
		$group = new RapumaTestBook('james');
		$group->cidList = 'mat,jas';
		$group->cType = 'x';
		$model->groups->data[$group->id->asString()] = $group;

// 		$group = new RapumaUsfmXetex();
// 		$group->draftBackground = 'linesWatermark, draftWatermark';
// 		$group->freezeTexSettings = 'False';
// 		$model->managers->append($group);
		
		$result = RapumaEncoder::encode($model, $expected);
   		var_dump($result);
 		
 		for ($i = 0, $c = count($result); $i < $c; $i++) {
 			$this->assertEqual(trim($expected[$i]), trim($result[$i]));
 		}
	
	}
}

?>