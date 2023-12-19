if(typeof WEB_LANG_PATH == "undefined" || !WEB_LANG_PATH) {
    console.error('Please set "WEB_LANG_PATH" variable in your main script.');
}
function setLanguage(lang) {
    localStorage.setItem('language', lang);
}
function getLanguage() {
    return localStorage.getItem('language');
}
function handleGetLanguage() {
    if(getLanguage() == null) handleSetLanguage('en',false);
    return getLanguage();
}
function handleSetLanguage(lang,can_load_labels=true) {
    setLanguage(lang);
    if(can_load_labels) loadGetLabels();
    $('#lang').val(lang);
}
function getLabels(callback) {
    $.ajax({
        url: WEB_LANG_PATH + getLanguage() + '.json',
        type: 'GET',
        dataType: 'json',
        async: true,
        contentType: "application/json",
        success: function(response) {
            callback(response);
        }
    });
}
function loadGetLabels() {
    getLabels(function(response){
        $('#lang').val(getLanguage());
        setLabelsInHTML(response);
    });
}
const language = handleGetLanguage();
loadGetLabels();