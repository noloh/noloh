<?php
/**
 * Link class
 *
 * A Control for a conventional web link. 
 * 
 * You can use the Link as follows
 * <pre>
 * function Foo()
 * {
 *	$tempLink = new Link('#', 0, 0);
 * 	//Adds a Link to the Controls ArrayList of some Container
 *	$this->Controls->Add($tempLink); 
 * }
 * </pre>
 * 
 * @package Controls/Core
 */

class Link extends Label
{
	/**
	 * @ignore
	 */
	const Blank = '_blank';
	/**
	 * @ignore
	 */
	const Self = '_self';
	
	private $Destination;
	private $Target;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Link
 	* Example
 	*	<pre> $tempVar = new Link('#', 0, 0, 80, 24);</pre>
 	* @param string $destination 
 	* @param string $text
	* @param integer $left The left coordinate of this element
	* @param integer $top The top coordinate of this element
	* @param integer $width The width of this element
	* @param integer $height The height of this element
	*/
	function Link($destination='', $text='', $left = 0, $top = 0, $width = 83, $height = 20)  
	{
		parent::Label($text, $left, $top, $width, $height);
		//$this->SetText($text);
		$this->SetDestination($destination);
	}
	/**
	 * Returns the destination for the Link, i.e., where the link will redirect the user after it is clicked. A
	 * value of '#' can be used for not redirecting anywhere but still having the look of a Link, useful for Click Events.
	 * @return string
	 */
	function GetDestination()
	{
		return $this->Destination===null?'':$this->Destination;
	}
	/**
	 * Sets the destination for the Link, i.e., where the link will redirect the user after it is clicked. A
	 * value of '#' can be used for not redirecting anywhere but still having the look of a Link, useful for Click Events.
	 * @param string $destination
	 */
	function SetDestination($destination)
	{
		$this->Destination = $destination;
		NolohInternal::SetProperty('href', $destination===null?'':$destination, $this);
	}
	/**
	 * @ignore
	 */
	function GetTarget()	{return ($this->Target === null)?self::Self:$this->Target;}
	/**
	 * @ignore
	 */
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