<?php

namespace Michcald\Dummy\App;

class Result
{
    private $totalHits = 0;

    private $results = array();

    public function addResult($result)
    {
        $this->results[] = $result;

        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function setTotalHits($totalHits)
    {
        $this->totalHits = (int)$totalHits;

        return $this;
    }

    public function getTotalHits()
    {
        return $this->totalHits;
    }
}