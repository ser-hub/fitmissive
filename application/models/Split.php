<?php

namespace Application\Models;

class Split
{
    private $splits = [];

    public function __construct($splits)
    {
        $this->splits = $splits;
    }

    public function getMonday()
    {
        return $this->splits['Monday'];
    }

    public function getTuesday()
    {
        return $this->splits['Tuesday'];
    }

    public function getWednesday()
    {
        return $this->splits['Wednesday'];
    }

    public function getThursday()
    {
        return $this->splits['Thursday'];
    }

    public function getFriday()
    {
        return $this->splits['Friday'];
    }

    public function getSaturday()
    {
        return $this->splits['Saturday'];
    }

    public function getSunday()
    {
        return $this->splits['Sunday'];
    }

    public function getSplits()
    {
        return $this->splits;
    }
}
