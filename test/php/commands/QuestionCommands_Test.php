<?php

use models\commands\QuestionCommands;
use models\QuestionModel;

require_once(dirname(__FILE__) . '/../TestConfig.php');
require_once(SimpleTestPath . 'autorun.php');

require_once(TestPath . 'common/MongoTestEnvironment.php');

class TestQuestionCommands extends UnitTestCase {

	function __construct()
	{
	}
	
	function testDeleteQuestions_NoThrow() {
		$e = new MongoTestEnvironment();
		$e->clean();
		
		$project = $e->createProject(SF_TESTPROJECT);
		$projectId = $project->id->asString();
		$question = new QuestionModel($project);
		$question->write();
		
		$questionId = $question->id->asString();
		QuestionCommands::deleteQuestions($projectId, array($questionId));
		
	}
	
}

?>