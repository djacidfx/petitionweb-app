const spinnerLoading = `<div class="d-flex justify-content-center">
<div class="spinner-border text-dark" role="status" style="width: 3rem; height: 3rem;">
<span class="visually-hidden">Loading...</span>
</div>
</div>`;
const IMAGE_MIME_REGEX = /^image\/(jpeg|png)$/i;

function ltrim(str, charlist) {
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([[\]().?/*{}+$^:])/g, '$1');
    const re = new RegExp('^[' + charlist + ']+', 'g');
    return (str + '').replace(re, '');
}
function trimRouteSlash_R(r) {
    return r.split('/')[0];
}
function trimRouteSlash_L(r) {
    const p = r.split('/');
    return p[p.length-1];
}
function cloneArray(a) {
    return JSON.parse(JSON.stringify(a));
}
function getLang() {
    let lang = localStorage.getItem('language');
    if(!lang) lang = 'en';
    return lang;
}
function getMultipleSelect(el) {
    return Array.from(el.querySelectorAll("option:checked"),e=>e.value);
}
function Ajax(url, method='GET', data={}, beforeSend=null, callback=null) {
    $.ajax({
        url: url,
        method: method.toUpperCase(),
        cache: false,
        data: JSON.stringify(data),
        beforeSend: (xhr) => {
            if(beforeSend !== null) beforeSend(xhr);
        },
        complete: (response) => {
            if(callback !== null) callback(response);
        }
    });
}
function AjaxBase(url, method='GET', data={}, beforeSend=null, callback=null) {
    Ajax(PATH+url, method, data, beforeSend, callback);
}
function API(path, method='get', data={}, beforeSend=null, callback=null) {
    AjaxBase(`/pages/api/_api.php?path=${path}&lang=${getLang()}&timestamp=${Number(new Date())}`, method, data, beforeSend, (response) => {
        var r = response;
        if(response.responseJSON) r = response.responseJSON;
        callback(r);
    });
}
function ShowAlertResponse(selector, status, head, desc='') {
    var color = "";
    var icon = "";
    if(status === true) {
        color = "success";
        icon = "far fa-check-circle";
    } else if(status === false) {
        color = "danger";
        icon = "far fa-times-circle";
    } else if(status === -1) {
        color = "warning";
        icon = "fas fa-exclamation-triangle";
    } else {
        color = "info";
        icon = "fas fa-info-circle";
    }
    $(selector).html(`<div class="alert alert-${color}"><h5><i class="${icon} me-2"></i>${head}</h5><div class="mb-0">${desc}</div></div>`);
}
function loadFile(file, callback) {
    var reader = new FileReader();
    reader.onload = (e) => {
        callback(e.target.result);
    };
    reader.readAsDataURL(file);
}
function Modal(title, body, save_btn=null, close_btn=null) {
    const _modal = new bootstrap.Modal(document.getElementById('modal'), {});
    if(title !== null) document.getElementById('modal-title').innerHTML = title;
    if(body !== null) document.getElementById('modal-body').innerHTML = body;
    if(save_btn !== null) {
        if(save_btn[0]) document.getElementById('modal-save-btn').innerHTML = save_btn[0];
        if(save_btn[1]) document.getElementById('modal-save-btn').onclick = save_btn[1];
    }
    if(close_btn !== null) document.getElementById('modal-close-btn').innerHTML = close_btn;
    _modal.show();
    return _modal;
}
