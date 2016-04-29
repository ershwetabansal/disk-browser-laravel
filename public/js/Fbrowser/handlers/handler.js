var element = require('../helpers/element.js');
var util = require('../helpers/util.js');
var mock = require('../mock/mock.js');

var diskHandler, dirHandler, fileHandler, eventHandler;
var disksParam, directoriesParam, filesParam, httpParams, authParams;

/************************************************
* Setup
************************************************/
function setupHandlers(disk, dir, file, eventH) {
	diskHandler = disk;
	dirHandler = dir;
	fileHandler = file;
	eventHandler = eventH;
}

function setupParameters(disk, dir, files, http, auth) {
	disksParam = disk;
	directoriesParam = dir;
	filesParam = files;
    httpParams = http;
    authParams = auth;
}

function setupElements() {
//Show/Hide manager controls and attach corresponding events
	createDirectorySetup();		
	uploadFileSetup();

	function createDirectorySetup() {
		var createDirBtn = element.getCreateNewDirectory();
		(directoriesParam.create) ? element.show(createDirBtn) : element.hide(createDirBtn);
	}

	function uploadFileSetup() {
		var uploadBtn = element.getUploadFileBtn();
		if (filesParam.upload) {
			loadUploadFormParameters();
			element.show(uploadBtn);
		} else {
			element.hide(uploadBtn);
		}

		function loadUploadFormParameters() {
			if (doesUploadParamExist()) {
				for (var i=0, len = filesParam.upload.params.length; i <len; i++) {
					var param = filesParam.upload.params[i];
					element.getUploadFileParameterContainer().append($(getFormElement(param)));
				}
			}
		}

		function getFormElement(param) {
			return '<input type="text" placeholder="'+param.label+'" name="'+param.name+'" class="form-control"/>'
		}

		function doesUploadParamExist() {
			return typeof(filesParam.upload) == 'object' && filesParam.upload.params;
		}
	}
}

function setupEvents() {
	//Create Directory Event
	if (directoriesParam.create) {
		eventHandler.attachCreateDirectoryEvent(directoriesParam.create);
	}

	//upload File event
	if (filesParam.upload) {
		eventHandler.attachUploadFileEvent(filesParam.upload);
	}
	
	//Attach events to other toolbar buttons
	eventHandler.attachRefreshFilesEvent();
	eventHandler.attachSortFilesEvent();
	eventHandler.attachAlignFilesAsListEvent();
	eventHandler.attachAlignFilesAsGridEvent();

	//Setup search events	
	eventHandler.attachSearchFilesEvent(disksParam && disksParam.search);

}

/************************************************
* Load Disks, directories and files
************************************************/

function load() {
	eventHandler.resetView();
	loadDisks();
	loadDirectories();
}

function loadDisks() {
	if (disksParam && disksParam.details && disksParam.details.length > 0) {
		diskHandler.loadDisks(disksParam.details);
	} else {
        diskHandler.noDiskSetup(disksParam);
    }
}

function loadDirectories() {
	makeAjaxRequest(directoriesParam.list, success, fail);

	function success(data) {
        dirHandler.loadDirectories(data);
        loadFiles();
	}
	function fail() {
		dirHandler.loadDirectories(mock.directories);
        loadFiles();
	}
}

function loadFiles(isRefresh) {
    var cache = (isRefresh != true);
	makeAjaxRequest(filesParam.list, success, fail, cache);

	function success(data) {
		fileHandler.loadFiles(data);
		setupSortDropdown();
	}
	function fail() {
		fileHandler.loadFiles(mock.files);
		setupSortDropdown();		
	}
}

function setupSortDropdown() {
	element.getSortFilesDropdown().empty();
	element.getSortFilesDropdown().append(getOptionElement('Sort by', ''));

	var responseParams = getFileResponseParams();
	for (var key in responseParams) {
		var param = responseParams[key];
		element.getSortFilesDropdown().append(getOptionElement(param, key));
	}

	function getOptionElement(show, value) {
		return $('<option value="'+value+'">'+show+'</option>');
	}
}

/************************************************
* Setup events for disks, directories and files
************************************************/

function attachDiskElementEvents() {
	element.selectFirst(element.getDiskNavbar());
	eventHandler.attachDiskElementEvent();
}

function attachDirectoryEvents(dirElement) {

	eventHandler.attachKeysEventOnDirectories(dirElement);
	eventHandler.attachClickEventOnDirectories(dirElement);

	renameDirectorySetup();	
	deleteDirectorySetup();

	function renameDirectorySetup() {
		if (directoriesParam.update) {
			eventHandler.attachRenameDirectoryEvent(dirElement, directoriesParam.update);
		}
	}

	function deleteDirectorySetup() {
		var deleteDirBtn = element.getDeleteDirectory();
		if (directoriesParam.destroy) {
			element.show(deleteDirBtn);
			eventHandler.attachDeleteDirectoryEvent();
		} else {
			element.hide(deleteDirBtn);
		}
	}
}

