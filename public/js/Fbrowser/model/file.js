var element = require('../helpers/element.js');
var util = require('../helpers/util.js');
var reqHandler = require('../handlers/handler.js');

function file() {

    var directory_files_array = [];
    var current_files_array = [];

    var currentView = 'grid';
    
/****************************************************
** Load files
*****************************************************/

	function loadFiles(data) {
        currentView = 'grid';
        directory_files_array = data;
        showFiles();
    }

    //Show files function can be called when we click on the library or
    // when we search a file
    function showFiles(filesArray) {
        resetFiles();
        current_files_array = (filesArray) ? filesArray : JSON.parse(JSON.stringify(directory_files_array));
        loadFileList(current_files_array);
        loadFileGrid(current_files_array);
        show();
        reqHandler.attachFileEvents();
         

        function loadFileGrid(filesArray) {
            var rootPath = reqHandler.getRootPathForCurrentDir();

            for (var i=0, len = filesArray.length; i < len; i++) {
                var file = filesArray[i];
                file.id = util.slugify(file.name);
                var gridElements = '<li id="'+file.id+'" tabindex="1"><div>';
                file.type = file.type || util.getFileType(file.name);

                var path = reqHandler.getAbsolutePath(file, file.path || rootPath);

                gridElements += (util.isImage(file.type)) ? '<img src="' + path + '" alt="' + file.name + '"/>'
                                :
                                '<i class="big-icon fa ' + util.getFontAwesomeClass(file.type) + ' fa-3x"></i>';
                gridElements += '<div>' + file.name + '</div>';

                gridElements += '</li>';
                element.getFilesGrid().append($(gridElements));
            }
        }
       
        function loadFileList(filesArray) {
            appendFileHeader();
            var tableBody = element.getFilesList().find('tbody');
            for (var i=0, len = filesArray.length; i < len; i++) {
                var file = filesArray[i];
                file.id = util.slugify(file.name);
                file.type = file.type || util.getFileType(file.name);

                var listElements = '<tr id="'+file.id+'" tabindex="1">';

                for (var key in reqHandler.getFileResponseParams()) {
                    listElements += '<td>';
                    if (key == 'name') {
                        listElements += '<i class="small-icon fa ' + util.getFontAwesomeClass(file.type) + '"></i>';
                    }
                    listElements += file[key];
                    listElements += '</td>';
                }

                listElements += '</tr>';

                tableBody.append($(listElements));
            }

            function appendFileHeader() {
                var headerElement = $('<thead></thead>');
                var rowElement = $('<tr></tr>');
                var thElement = '';
                for (var key in reqHandler.getFileResponseParams()) {
                    thElement += '<th id="'+key+'">' + reqHandler.getFileResponseParams()[key] + '<span></span></th>';
                }

                rowElement.append($(thElement));
                headerElement.append(rowElement);

                element.getFilesList().append(headerElement);
                element.getFilesList().append($('<tbody></tbody>'));
            }
        }

        function show() {
            if (currentView == 'grid') {
                element.show(element.getFilesGrid());
            } else {
                element.show(element.getFilesList());
            }
        }

        function resetFiles() {
            element.getFilesGrid().empty();
            element.getFilesList().empty();

            element.hide(element.getFilesGrid());
            element.hide(element.getFilesList());
        }

    }

/****************************************************
** Show files as list and grid
*****************************************************/

    function showFileList() {
        currentView = 'list';
        element.hide(element.getFilesGrid());
        element.show(element.getFilesList());
    }

    function showFileGrid() {
        currentView = 'grid';
        element.show(element.getFilesGrid());
        element.hide(element.getFilesList());
    }

/****************************************************
** Sort files by selected type
*****************************************************/

    function sortFilesBy(type, isAsc) {
        isAsc = (typeof(isAsc) == "undefined") ? true : isAsc;

        cleanUpView(true);
        if (type == '') {
            showFiles();
            updateAscDescOrderClass();
        } else {
            var sortedFiles = util.sortByType(current_files_array, type, isAsc);
            showFiles(sortedFiles);
            updateAscDescOrderClass();
        }

        function updateAscDescOrderClass()
        {
            element.getFilesList().find('th').each(function(){
                var element = $(this);
                element.removeClass('asc').removeClass('desc');
                if (element.attr('id') == type && type != '') {
                    (isAsc) ? element.addClass('asc') : element.addClass('desc');
                    return false;
                }
            });
        }

    }


/****************************************************
** Search files
*****************************************************/

    function searchFiles(text) {
        var searchedFiles = [];
        for (var i=0, len = current_files_array.length; i < len; i++) {
            var file = current_files_array[i];
            if (file.name.toLowerCase().indexOf(text.toLowerCase()) > - 1) {
                searchedFiles.push(file);
            }
        }
        showFiles(searchedFiles);
    }

/****************************************************
** Show and hide file details
*****************************************************/

    function showFileDetails(file) {
        var fileDetails = element.getFileDetailsDiv();
        fileDetails.empty();
        element.show(fileDetails);
        for (var key in reqHandler.getFileResponseParams()) {
            fileDetails.append($(getFileDetailElement(reqHandler.getFileResponseParams()[key], file[key])));
        }

        function getFileDetailElement(key, value) {
	    	return '<li>' + 
	    	'<label>'+key+':&nbsp;</label>' +
	    	'<span>'+value+'</span>' + 
	    	'<span>&nbsp;</span>' + 
	    	'</li>'
	    }
    }

    function hideFileDetails() {
        var fileDetails = element.getFileDetailsDiv();
        fileDetails.empty();
        element.hide(fileDetails);
    }

/****************************************************
** Get current file element and details
*****************************************************/
    function getCurrentFileDetails() {

        var fileList = (currentView =='list') ? element.getFilesList() : element.getFilesGrid();
        var fileElement = fileList.find('.active');
        var id = fileElement.attr('id');
        for (var i=0, len = current_files_array.length; i < len; i++) {
            var file = current_files_array[i];
            if (file.id == id) {
                return file;
            }
        }
        return {};
    }

    function getCurrentFileElement() {

        var fileList = (currentView =='list') ? element.getFilesList() : element.getFilesGrid();
        return fileList.find('.active');
    }

    function cleanUpView(fromSort) {

        if (!fromSort) {
            element.getSortFilesDropdown().val('');
        }
        element.unselect(getCurrentFileElement());
        hideFileDetails();
        element.hide(element.getFileManageMenu());

    }

    function focusFirstElement() {
        var firstElement;
        if (currentView == 'list') {
            firstElement = element.getFilesList().find('tbody > tr:first-child');
        } else {
            firstElement = element.getFilesGrid().find('li:first-child');
        }

        firstElement.click();
        firstElement.focus();
    }

    function addFileOnUpload(file) {
        directory_files_array.push(file);
        showFiles();
    }
/****************************************************
** 
*****************************************************/

    return {
    	loadFiles : loadFiles,
        showFiles : showFiles,
        showFileList : showFileList,
        showFileGrid : showFileGrid,

        searchFiles : searchFiles,
        sortFilesBy : sortFilesBy,

        showFileDetails : showFileDetails,
        hideFileDetails : hideFileDetails,

        getCurrentFileDetails : getCurrentFileDetails,
        getCurrentFileElement : getCurrentFileElement,

        cleanUpView : cleanUpView,
        focusFirstElement: focusFirstElement,
        addFileOnUpload: addFileOnUpload
        
    };
}
module.exports = file;