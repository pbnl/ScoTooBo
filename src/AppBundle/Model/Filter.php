<?php

namespace AppBundle\Model;


class Filter
{
    /**
     * Attributes the system should use to filter
     *
     * @var array
     */
    private $filterAttributes = array();

    /**
     * Texts/Values/Names the system should use to filter
     *
     * @var array
     */
    private $filterTexts = array();

    /**
     * Adds one filter option to the filter
     *
     * @param string $attribute
     * @param string $text
     */
    public function addFilter(string $attribute, $text = "")
    {
        if($text == null) $text = "";
        array_push($this->filterAttributes, $attribute);
        array_push($this->filterTexts, $text);
    }

    /**
     * @return array
     */
    public function getFilterAttributes()
    {
        return $this->filterAttributes;
    }

    /**
     * @return array
     */
    public function getFilterTexts()
    {
        return $this->filterTexts;
    }






}