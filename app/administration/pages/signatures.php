<?php
$myPDO->setTable(DB::TBL_SIGNS);
$db_object = $myPDO;
if(isset($_REQUEST['petition']) && $_REQUEST['petition']) {
    $db_method = 'filterAll';
    $db_params = [['petition_id'], [$_REQUEST['petition']], '=', 'AND', '*'];
    $data = $myPDO->filterAll(['petition_id'], [$_REQUEST['petition']], '=', 'AND', '*');
} else {
    $db_method = 'selectAll';
    $db_params = [null, null, '*'];
    $data = $myPDO->selectAll(null, null, '*');
}
require_once __DIR__.'/../pagination.php';
$signatures = $data;
?>
<div class="container">
    <div class="text-center">
        <h3 class="text-center mb-3">{{lang.admin.signatures_head}}</h3>
        <p class="text-center mb-0"><?php echo $pagination_array['total'];?> {{lang.admin.pagination.total_results}} - <?php echo $count;?> {{lang.admin.pagination.results_per_page}}</p>
    </div>
    <hr>
    <ul class="list-group" dir="ltr" style="padding-left: 0;">
        <?php
        foreach($signatures as $signature):
            $petition = Petition::Read('id', $signature['petition_id']);
            $signee = Petition::GetCreator($signature['signee']);
        ?>
        <li class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between align-content-center align-items-center flex-row flex-nowrap">
                <h5 class="mb-0"><small><i class="flag flag-<?php echo convertCountryNameToFontIconName($signee['country']);?>" title="<?php echo $signee['country'];?>"></i><?php echo '('.$signee['country'].')</small> '.$signee['name'].' <small>- '.$signee['email'].'</small>';?></h5>
                <?php if($signature['verified']) { ?> <span class="badge bg-success rounded-pill">{{lang.admin.verified}}</span>
                <?php } else { ?> <span class="badge bg-danger rounded-pill">{{lang.admin.not_verified}}</span> <?php } ?>
            </div>
            <div class="mt-1">
                <a href="/petitions/<?php echo $petition['id'];?>" target="_blank" router-link><?php echo $petition['code'];?> - <?php echo substr($petition['title'], 0, 64).'...';?></a>
            </div>
            <small class="text-muted"><?php echo timestamp2Date($signature['creation_time']);?></small>
        </li>
        <?php endforeach;?>
    </ul>
    <?php
    if(count($signatures) > 0) {
        if(isset($_REQUEST['petition']) && $_REQUEST['petition']) $extra_params = ['petition' => $_REQUEST['petition']];
        ViewPaginationNav($pagination_array, $page, );
    }
    ?>
</div>
