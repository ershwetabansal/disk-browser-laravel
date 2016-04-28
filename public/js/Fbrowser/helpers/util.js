var allowedImageTypes = ['jpg', 'png', 'gif', 'jpeg'];
var allowedDocTypes = ['doc', 'docx'];
var allowedPdfTypes = ['pdf'];
var allowedTextTypes = ['txt'];
var allowedExcelType = ['xls', 'xlsx'];

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
    return (allowedImageTypes.indexOf(type) > -1);
}

function getFontAwesomeClass(type) {
    var faClasses = {};

    function updateClasses(array, css) {
        array.forEach(function(item) {
            faClasses[item] = css;
        });  
    }
    
    updateClasses(allowedImageTypes, 'fa-file-image-o');
    updateClasses(allowedExcelType, 'fa-file-excel-o');
    updateClasses(allowedTextTypes, 'fa-file-text-o');
    updateClasses(allowedPdfTypes, 'fa-file-pdf-o');
    updateClasses(allowedDocTypes, 'fa-file-word-o');

    return faClasses[type];
}

function compareAsc(a, b, prop) {
    if (typeof(a[prop]) == 'number') {
        return compareAscNumbers(a, b, prop);
    } else {
        if (a[prop] < b[prop])
            return -1;
        else if (a[prop] > b[prop])
            return 1;
        else
            return 0;

    }
}

function compareAscNumbers(a, b, prop) {
    return a[prop] - b[prop];
}

function compareDesc(a, b, prop) {
    if (typeof(a[prop]) == 'number') {
        return compareDescNumbers(a, b, prop);
    } else {
        if (a[prop] > b[prop])
            return -1;
        else if (a[prop] < b[prop])
            return 1;
        else
            return 0;
    }
}

function compareDescNumbers(a, b, prop) {
    return b[prop] - a[prop];
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

function getCookie(cookieName) {
    var cookies = document.cookie;
    var cookieArray = cookies.split(';');

    for (var i = 0, len = cookieArray.length; i < len; i++) {
        var cookie = cookieArray[i];
        var keyValue = cookie.split('=');
        if (keyValue.length > 0 && keyValue[0] == cookieName) {
            return keyValue[1];
        }
    }
}

module.exports = {
    slugify: slugify,
    unSlugify: unSlugify,
    isImage: isImage,
    getFontAwesomeClass: getFontAwesomeClass,
    sortByType: sortByType,
    getCookie: getCookie
};