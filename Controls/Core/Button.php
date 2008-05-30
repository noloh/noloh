<?php
/**
 * @package Web.UI.Controls
 */
 
/**
 * Button class
 * 
 * A Button is a Control for a conventional web button.
 * 
 * The following is an example of instantiating and adding a button
 * <code>
 *
 *      function Foo()
 *      {
 *          $tmpButton = new Button("Some Button");
 *          //Adds a button to the Controls of some Container or Panel object
 *          $this->Controls->Add($tmpButton);
 *          //Sets the Click event of button, when $tmpButton is clicked, it will
 *          //trigger function SomeFunc
 *          $tmpButton->Click = new ServerEvent($this, "SomeFunc");
 *      }
 *      function SomeFunc()
 *      {
 *          Alert("Click event was triggered");
 *      }
 *		
 * </code>
 */
class Button extends Control 
{
	const Normal = 'Button';
	const Submit = 'Submit';
	/**
	* @property string $Type The type of the button
	* The Type of this Button, the Default is Button::Normal, but it can also be set to Button::Submit for the purposes of Forms. {@see Form}
	*/
	private $Type;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Button
	*	<code> $tmpBut = new Button(0, 0, 80, 24, Button::Normal);</code>
	* @param string $text The Text of this element
	* @param integer $left The left coordinate of this element
	* @param integer $top The top coordinate of this element
	* @param integer $width The width of this element
	* @param integer $height The height of this element
	* @param string $type The type of this button
	* @return Button
	*/
	function Button($text='', $left = 0, $top = 0, $width = 80, $height = 24, $type = Button::Normal)
	{
		parent::Control($left, $top, $width, $height);
		$this->SetType($type);
		$this->SetText($text);
	}
	/**
	 * Gets the type of this button, default button type is Button::Normal
	 * @return Button::Normal | Button::Submit 
	 */
	function GetType()
	{
		return ($this->Type === null)?Button::Normal:$this->Type;
	}
	/**
	 * Sets the type of this button, the default is Button::Normal
	 * @param string $type possible values are Button::Normal, Button::Submit
	 */
	function SetType($type)
	{
		$this->Type = ($type == Button::Normal?null:$type);
		NolohInternal::SetProperty('type', $type, $this);
	}
	/**
	 * This will set the text that is displayed on the button
	 * @param string $text
	 */
	function SetText($text)
	{
		parent::SetText($text);
		NolohInternal::SetProperty('value', $text, $this);
	}
	/**
	* @ignore
	*/
	function Show()
	{
		NolohInternal::Show('INPUT', parent::Show(), $this);
	}
}
?>