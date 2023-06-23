<?php
/**
 * MarkupItem class
 *
 * A MarkupItem serves as the parent class of both {@see Eventee} and {@see Larva}. It has no purpose other than
 * organization and inheritance. In particular, there is no reason for the developer to extend this class themselves.
 * 
 * A MarkupItem represents a mark-up tag that has an "n" namespace before the tag name and a descriptor property, for example:<br>
 * <pre><n:a descriptor="keyword:value"></n:a></pre>
 * 
 * The descriptor has two pieces of information, the keyword and value, separated by a colon. When no colon is present, the entire
 * string will be treated as the keyword and the MarkupItem will have no value. Corresponding to those, every MarkupItem has a Keyword 
 * and Value property. The developer can then decide what they will do with the MarkupItem depending on that Keyword and Value, and
 * whether the MarkupItem is an Eventee or Larva. 
 * 
 * A Larva is a MarkupItem whose tag name is n:larva. Any other tag is an eventee.
 * 
 * For more information, please see
 * @see RichMarkupRegion
 * 
 * @package Controls/Auxiliary
 */
abstract class MarkupItem extends Base
{
	/**
	 * @ignore
	 */
	protected $Id;
	private $Keyword;
	private $Value;
	/**
	 * The Id of the RichMarkupRegion from which the MarkupItem was parsed
	 * @access protected
	 * @var string
	 */
	protected $PanelId;
	
	/**
	 * @ignore
	 */
	function __construct($id, $keyword, $value, $panelId)
	{
		$this->Id = $id;
		$this->Keyword = $keyword;
		$this->Value = $value;
		$this->PanelId = $panelId;
	}
	/**
	 * Gets the Keyword of the MarkupItem.
	 * For example, <pre><n:a descriptor="X:Y"></n:a></pre> has the keyword X.
	 * @return string
	 */
	function GetKeyword()
	{
		return $this->Keyword;
	}
	/**
	 * Gets the Value of the MarkupItem.
	 * For example, <pre><n:a descriptor="X:Y"></n:a></pre> has the keyword Y.
	 * @return string
	 */	
	function GetValue()
	{
		return $this->Value;
	}
	/**
	 * A unique Id for the MarkupItem
	 * @return string
	 */
	function GetId()
	{
		return $this->Id;
	}
}

?>