function select_lang(el) {
    $('app').addClass('hidden');
    $('app-loading').removeClass('hidden');
    const new_lang = $(el).val();
    if(new_lang != getLanguage()) {
        handleSetLanguage(new_lang);
        updateLangsOptions();
        check_lang_rtl(new_lang);
        if(!ADMIN_PAGE_BOOL) reshow_route(true);
    }
}
function check_lang_rtl(lang=null) {
    if(!lang) lang = getLanguage();
    callback = function() {
        $('app').removeClass('hidden');
        $('app-loading').addClass('hidden');
    };
    $('html').attr('lang', lang);
    if(lang == 'ar') {
        $('html').attr('dir', 'rtl');
        load_stylesheet("stylesheet-id-2", "https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.rtl.min.css", "sha512-0fgPkutNic5wCmG1rtv9EISYefZFgMt6gDn8hcoyj/oSHhKuJtVhRm8eilzS/GruFNgIglpJb7WMbbTNY3Xubg==", callback);
        load_stylesheet("stylesheet-id-1", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css", "sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N", callback);
    } else {
        $('html').attr('dir', 'ltr');
        load_stylesheet("stylesheet-id-2", "https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css", "sha512-MAFufI57w9mLGud8BKZDbAT57+wu4QWMJJ9Bj5UXFaW99rswsKCvXKRxWlHwdo0yT1Of6TvvWfMqE16ktRcxfA==", callback);
        load_stylesheet("stylesheet-id-1", "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css", "sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC", callback);
    }
}
function load_stylesheet(xid, href, integ=null, callback=null) {
    $('#'+xid).remove();
    const head  = document.getElementsByTagName('head')[0];
    const link  = document.createElement('link');
    link.id = xid;
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = href;
    link.setAttribute('crossorigin', "anonymous");
    if(integ) link.setAttribute('integrity', integ);
    head.prepend(link);
    if(callback) {
        setTimeout(callback, 100);
    }
}
function reloadPage() {
    window.location.reload();
}
function searchSite(query) {
    load_page_content_with_args('search', {'code': query});
}
