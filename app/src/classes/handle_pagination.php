<?php
require_once __DIR__.'/myPagination.class.php';
function PaginationHandling($ResultsPerPage,$RecordsCount,$PageNumber) {
    if(!$ResultsPerPage) $ResultsPerPage = 25;
    if(!$RecordsCount) $RecordsCount = 0;
    if(!$PageNumber) $PageNumber = 1;

    $myPagination = new myPagination($ResultsPerPage,$RecordsCount,$PageNumber);
    $Query = "LIMIT ".$myPagination->getOffset().", ".$ResultsPerPage;
    $PaginationArray = $myPagination->returnIt();

    return [
        $Query,
        $myPagination,
        $PaginationArray
    ];
}
?>