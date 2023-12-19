<?php
require_once __DIR__.'/src/config.php';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petitions Web App</title>
    <link rel="shortcut icon" href="<?php echo path_join('/public/img/favicon.ico');?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="stylesheet-id-2" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.9.0/mdb.min.css" integrity="sha512-MAFufI57w9mLGud8BKZDbAT57+wu4QWMJJ9Bj5UXFaW99rswsKCvXKRxWlHwdo0yT1Of6TvvWfMqE16ktRcxfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="stylesheet-id-1" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?php echo path_join('/public/css/app.css');?>" rel="stylesheet">
    <script src="<?php echo path_join('/public/js/storage.js');?>"></script>
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
                    <a class="navbar-brand me-4" lpage="home">{{web.name}}</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" lpage="home" lpage-nav>{{lang.home}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" lpage="start" lpage-nav>{{lang.start_petition}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" lpage="petitions" lpage-nav>{{lang.petitions}}</a>
                            </li>
                        </ul>
                        <div class="d-flex me-1">
                            <input name="query" id="search_query" class="form-control me-2" type="search" :placeholder="lang.search" :aria-label="lang.search">
                            <button class="btn btn-outline-primary" type="button" onclick="searchSite(document.getElementById('search_query').value)">{{lang.search}}</button>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-outline-secondary me-1" :title="lang.reload" onclick="reshow_route(true)"><i class="fas fa-sync-alt"></i></button>
                            <button class="btn btn-outline-dark" :title="lang.refresh" onclick="reloadPage()"><i class="fas fa-redo-alt"></i></button>
                        </div>
                    </div>
                </div>
            </nav>
        </topbar>
        <content class="container my-5 p-4" id="page-content"></content>
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
        <footer class="mt-5">
            <div class="container">
                <div class="row align-items-start">
                    <div id="first-col-footer" class="col-4">
                        <h5>{{lang.take_action}}</h5>
                        <div>
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link" lpage="start">{{lang.start_petition}}</a></li>
                                <li class="nav-item"><a class="nav-link" lpage="petitions">{{lang.petitions}}</a></li>
                                <li class="nav-item"><a class="nav-link" lpage="popular_petitions">{{lang.popular_petitions}}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="second-col-footer" class="col-4">
                        <h5>{{lang.connect}}</h5>
                        <div>
                            <ul class="navbar-nav">
                                <li class="nav-item"><a href="https://facebook.com/" class="nav-link" target="_blank">{{lang.facebook}}</a></li>
                                <li class="nav-item"><a href="https://twitter.com/" class="nav-link" target="_blank">{{lang.twitter}}</a></li>
                                <li class="nav-item"><a href="https://youtube.com/" class="nav-link" target="_blank">{{lang.youtube}}</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="third-col-footer" class="col-4">
                        <div>
                            <select class="form-select" id="lang_option_selector" onchange="select_lang(this)"></select>
                        </div>
                        <div>
                            <a class="nav-link" lpage="about">{{lang.about}}</a>
                            <a class="nav-link" lpage="privacy">{{lang.privacy}}</a>
                            <a class="nav-link" lpage="contact">{{lang.contact}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <copyright>
            <div class="container text-left" dir="ltr">
                <small>
                    (C) 2021, {{web.name}}.<br>
                    Web App by <b><a href="https://primo-businesses.blogspot.com/" target="_blank">Primo</a></b>.
                </small>
            </div>
        </copyright>
    </app>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Vue App -->
    <script>
    const vue = new Vue({
        el: '#app',
        data: {
            web: {
                name: '<?php echo WEB_NAME;?>',
                path: '<?php echo PATH;?>'
            },
            lang: {},
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
    </script>
    <!-- Major Config -->
    <script>
    ADMIN_PAGE_BOOL = false;
    const request_route = "<?php echo (isset($_REQUEST['route']) && $_REQUEST['route'] ? $_REQUEST['route'] : "");?>";
    const Routes = ['home', 'start', 'petitions', 'popular_petitions', 'petition', 'search', 'about', 'privacy', 'contact'];
    const Languages = {
        'en': 'English',
        'ar': 'Arabic'
    };
    </script>
    <script src="<?php echo path_join('/public/js/routing.js');?>"></script>
    <script src="<?php echo path_join('/public/js/page.js');?>"></script>
    <script src="<?php echo path_join('/public/js/page_routing.js');?>"></script>
    <!-- Page -->
    <script>
    load_route_first_time();
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
    <script>
    check_lang_rtl();
    </script>
</body>
</html>
