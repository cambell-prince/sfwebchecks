'use strict';

/* SF Typeset Services */

angular.module('sftypeset.services', [])
	.service('componentService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, componentId, callback) {
			jsonRpc.call('/api/typeset', 'component_read', [projectId, componentId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('/api/typeset', 'component_update', [projectId, model], callback);
		};
		this.remove = function(projectId, componentIds, callback) {
			jsonRpc.call('/api/typeset', 'component_delete', [projectId, componentIds], callback);
		};
		this.list = function(projectId, callback) {
			jsonRpc.call('/api/typeset', 'component_list', [projectId], callback);
		};
		this.settings_dto = function(projectId, componentId, callback) {
			jsonRpc.call('/api/typeset', 'component_settings_dto', [projectId, componentId], callback);
		};
	}])
	;