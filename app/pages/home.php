<?php
$taking_actions_number = DB::Count(DB::TBL_SIGNS, null, null) + DB::Count(DB::TBL_PETS, null, null);
?>
<div id="home-page">
    <div class="text-center">
        <h3 class="mb-4"><?php echo $lang->home_big_title;?></h3>
        <h3 class="mb-4" style="font-weight: normal;"><?php echo number_format($taking_actions_number).' '.$lang->people_taking_action;?></h3>
        <p><button <?php echo js_onclick_load_page('start');?> class="btn btn-lg btn-success"><?php echo $lang->start_a_petition_now;?></button></p>
        <p><button <?php echo js_onclick_load_page('petitions');?> class="btn btn-lg btn-outline-success"><?php echo $lang->discover_petitions;?></button></p>
    </div>
</div>
