<?php
class myPagination {
    public $resultsPerPage = 10;
    public function __construct($resultsPerPage,$total_records_number,$page_no) {
        $this->resultsPerPage = intval($resultsPerPage);
        $this->total_records_number = intval($total_records_number);
        $this->page_no = ($page_no ? intval($page_no) : 1);
    }

    public function getOffset() {
        return ($this->page_no - 1) * $this->resultsPerPage;
    }
    public function getPagesCount() {
        return ceil($this->total_records_number / $this->resultsPerPage);
    }
    public function nextPage() {
        $next_page = $this->page_no + 1;
        return ($this->lastPage() > $this->page_no ? $next_page : null);
    }
    public function prevPage() {
        $prev_page = $this->page_no - 1;
        return ($this->page_no <= 1 ? null : $prev_page);
    }
    public function lastPage() {
        return $this->getPagesCount();
    }
    public function pageFrom() {
        return (($this->page_no - 1) * $this->resultsPerPage) + 1;
    }
    public function pageTo() {
        return $this->pageFrom() + $this->resultsPerPage - 1;
    }

    public function returnIt() {
        return [
            'from'=>$this->pageFrom(),
            'to'=>$this->pageTo(),
            'current_page'=>$this->page_no,
            'first_page'=>1,
            'next_page'=>$this->nextPage(),
            'prev_page'=>$this->prevPage(),
            'last_page'=>$this->lastPage(),
            'per_page'=>$this->resultsPerPage,
            'pages'=>$this->getPagesCount(),
            'total'=>$this->total_records_number,
        ];
    }
}
?>