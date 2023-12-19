const current_route = () => {
    const p = window.location.pathname.split('/');
    const r = ADMIN_PATH.split('/');
    return p[r.length];
};
const handle_current_route = () => {
    return (current_route() ? current_route() : "home");
};
const update_nav_lpage = () => {
    $('[router-link]').each((i,el) => {
        $(el).removeClass('active');
        const page_name = $(el).attr('href');
        $(el).attr('href', ADMIN_PATH+page_name);
        if(page_name == '/'+handle_current_route()) {
            $(el).addClass('active');
        }
    });
};
update_nav_lpage();
