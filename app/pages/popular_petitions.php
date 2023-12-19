<div id="petitions-page">
    <div class="text-center">
        <h3 class="mb-4"><?php echo $lang->popular_petitions;?></h3>
    </div>
    <div class="mt-4"></div>
    <hr>
    <div id="petitions-home" class="text-center p-0">
        <div class="container-fluid">
            <?php
                $random_petitions = Petition::ReadPopularRandom(10);
                if($random_petitions) {
                    echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
                    foreach($random_petitions as $petition) {
                        Petition::ShowSingleSmall($petition);
                    }
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> '.$lang->petition_view->no_petitions_av.'
                    </div>';
                }
            ?>
        </div>
    </div>
</div>