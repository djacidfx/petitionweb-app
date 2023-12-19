<?php
require_once __DIR__.'/../src/classes/handle_pagination.php';

function ViewPaginationNav($pagination_array, $page, $other_request_params=null) {
    $r_params = '';
    if($other_request_params) {
        $r_params = '&';
        foreach($other_request_params as $k => $v) {
            $r_params .= $k.'='.$v.'&';
        }
        $r_params = substr($r_params, 0, -1);
    }
?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li :title="lang.admin.pagination.first_page" class="page-item<?php echo ($page==1 ? ' disabled' : '');?>"><a class="page-link" href="?page=1<?php echo $r_params;?>">&laquo;&laquo;</a></li>
        <li :title="lang.admin.pagination.prev_page" class="page-item<?php echo ($page==1 ? ' disabled' : '');?>"><a class="page-link" href="?page=<?php echo ($page > 1 ? $page-1 : '1').$r_params;?>">&laquo;</a></li>
        <?php
        for($p = 1; $p <= $pagination_array['pages']; $p++) {
            echo '<li :title="lang.admin.pagination.no_page + \' '.$p.'\'" class="page-item'.($page==$p ? ' active' : '').'"><a class="page-link" href="?page='.$p.$r_params.'">'.$p.'</a></li>';
        }
        ?>
        <li :title="lang.admin.pagination.next_page" class="page-item<?php echo ($page==$pagination_array['pages'] ? ' disabled' : '');?>"><a class="page-link" href="?page=<?php echo ($page < $pagination_array['pages'] ? $page+1 : $pagination_array['pages']).$r_params;?>">&raquo;</a></li>
        <li :title="lang.admin.pagination.last_page" class="page-item<?php echo ($page==$pagination_array['pages'] ? ' disabled' : '');?>"><a class="page-link" href="?page=<?php echo $pagination_array['pages'].$r_params;?>">&raquo;&raquo;</a></li>
    </ul>
</nav>
<?php
}

if(!isset($_REQUEST['page']) || !$_REQUEST['page'] || $_REQUEST['page'] <= 0) $_REQUEST['page'] = 1;
$page = $_REQUEST['page'];
$count = 50;
$pagination = PaginationHandling($count,count($data),$page);
$pagination_db_query = $pagination[0];
$pagination_array = $pagination[2];
$db_params[] = 'ORDER BY id DESC '.$pagination_db_query;
$data = call_user_func_array([$db_object, $db_method], $db_params);
?>
