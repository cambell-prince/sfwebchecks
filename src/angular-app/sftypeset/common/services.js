'use strict';

/* SF Typeset Services */

angular.module('sftypeset.services', [])
	.service('groupService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, groupId, callback) {
			jsonRpc.call('/api/typeset', 'group_read', [projectId, groupId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('/api/typeset', 'group_update', [projectId, model], callback);
		};
		this.remove = function(projectId, groupIds, callback) {
			jsonRpc.call('/api/typeset', 'group_delete', [projectId, groupIds], callback);
		};
		this.list = function(projectId, callback) {
			jsonRpc.call('/api/typeset', 'group_list', [projectId], callback);
		};
		this.settings_dto = function(projectId, groupId, callback) {
			jsonRpc.call('/api/typeset', 'group_settings_dto', [projectId, groupId], callback);
		};
	}])
	;