const current_routes = () => {
    const p = window.location.pathname.split('/');
    const r = PATH.split('/');
    const xp = cloneArray(p);
    xp.splice(0, r.length);
    return xp;
};
const current_route = () => {
    const p = window.location.pathname.split('/');
    const r = PATH.split('/');
    return p[r.length];
};
const handle_current_route = () => {
    return (current_route() ? current_route() : "home");
};