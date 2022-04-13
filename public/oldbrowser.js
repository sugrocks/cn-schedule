/* eslint-disable no-var */
// This file should be loaded only in case of a lack of support for "nomodule",
// which implies that the browser doesn't supports modules, required for the webapp
var errorMsgUnsupported = '<h1>CN Schedule</h1>'
errorMsgUnsupported += '<strong>You\'re currently using an unsupported browser</strong>.<br/>'
errorMsgUnsupported += 'Please upgrade to the latest version of Chrome, Opera, Brave, Firefox, Microsoft Edge or Safari to continue.<br/>'
errorMsgUnsupported += '<a href="https://whatbrowser.org/">More details on having a good and updated browser here.</a>'

document.getElementById('jserror').innerHTML = errorMsgUnsupported
throw new Error('Unsupported browser')
