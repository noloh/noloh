<?php
/**
 * @package Web.UI.Controls
 * Link class file.
 */
 
/**
 * Link class
 *
 * A Control for a Link
 *
 * Properties
 * - <b>Href</b>, string, 
 *   <br>Gets or Sets the Href of the Link, usually # due to actions set via Events
 * 
 * You can use the Link as follows
 * <code>
 *
 *		function Foo()
 *		{
 *			$tempLink = new Link("#", 0,0);
 *			$this->Controls->Add($tempLink); //Adds a Link to the Controls class of some Container
 *		}
 *		
 * </code>
 */

class Link extends Label
{
	/**
	* Href, The Href of this Link, the Default is ""
	* @var string
	*/
	const Blank = '_blank', Self = '_self';
	
	private $Destination;
	private $Target;
	
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new Link("#", 0, 0, 80, 24);</code>
 	* @param string|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	* @param integer|optional
	*/
	function Link($destination='', $text='', $left = 0, $top = 0, $width = 83, $height = 20)  
	{
		parent::Label($text, $left, $top, $width, $height);
		//$this->SetText($text);
		$this->SetDestination($destination);
	}
	
	function GetDestination()
	{
		return $this->Destination;
	}
	
	function SetDestination($newDestination)
	{
		$this->Destination = $newDestination;
		NolohInternal::SetProperty('href', $newDestination, $this);
	}
	function GetTarget()	{return ($this->Target === null)?self::Self:$this->Target;}
	function SetTarget($targetType)
	{
		$this->Target = ($targetType == self::Self)?null:$targetType;
		NolohInternal::SetProperty('target', $targetType, $this);
	}
	/*function SetText($newText)
	{
		parent::SetText($newText);
		NolohInternal::SetProperty("innerHTML", $newText, $this);
	}*/
	/**
	* @ignore
	*/	
	function Show()
	{
		//$initialProperties = Control::Show();
		//$initialProperties .= ",'style.wordWrap','break-word','style.overflow','hidden'";
		NolohInternal::Show('A', Control::Show(), $this);
	}
}
?>