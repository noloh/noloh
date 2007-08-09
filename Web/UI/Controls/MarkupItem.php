<?php
/**
 * @package UI
 * @subpackage Controls
 */

/**
 * A MarkupItem serves as the parent class of both {@see Eventee} and {@see Larva}. It has no purpose other than
 * organization and inheritance. In particular, there is no reason for the developer to extend this themselves.
 * 
 * A MarkupItem represents a mark-up tag that has an "n" namespace before the tag name and a descriptor property, for example:<br>
 * <code><n:a descriptor="keyword:value"></n:a></code>
 * 
 * The descriptor has two pieces of information, the keyword and value, separated by a colon. When no colon is present, the entire
 * string will be treated as the keyword and the MarkupItem will have no value. Corresponding to those, every MarkupItem has a Keyword 
 * and Value property. The developer can then decide what they will do with the MarkupItem depending on that Keyword and Value, and
 * whether the MarkupItem is an Eventee or Larva. 
 * 
 * A Larva is a MarkupItem whose tag name is n:component. Any other tag is an eventee.
 * 
 * For more information, please see
 * @see EventMarkupPanel
 */
abstract class MarkupItem extends Object
{
	/**
	 * A unique Id
	 * @access protected
	 * @var string
	 */
	protected $Id;
	private $Keyword;
	private $Value;
	/**
	 * The Id of the EventMarkupPanel from which the MarkupItem was parsed
	 * @access protected
	 * @var string
	 */
	protected $PanelId;
	
	/**
	 * @ignore
	 */
	function MarkupItem($id, $keyword, $value, $panelId)
	{
		$this->Id = $id;
		$this->Keyword = $keyword;
		$this->Value = $value;
		$this->PanelId = $panelId;
	}
	/**
	 * Gets the Keyword of the MarkupItem.
	 * For example, <code><n:a descriptor="X:Y"></n:a></code> has the keyword X.
	 * @return string
	 */
	function GetKeyword()
	{
		return $this->Keyword;
	}
	/**
	 * Gets the Value of the MarkupItem.
	 * For example, <code><n:a descriptor="X:Y"></n:a></code> has the keyword Y.
	 * @return string
	 */	
	function GetValue()
	{
		return $this->Value;
	}
}

?>