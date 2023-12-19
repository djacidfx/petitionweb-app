<?php
$__r = explode("/", $_REQUEST['route']);
$id = str_replace("admins", "", end($__r));
if($id):
    if($id !== 'create') $id = intval($id);
    $admin = SystemAdmins::Read('id', $id);
    if(!$admin) $admin = false;
    if($id === 'create') $admin = null;
    if($admin !== false) {
        $admins = DB::ReadAll(DB::TBL_ADMINS);
        $admins_count = count($admins);
        if($_POST && $_POST['_method'] === 'PUT') {
            $user = protectInput(sanitize_string($_POST['user']));
            $pass = $_POST['pass'];
            if($admin && $user) {
                $op = SystemAdmins::Update($admin['id'], $user, $pass);
                if($op) {
                    echo showResponseAlert('success', '{{lang.admin.update_done}}', 'h5');
                    $admin = SystemAdmins::Read('id', $id);
                } else {
                    echo showResponseAlert('danger', '{{lang.admin.update_error}}', 'h5');
                }
            }
        } elseif($_POST && $_POST['_method'] === 'POST') {
            $user = protectInput(sanitize_string($_POST['user']));
            $pass = $_POST['pass'];
            if($user && $pass) {
                $op = SystemAdmins::Create($user, $pass);
                if($op) {
                    header('location: '.admin_path_join('/admins/'.$op[1]));
                    exit;
                } else {
                    echo showResponseAlert('danger', '{{lang.admin.create_error}}', 'h5');
                }
            }
        }
?>
<div class="container text-center">
    <h3><?php echo ($admin ? $admin['user'] : '{{lang.admin.admins.create_new}}');?></h3>
    <hr>
    <div>
        <form method="post" class="petition-form-group form-section" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="<?php echo ($admin ? 'PUT' : 'POST');?>">
            <div class="input-group flex-nowrap mb-2">
                <label class="input-group-text" for="input-user">{{lang.admin.admins.user}}</label>
                <input type="text" class="form-control" name="user" id="input-user" :placeholder="lang.admin.admins.user" value="<?php echo (isset($admin['user']) ? $admin['user'] : '');?>">
            </div>
            <div class="input-group flex-nowrap mb-2">
                <label class="input-group-text" for="input-pass">{{lang.admin.admins.pass}}</label>
                <input type="password" class="form-control" name="pass" id="input-pass" :placeholder="lang.admin.admins.pass">
            </div>
            <div class="input-group flex-nowrap mb-2 mt-3 text-center justify-content-center">
                <button class="btn btn-lg btn-success" type="submit"><?php echo ($admin ? '{{lang.update}}' : '{{lang.create}}');?></button>
            </div>
        </form>
        <?php if($admin) { ?>
        <hr>
        <form class="form-section">
            <div class="text-start">
                <p class="mb-0">{{lang.admin.petition.creation_time}}: <?php echo timestamp2Date($admin['creation_time']);?></p>
            </div>
            <hr>
        </form>
        <div class="mt-3 text-center">
            <form method="POST" id="delete-petition-form" class="form-section">
                <button id="delete_petition_btn" class="btn btn-danger" type="submit"<?php echo ($admins_count > 1 ? '' : ' disabled');?>>{{lang.admin.delete}}</button>
                <?php echo ($admins_count > 1 ? '' : '<p>{{lang.admin.admins.cant_delete_when_count_1}}</p>'); ?>
                <input type="hidden" name="delete-petition" value="1">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" id="delete-confirmation-msg" :value="lang.admin.delete_confirmation_msg">
            </form>
            <?php
            if($admins_count > 1) {
                if($_POST && $_POST['_method'] === 'DELETE') {
                    $op = SystemAdmins::Delete($id);
                    if($op) {
                        echo "<script>
                        var els = document.getElementsByClassName('form-section');
                        for(var i = 0; i < els.length; i++) {
                            els[i].style.display = 'none';
                        }
                        </script>";
                        echo showResponseAlert('success', '{{lang.admin.delete_done}}', 'h5');
                    } else {
                        echo showResponseAlert('danger', '{{lang.admin.delete_error}}', 'h5');
                    }
                }
            }
            ?>
        </div>
        <?php } ?>
    </div>
    <script>
    window.onload = () => {
        $(document).ready(() => {
            $('#delete_petition_btn').click((ev) => {
                if(confirm($('#delete-confirmation-msg').val())) {
                    $('#delete-petition-form').submit();
                } else {
                    ev.preventDefault();
                }
            });
        });
    };
    </script>
</div>
<?php
    } else {
        echo "<h4 class='alert alert-warning'>{{lang.admin.admins.not_exist}}.</h4>";
    }
?>
<?php
else:
    $admins = DB::ReadAll(DB::TBL_ADMINS);
?>
<div class="text-center">
    <h3 class="text-center mb-3">{{lang.admin.admins_head}}</h3>
    <p class="mb-2"><a href="/admins/create" router-link class="btn btn-primary">{{lang.admin.admins.create}}</a></p>
</div>
<hr>
<div class="table-responsive">
    <table id="dataTable" class="table table-striped table-hover table-bordered table-sm align-middle display" style="width:100%">
        <thead>
            <tr>
                <th scope="col">{{lang.admin.admins.id}}</th>
                <th scope="col">{{lang.admin.admins.user}}</th>
                <th scope="col">{{lang.admin.petition.creation_time}}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach(array_reverse($admins) as $admin):
            ?>
            <tr onclick="window.location.href = '<?php echo admin_path_join('/admins/'.$admin['id']);?>'" class="clickable">
                <th scope="row"><?php echo $admin['id'];?></th>
                <td><?php echo $admin['user'];?></td>
                <td><small><?php echo timestamp2Date($admin['creation_time']);?></small></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

<script>
window.onload = () => {
    $(document).ready(() => {
        $('#dataTable').DataTable({
            "order": [[ 0, "desc" ]]
        });
    });
};
</script>
<?php
endif;
?>
