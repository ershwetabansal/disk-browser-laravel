var util = require('../helpers/util.js');
var element = require('../helpers/element.js');
var mock = require('../mock/mock.js');
var reqHandler = require('../handlers/handler.js');

var directoriesData = {};

/****************************************************
** Constructor for disk function class
*****************************************************/
function directory() {
    return {
        loadDirectories : loadDirectories,
        showSubDirectories : showSubDirectories,
        hideSubDirectories : hideSubDirectories,
        
        addNewDirectoryToSelectedDirectory : addNewDirectoryToSelectedDirectory,
        getNewDirectoryData : getNewDirectoryData,

        saveDirectory : saveDirectory,
        removeDirectory : removeDirectory,

        renameDirectory : renameDirectory,

        getCurrentDirectory : getCurrentDirectory,
        getCurrentDirectoryPath : getCurrentDirectoryPath,
        childDirOpen : childDirOpen,
        isRootDirectory : isRootDirectory
        
    };
}

/****************************************************
** Load directories and sub directories
*****************************************************/

function loadDirectories(data) {
    directoriesData = {};
    addDirectoriesElements(element.getDirectories(), data, true);
    reqHandler.attachDirectoryEvents(element.getDirectories());
    element.selectFirst(element.getDirectories());
}

function showSubDirectories(liElement) {
    if (liElement.find('ul').length == 0) {
        var directory = getDirectoryData(liElement);

        if (directory && directory.directories && directory.directories.length > 0) {
            liElement.append($('<ul></ul>'));
            addDirectoriesElements(liElement.find('ul'), directory.directories, false);
            reqHandler.attachDirectoryEvents(liElement.find('ul'));
        }
    }
}

function hideSubDirectories(liElement) {
    liElement.find('ul').remove();
}

function addDirectoriesElements(directoryUlElement, directories, isRoot) {
    directoryUlElement.empty();
    
    if (isRoot) addRootDirTo(directoryUlElement);

    for (var i=0, len = directories.length; i < len; i++) {
        var directory = directories[i];
        directory.id = util.slugify(directory.name);
        var li = getDirectoryElement(directory);
        directoryUlElement.append($(li));

        directoriesData[directory.id] = directory;
    }
}

/****************************************************
** Rename directory
*****************************************************/

function renameDirectory (dirElement) {
    var editable = dirElement.find('span.editable');
    editable.replaceWith('<input value="' + editable.text() + '"/>');
    var inputElement = dirElement.find('input');
    return inputElement;
}

/****************************************************
** Create new directory
*****************************************************/
function addNewDirectoryToSelectedDirectory() {
    var selectedDir = getCurrentDirectory().element;
    var parentDir;

    if (isRootDirectory(selectedDir)) {
        parentDir = selectedDir.closest('ul');
    } else {
        if (selectedDir.find('> ul').length == 0) {
            selectedDir.append($('<ul></ul>'));
        }
        parentDir = selectedDir.find('> ul');
    }
    parentDir.append($(getNewDirectoryElement()));

    return parentDir.find('input');
}

function getNewDirectoryData(inputElement) {
    var parent_dir = getDirectoryData(inputElement.closest('ul').closest('li'));
    return {
        name : inputElement.val()
    }
}

function saveDirectory(inputElement, value) {
    if (!value) value = inputElement.val();
    var directoryBox = inputElement.closest('div')
    directoryBox.attr('id', util.slugify(value));
    inputElement.replaceWith('<span class="editable">' + value+ '</span>');
    directory.name = value;
    directoriesData[util.slugify(value)] = directory;
    return directoryBox.closest('li');
}

function removeDirectory(element) {
    var liElement = element;
    if (!element.is('li')) {
        liElement = element.closest('li');
    }
    liElement.remove();
}

/****************************************************
** Support functions
*****************************************************/

function isRootDirectory(element) {
    return element.find('>div').attr('id') == '-root-';
}

function getCurrentDirectory() {
    var dir = element.getDirectories().find('li.active');
    return {
        data : getDirectoryData(dir),
        element : dir
    }
}

function getCurrentDirectoryPath() {
    var currentElement = getCurrentDirectory().element;

    if (currentElement && currentElement.length > 0) {
        if (isRootDirectory(currentElement)) {
            return '';
        } else {
            var pathArray = getMainDirectory(currentElement, []);
            var path = '';
            for (var i = pathArray.length - 1; i >= 0; i--) {
                path += '/' + pathArray[i] ;
            }
            return path;
        }
    }
}

function getMainDirectory(element, path) {
    var parent = element.parent().closest('li');
    if (parent.length > 0 && !element.is(parent)) {
        path.push(getDirectoryData(element).name);
        return getMainDirectory(parent, path);
    } else {
        path.push(getDirectoryData(element).name);
        return path;
    }
}

function getDirectoryData(liElement) {
    var dir_name = liElement.find('> div').attr('id');
    if (dir_name) return directoriesData[dir_name];
}

function childDirOpen(liElement) {
    return (liElement.find('ul').length > 0);
}

function addRootDirTo(directoryUlElement) {
    var rootDir = {id : '-root-', name : "..", path: ""};
    directoriesData[rootDir.id] = rootDir;

    var li = getDirectoryElement(rootDir);
    directoryUlElement.append($(li));
}

function getDirectoryElement(directory) {
    return '<li tabindex="1" >' +
    '<div id="' + directory.id + '">' +
    '<i class="fa fa-folder"></i>' +
    '<span class="editable">' + directory.name + '</span>' +
    '</div>' +
    '</li>';
}

function getNewDirectoryElement(directory) {
    return '<li tabindex="1" >' +
    '<div>' +
    '<i class="fa fa-folder"></i>' +
    '<input value="untitled"/>' +
    '</div>' +
    '</li>';
}

function isRootDirectory() {
    return (getCurrentDirectory().data.id == '-root-');
}

module.exports = directory;
