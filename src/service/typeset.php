<?php

use libraries\palaso\CodeGuard;
use libraries\palaso\JsonRpcServer;
use models\UserModel;
use models\ProjectModel;
use models\dto\ProjectSettingsDto;
use models\commands\GroupCommands;
use models\commands\UserCommands;
use models\mapper\Id;
use models\mapper\JsonEncoder;
use models\mapper\JsonDecoder;
use models\typeset\GroupModel;

require_once(APPPATH . 'config/sf_config.php');

require_once(APPPATH . 'models/ProjectModel.php');
require_once(APPPATH . 'models/UserModel.php');
require_once(APPPATH . 'models/typeset/GroupModel.php');

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
	// COMPONENT API
	//---------------------------------------------------------------
	
	public function group_update($projectId, $object) {
		$projectModel = new \models\ProjectModel($projectId);
		$isNewComponent = ($object['id'] == '');
		if ($isNewComponent) {
			$groupModel = GroupModel::create($projectModel, $object);
		} else {
			$groupModel = GroupModel::readd($projectModel, $object['id']);
		}
		JsonDecoder::decode($groupModel, $object);
		$groupId = $groupModel->write();
// 		if ($isNewComponent) {
// 			ActivityCommands::addComponent($projectModel, $groupId, $groupModel);
// 		}
		return $groupId;
	}
	
	public function group_read($projectId, $groupId) {
		$projectModel = new \models\ProjectModel($projectId);
		$groupModel = ComponentModel::readd($projectModel, $groupId);
		return JsonEncoder::encode($groupModel);
	}
	
	public function group_delete($projectId, $groupIds) {
		return ComponentCommands::deleteComponents($projectId, $groupIds);
	}
	/*
	public function group_list($projectId) {
		$projectModel = new \models\ProjectModel($projectId);
		$groupListModel = new \models\ComponentListModel($projectModel);
		$groupListModel->read();
		return $groupListModel;
	}
	*/
	public function group_list($projectId) {
		return \models\typeset\GroupListDto::encode($projectId, $this->_userId);
	}

	public function group_settings_dto($projectId, $groupId) {
		return \models\dto\GroupSettingsDto::encode($projectId, $groupId, $this->_userId);
	}
	
}

?>
