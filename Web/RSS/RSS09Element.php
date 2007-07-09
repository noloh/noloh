<?php

class XML_Feed_Parser_RSS09Element extends XML_Feed_Parser_RSS09
{
    /**
     * This will be a reference to the parent object for when we want
     * to use a 'fallback' rule 
     * @var XML_Feed_Parser_RSS09
     */
    protected $parent;

    /**
     * Our specific element map 
     * @var array
     */
    protected $map = array(
    	'title' => array('Text'),
    	'link' => array('Link'));

    /**
     * Store useful information for later.
     *
     * @param   DOMElement  $element - this item as a DOM element
     * @param   XML_Feed_Parser_RSS1 $parent - the feed of which this is a member
     */
    function __construct(DOMElement $element, $parent, $xmlBase = '')
    {
    	$this->model = $element;
    	$this->parent = $parent;
    }
}

?>