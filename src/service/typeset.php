<?php

use models\UserModel;
use models\dto\ProjectSettingsDto;
use models\ProjectModel;
use libraries\palaso\CodeGuard;
use libraries\palaso\JsonRpcServer;
use models\commands\ComponentCommands;
use models\commands\UserCommands;
use models\mapper\Id;
use models\mapper\JsonEncoder;
use models\mapper\JsonDecoder;

require_once(APPPATH . 'config/sf_config.php');

require_once(APPPATH . 'models/ProjectModel.php');
require_once(APPPATH . 'models/UserModel.php');
require_once(APPPATH . 'models/typeset/ComponentModel.php');

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
	
	public function component_update($projectId, $object) {
		$projectModel = new \models\ProjectModel($projectId);
		$componentModel = new \models\typeset\ComponentModel($projectModel);
		$isNewComponent = ($object['id'] == '');
		if (!$isNewComponent) {
			$componentModel->read($object['id']);
		}
		JsonDecoder::decode($componentModel, $object);
		$componentId = $componentModel->write();
		if ($isNewComponent) {
			ActivityCommands::addComponent($projectModel, $componentId, $componentModel);
		}
		return $componentId;
	}
	
	public function component_read($projectId, $componentId) {
		$projectModel = new \models\ProjectModel($projectId);
		$componentModel = new \models\ComponentModel($projectModel, $componentId);
		return JsonEncoder::encode($componentModel);
	}
	
	public function component_delete($projectId, $componentIds) {
		return ComponentCommands::deleteComponents($projectId, $componentIds);
	}
	/*
	public function component_list($projectId) {
		$projectModel = new \models\ProjectModel($projectId);
		$componentListModel = new \models\ComponentListModel($projectModel);
		$componentListModel->read();
		return $componentListModel;
	}
	*/
	public function component_list($projectId) {
		return \models\typeset\ComponentListDto::encode($projectId, $this->_userId);
	}

	public function component_settings_dto($projectId, $componentId) {
		return \models\dto\ComponentSettingsDto::encode($projectId, $componentId, $this->_userId);
	}
	
}

?>
