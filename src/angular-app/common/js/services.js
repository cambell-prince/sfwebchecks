'use strict';

// Services
// ScriptureForge common services
angular.module('sf.services', ['jsonRpc'])
	.service('userService', ['jsonRpc', function(jsonRpc) {
		this.read = function(id, callback) {
			jsonRpc.call('/api/sf', 'user_read', [id], callback);
		};
		this.update = function(model, callback) {
			jsonRpc.call('/api/sf', 'user_update', [model], callback);
		};
		this.remove = function(userIds, callback) {
			jsonRpc.call('/api/sf', 'user_delete', [userIds], callback);
		};
		this.list = function(callback) {
			// TODO Paging CP 2013-07
			jsonRpc.call('/api/sf', 'user_list', [], callback);
		};
		this.typeahead = function(term, callback) {
			jsonRpc.call('/api/sf', 'user_typeahead', [term], callback);
		};
		this.changePassword = function(userId, newPassword, callback) {
			jsonRpc.call('/api/sf', 'change_password', [userId, newPassword], callback);
		};
		this.userNameExists = function(username, callback) {
			jsonRpc.call('/api/sf', 'username_exists', [username], callback);
		};
		this.create = function(model, callback) {
			jsonRpc.call('/api/sf', 'user_create', [model], callback);
		};
	}])
	.service('projectService', ['jsonRpc', function(jsonRpc) {
		
		this.TYPE_ALL             = function() { return ''; };
		this.TYPE_COMMUNITY_CHECK = function() { return 'check'; };
		this.TYPE_TYPESET         = function() { return 'typeset'; };
		
		this.read = function(projectId, callback) {
			jsonRpc.call('/api/sf', 'project_read', [projectId], callback);
		};
		this.update = function(model, callback) {
			jsonRpc.call('/api/sf', 'project_update', [model], callback);
		};
		this.remove = function(projectIds, callback) {
			jsonRpc.call('/api/sf', 'project_delete', [projectIds], callback);
		};
		// Eventually this will need to become:
		//this.list = function(userId, callback) {
			//jsonRpc.call('/api/sf', 'project_list_dto', [userId], callback);
		//};
		this.list = function(type, callback) {
			jsonRpc.call('/api/sf', 'project_list_dto', [type()], callback);
		};
		this.readUser = function(projectId, userId, callback) {
			jsonRpc.call('/api/sf', 'project_readUser', [projectId, userId], callback);
		};
		this.updateUser = function(projectId, model, callback) {
			jsonRpc.call('/api/sf', 'project_updateUser', [projectId, model], callback);
		};
		this.removeUsers = function(projectId, users, callback) {
			jsonRpc.call('/api/sf', 'project_deleteUsers', [projectId, users], callback);
		};
		this.listUsers = function(projectId, callback) {
			// TODO Paging CP 2013-07
			jsonRpc.call('/api/sf', 'project_listUsers', [projectId], callback);
		};
	}])
	.service('textService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, textId, callback) {
			jsonRpc.call('/api/sf', 'text_read', [projectId, textId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('/api/sf', 'text_update', [projectId, model], callback);
		};
		this.remove = function(projectId, textIds, callback) {
			jsonRpc.call('/api/sf', 'text_delete', [projectId, textIds], callback);
		};
		this.list = function(projectId, callback) {
			jsonRpc.call('/api/sf', 'text_list_dto', [projectId], callback);
		};
		this.settings_dto = function(projectId, textId, callback) {
			jsonRpc.call('/api/sf', 'text_settings_dto', [projectId, textId], callback);
		};
	}])
	.service('questionsService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, questionId, callback) {
			jsonRpc.call('/api/sf', 'question_read', [projectId, questionId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('/api/sf', 'question_update', [projectId, model], callback);
		};
		this.remove = function(projectId, questionIds, callback) {
			jsonRpc.call('/api/sf', 'question_delete', [projectId, questionIds], callback);
		};
		this.list = function(projectId, textId, callback) {
			jsonRpc.call('/api/sf', 'question_list_dto', [projectId, textId], callback);
		};
	}])
	.service('questionService', ['jsonRpc', function(jsonRpc) {
		this.read = function(projectId, questionId, callback) {
			jsonRpc.call('/api/sf', 'question_comment_dto', [projectId, questionId], callback);
		};
		this.update = function(projectId, model, callback) {
			jsonRpc.call('/api/sf', 'question_update', [projectId, model], callback);
		};
		this.update_answer = function(projectId, questionId, model, callback) {
			jsonRpc.call('/api/sf', 'question_update_answer', [projectId, questionId, model], callback);
		};
		this.remove_answer = function(projectId, questionId, answerId, callback) {
			jsonRpc.call('/api/sf', 'question_remove_answer', [projectId, questionId, answerId], callback);
		};
		this.update_comment = function(projectId, questionId, answerId, model, callback) {
			jsonRpc.call('/api/sf', 'question_update_comment', [projectId, questionId, answerId, model], callback);
		};
		this.remove_comment = function(projectId, questionId, answerId, commentId, callback) {
			jsonRpc.call('/api/sf', 'question_remove_comment', [projectId, questionId, answerId, commentId], callback);
		};
	}])
	.service('activityPageService', ['jsonRpc', function(jsonRpc) {
		this.list_activity = function(offset, count, callback) {
			jsonRpc.call('/api/sf', 'activity_list_dto', [offset, count], callback);
		};
	}])
	.service('sessionService', ['jsonRpc', function(jsonRpc) {
		this.currentUserId = function() {
			return window.session.userId;
		};
		
		this.realm = {
			SITE: function() { return window.session.userSiteRights; }
		};
		this.domain = {
				ANY:       function() { return 100;},
				USERS:     function() { return 110;},
				PROJECTS:  function() { return 120;},
				TEXTS:     function() { return 130;},
				QUESTIONS: function() { return 140;},
				ANSWERS:   function() { return 150;},
				COMMENTS:  function() { return 160;}
		};
		this.operation = {
				CREATE:       function() { return 1;},
				EDIT_OWN:     function() { return 2;},
				EDIT_OTHER:   function() { return 3;},
				DELETE_OWN:   function() { return 4;},
				DELETE_OTHER: function() { return 5;},
				LOCK:         function() { return 6;}
		};
		
		this.hasRight = function(rights, domain, operation) {
			var right = domain() + operation();
			return rights.indexOf(right) != -1;
		};
		
		this.session = function() {
			return window.session;
		};
		
		this.getCaptchaSrc = function(callback) {
			jsonRpc.call('/api/sf', 'get_captcha_src', [], callback);
		};
	}])
	// TODO move this at least into sfchecks/common/js/services.  Might not really carry its weight. CP 2013-09
	.service('linkService', function() {
		this.href = function(url, text) {
			return '<a href="' + url + '">' + text + '</a>';
		};
		
		this.project = function(projectId) {
			return '/app/sfchecks#/project/' + projectId;
			
		};
		
		this.text = function(projectId, textId) {
			return this.project(projectId) + "/" + textId;
		};
		
		this.question = function(projectId, textId, questionId) {
			return this.text(projectId, textId) + "/" + questionId;
		};
		
		this.user = function(userId) {
			return '/app/userprofile/' + userId;
		};
	})
	;
