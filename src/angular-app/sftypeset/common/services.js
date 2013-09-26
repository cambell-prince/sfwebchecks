'use strict';

/* SF Typeset Services */

angular.module('sftypeset.services', [])
	.service('dashService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, callback) {
			jsonRpc.call('/api/typeset', 'dash_read', [projectId], callback);
		};
		this.run = function(projectId, type, id, callback) {
			jsonRpc.call('/api/typeset', 'run', [projectId, type, id], callback);			
		};
		this.run_poll = function(projectId, runId, callback) {
			jsonRpc.call('/api/typeset', 'run_poll', [projectId, runId], callback);			
		};
		this.settings_read = function(projectId, groupId, callback) {
			jsonRpc.call('/api/typeset', 'project_settings_read', [projectId, groupId], callback);
		};
	}])
	;