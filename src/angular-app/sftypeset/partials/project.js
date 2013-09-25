'use strict';

angular.module(
		'sftypeset.project',
		[ 'sf.services', 'palaso.ui.listview', 'palaso.ui.typeahead', 'ui.bootstrap', 'sgw.ui.breadcrumb' ]
	)
	.controller('ProjectCtrl', ['$scope', 'groupService', '$routeParams', 'sessionService', 'breadcrumbService',
	                            function($scope, groupService, $routeParams, ss, breadcrumbService) {
		var projectId = $routeParams.projectId;
		$scope.projectId = projectId;
		
		// Rights
		$scope.rights = {};
		$scope.rights.deleteOther = false; 
		$scope.rights.create = false; 
		$scope.rights.editOther = false; //ss.hasRight(ss.realm.SITE(), ss.domain.PROJECTS, ss.operation.EDIT_OTHER);
		$scope.rights.showControlBar = $scope.rights.deleteOther || $scope.rights.create || $scope.rights.editOther;

		// Breadcrumb
		breadcrumbService.set('top',
				[
				 {href: '/app/sftypeset#/projects', label: 'My Projects'},
				 {href: '/app/sftypeset#/project/' + $routeParams.projectId, label: 'unknown'},
				]
		);

		// DTO Data
		$scope.groups = [];
		var getDto = function() {
			console.log("getDto()");
			groupService.list(projectId, function(result) {
				if (result.ok) {
					$scope.groups = result.data.entries;
					$scope.groupsCount = result.data.count;

					$scope.project = result.data.project;
					$scope.project.settingsUrl = 'sftypeset#/project/' + $scope.project.id + '/settings';
					breadcrumbService.updateCrumb('top', 1, {label: $scope.project.name});

					var rights = result.data.rights;
					$scope.rights.deleteOther = ss.hasRight(rights, ss.domain.TEXTS, ss.operation.DELETE_OTHER); 
					$scope.rights.create = ss.hasRight(rights, ss.domain.TEXTS, ss.operation.CREATE); 
					$scope.rights.editOther = ss.hasRight(ss.realm.SITE(), ss.domain.PROJECTS, ss.operation.EDIT_OTHER);
					$scope.rights.showControlBar = $scope.rights.deleteOther || $scope.rights.create || $scope.rights.editOther;
				}
			});
		};
		getDto();
		
		
	}])
	.controller('ProjectSettingsCtrl', ['$scope', '$location', '$routeParams', 'breadcrumbService', 'userService', 'projectService', 'sessionService',
	                                 function($scope, $location, $routeParams, breadcrumbService, userService, projectService, ss) {
		var projectId = $routeParams.projectId;
		$scope.project = {};
		$scope.project.id = projectId;

		// Breadcrumb
		breadcrumbService.set('top',
				[
				 {href: '/app/sftypeset#/projects', label: 'My Projects'},
				 {href: '/app/sftypeset#/project/' + $routeParams.projectId, label: ''},
				 {href: '/app/sftypeset#/project/' + $routeParams.projectId + '/settings', label: 'Settings'},
				]
		);

		$scope.updateProject = function() {
			var newProject = {
				id: $scope.project.id,
				projectname: $scope.project.name
			};
			projectService.update(newProject, function(result) {
				if (result.ok) {
					console.log('Updated OK');
				}
			});
		};
	
		// ----------------------------------------------------------
		// List
		// ----------------------------------------------------------
		$scope.selected = [];
		$scope.updateSelection = function(event, item) {
			var selectedIndex = $scope.selected.indexOf(item);
			var checkbox = event.target;
			if (checkbox.checked && selectedIndex == -1) {
				$scope.selected.push(item);
			} else if (!checkbox.checked && selectedIndex != -1) {
				$scope.selected.splice(selectedIndex, 1);
			}
		};
		$scope.isSelected = function(item) {
			return item != null && $scope.selected.indexOf(item) >= 0;
		};
		
		$scope.users = [];
		$scope.queryProjectUsers = function() {
			projectService.listUsers($scope.project.id, function(result) {
				if (result.ok) {
					$scope.project.name = result.data.projectName;
					$scope.project.users = result.data.entries;
					$scope.project.userCount = result.data.count;
					// Rights
					var rights = result.data.rights;
					$scope.rights = {};
					$scope.rights.deleteOther = ss.hasRight(rights, ss.domain.USERS, ss.operation.DELETE_OTHER); 
					$scope.rights.create = ss.hasRight(rights, ss.domain.USERS, ss.operation.CREATE); 
					$scope.rights.editOther = ss.hasRight(rights, ss.domain.USERS, ss.operation.EDIT_OTHER);
					$scope.rights.showControlBar = $scope.rights.deleteOther || $scope.rights.create || $scope.rights.editOther;
					// Breadcrumb
					breadcrumbService.updateCrumb('top', 1, {label: result.data.bcs.project.crumb});
					
				}
			});
		};
		
		$scope.removeProjectUsers = function() {
			console.log("removeUsers");
			var userIds = [];
			for(var i = 0, l = $scope.selected.length; i < l; i++) {
				userIds.push($scope.selected[i].id);
			}
			if (l == 0) {
				// TODO ERROR
				return;
			}
			projectService.removeUsers($scope.project.id, userIds, function(result) {
				if (result.ok) {
					$scope.queryProjectUsers();
					// TODO
				}
			});
		};
		
		// Roles in list
		$scope.roles = [
	        {key: 'user', name: 'User'},
	        {key: 'project_admin', name: 'Project Admin'}
        ];
		
		$scope.onRoleChange = function(user) {
			var model = {};
			model.id = user.id;
			model.role = user.role;
			console.log('userchange...', model);
			projectService.updateUser($scope.project.id, model, function(result) {
				if (result.ok) {
					// TODO broadcast notice
					console.log('userchanged');
				}
			});
		};
		
		// ----------------------------------------------------------
		// Typeahead
		// ----------------------------------------------------------
	    $scope.users = [];
	    $scope.addModes = {
	    	'addNew': { 'en': 'Create New', 'icon': 'icon-user'},
	    	'addExisting' : { 'en': 'Add Existing', 'icon': 'icon-user'},
	    	'invite': { 'en': 'Send Invite', 'icon': 'icon-envelope'}
	    };
	    $scope.addMode = 'addNew';
	    $scope.typeahead = {};
	    $scope.typeahead.userName = '';
		
		$scope.queryUser = function(userName) {
			console.log('searching for ', userName);
			userService.typeahead(userName, function(result) {
				// TODO Check userName == controller view value (cf bootstrap typeahead) else abandon.
				if (result.ok) {
					$scope.users = result.data.entries;
					$scope.updateAddMode();
				}
			});
		};
		$scope.addModeText = function(addMode) {
			return $scope.addModes[addMode].en;
		};
		$scope.addModeIcon = function(addMode) {
			return $scope.addModes[addMode].icon;
		};
		$scope.updateAddMode = function(newMode) {
			if (newMode in $scope.addModes) {
				$scope.addMode = newMode;
			} else {
				// This also covers the case where newMode is undefined
				$scope.calculateAddMode();
			}
		}
		$scope.calculateAddMode = function() {
			// TODO This isn't adequate.  Need to watch the 'typeahead.userName' and 'selection' also. CP 2013-07
			if ($scope.typeahead.userName.indexOf('@') != -1) {
				$scope.addMode = 'invite';
			} else if ($scope.users.length == 0) {
				$scope.addMode = 'addNew';
			} else if (!$scope.typeahead.userName) {
				$scope.addMode = 'addNew';
			} else {
				$scope.addMode = 'addExisting';
			}
		};
		
		$scope.addProjectUser = function() {
			var model = {};
			if ($scope.addMode == 'addNew') {
				model.name = $scope.typeahead.userName;
			} else if ($scope.addMode == 'addExisting') {
				model.id = $scope.user.id;
			} else if ($scope.addMode == 'invite') {
				model.email = $scope.typeahead.userName;
			}
			console.log("addUser ", model);
			projectService.updateUser($scope.project.id, model, function(result) {
				if (result.ok) {
					// TODO broadcast notice and add
					$scope.queryProjectUsers();
				}
			});
		};
	
		$scope.selectUser = function(item) {
			console.log('user selected', item);
			$scope.user = item;
			$scope.typeahead.userName = item.name;
			$scope.updateAddMode('addExisting');
		};
	
		$scope.imageSource = function(avatarRef) {
			return avatarRef ? '/images/avatar/' + avatarRef : '/images/avatar/anonymous02.png';
		};
	
	}])
	;
