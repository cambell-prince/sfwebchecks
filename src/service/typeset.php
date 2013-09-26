<?php

use libraries\palaso\CodeGuard;
use libraries\palaso\JsonRpcServer;
use models\UserModel;
use models\ProjectModel;
use models\dto\ProjectSettingsDto;
use models\commands\UserCommands;
use models\mapper\Id;
use models\mapper\JsonEncoder;
use models\mapper\JsonDecoder;

require_once(APPPATH . 'config/sf_config.php');

require_once(APPPATH . 'models/ProjectModel.php');
require_once(APPPATH . 'models/UserModel.php');

class Typeset
{
	/**
	 * @var string
	 */
	private $_userId;
	
	private $_controller;
	
	public function __construct($controller) {
		$this->_userId = (string)$controller->session->userdata('user_id');
		$this->_controller = $controller;

		// TODO put in the LanguageForge style error handler for logging / jsonrpc return formatting etc. CP 2013-07
 		ini_set('display_errors', 0);
	}
	
	//---------------------------------------------------------------
	// DASHBOARD API
	//---------------------------------------------------------------
	
	public function dash_read($projectId) {
		return \models\typeset\dto\DashDto::encode($projectId, $this->_userId);
	}
	
	//---------------------------------------------------------------
	// RUN API
	//---------------------------------------------------------------
	
	public function run($projectId, $type, $id) {
		return \models\typeset\commands\RunCommand::run($projectId, $type, $id, $this->_userId);
	}
	
	public function run_poll($projectId, $runId) {
		return \models\typeset\commands\RunCommand::poll($projectId, $runId);
	}
	
	public function project_settings_read($projectId, $groupId) {
		throw new \Exception("NYI");
// 		return \models\dto\GroupSettingsDto::encode($projectId, $groupId, $this->_userId);
	}
	
}

?>
