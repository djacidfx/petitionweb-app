function load_page(name, selector, callback=null) {
    $.ajax({
        // url: PATH+'/pages/'+name,
        url: PATH+'/pages/_load.php',
        method: 'GET',
        context: document.body,
        cache: false,
        data: {
            'timestamp': Number(new Date()),
            'lang': getLang(),
            'page': ltrim(name, '?')
        },
        beforeSend: (xhr) => {
            $(selector).html(spinnerLoading);
        }
    }).done((response) => {
        $(selector).html(response);
        $('.for-lang--'+getLang()).css('display', 'inline');
        $('#for-lang--'+getLang()).css('display', 'block');
        if(callback !== null) {
            callback(response);
        }
    });
}

function pushPageLocation(name, args, full_name, page_type) {
    window.history.pushState({"name": name, "args": args, "type": page_type}, "", PATH+'/'+full_name);
}

function load_page_content(name, push_history=true) {
    if(!Routes.includes(name)) {
        name = '404';
    }
    load_page(name+'.php', '#page-content', (response) => {
        if(push_history) pushPageLocation(name, null, name, 'single');
        update_nav_lpage();
    });
}
function load_page_content_with_args(name, args={'code': 'xxx'}, push_history=true) {
    if(!Routes.includes(name)) {
        name = '404';
    }
    const page_name = name+'.php';
    let params = "?";
    for(const key in args) {
        if(Object.hasOwnProperty.call(args, key)) {
            const element = args[key];
            params += key+"="+element+"&";
        }
    }
    params = params.slice(0,-1);
    const route_path = page_name+params;
    const params_array_1 = params.split("&");
    let params_route = "";
    for(let i = 0; i < params_array_1.length; i++) {
        params_route += params_array_1[i].split("=")[1]+"/";
    }
    params_route = params_route.slice(0,-1);
    const show_path = name+'/'+params_route;
    load_page(route_path, '#page-content', (response) => {
        if(push_history) pushPageLocation(name, params_route, show_path, 'multi');
        update_nav_lpage();
    });
}
function load_page_content_with_path_and_args(path, name_str="{name}?code={1}", push_history=true) {
    const name = path[0];
    if(!Routes.includes(name)) {
        name = '404';
    }
    let full_name = "";
    let route_name = "";
    let fix_name = "";
    fix_name = name_str.replace(/\{name\}/gi, name+'.php');
    const regex1 = /(\{([0-9]*)\})/gi;
    const matches1 = fix_name.match(regex1);
    full_name = fix_name;
    let the_path_n = "";
    if(matches1 && matches1.length > 0) {
        console.log("matches1", matches1);
        for(let i = 0; i < matches1.length; i++) {//TODO: only working with 1 var, otherwise will cause a huge error and will not work correctly
            const n = matches1[i].replace("{", "").replace("}","");
            full_name = fix_name.replace(regex1, path[n]);
            the_path_n = path[n];
            route_name = name+'/'+the_path_n;
        }
    } else {
        full_name = fix_name;
        route_name = name+'/'+full_name.split(".php")[1];
        the_path_n = route_name.split('[')[1].replace("[", "").replace("]", "");
        route_name = name+'/'+the_path_n;
    }
    console.log(full_name);
    console.log(route_name);
    load_page(full_name, '#page-content', (response) => {
        if(push_history) pushPageLocation(name, the_path_n, route_name, 'multi');
        update_nav_lpage();
    });
}

function update_nav_lpage() {
    $('[lpage-nav]').each((i,el) => {
        $(el).removeClass('active');
        const page_name = $(el).attr('lpage');
        if(page_name == handle_current_route()) {
            $(el).addClass('active');
        }
    });
}

function update_lpage_links() {
    function fnClick(ev, el, page_name) {
        ev.preventDefault();
        if(page_name) load_page_content(page_name);
        if(el) el.removeEventListener("click", fnClick);
    }
    $('[lpage]').each((i, el) => {
        const page_name = $(el).attr('lpage');
        el.href = page_name;
        el.addEventListener('click', (ev) => {
            ev.preventDefault();
            fnClick(ev, el, page_name);
        });
    });
}

update_lpage_links();

window.onpopstate = (e) => {
    if(e.state) {
        if(e.state.type === "single") {
            load_page_content(e.state.name, false);
        } else {
            load_page_content_with_args(e.state.name, {'code': e.state.args}, false); //TODO: only working with one var
        }
    }
};
