<div class="container">
    <h2 class="text-center d-block w-100 bg-dark text-light p-3">{{lang.admin.admins.administration}}</h2>
    <div><?php echo (isset($auth_response) ? $auth_response : '');?></div>
    <form method="POST">
        <div class="input-group flex-nowrap mb-2">
            <label class="input-group-text" for="input-user">{{lang.admin.admins.user}}</label>
            <input type="text" class="form-control" name="user" id="input-user" :placeholder="lang.admin.admins.user">
        </div>
        <div class="input-group flex-nowrap mb-2">
            <label class="input-group-text" for="input-pass">{{lang.admin.admins.pass}}</label>
            <input type="password" class="form-control" name="password" id="input-pass" :placeholder="lang.admin.admins.pass">
        </div>
        <div class="input-group flex-nowrap">
            <button class="btn btn-lg btn-primary d-block w-100">{{lang.admin.admins.login}}</button>
        </div>
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="_action" value="AUTH">
    </form>
</div>
