var browser = new FileBrowser();
var util = new FileBrowserUtility();

function FileBrowserUtility() {
    var fbElement = $('#FileBrowser');
    var assetWindow = fbElement.find('.assets');
    var directoryWindow = fbElement.find('.directories');
    var directoriesList = directoryWindow.find('ul#directories-list');
    var assetList = assetWindow.find('#assets-list');
    var assetGrid = assetWindow.find('#assets-grid');
    var diskNavbar = directoryWindow.find('#disk_navbar');
    var rootDirectory = directoriesList.find('#root');

    function slugify(name) {
        return name.toLowerCase().replace(new RegExp(' ', 'g'), '_')
        .replace(new RegExp('/', 'g'), '_')
        .replace('.', '_')
        ;
    }

    function unSlugify(name) {
        return capitalizeFirstLetter(name.replace(new RegExp('_', 'g'), ' '));
    }

    function isImage(type) {
        var allowedImageTypes = ['jpg', 'png', 'gif', 'jpeg'];
        return (allowedImageTypes.indexOf(type) > -1);
    }

    function getFontAwesomeClass(type) {
        switch (type) {
            case 'pdf' :
                return 'fa-file-pdf-o';
            case 'jpg' :
                return 'fa-file-image-o';
            case 'png' :
                return 'fa-file-image-o';
            case 'gif' :
                return 'fa-file-image-o';
            case 'jpeg' :
                return 'fa-file-image-o';
            case 'txt' :
                return 'fa-file-text-o';
            case 'xls' :
                return 'fa-file-excel-o';
            case 'xlsx' :
                return 'fa-file-excel-o';
            case 'doc' :
                return 'fa-file-word-o';
            case 'docx' :
                return 'fa-file-word-o';
        }
    }

    function getFileBrowser() {
        return fbElement;
    }

    function getAssetWindow() {
        return assetWindow;
    }

    function getDirectoryWindow() {
        return directoryWindow;
    }

    function getAssetsList() {
        return assetList;
    }

    function getAssetsGrid() {
        return assetGrid;
    }

    function getDirectories() {
        return directoriesList;
    }

    function getDiskNavbar() {
        return diskNavbar;
    }

    function getRootDirectory() {
        return rootDirectory;
    }

    function compareAsc(a, b, prop) {
        if (a[prop] < b[prop])
            return -1;
        else if (a[prop] > b[prop])
            return 1;
        else
            return 0;
    }

    function compareDesc(a, b, prop) {
        if (a[prop] > b[prop])
            return -1;
        else if (a[prop] < b[prop])
            return 1;
        else
            return 0;
    }


    function sortByType(object, type, order) {
        return object.sort(function (a, b) {
            if (order) {
                return compareAsc(a, b, type);
            } else {
                return compareDesc(a, b, type);                
            }
        });
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    return {
        slugify: slugify,
        unSlugify: unSlugify,
        isImage: isImage,
        getFontAwesomeClass: getFontAwesomeClass,
        getFileBrowser: getFileBrowser,
        getAssetWindow: getAssetWindow,
        getDirectoryWindow: getDirectoryWindow,
        getAssetsList: getAssetsList,
        getAssetsGrid: getAssetsGrid,
        getDirectories: getDirectories,
        getRootDirectory: getRootDirectory,
        sortByType: sortByType,
        getDiskNavbar: getDiskNavbar
    }
}

function FileBrowser() {

    var localObject = {};
    var directoryHandler;
    var diskHandler, assetHandler, searchCloseBtn, searchInput, searchFileOptions;

    function setup(callbackObject) {
        searchCloseBtn = util.getFileBrowser().find('#fb_search_cancel');
        searchInput = util.getFileBrowser().find('#fb_search_input');
        searchFileOptions = util.getFileBrowser().find('#fb_file_search_options');

        localObject = callbackObject;
        if (!localObject.size_unit) localObject.size_unit = '';
        //TODO insert the modal box in the document body

        //Add the event listeners for toolbar buttons
        if (localObject.create_new_directory_url) {
            util.getFileBrowser().find('#fb_create_new_directory').click(addNewDirectory);
        } else {
            util.getFileBrowser().find('#fb_create_new_directory').addClass('hidden');
        }
        util.getFileBrowser().find('#upload_file').on('change', onFileSelect);
        util.getFileBrowser().find('#upload_file_btn').click(fileUploadButtonClicked);
        util.getFileBrowser().find('#cancel_file_upload').click(closeFileUpload);
        util.getFileBrowser().find('#upload_file_to_Server').click(onFileUpload);
        util.getFileBrowser().find('#fb_refresh').click(onRefresh);
        util.getFileBrowser().find('#fb_align_list').click(showAsList);
        util.getFileBrowser().find('#fb_align_grid').click(showAsGrid);
        util.getFileBrowser().find('#fb_sort_files').on('change', sortFiles);
        util.getFileBrowser().find('#fb_search_submit').click(searchFiles);
        util.getFileBrowser().find('#fb_search_cancel').click(closeFileSearch);
        util.getFileBrowser().find('#fb_search_input').on('change', searchFiles);

        updateSortOptions();
    }

    function addNewDirectory() {
        directoryHandler.addNewDirectory();
    }

    function fileUploadButtonClicked() {
        util.getFileBrowser().find('#upload_file').click();
    }

    function onFileSelect() {
        util.getFileBrowser().find('#file_browser_upload').removeClass('hidden');
        util.getFileBrowser().find('#upload_disk').val(diskHandler.getCurrentDisk());
        util.getFileBrowser().find('#upload_path').val(directoryHandler.getCurrentActiveDirectory().path);
        util.getFileBrowser().find('#upload_file_name').focus();
    }

    function closeFileUpload() {
        util.getFileBrowser().find('#file_browser_upload').addClass('hidden');
        util.getFileBrowser().find('#upload_file').val('');
        util.getFileBrowser().find('#upload_file_name').val('');
    }

    function onFileUpload() {
        if (isUploadFormValidated()) {
            var formData = new FormData(util.getFileBrowser().find('#file_browser_upload')[0]);
            util.getFileBrowser().find('#upload_file_loading').removeClass('hidden');
            util.getFileBrowser().find('#file_browser_upload').addClass('hidden');
            $.ajax({
                url: localObject.file_upload_url,
                type: 'POST',
                success: function() {
                    util.getFileBrowser().find('#upload_file_loading').addClass('hidden');
                    assetHandler.pullAssets();
                    closeFileUpload();
                },
                error: function() {
                    util.getFileBrowser().find('#upload_file_loading').addClass('hidden');
                    util.getFileBrowser().find('#file_browser_upload').removeClass('hidden');
                    alert("Error in uploading file");
                },
                // Form data
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            });
        } else {
            alert("Please enter required fields");
            util.getFileBrowser().find('#upload_file_name').focus();
        }
    }

    function isUploadFormValidated() {
        var file_name = util.getFileBrowser().find('#upload_file_name').val();
        return (file_name && file_name != '');
    }

    function onRefresh() {
        assetHandler.pullAssets();
    }

    function showAsList() {
        assetHandler.showAssetList();
    }

    function showAsGrid() {
        assetHandler.showAssetGrid();
    }

    function sortFiles() {
        assetHandler.sortAssetsBy($(this).val());
    }

    function closeFileSearch() {
        searchInput.val('');
        assetHandler.showAssets();
        searchCloseBtn.addClass('hidden');
        searchFileOptions.addClass('hidden');
    }

    function searchFiles(addFileOptions) {
        if (searchInput.val() == '') {
            closeFileSearch();
        } else {
            searchCloseBtn.removeClass('hidden');
            searchFileOptions.removeClass('hidden');
            assetHandler.searchAssets(searchInput.val());
            if (addFileOptions !=false && localObject.search_disk_url) {
                addFileSearchOptions();
            }
        }
    }

    function searchThisDisk(onsuccess) {
        var params = {
            search_text : searchInput.val()
        };
        if (diskHandler.getCurrentDisk()) params.disk = diskHandler.getCurrentDisk();
        $.post(localObject.search_disk_url, params,
                function (data) {
                    assetHandler.showAssets(data);
                    onsuccess();
            }).fail(function () {
                // alert("failed to search");
                //TODO Needs to be removed
                    assetHandler.showAssets(mockData().files());
                    onsuccess();
            });

    }
    
    function addFileSearchOptions() {
        searchFileOptions.removeClass('hidden');

        var thisDirectory = searchFileOptions.find('#search_this_directory');
        var thisDisk = searchFileOptions.find('#search_this_disk');

        var directory = directoryHandler.getCurrentActiveDirectory().name;
        if (!directory) directory = 'root';
        thisDirectory.text(directory);

        thisDirectory.off('click');
        thisDirectory.click(function(){
            if (!thisDirectory.hasClass('active')) {
                searchFiles(false);
                thisDirectory.addClass('active');
                thisDisk.removeClass('active');
            }
        });

        var disk = "disk : " + diskHandler.getCurrentDisk();
        if (!disk) disk = 'This disk';
        thisDisk.text(disk);
        thisDisk.off('click');
        thisDisk.click(function(){
            if (!thisDisk.hasClass('active')) {
                searchThisDisk(function(){
                    thisDisk.addClass('active');
                    thisDirectory.removeClass('active');
                });
            }
        });

    }

    function updateSortOptions() {
        var sortSelector = util.getFileBrowser().find('#fb_sort_files');

        //setup asset response parameters
        localObject.asset_response_parameters = ['name', 'size', 'type', 'last_modified_date'];
        for (var i = 0, len = localObject.asset_response_parameters.length; i < len; i++) {
            var sortType = localObject.asset_response_parameters[i];
            sortSelector.append($('<option value="' + sortType + '">' + util.unSlugify(sortType) + '</option>'));
        }
    }

    function onDiskChange() {
        refreshDirectories();
        clearAssetsResiduals();
    }

    function refreshDirectories() {
        directoryHandler.clear();
        directoryHandler.getAssetDirectories();
    }

    function clearAssetsResiduals() {
        closeFileUpload();
        closeFileSearch();
        assetHandler.hideAssetDetails();
    }

    function showPrimaryBtn(show) {
        var primary_btn = util.getFileBrowser().find('#fb-primary-btn');
        if (show) primary_btn.removeClass('hidden');
        else primary_btn.addClass('hidden');
    }

    function openFileBrowser(options) {

        function setUpPrimaryBtn() {
            console.log("asset focused");
            var primary_btn = util.getFileBrowser().find('#fb-primary-btn');
            if (options.text) {
                primary_btn.text(options.text);
            } else {
                primary_btn.text('Insert');
            }
            primary_btn.click(function(){
                onAssetSelection();
            });
        }

        function onAssetSelection() {
            console.log(" asset selected ");
            util.getFileBrowser().modal('hide');
            if(options.onClick) options.onClick(assetHandler.getAssetPath());
        }

        util.getFileBrowser().modal({
            keyboard: false,
            backdrop: 'static'
        });
        setUpPrimaryBtn(options);
        diskHandler = new DiskHandler(localObject, onDiskChange);
        assetHandler = new AssetHandler(localObject, diskHandler, showPrimaryBtn, onAssetSelection);
        directoryHandler = new DirectoryHandler(localObject, assetHandler, diskHandler, clearAssetsResiduals);
        refreshDirectories();
    }

    return {
        setup: setup,
        browse: openFileBrowser
    }
}

function DiskHandler(localObject, onrefresh) {

    var disks;
    var diskParam;
    var currentUsedDisk;
    var rootPath;
    if (!localObject.disk_options) {
        rootPath = localObject.root_directory;
    } else {

        disks = localObject.disk_options.disks;
        diskParam = localObject.disk_options.param;
        util.getDiskNavbar().empty();

        var i = 0;
        for (var key in disks) {
            if (i == 0) currentUsedDisk = key;
            util.getDiskNavbar().append($(getDiskNavElement(key, i)));
            attachDiskHandler(key);
            i++;
        }
    }

    function attachDiskHandler(disk) {

        util.getDiskNavbar().find('#disk_'+disk).click(function(){
            updateCurrentDisk(disk);
            onrefresh();
        });
    }

    function getDiskNavElement(name, index) {
        if (index == 0) {
            return '<li role="presentation" class="active"><a role="tab" data-toggle="tab" id="disk_'+name+'">' + name + '</a></li>';
        }
        return '<li role="presentation"><a role="tab" data-toggle="tab" id="disk_'+name+'">' + name + '</a></li>';
    }

    function updateCurrentDisk(disk) {
        currentUsedDisk = disk;
    }

    function getCurrentDisk() {
        return currentUsedDisk;
    }

    function getRootPath() {
        if (rootPath) {
            return removeSlash(rootPath);
        } else {
            return removeSlash(disks[currentUsedDisk]);
        }
    }

    function removeSlash(path) {
        if (path.endsWith('/'))
            return path.substr(0,path.length - 2);
        else {
            return path;
        }
    }

    function addDiskParamToObject(object) {
        if (diskParam) {
            object[diskParam] = currentUsedDisk;
        }
    }

    function addDiskParamToURL(url) {
        if (diskParam) {
            url += (url.indexOf('?') == -1) ? '?' : '&';
            url += diskParam + "=" + currentUsedDisk;
        }
        return url;
    }

    return {
        updateCurrentDisk: updateCurrentDisk,
        getCurrentDisk: getCurrentDisk,
        getRootPath: getRootPath,
        addDiskParamToObject: addDiskParamToObject,
        addDiskParamToURL: addDiskParamToURL
    }

}

function DirectoryHandler(localObject, assetHandler, diskHandler, onDirectoryChange) {
    var ulElement = util.getDirectories();

    function getAssetDirectories() {

        setUpRootDirectory();

        if (localObject.asset_directories_url) {
            $.get(diskHandler.addDiskParamToURL(localObject.asset_directories_url), function (data) {
                var directories = addRelativePathToDirectoryStructure(data, '');
                addDirectoriesTo(ulElement, directories);
                util.getRootDirectory().click();
                util.getRootDirectory().focus();
                attachKeysHandler(ulElement.find('li'));
            }).fail(function () {
                //alert("failed to access asset URL");
                //TODO need to remove this mock data
                var directories = addRelativePathToDirectoryStructure(mockData().directories, '');
                addDirectoriesTo(ulElement, directories);
                util.getRootDirectory().click();
                util.getRootDirectory().focus();
                attachKeysHandler(ulElement.find('li'));
            });
        } else {
            console.error("getAssetDirectories function is not set");
        }
    }

    function setUpRootDirectory() {
        var directory = {name: 'root', path: ''};
        util.getRootDirectory().closest('li').removeClass('active');
        util.getRootDirectory().click(
            function (event) {
                diskHandler.addDiskParamToObject(directory);
                openDirectory(event, JSON.stringify(directory));
            }
        );

        util.getRootDirectory().on('visit', function() {
            assetHandler.pullAssets(directory);
        });
    }

    function addRelativePathToDirectoryStructure(directories, basePath) {
        for (var i = 0, len = directories.length; i < len; i++) {
            var directory = directories[i];
            directory.path = basePath + "/" + directory.name;
            if (directory.directories) {
                directory.directories = addRelativePathToDirectoryStructure(directory.directories, directory.path);
            }
        }
        return directories;
    }

    function addDirectoriesTo(element, directories) {
        for (var index in directories) {
            var directory = directories[index];
            directory.slug = util.slugify(directory.path);
            var li = getDirectoryElement(directory);
            element.append($(li));
            directoryEventHandler(directory, false);
        }
    }

    function attachKeysHandler(elements) {
        elements.each(function (index) {
            $(this).keydown(function (e) {
                attachUpAndArrowKeysEvent(e);
            });
        });
    }

    function attachUpAndArrowKeysEvent(event) {
        switch (event.which) {
            case 37: // left
                $(event.target).find('div').first().trigger('close-directories');
                break;

            case 38: // up
                var elements = ulElement.find('li');
                elements.each(function (index) {
                    if ($(this).is($(event.target)) && index > 0) {
                        var prev = elements[index - 1];
                        $(prev).find('div').first().trigger('visit');
                        $(prev).focus();
                        setTheSelectedDirectoryToActive($(prev));
                        return false;
                    }
                });
                break;


            case 39: // right
                $(event.target).find('div').first().trigger('open-directories');
                break;

            case 40: // down
                var elements = ulElement.find('li');
                elements.each(function (index) {
                    if ($(this).is($(event.target)) && index < elements.length - 1) {
                        var next = elements[index + 1];
                        $(next).find('div').first().trigger('visit');
                        $(next).focus();
                        setTheSelectedDirectoryToActive($(next));
                        return false;
                    }
                });
                break;
            case 9: //Tab
                assetHandler.focusFirstElement();
                break;
            default:
                return; // exit this handler for other keys
        }
    }


    function directoryEventHandler(directory, isNew) {
        var element = ulElement.find('#' + directory.slug);

        if (!isNew) {
            element.click(
                function (event) {
                    openDirectory(event, JSON.stringify(directory));
                }
            );

            element.on('open-assets', function() {
                assetHandler.pullAssets(directory);
            });

            element.on('open-directories', function() {
                showSubDirectories($(this).closest('li'), directory, false);
            });

            element.on('close-directories', function() {
                hideSubDirectories($(this).closest('li'));
            });

            if (localObject.rename_directory_url) {
                element.dblclick(
                    function () {
                        var element = $(this).find('span.editable');
                        element.replaceWith('<input value="' + directory.name + '"/>');
                        attachRenameURLEvent($(this).find('input'), directory);
                    }
                );
            }
        } else {
            var inputElement = element.find('input');
            inputElement.focus();
            inputElement.select();
            attachCreateNewFolderEvent(inputElement, directory);
        }
    }

    function attachCreateNewFolderEvent(inputElement, directory) {
        inputElement.on('focusout', function () {
            var newValue = $(this).val();
            directory.name = newValue;
            $.post(localObject.create_new_directory_url, directory,
                function (data) {
                    alert("Created the new folder successfully");
                    directory.slug = util.slugify(directory.name);
                    inputElement.closest('div').attr('id', directory.slug);
                    inputElement.replaceWith('<span class="editable">' + directory.name + '</span>');
                    directoryEventHandler(directory, false);
            }).fail(function () {
                    inputElement.closest('li').remove();
            });
        });
        inputElement.keydown(function (e) {
            if (e.which == 13) {
                inputElement.focusout();
            }
        });
    }

    function attachRenameURLEvent(inputElement, directory) {
        inputElement.focus();
        inputElement.select();
        var oldValue = inputElement.val();
        inputElement.on('focusout', function () {
            var newValue = $(this).val();
            if (newValue != oldValue) {
                var focusoutElement = $(this);
                var params = {
                    name: newValue,
                    path: directory.path
                };
                $.post(localObject.rename_directory_url, params,
                    function (data) {
                        alert("Folder has been renamed successfully");
                        inputElement.replaceWith('<span class="editable">' + newValue + '</span>');
                    }).fail(function () {
                        alert("failed to access rename directory URL");
                        inputElement.replaceWith('<span class="editable">' + oldValue + '</span>');
                });
            } else {
                inputElement.replaceWith('<span class="editable">' + oldValue + '</span>');
            }
        });
        inputElement.keydown(function (e) {
            if (e.which == 13) {
                inputElement.focusout();
            }
        });

    }

    function openDirectory(event, directoryObject) {
        var liElement = $(event.target).closest('li');
        var directory = JSON.parse(directoryObject);

        var directoryAlreadyVisited = isDirectoryAlreadyVisited(liElement);
        var directoryAlreadyOpen = isDirectoryAlreadyOpen(liElement);

        setTheSelectedDirectoryToActive(liElement);

        if (!directoryAlreadyVisited) {
            showSubDirectories(liElement, directory, true);
        }

        if (!directoryAlreadyOpen) {
            assetHandler.pullAssets(directory);
        }
        onDirectoryChange();
    }

    function isDirectoryAlreadyVisited(liElement) {
        return liElement.find('i.fa-folder').length > 0 && liElement.find('ul').length > 0 && !liElement.hasClass('active');
    }

    function isDirectoryAlreadyOpen(liElement) {
        return liElement.hasClass('active');
    }

    function setTheSelectedDirectoryToActive(liElement) {
        ulElement.find('i.fa-folder-open').removeClass('fa-folder-open').addClass('fa-folder');

        liElement.find('i').first().removeClass('fa-folder');
        liElement.find('i').first().addClass('fa-folder-open');
        getSelectedFolderElement().removeClass('active');
        liElement.addClass('active');
    }

    function showSubDirectories(liElement, directory, shouldHideIfOpen) {
        if (liElement.find('ul').length == 0) {
            if (directory.directories && directory.directories.length > 0) {
                liElement.append($('<ul></ul>'));
                addDirectoriesTo(liElement.find('ul'), directory.directories);
            }
        } else {
            if (shouldHideIfOpen) hideSubDirectories(liElement);
        }
    }

    function hideSubDirectories(liElement) {
        liElement.find('ul').remove();
    }

    function getUniqueName(folderName, identifier) {
        ulElement.find('li').each(function () {
            if ($(this).find('input').val() == folderName) {
                identifier++;
                folderName = getUniqueName(folderName + identifier, identifier);
                return false;
            }
        });
        return folderName;
    }

    function addNewDirectory() {
        var directoryName = getUniqueName('untitled', 0);
        var directory = {
            name: directoryName,
            slug: util.slugify(directoryName)
        };

        var newElement = getNewDirectoryElement(directory);
        var selectedDirectory = getSelectedFolderElement();
        if (isRootDirectory(selectedDirectory)) {
            selectedDirectory = getSelectedFolderElement().parent();
        } else {
            if (selectedDirectory.find('ul').length == 0) {
                selectedDirectory.append($('<ul></ul>'));
            }
            selectedDirectory = selectedDirectory.find('ul');
        }
        selectedDirectory.append($(newElement));
        directoryEventHandler(directory, true);

    }

    function isRootDirectory(element) {
        return element.attr('id') == 'root';
    }

    function getDirectoryElement(directory) {
        return '<li tabindex="1" data-name="' + directory.name + '" data-path="' + directory.path + '">' +
            '<div id="' + directory.slug + '"><i class="fa fa-folder"></i>' +
            '<span class="editable">' + directory.name +
            '</span></div>' +
            '</li>';
    }

    function getNewDirectoryElement(directory) {
        return '<li>' +
            '<div id="' + directory.slug + '"><i class="fa fa-folder"></i>' +
            '<input value="' + directory.name + '"/>' +
            '</div>' +
            '</li>';
    }

    function getSelectedFolderElement() {
        return ulElement.find('.active');
    }


    function clearDirectories() {
        ulElement.find('li').each(function () {
            if ($(this).find('div').attr('id') != 'root') {
                $(this).remove();
            }
        });
        assetHandler.clear();
    }

    function getCurrentActiveDirectory() {
        return { 
            path : getSelectedFolderElement().attr('data-path'),
            name : getSelectedFolderElement().attr('data-name')
        };
    }


    return {
        getAssetDirectories: getAssetDirectories,
        clear: clearDirectories,
        addNewDirectory: addNewDirectory,
        getCurrentActiveDirectory : getCurrentActiveDirectory
    };

}

function AssetHandler(localObject, diskHandler, onAssetFocus, onAssetSelection) {
    var assetElement = util.getAssetWindow();
    var currentAssetObject = [];
    var currentDirectory = {};
    var currentView = 'grid';
    var selectedAsset = {};

    function pullAssetsAndDisplay(directory) {
        if (directory) currentDirectory = directory;
        if (!currentDirectory) return;

        resetAssets();
        appendAssetHeader();
        if (localObject.asset_files_url) {
            $.get(diskHandler.addDiskParamToURL(localObject.asset_files_url + "?folder=" + currentDirectory.path), function (data) {
                currentAssetObject = data;
                showAssets(currentAssetObject);
            }).fail(function () {
                //alert("failed to access asset URL");
                //TODO need to remove this mock data
                currentAssetObject = mockData().files(currentDirectory.path);
                showAssets(currentAssetObject);
            });
        } else {
            console.error("getAssetDirectories function is not set");
        }
    }

    function showAssetGrid(assets) {
        hideAssetDetails();
        if (!assets) assets = currentAssetObject;
        currentView = 'grid';
        util.getAssetsGrid().removeClass('hidden');
        util.getAssetsList().addClass('hidden');
        util.getAssetsGrid().empty();
        for (var index in assets) {
            var asset = assets[index];
            var gridElements = '<li id="'+util.slugify(asset.name)+'" tabindex="1"><div>';
            var path = getAssetPath(asset);

            gridElements += (util.isImage(asset.type)) ? '<img src="' + path + '" alt="' + asset.name + '"/>'
                            :
                            '<i class="big-icon fa ' + util.getFontAwesomeClass(asset.type) + ' fa-3x"></i>';
            gridElements += '<div>' + asset.name + '</div>';

            gridElements += '</div></li>';
            util.getAssetsGrid().append($(gridElements));
            assetEventHandler(asset);
        }

    }
    
    function getAssetPath(asset) {
        var path = asset.path;
        if (!path) path = diskHandler.getRootPath() +
            ((currentDirectory.path && currentDirectory.path != '/') ? (currentDirectory.path + '/') : ('/')) +
            asset.name;

        return path;
    }

    function showAssetList(assets) {
        hideAssetDetails();
        if (!assets) assets = currentAssetObject;
        currentView = 'list';
        util.getAssetsGrid().addClass('hidden');
        util.getAssetsList().removeClass('hidden');
        var tableBody = util.getAssetsList().find('tbody');
        tableBody.empty();
        
        for (var index in assets) {
            var asset = assets[index];
            var listElements = '<tr id="'+util.slugify(asset.name)+'" tabindex="1">';

            for (var i = 0, len = localObject.asset_response_parameters.length; i < len; i++) {
                var sortType = localObject.asset_response_parameters[i];
                listElements += '<td>';
                if (i == 0) {
                    listElements += '<i class="small-icon fa ' + util.getFontAwesomeClass(asset.type) + '"></i>';
                }
                listElements += getDisplayName(asset[sortType], sortType);
                listElements += '</td>';
            }

            listElements += '</tr>';

            tableBody.append($(listElements));
            assetEventHandler(asset);
        }
    }

    function getDisplayName(name, type)
    {
        if (type == 'size') {
            return name + " " + localObject.size_unit;
        }

        return name;
    }

    function appendAssetHeader() {
        var headerElement = $('<thead></thead>');
        var rowElement = $('<tr></tr>');
        var thElement = '';
        if (localObject.asset_response_parameters) {
            for (var i = 0, len = localObject.asset_response_parameters.length; i < len; i++) {
                var sortType = localObject.asset_response_parameters[i];
                thElement += '<th id="'+sortType+'">' + util.unSlugify(sortType) + '<span></span></th>';
            }
        }

        rowElement.append($(thElement));
        headerElement.append(rowElement);

        util.getAssetsList().append(headerElement);
        util.getAssetsList().append($('<tbody></tbody>'));

        util.getAssetsList().find('th').each(function(){
            $(this).click(function(){
                var isAsc = !$(this).hasClass('asc');
                
                updateAscDescOrderClass($(this).attr('id'), isAsc);
                
                sortAssetsBy($(this).attr('id'), isAsc);
            });
        });
    }

    function updateAscDescOrderClass(type, isAsc)
    {
        util.getAssetsList().find('th').each(function(){
            $(this).removeClass('asc').removeClass('desc');
            if ($(this).attr('id') == type) {
                if (isAsc) {
                    $(this).addClass('asc');
                } else {
                    $(this).addClass('desc');
                }
                return false;
            }
        });
    }

    function assetEventHandler(asset) {
        var element;
        if (currentView == 'grid') {
            element = util.getAssetsGrid().find('#' + util.slugify(asset.name));
        } else {
            element = util.getAssetsList().find('#' + util.slugify(asset.name));
        }
        element.click(
           function () {
               attachClickEvent($(this), asset);
           }
        );

        element.on('keydown', function (e) {
            attachUpAndArrowKeysEvent(e);
        });
    }

    function attachClickEvent(element, asset) {
        if (currentView == 'grid') {
            showAssetDetails(asset, true);
            util.getAssetsGrid().find('li').removeClass('active');
        } else {
            util.getAssetsList().find('tr').removeClass('active');
        }
        onAssetFocus(true);
        element.addClass('active');
        selectedAsset = asset;
    }

    function attachUpAndArrowKeysEvent(event) {
        function focusElement(elements, element, previous) {
            elements.each(function(index) {
                if ($(this).is(element)) {
                    if ((previous && index > 0) || (!previous && index < elements.length - 1)) {
                        var toBeSelected = (previous == true) ? elements[index - 1] : elements[index + 1];
                        $(toBeSelected).click();
                        $(toBeSelected).focus();
                    }
                    return false;
                }
            });
        }
        switch (event.which) {
            case 37: // left
                if (currentView == 'grid') {
                    var elements = util.getAssetsGrid().find('li');
                    focusElement(elements, $(event.target), true);
                }
                break;

            case 38: // up
                if (currentView == 'list') {
                    var elements = util.getAssetsList().find('tr');
                    focusElement(elements, $(event.target), true);
                }
                break;


            case 39: // right
                if (currentView == 'grid') {
                    var elements = util.getAssetsGrid().find('li');
                    focusElement(elements, $(event.target), false);
                }
                break;

            case 40: // down
                if (currentView == 'list') {
                    var elements = util.getAssetsList().find('tr');
                    focusElement(elements, $(event.target), false);
                }
                break;
            case 13: // enter
                onAssetSelection();
                break;
            default:
                return; // exit this handler for other keys
        }
    }

    function showAssetDetails(asset) {
        var fileDetails = util.getFileBrowser().find('#show-file-details');
        fileDetails.empty();
        fileDetails.removeClass('hidden');
        for (var key in asset) {
            var li = $('<li></li>');
            li.append($('<label>'+util.unSlugify(key)+':&nbsp;</label>'));
            li.append($('<span>'+getDisplayName(asset[key], key)+'</span>'));
            li.append($('<span>&nbsp;</span>'));
            fileDetails.append(li);
        }
    }

    function hideAssetDetails() {
        var fileDetails = util.getFileBrowser().find('#show-file-details');
        fileDetails.empty();
        fileDetails.addClass('hidden');
    }

    function clearAssets() {
        util.getAssetsGrid().empty();
        util.getAssetsList().empty();
        assetElement.find('table.header').empty();
        currentAssetObject = [];
    }

    function sortAssetsBy(type, isAsc) {
        if (typeof(isAsc) == "undefined") {
            isAsc = true;
            updateAscDescOrderClass(type, isAsc);
        };

        var sortedObject = util.sortByType(currentAssetObject, type, isAsc);
        currentAssetObject = sortedObject;
        showAssets(currentAssetObject);
    }

    function showAssets(assets) {
        if (currentView == 'grid') {
            showAssetGrid(assets);
        } else {
            showAssetList(assets);
        }
    }

    function resetAssets() {
        clearAssets();
        util.getFileBrowser().find('#fb_sort_files').val('');
    }

    function searchAssets(text) {
        var searchedAssets = [];
        for (var index in currentAssetObject) {
            var asset = currentAssetObject[index];
            if (asset.name.toLowerCase().indexOf(text.toLowerCase()) > - 1) {
                searchedAssets.push(asset);
            }
        }
        showAssets(searchedAssets);
    }

    function getSelectedAssetPath() {
        if (selectedAsset)
        return getAssetPath(selectedAsset);
    }

    function focusFirstElement() {
        var element;
        if (currentView == 'list') {
            element = util.getAssetsList().find('tr:eq(1)');
        } else {
            element = util.getAssetsGrid().find('li:eq(0)');
        }
        element.click();
        element.focus();
    }

    return {
        pullAssets: pullAssetsAndDisplay,
        clear: resetAssets,
        sortAssetsBy: sortAssetsBy,
        showAssetGrid: showAssetGrid,
        showAssetList: showAssetList,
        searchAssets: searchAssets,
        showAssets: showAssets,
        hideAssetDetails: hideAssetDetails,
        getAssetPath: getSelectedAssetPath,
        focusFirstElement: focusFirstElement
    };
}


function mockData() {
    return {
        directories: [
            {
                name: 'cats',
                directories: [
                    {
                        name: '2016',
                        directories: [
                            {name: '01'},
                            {name: '02'},
                            {name: '03'},
                            {name: '04'},
                            {name: '05'},
                            {name: '06'},
                            {name: '07'},
                            {name: '08'},
                            {name: '09'},
                            {name: '10'}
                        ]
                    }
                ]
            },
            {
                name: 'dogs',
                directories: [
                    {
                        name: '2016',
                        directories: [
                            {name: '01'},
                            {name: '02'},
                            {name: '03'},
                            {name: '04'},
                            {name: '05'},
                            {name: '06'},
                            {name: '07'},
                            {name: '08'},
                            {name: '09'},
                            {name: '10'}
                        ]
                    }
                ]
            },
            {
                name: 'monkeys',
                directories: [
                    {
                        name: '2016',
                        directories: [
                            {name: '01'},
                            {name: '02'},
                            {name: '03'},
                            {name: '04'},
                            {name: '05'},
                            {name: '06'},
                            {name: '07'},
                            {name: '08'},
                            {name: '09'},
                            {name: '10'}
                        ]
                    }
                ]
            }
        ],
        files: function (path) {
            if (path == '/cats') {
                return [
                    {
                        name: 'Black Cat.jpg',
                        path: 'https://www.petfinder.com/wp-content/uploads/2013/09/cat-black-superstitious-fcs-cat-myths-162286659.jpg',
                        size: '50',
                        last_modified_date: '2015-01-01 00:00',
                        type: 'jpg'
                    },
                    {
                        name: 'Kitten.jpg',
                        path: 'http://www.medhatspca.ca/sites/default/files/news_photos/2014-Apr-15/node-147/cute-little-cat.jpg',
                        size: '50',
                        last_modified_date: '2015-01-01 00:00',
                        type: 'jpg'
                    },
                    {
                        name: 'Fat Cat.jpg',
                        path: 'http://images.thesurge.com/app/uploads/2015/12/cat-.jpg?1bccdf',
                        size: '50',
                        last_modified_date: '2015-01-01 00:00',
                        type: 'jpg'
                    }
                ];
            }
            if (path == '/cats/2016') {
                return [
                    {
                        name: 'Three Cats.jpg',
                        path: 'http://www.cats.org.uk/uploads/branches/211/5507692-cat-m.jpg',
                        size: '50',
                        last_modified_date: '2015-01-12 00:00',
                        type: 'jpeg'
                    },
                    {
                        name: 'Staring Cat.jpg',
                        path: 'http://www.petsrus.ie/resources/cat.jpg?timestamp=1422015600638',
                        size: '50',
                        last_modified_date: '2015-01-01 00:00',
                        type: 'png'
                    },
                    {
                        name: 'Laughing Cat.jpg',
                        path: 'http://cdn.revistadonna.clicrbs.com.br/wp-content/uploads/sites/9/2014/07/Smiling_Cat.jpg',
                        size: '50',
                        last_modified_date: '2015-01-10 00:00',
                        type: 'jpg'
                    }
                ];
            }
            return [
                    {
                        name: 'article_1.pdf',
                        size: '30',
                        last_modified_date: '2015-01-12 00:00',
                        type: 'pdf'
                    },
                    {
                        name: 'article_2.xlsx',
                        size: '40',
                        last_modified_date: '2015-01-01 00:00',
                        type: 'xlsx'
                    },
                    {
                        name: 'whatever.docx',
                        size: '50',
                        last_modified_date: '2015-01-10 00:00',
                        type: 'docx'
                    }
                ];
        }
    };
}