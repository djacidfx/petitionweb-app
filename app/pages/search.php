<?php
$query = (isset($_REQUEST['code']) && $_REQUEST['code'] ? $_REQUEST['code'] : null);
if($query) {
    echo "<h5 class='mb-4'>$lang->searching_about '$query'</h5>";
    echo "<hr>";
?>
<div class="container-fluid">
    <?php
        $result_petitions = Petition::SearchResults($query);
        if($result_petitions) {
            echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
            foreach($result_petitions as $petition) {
                Petition::ShowSingleSmall($petition);
            }
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">
                <i class="fas fa-info-circle"></i> '.$lang->no_search_results.'
            </div>';
        }
    ?>
</div>
<?php
}
?>