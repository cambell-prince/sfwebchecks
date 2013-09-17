<?php
namespace models\typeset;

use models\UserModel;
use models\ProjectModel;
use models\dto\RightsHelper;
use models\typeset\GroupListModel;
use models\typeset\GroupModel;

class GroupListDto
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
		$groupList = new GroupListModel($projectModel);
		$groupList->read();

		$data = array();
		$data['rights'] = RightsHelper::encode($userModel, $projectModel);
		$data['count'] = $groupList->count;
		$data['project'] = array(
				'name' => $projectModel->projectname,
				'id' => $projectId);
		$data['entries'] = $groupList->entries;
		return $data;
	}
}

?>
