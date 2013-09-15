<?php

namespace models\typeset;

use models\UserModel;
use models\ProjectModel;
use models\dto\RightsHelper;
use models\typeset\ComponentListModel;
use models\typeset\ComponentModel;

class ComponentListDto
{
	/**
	 *
	 * @param string $projectId
	 * @param string $userId
	 * @returns array - the DTO array
	 */
	public static function encode($projectId, $userId) {
		$userModel = new UserModel($userId);
		$projectModel = new ProjectModel($projectId);
		$componentList = new ComponentListModel($projectModel);
		$componentList->read();

		$data = array();
		$data['rights'] = RightsHelper::encode($userModel, $projectModel);
		$data['count'] = $componentList->count;
		$data['project'] = array(
				'name' => $projectModel->projectname,
				'id' => $projectId);
		$data['entries'] = $componentList->entries;
		return $data;
	}
}

?>
