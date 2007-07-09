<?php

require_once 'Parser/Type.php';
require_once 'Parser/Exception.php';

class XMLParser implements Iterator
{
	private $XMLFeed;
    public $FeedModel;
	protected $IdMappings = array();

	function XMLParser($whatXMLFeed, $isStrict = false)
	{
		$this->FeedModel = new DOMDocument();
        $this->FeedModel->GetXML($whatXMLFeed);

		$doc_element = $this->FeedModel->DocumentElement;
		switch (true) 
		{
    		case ($doc_element->namespaceURI == 'http://www.w3.org/2005/Atom'):
    		    require_once 'Parser/Atom.php';
    		    require_once 'Parser/AtomElement.php';
    			$class = 'XML_Feed_Parser_Atom';
    			break;
		    case ($doc_element->namespaceURI == 'http://purl.org/atom/ns#'):
    		    require_once 'Parser/Atom.php';
    		    require_once 'Parser/AtomElement.php';
    		    $class = 'XML_Feed_Parser_Atom';
    		    trigger_error(
    		        'Atom 0.3 deprecated, using 1.0 parser which won\'t provide 
    		        all options', E_USER_WARNING);
    		    break;
		    case ($doc_element->hasChildNodes() and $doc_element->childNodes->length > 1 
		        and $doc_element->childNodes->item(1)->namespaceURI == 
		        'http://purl.org/rss/1.0/'):
		        require_once 'Parser/RSS1.php';
		        require_once 'Parser/RSS1Element.php';
    		    $class = 'XML_Feed_Parser_RSS1';
    		    break;
    		case ($doc_element->hasChildNodes() and $doc_element->childNodes->length > 1
    		    and $doc_element->childNodes->item(1)->namespaceURI == 
    		    'http://my.netscape.com/rdf/simple/0.9/'):
		        require_once 'Parser/RSS09.php';
		        require_once 'Parser/RSS09Element.php';
    		    $class = 'XML_Feed_Parser_RSS09';
    		    break;
		    case ($doc_element->tagName == 'rss' and 
		        $doc_element->hasAttribute('version') and 
		        $doc_element->getAttribute('version') == 2):
	            require_once 'Parser/RSS2.php';
	            require_once 'Parser/RSS2Element.php';
	            $class = 'XML_Feed_Parser_RSS2';
    		    break;
    		case ($doc_element->tagName == 'rss' and
        		$doc_element->hasAttribute('version') and 
		        $doc_element->getAttribute('version') == 0.91):
		        trigger_error(
    		        'RSS 0.91 has been superceded by RSS2.0. Using RSS2.0 parser.', 
    		        E_USER_WARNING);
		        require_once 'Parser/RSS2.php';
	            require_once 'Parser/RSS2Element.php';
	            $class = 'XML_Feed_Parser_RSS2';
    		    break;
    		case ($doc_element->tagName == 'rss' and
        		$doc_element->hasAttribute('version') and 
		        $doc_element->getAttribute('version') == 0.92):
		        trigger_error(
    		        'RSS 0.92 has been superceded by RSS2.0. Using RSS2.0 parser.', 
    		        E_USER_WARNING);
		        require_once 'Parser/RSS2.php';
	            require_once 'Parser/RSS2Element.php';
	            $class = 'XML_Feed_Parser_RSS2';
    		    break;
    		default:
		        throw new XMLParserException('Feed type unknown');
		        break;
		}

		/* Instantiate feed object */
        $this->FeedModel = new $class($this->FeedModel, $isStrict);
	}

	function __call($call, $attributes)
	{
	    return $this->FeedModel->$call($attributes);
	}

    function __get($val)
    {
        return $this->FeedModel->$val;
    }

	function next()
	{
	    if (isset($this->current_item) && $this->current_item <= $this->FeedModel->numberEntries - 1) 
	        ++$this->current_item;
	    else if (! isset($this->current_item))
	        $this->current_item = 0;
	    else
	        return false;
	}

	function current()
	{
	    return $this->getEntryByOffset($this->current_item);
	}

	function key()
	{
	    return $this->current_item;
	}

	function valid()
	{
	    return $this->current_item < $this->FeedModel->numberEntries;
	}

	function rewind()
	{
	    $this->current_item = 0;
	}

	function GetEntryById($whatId)
	{
		if (isset($this->IdMappings[$whatId])) 
			return $this->getEntryByOffset($this->idMappings[$whatId]);
		return $this->feed->getEntryById($id);
	}

	function GetEntryByIndex($whatIndex)
	{
		if ($offset < $this->FeedModel>numberEntries) 
		{
			if (isset($this->FeedModel->entries[$whatIndex]))
				return $this->FeedModel->entries[$whatIndex];
			else 
			{
				try
				{
					$this->FeedModel->getEntryByOffset($whatIndex);
				}
				catch (Exception $e)
				{
					return false;
				}
				$id = $this->feed->entries[$whatIndex]->getID();
				$this->idMappings[$id] = $whatIndex;
				return $this->feed->entries[$whatIndex];
			}
		}
		else 
			return false;
	}
	
	function __toString()
	{
	    return $this->FeedModel->__toString();
	}
}
?>