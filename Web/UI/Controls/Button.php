<?php
/**
 * @package Web.UI.Controls
 * Button class file.
 */
/**
 * Button class
 *
 * A Control for a conventional web button.
 * 
 * The following is an example of instantiating and adding a button
 * <code>
 *
 *      function Foo()
 *      {
 *          $tmpButton = new Button("Some Button");
 *          //Adds a button to the Controls class of some Container object
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
 * 
 * @property string $Type The type of the button
 * The Type of this Button, the Default is "Normal", can also be set to "Submit".
 */
	class Button extends Control 
	{
		const Normal = 'Button';
		const Submit = 'Submit';
		
		private $Type;
		/**
		* Constructor.
		* for inherited components, be sure to call the parent constructor first
	 	* so that the component properties and events are defined.
	 	* Example
	 	*	<code> $tmpBut = new Button(0, 0, 80, 24, Button::Normal);</code>
	 	* @param string[optional] $text The Text of this element
		* @param integer[optional] $left The left coordinate of this element
		* @param integer[optional] $top The top coordinate of this element
		* @param integer[optional] $width The width of this element
		* @param integer[optional] $height The height of this element
		* @param string [optional] $type The type of this button
		*/
		function Button($text='', $left = 0, $top = 0, $width = 80, $height = 24, $type = Button::Normal)
		{
			parent::Control($left, $top, $width, $height);
			$this->SetType($type);
			$this->SetText($text);
		}
		/**
		 * Gets the type of this button, default button type is Button::Normal
		 *
		 * @return Button::Normal | Button::Submit 
		 */
		function GetType()
		{
			return ($this->Type === null)?Button::Normal:$this->Type;
		}
		/**
		 * Sets the type of this button, the default is Button::Normal
		 *
		 * @param string $type possible values are Button::Normal, Button::Submit
		 */
		function SetType($type)
		{
			$this->Type = ($type == Button::Normal?null:$type);
			NolohInternal::SetProperty('type', $type, $this);
		}
		/**
		 * This will set the text that is displayed on the button
		 *
		 * @param string $newText
		 */
		function SetText($newText)
		{
			parent::SetText($newText);
			NolohInternal::SetProperty('value', $newText, $this);
		}
		/**
		* @ignore
		*/
		function Show()
		{
			//$initialProperties = parent::Show();
			//$initialProperties .= ",'type','$this->Type'";
			NolohInternal::Show('INPUT', parent::Show(), $this);
		}
	}
?>