function attachFileEvents() {
	eventHandler.attachKeysEventOnFiles();
	eventHandler.attachClickEventOnFiles();
	eventHandler.attachSortEventOnHeader();
	eventHandler.attachFileContextMenuEvent();
	eventHandler.attachFileManageMenuEvent();

	renameFileSetup();	
	deleteFileSetup();
	downloadFileSetup();
	viewFileSetup();

	function renameFileSetup() {
		var renameBtn = element.getRenameFile();
		if (filesParam.update) {
			element.show(renameBtn);
			eventHandler.attachRenameFileEvent(filesParam.update);
		} else {
			element.hide(renameBtn);
		}
	}

	function deleteFileSetup() {
		var deleteBtn = element.getRemoveFile();
		if (filesParam.destroy) {
			element.show(deleteBtn);
			eventHandler.attachRemoveFileEvent(filesParam.destroy);
		} else {
			element.hide(deleteBtn);
		}
	}

	function downloadFileSetup() {
		var downloadBtn = element.getDownloadFile();
		if (filesParam.absolute_path != false || filesParam.download) {
			element.show(downloadBtn);
			var isAbsolutePath = (filesParam.absolute_path != false);
			eventHandler.attachDownloadFileEvent(isAbsolutePath);
		} else {
			element.hide(downloadBtn);
		}
	}

	function viewFileSetup() {
		var viewBtn = element.getViewFile();
		if (filesParam.view) {
			element.show(viewBtn);
			eventHandler.attachViewFileEvent();
		} else {
			element.hide(viewBtn);
		}
	}
}

/************************************************
* Return handlers and setup object parameters
************************************************/

function getFileHandler() {
	return fileHandler;
}

function getDirHandler() {
	return dirHandler;
}

function getDiskHandler() {
	return diskHandler;
}

function isSearchEnabled() {
	return disksParam && disksParam.search == true;
}

function getDiskParameter() {
	return disksParam;
}

/************************************************
 * Handle paths and ajax request
 ************************************************/

function getAbsolutePath(file, path) {
    if (typeof(path) != 'undefined') {
        return path + "/" + file.name;
    } else {
        return file.path;
    }
}

function getRootPathForCurrentDir() {

    var root = diskHandler.getRootPath();
    if (typeof(root) != 'undefined') {
        return root + dirHandler.getCurrentDirectoryPath();
    }
}

function getCurrentFilePath() {
    return getAbsolutePath(fileHandler.getCurrentFileDetails(), getRootPathForCurrentDir());
}

function makeAjaxRequest(url, successCallback, failureCallback, cache, data, isUpload) {

    var method = 'POST';

    if (typeof(cache) == 'undefined') {
        cache = true;
    }
    var params = (isUpload != true) ? addCommonParameters(data) : addCommonParametersToFormData(data);

    $.ajax(getAjaxParameters()).success(function (data) {
        if (successCallback) successCallback(data);
    }).fail(function () {
        if (failureCallback) failureCallback();
    });

    function getAjaxParameters() {
        var parameters = {
            url : url,
            method : method,
            data : params,
            cache: cache,
            beforeSend: function (request)
            {
                if (httpParams && httpParams.headers) {
                    for (var key in httpParams.headers) {
                        request.setRequestHeader(key, httpParams.headers[key]);
                    }
                }
            }
        };

        if (isUpload == true) {
            parameters.processData = false;
            parameters.contentType = false;
        }
        return parameters;
    }

}


function addCommonParametersToFormData(formData) {
	var params = addCommonParameters();
	for (var key in params) {
		formData.append(key, params[key]);
	}

	return formData;
}

function addCommonParameters(params) {

    params = params || {};
    var disk = diskHandler.getCurrentDisk();
    if (disk) {
        params.disk = disk.name;
    }

    var dirPath = dirHandler.getCurrentDirectoryPath();
    dirPath =  dirPath || '/';
    if (dirPath) {
        params.path = dirPath;
    }

    return params;
}

/****************************************************
 ** file response parameters
 *****************************************************/

function getFileResponseParams() {
    return {
        name : 'Name',
        type : 'Type',
        size : 'size' + ((filesParam.size_unit) ? ('('+filesParam.size_unit+')') : ''),
        last_modified_date : 'Last Modified Date'
    };
}

function updateButtonDetails(details) {
    if (details && details.text && details.onClick) {
    	details.text = details.text || 'Fetch path';
        element.getPrimarySubmitButton().text(details.text);
        element.getPrimarySubmitButton().off('click');
        element.getPrimarySubmitButton().click(function() {
            if (details.onClick) {details.onClick(getCurrentFilePath());}
            element.hide(element.getPrimarySubmitButton());
            element.closeModal();
        });
    }
}

module.exports = {
	setupHandlers: setupHandlers,
	setupParameters: setupParameters,
	setupElements: setupElements,
	setupEvents: setupEvents,

	load: load,
	loadFiles: loadFiles,
    loadDirectories: loadDirectories,

	getFileHandler: getFileHandler,
	getDiskHandler: getDiskHandler,
	getDirHandler: getDirHandler,

	getDiskParameter: getDiskParameter,
	isSearchEnabled: isSearchEnabled,

	attachDiskElementEvents: attachDiskElementEvents,
	attachDirectoryEvents: attachDirectoryEvents,
	attachFileEvents: attachFileEvents,

	getAbsolutePath: getAbsolutePath,
    makeAjaxRequest: makeAjaxRequest,
    getRootPathForCurrentDir: getRootPathForCurrentDir,
    getCurrentFilePath: getCurrentFilePath,
    getFileResponseParams: getFileResponseParams,
    updateButtonDetails : updateButtonDetails

}