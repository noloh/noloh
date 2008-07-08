<?php
/**
 * Button class
 * 
 * A Button is a Control for a conventional web button. From a design standpoint, it invites a user to click on it to trigger a certain behavior
 * typically described by its Text. Thus, it commonly has a Click Event. A button also has a Type property, which can either be Button::Normal
 * or Button::Submit. A Button::Submit is only used in connection with Forms, please see the reference article on Forms for more information on
 * Submitting; Note, however, that Forms are <b>strongly</b> discouraged and should be used only when a specific situation calls for it, i.e., next to never.
 * 
 * The following is an example of instantiating and adding a button
 * <pre>
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
 * </pre>
 * 
 * @package Controls/Core
 */
class Button extends Control 
{
	/**
	 * A Normal Button, the default Type.
	 */
	const Normal = 'Button';
	/**
	 * A Button that Submits a Form that it is in. 
	 */
	const Submit = 'Submit';
	/**
	* @property string $Type The type of the button
	* The Type of this Button, the Default is Button::Normal, but it can also be set to Button::Submit for the purposes of Forms. {@see Form}
	*/
	private $Type;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Button
	*	<pre> $tmpBut = new Button(0, 0, 80, 24, Button::Normal);</pre>
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
	 * Gets the Type of this button, which is Button::Normal by default
	 * @return Button::Normal|Button::Submit 
	 */
	function GetType()
	{
		return $this->Type === null ? Button::Normal : $this->Type;
	}
	/**
	 * Sets the type of this button, the default is Button::Normal
	 * @param Button::Normal|Button::Submit $type possible values are Button::Normal, Button::Submit
	 */
	function SetType($type)
	{
		$this->Type = $type == Button::Normal ? null : $type;
		NolohInternal::SetProperty('type', $type, $this);
	}
	/**
	 * This will set the Text that is displayed on the button
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