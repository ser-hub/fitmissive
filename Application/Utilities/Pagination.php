<?php

namespace Application\Utilities;

class Pagination
{
    private $pagesToShow;
    private $selectedClass = 'selected';
    private $pageDelimiter = '|';
    private $sectionDelimiter = '...';
    private $link;
    private $totalPages;

    public function __construct($totalPages, $pagesToShow)
    {
        $this->pagesToShow = $pagesToShow;
        $this->totalPages = $totalPages;
    }

    public function show($currentPage)
    {
        if ($currentPage > $this->totalPages) {
            return;
        }

        $longFormat = $this->totalPages > ($this->pagesToShow * 3) + 1;

        if (!$longFormat) $this->pagesToShow = $this->totalPages;

        for ($i = 1; $i <= $this->pagesToShow; $i++) {
            if ($i == $currentPage) echo "<span class='" . $this->selectedClass . "'>";
            echo '<a href="' . $this->link . '&page=' . $i . '">' . $i . '</a>';
            if ($i == $currentPage) echo "</span>";
            if ($i != $this->totalPages) echo ' ' . $this->pageDelimiter . ' ';
        }

        if ($longFormat) {
            if ($currentPage > $this->pagesToShow + 3) {
                echo $this->sectionDelimiter . ' ' . $this->pageDelimiter . " ";
            }

            for ($i = 0; $i < 5; $i++) {
                if (($currentPage - 2) + $i > $this->pagesToShow && ($currentPage - 2) + $i < $this->totalPages - $this->pagesToShow) {
                    if ($i == 2) echo "<span class='" . $this->selectedClass . "'>";
                    echo '<a href="' . $this->link . '&page=' . ($currentPage - 2) + $i . '">' . ($currentPage - 2) + $i . '</a>';
                    if ($i == 2) echo "</span>";
                    if (($currentPage - 2) + $i != $this->totalPages) echo ' ' . $this->pageDelimiter . ' ';
                }
            }

            if ($currentPage < $this->totalPages - ($this->pagesToShow + 3)) {
                echo $this->sectionDelimiter . ' ' . $this->pageDelimiter . " ";
            }

            for ($i = $this->totalPages - $this->pagesToShow + 1; $i <= $this->totalPages; $i++) {
                if ($i == $currentPage) echo "<span class='" . $this->selectedClass . "'>";
                echo '<a href="' . $this->link . '&page=' . $i . '">' . $i . '</a>';
                if ($i == $currentPage) echo "</span>";
                if ($i != $this->totalPages) echo ' ' . $this->pageDelimiter . ' ';
            }
        }
    }

    public function setPagesToShow($pagesToShow)
    {
        $this->pagesToShow = $pagesToShow;
    }

    public function setSelectedClass($selectedClass)
    {
        $this->selectedClass = $selectedClass;
    }

    public function setPageDelimiter($pageDelimiter)
    {
        $this->pageDelimiter = $pageDelimiter;
    }

    public function setSectionDelimiter($sectionDelimiter)
    {
        $this->sectionDelimiter = $sectionDelimiter;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }
}