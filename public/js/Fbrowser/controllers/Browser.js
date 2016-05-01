var Manager = require('./manager.js');
var element = require('../helpers/element.js');

var manager;
function browserSetup(setupObject) {
	manager = new Manager(setupObject);

    if (manager.validateSetupObject()) {
        manager.doInitialSetup();
    }
}

function openBrowser(details) {
	if (manager.validateSetupObject()) {
		manager.load(details.button);
		element.openModal(details.resize);
	} else {
		alert("Please check consoler errors.");
	}
}

function Browser(){
	return { 
		setup : browserSetup,
		openBrowser : openBrowser
	};
}

window.FileBrowser = function() {
	return {
		getInstance : function() {
			return new Browser();
		}
	}
}