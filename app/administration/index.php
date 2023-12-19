<?php
session_start();
require_once __DIR__.'/../src/config.php';
require_once __DIR__.'/main.php';
require_once __DIR__.'/auth.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petitions Web App - Administration Panel</title>
    <link rel="shortcut icon" href="<?php echo path_join('/public/img/favicon.ico');?>" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="stylesheet-id-2" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css" integrity="sha512-MAFufI57w9mLGud8BKZDbAT57+wu4QWMJJ9Bj5UXFaW99rswsKCvXKRxWlHwdo0yT1Of6TvvWfMqE16ktRcxfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="stylesheet-id-1" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?php echo path_join('/public/css/app.css');?>" rel="stylesheet">
    <script src="<?php echo path_join('/public/js/core.js');?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
</head>
<body>
    <input type="hidden" id="lang" value="en">
    <app-loading class="m-5 p-5"></app-loading>
    <script>document.querySelector('app-loading').innerHTML = spinnerLoading;</script>
    <app id="app" class="hidden">
        <topbar class="mb-5">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a href="<?php echo admin_path_join('/dashboard');?>" class="navbar-brand me-4">{{web.name}}</a>
                    <button class="navbar-toggler btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item mx-1"><a href="/dashboard" class="btn btn-link" router-link>{{lang.admin.dashboard}}</a></li>
                            <li class="nav-item mx-1"><a href="/petitions" class="btn btn-link" router-link>{{lang.admin.manage_petitions}}</a></li>
                            <li class="nav-item mx-1"><a href="/signatures" class="btn btn-link" router-link>{{lang.admin.view_signatures}}</a></li>
                            <li class="nav-item mx-1"><a href="/admins" class="btn btn-link" router-link>{{lang.admin.admins_head}}</a></li>
                        </ul>
                        <div class="d-flex">
                            <?php if(admin_path_back()) { ?> <button class="btn btn-outline-info mx-1" :title="lang.back" onclick="window.location.href = '<?php echo admin_path_back();?>';"><i class="fas fa-chevron-left"></i></button> <?php } ?>
                            <button class="btn btn-outline-secondary mx-1" :title="lang.admin.visit_web" onclick="window.open('../');"><i class="fas fa-external-link-alt"></i></button>
                            <button class="btn btn-outline-dark mx-1" :title="lang.refresh" onclick="reloadPage()"><i class="fas fa-redo-alt"></i></button>
                        </div>
                    </div>
                </div>
            </nav>
        </topbar>
        <content class="container my-5 p-4" id="page-content">
        <?php
        if($_REQUEST['route']) {
            $page_file = __DIR__.'/pages/';
            $pf = function($n) use($page_file) {
                return $page_file.$n.'.php';
            };
            $page_name = explode("/", $_REQUEST['route']);
            $page_name = reset($page_name);
            if(!file_exists($pf($page_name))) {
                $page_name = '404';
                header('location: '.admin_path_join('/404'));
                exit;
            }
            require_once $pf($page_name);
        }
        ?>
        </content>
        <modal id="modal" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title">{{lang.error}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal-body"></div>
                    <div class="modal-footer" id="modal-footer">
                        <button type="button" class="btn btn-secondary" id="modal-close-btn" data-bs-dismiss="modal">{{lang.ok}}</button>
                        <button type="button" class="btn btn-primary" id="modal-save-btn" style="display: none;">{{lang.save}}</button>
                    </div>
                </div>
            </div>
        </modal>
        <copyright>
            <div class="container text-left" dir="ltr">
                <?php if(SystemAdmins::IsSessionSet()) { ?>
                <div class="mb-2">
                    <a href="/logout" router-link class="btn btn-sm btn-outline-danger">{{lang.admin.admins.logout}}</a>
                </div>
                <?php } ?>
                <div>
                    <select class="form-select" id="lang_option_selector" onchange="select_lang(this)"></select>
                </div>
                <small>
                    (C) 2021, {{web.name}}.<br>
                    Web App by <b><a href="https://primo-businesses.blogspot.com/" target="_blank">Primo</a></b>.
                </small>
            </div>
        </copyright>
    </app>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.10.25/datatables.min.js"></script>    <!-- Vue App -->
    <script>
    const vue = new Vue({
        el: '#app',
        data: {
            web: {
                name: '<?php echo WEB_NAME;?>',
                path: '<?php echo PATH;?>'
            },
            lang: {
                petition_view: {},
                admin: {
                    pagination: {},
                    petition: {},
                    admins: {}
                }
            },
            lang_code: 'en'
        },
        methods: {
            join_path: function(path) {
                return this.web.path+path;
            }
        },
        computed: {
            now: function() {
                return Date.now();
            }
        },
        created: function() {

        },
        mounted: function() {
            setTimeout(()=>{
                $('app-loading').addClass('hidden');
                $('app').removeClass('hidden');
            }, 200);
        }
    });
    const PATH = '/'+trimRouteSlash_L(vue.web.path);
    const ADMIN_PATH = PATH+'/<?php echo ADMIN_DIR_NAME;?>';
    </script>
    <!-- Major Config -->
    <script>
    ADMIN_PAGE_BOOL = true;
    const Languages = {
        'en': 'English',
        'ar': 'Arabic'
    };
    </script>
    <!-- Language Config -->
    <script>
    const WEB_LANG_PATH = PATH+'/lang/';
    </script>
    <script src="<?php echo path_join('/public/js/lang.js');?>"></script>
    <script>
    function setLabelsInHTML(labels) {
        vue.lang = labels;
    }
    function updateLangsOptions() {
        let h = '<option disabled>Select a Language</option>';
        for(const lang_code in Languages) {
            const lang_name = Languages[lang_code];
            let a = '';
            if(lang_code == getLanguage()) a = 'selected disabled';
            h += `<option value="${lang_code}" ${a}>${lang_name}</option>`;
        }
        $('#lang_option_selector').html(h);
    }
    updateLangsOptions();
    vue.lang_code = getLanguage();
    </script>
    <script src="<?php echo path_join('/public/js/app.js');?>"></script>
    <script>check_lang_rtl();</script>
    <script src="<?php echo path_join('/public/js/admin-paging.js');?>"></script>
</body>
</html>
