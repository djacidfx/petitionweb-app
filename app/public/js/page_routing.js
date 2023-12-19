function check_route_page_on_load(name=null, paths=null, first_time=true, push_history=true) {
    if(paths === null) paths = current_routes();
    const xPath = paths;
    if(xPath && xPath[1]) {
        if(name === null) name = xPath[0];
        console.log('load page with args, name=',name, ', args=',paths, ', first_time=',first_time);
        if(first_time) {
            load_page_content_with_path_and_args(xPath, "{name}?code={1}", push_history);
        } else {
            load_page_content_with_args(name, {'code': xPath[1]}, push_history);//TODO: only working with code
        }
    } else {
        if(name === null) name = handle_current_route();
        console.log('load page, name=',name, ', args=',paths, ', first_time=',first_time);
        if(first_time) {
            load_page_content((request_route ? request_route : name), push_history);
        } else {
            load_page_content(name, push_history);
        }
    }
}
function reshow_route(push_history=true) {
    check_route_page_on_load(null, null, false, push_history);
}
function load_route_first_time(push_history=true) {
    check_route_page_on_load(null, null, true, push_history);
}