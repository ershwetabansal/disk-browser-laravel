var util = require('../helpers/util.js');
var element = require('../helpers/element.js');
var reqHandler = require('../handlers/handler.js');

var disks = {};
var defaultPathParam = {
    relative : true
};
var defaultSearch = false;
/****************************************************
** Constructor for disk function class
*****************************************************/

function disk() {
    return {
        loadDisks : loadDisks,
        noDiskSetup : noDiskSetup,
        getCurrentDisk : getCurrentDisk,
        getRootPath : getRootPath
    };
}

/****************************************************
** Load Disks as nav bar from user defined disk data
*****************************************************/

function loadDisks(diskData) {

    addDisksElements();
    reqHandler.attachDiskElementEvents();

    function addDisksElements() {

        var diskElement = element.getDiskNavbar();
        diskElement.empty();
        disks = {};
        for (var i=0, len=diskData.length; i < len; i++) {
            var disk = diskData[i];
            disk.id = 'disk_' + util.slugify(disk.name);
            diskElement.append($(getDiskNavElement(diskData[i])));
            disk.path = disk.path || defaultPathParam;
            disks[disk.id] = disk;
        }
    }

    function getDiskNavElement(disk) {

        return '<li role="presentation" id="'+disk.id+'"><a role="tab" data-toggle="tab" >' + disk.label + '</a></li>';
    
    }

}


function noDiskSetup(object) {

    disks = {
        disk_1 : {
            id : 'disk_1',
            search : object.search || defaultSearch,
            path : object.path || defaultPathParam
        }
    };
}

/****************************************************
** Get currently selected disk data
*****************************************************/
function getCurrentDisk() {
    
    var selectedDisk = element.getDiskNavbar().find('li.active');
    if (selectedDisk.length > 0) {
        var disk_name = selectedDisk.attr('id');
        return disks[disk_name];
    } else {
        return disks['disk_1'];
    }
}

function getRootPath() {

    var currentDisk = getCurrentDisk();
    if (currentDisk) {
        var relative = currentDisk.path.relative;
        var root = currentDisk.path.root;
        var cookie = currentDisk.path.cookie;
        if (root && root != '') {
            return root;
        } else if (relative == true) {
            return currentDisk.name;
        } else if (cookie && cookie != '') {
            return util.getCookie(cookie);
        }
    }
}

module.exports = disk;