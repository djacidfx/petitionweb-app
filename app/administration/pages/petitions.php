<?php
$PetitionStatus = [
    '0' => 'lang.admin.petition.status_ongoing',
    '-1' => 'lang.admin.petition.status_fail',
    '1' => 'lang.admin.petition.status_done'
];
$PetitionVerificationStatus = [
    '0' => 'lang.admin.not_verified',
    '1' => 'lang.admin.verified'
];

$__r = explode("/", $_REQUEST['route']);
$id = str_replace("petitions", "", end($__r));
$id = intval($id);
if($id > 0):
    $petition = Petition::Read('id', $id);
    if($petition) {
        if($_POST && $_POST['_method'] === 'PUT') {
            $title = protectInput(sanitize_string($_POST['title']));
            $body = protectInput($_POST['body']);
            $tags = $_POST['tags'];
            $tags_data = json_encode($tags);
            $status = intval($_POST['status']);
            $verification_status = intval($_POST['verified']);
            $new_img = $_FILES['img'];
            $img_id = '';
            if($title && $body && $tags) {
                if($new_img['name']) {
                    if(in_array($new_img['type'], IMAGE_TYPES)) {
                        $img_data = file_get_contents($new_img['tmp_name']);
                        $img_data = 'data:'.$new_img['type'].';base64,'.base64_encode($img_data);
                        $img_op = Image::Create($img_data);
                        if($img_op[0]) {
                            $img_id = intval($img_op[1]);
                        }
                    }
                }
                $op = Petition::Update($petition['id'], $title, $body, $tags_data, $status, $verification_status, $img_id);
                if($op) {
                    echo showResponseAlert('success', '{{lang.admin.update_done}}', 'h5');
                    $petition = Petition::Read('id', $id);
                } else {
                    echo showResponseAlert('danger', '{{lang.admin.update_error}}', 'h5');
                }
            }
        }
        $inputs_list = [
            [
                'label' => 'lang.admin.petition.title',
                'id' => 'title',
                'type' => 'text'
            ],
            [
                'label' => 'lang.admin.petition.body',
                'id' => 'body',
                'type' => 'textarea'
            ]
        ];
        $select_list = [
            [
                'label' => 'lang.admin.petition.tags',
                'id' => 'tags',
                'vals' => ALL_PETITION_TAGS,
                '2d' => false,
                'multi' => true
            ],
            [
                'label' => 'lang.admin.petition.status',
                'id' => 'status',
                'vals' => $PetitionStatus,
                '2d' => true,
                'multi' => false
            ],
            [
                'label' => 'lang.admin.petition.verification',
                'id' => 'verified',
                'vals' => $PetitionVerificationStatus,
                '2d' => true,
                'multi' => false
            ]
        ];
?>
<div class="container text-center">
    <h3><?php echo $petition['code'];?></h3>
    <hr>
    <div>
        <form method="post" class="petition-form-group form-section" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            <?php foreach($inputs_list as $input): ?>
            <div class="input-group flex-nowrap mb-2">
                <label class="input-group-text" for="input-<?php echo $input['id'];?>">{{<?php echo $input['label'];?>}}</label>
                <?php if($input['type'] !== 'textarea' && $input['type'] !== 'file') { ?> <input name="<?php echo $input['id'];?>" type="<?php echo $input['type'];?>" id="input-<?php echo $input['id'];?>" :placeholder="<?php echo $input['label'];?>" value="<?php echo $petition[$input['id']];?>" class="form-control">
                <?php } elseif($input['type'] === 'textarea') { ?> <textarea name="<?php echo $input['id'];?>" style="height:200px;" class="form-control" id="input-<?php echo $input['id'];?>" :placeholder="<?php echo $input['label'];?>"><?php echo $petition[$input['id']];?></textarea> <?php } ?>
            </div>
            <?php endforeach;?>
            <?php foreach($select_list as $select): ?>
            <div class="input-group flex-nowrap mb-2">
                <label class="input-group-text" for="input-<?php echo $select['id'];?>">{{<?php echo $select['label'];?>}}</label>
                <select <?php echo ($select['multi'] ? 'multiple style="height: 100px;"' : '');?> name="<?php echo $select['id'].($select['multi'] ? '[]' : '');?>" id="input-<?php echo $select['id'];?>" class="form-control">
                    <?php
                    if($select['2d']) {
                        foreach($select['vals'] as $k => $v) {
                            echo "<option value='$k'".($petition[$select['id']] == $k ? 'selected' : '').">".'{{'.$v.'}}'."</option>";
                        }
                    } else {
                        foreach($select['vals'] as $i => $v) {
                            $tags = Petition::GetTags($petition[$select['id']]);
                            echo "<option value='$v'".(in_array($v, $tags) ? 'selected' : '').">$v</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <?php endforeach;?>
            <div class="input-group flex-nowrap text-center justify-content-center">
                <?php
                $img__ = Image::Read($petition['img']);
                if($petition['img'] && $img__) {
                    echo '<p><img id="img-src" src="'.$img__['data'].'" style="width: 600px; max-width: 100%"></p>';
                } else {
                    echo '<p><i>[None]</i></p>';
                }
                ?>
            </div>
            <div class="input-group flex-nowrap mb-2">
                <input type="file" class="form-control" name="img">
            </div>
            <div class="input-group flex-nowrap mb-2 mt-3 text-center justify-content-center">
                <button class="btn btn-lg btn-success" type="submit">{{lang.update}}</button>
            </div>
            <hr>
        </form>
        <?php
        $creator = Petition::GetCreator($petition['creator']);
        ?>
        <form class="form-section">
            <div class="text-start">
                <p class="mb-0">{{lang.petition_view.by}}: <?php echo $creator['name'].' - '.$creator['email'].' - '.$creator['country'];?></p>
                <p class="mb-0">{{lang.admin.petition.creation_time}}: <?php echo timestamp2Date($petition['creation_time']);?></p>
                <p class="mb-2">{{lang.admin.petition.update_time}}: <?php echo timestamp2Date($petition['update_time']);?></p>
                <p class="mb-0"><a class="btn btn-secondary" href="/signatures?petition=<?php echo $id;?>" router-link target="_blank">{{lang.admin.petition.show_signatures}}</a></p>
            </div>
            <hr>
        </form>
        <div class="mt-3 text-center">
            <form method="POST" id="delete-petition-form" class="form-section">
                <button id="delete_petition_btn" class="btn btn-danger" type="submit">{{lang.admin.delete}}</button>
                <input type="hidden" name="delete-petition" value="1">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" id="delete-confirmation-msg" :value="lang.admin.delete_confirmation_msg">
            </form>
            <?php
            if($_POST && $_POST['_method'] === 'DELETE') {
                $op = Petition::Delete($id);
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
            ?>
        </div>
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
        echo "<h4 class='alert alert-warning'>{{lang.petition_view.petition_does_not_exist}}.</h4>";
    }
?>
<?php
else:
    $petitions = DB::ReadAll(DB::TBL_PETS);
?>
<div class="text-center">
    <h3 class="text-center mb-3">{{lang.admin.petitions_head}}</h3>
</div>
<hr>
<div class="table-responsive">
    <table id="dataTable" class="table table-striped table-hover table-bordered table-sm align-middle display" style="width:100%">
        <thead>
            <tr>
                <th scope="col">{{lang.admin.petition.id}}</th>
                <th scope="col">{{lang.admin.petition.code}}</th>
                <th scope="col">{{lang.admin.petition.title}}</th>
                <th scope="col">{{lang.admin.petition.tags}}</th>
                <th scope="col">{{lang.admin.petition.status}}</th>
                <th scope="col">{{lang.admin.petition.creator}}</th>
                <th scope="col">{{lang.admin.petition.creation_time}}</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach(array_reverse($petitions) as $petition):
            ?>
            <tr onclick="window.location.href = '<?php echo admin_path_join('/petitions/'.$petition['id']);?>'" class="clickable <?php echo ($petition['verified'] ? ($petition['status'] > 0 ? 'table-success' : ($petition['status'] < 0 ? 'table-danger' : '')) : 'table-warning');?>" :title="<?php echo $PetitionVerificationStatus[$petition['verified']];?>">
                <th scope="row"><?php echo $petition['id'];?></th>
                <td><?php echo $petition['code'];?></td>
                <td><?php echo $petition['title'];?></td>
                <td><?php echo implode(", ", json_decode($petition['tags'],true));?></td>
                <td><small><?php echo '{{'.$PetitionStatus[$petition['status']].'}}';?></small></td>
                <td><small><?php echo Petition::GetCreator($petition['creator'])['email'];?></small></td>
                <td><small><?php echo timestamp2Date($petition['creation_time']);?></small></td>
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
