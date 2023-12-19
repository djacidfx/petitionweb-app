<?php
$n__verf_pets = DB::Count(DB::TBL_PETS, ['verified'], [1]);
$n__not_verf_pets = DB::Count(DB::TBL_PETS, ['verified'], [0]);
$n__total_pets = $n__verf_pets+$n__not_verf_pets;

$n__verf_signs = DB::Count(DB::TBL_SIGNS, ['verified'], [1]);
$n__not_verf_signs = DB::Count(DB::TBL_SIGNS, ['verified'], [0]);
$n__total_signs = $n__verf_signs+$n__not_verf_signs;
?>
<div class="container text-center">
    <h4>{{lang.admin.welcome_msg1}}</h4>
    <p class="mb-0">{{lang.admin.welcome_msg2}}</p>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col m-3">
                <a href="<?php echo admin_path_join('/petitions');?>" class="btn btn-secondary" data-mdb-ripple-color="dark" role="button"><h5>{{lang.admin.manage_petitions}}</h5></a>
            </div>
            <div class="col m-3">
                <a href="<?php echo admin_path_join('/signatures');?>" class="btn btn-primary" data-mdb-ripple-color="dark" role="button"><h5>{{lang.admin.view_signatures}}</h5></a>
            </div>
        </div>
    </div>
    <hr>
    <div class="container dh-stats">
        <h4>{{lang.admin.dashboard_stats}}</h4>
        <div class="row">
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_petitions}}</h5>
                <h3 class="card-body"><?php echo number_format($n__total_pets);?></h3>
            </div>
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_verf_petitions}}</h4>
                <h3 class="card-body"><?php echo number_format($n__verf_pets);?></h3>
            </div>
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_not_verf_petitions}}</h4>
                <h3 class="card-body"><?php echo number_format($n__not_verf_pets);?></h3>
            </div>
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_signs}}</h4>
                <h3 class="card-body"><?php echo number_format($n__total_signs);?></h3>
            </div>
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_verf_signs}}</h4>
                <h3 class="card-body"><?php echo number_format($n__verf_signs);?></h3>
            </div>
            <div class="col-4 my-3 card">
                <h5 class="card-header">{{lang.admin.count_not_verf_signs}}</h4>
                <h3 class="card-body"><?php echo number_format($n__not_verf_signs);?></h3>
            </div>
        </div>
    </div>
</div>
