<?php
//deals with pagination and all the pages
if(!isset($_GET['search'])){
    $lastPage = $userLister->getLastPageNumber();
    if($lastPage >= 7) {
        if (isset($_GET['page'])) {
            $currentPage = $_GET['page'];
            if (!is_numeric($currentPage) || $currentPage <= 1) {
                $currentPage = 1;
                $previousPage = 1;
                $nextPage = 2;
                $pages = [1, 2, 3, '...', $lastPage];
            } else {
                switch ($currentPage) {
                    case $lastPage:
                        $previousPage = $currentPage - 1;
                        $nextPage = $currentPage;
                        $pages = [1, '...', $lastPage - 2, $lastPage - 1, $lastPage];
                        break;
                    case $lastPage - 1:
                    case $lastPage - 2:
                        $previousPage = $currentPage - 1;
                        $nextPage = $currentPage + 1;
                        $pages = [1, '...', $currentPage - 2, $currentPage - 1, $currentPage, $lastPage];
                        break;
                    case 2:
                        $previousPage = $currentPage - 1;
                        $nextPage = $currentPage + 1;
                        $pages = [1, 2, 3, '...', $lastPage];
                        break;
                    case 3:
                    case 4:
                    case 5:
                        $previousPage = $currentPage - 1;
                        $nextPage = $currentPage + 1;
                        $pages = [1, 2, 3, 4, 5, 6, '...', $lastPage];
                        break;
                    default:
                        $previousPage = $currentPage - 1;
                        $nextPage = $currentPage + 1;
                        $pages = [1, '...', $currentPage - 2, $currentPage - 1, $currentPage, $currentPage + 1, $currentPage + 2, '...', $lastPage];
                        break;
                }
            }
        } else {
            $currentPage = 1;
            $previousPage = 1;
            $nextPage = 2;
            $pages = [1, 2, 3, '...', $lastPage];
        }
    }else{
        if (isset($_GET['page'])){

        }
    }
    $view->pagination ='
        <nav class="d-flex justify-content-center align-items-center">
          <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="index.php?page='.$previousPage.'" tabindex="-1">Previous</a>
              </li>
        ';
    foreach ($pages as $page){
        $current = $page == $currentPage ? 'active' : null;
        $isNumber = is_numeric($page)? null : 'disabled';
        $view->pagination .= '
                <li class="page-item '. $current.' '.$isNumber.'"><a class="page-link" href="index.php?page='.$page.'">'.$page.'</a></li>
        ';
    }
    $view->pagination .='
                <li class="page-item">
                    <a class="page-link" href="index.php?page='.$nextPage.'">Next</a>
                </li>
            </ul>
        </nav>
        ';}else{
    $view->pagination = null;
}