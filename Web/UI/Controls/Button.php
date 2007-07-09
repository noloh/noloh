<?php
/**
 * Button class file.
 */
 
/**
 * Button class
 *
 * A Control for a Button
 *
 * Properties
 * - <b>Type</b>, string, 
 *   <br>Gets or Sets the Type of Button
 * 
 * You can use the Button as follows
 * <code>
 *
 *		function Foo()
 *		{
 *			$tempButton = new Button(0,0);
 * 			$this->Controls->Add($tempButton); //Adds a button to the Controls class of some Container
 *		}
 *		
 * </code>
 */
	class Button extends Control 
	{
		const Normal = "Button";
		const Submit = "Submit";
		/**
 		* Type, The Type of this Button, the Default is "Button", can also be set to "Reset" or "Submit".
 		* @var string
 		*/
		private $Type;
		
		/**
		* Constructor.
		* for inherited components, be sure to call the parent constructor first
	 	* so that the component properties and events are defined.
	 	* Example
	 	*	<code> $tempVar = new Button(0, 0, 80, 24, Button::Normal);</code>
	 	* @param string|optional
		* @param integer|optional
		* @param integer|optional
		* @param integer|optional
		* @param integer|optional
		* @param string|optional
		*/
		function Button($text="", $left = 0, $top = 0, $width = 80, $height = 24, $type = Button::Normal)
		{
			parent::Control($left, $top, $width, $height);
			$this->SetType($type);
			$this->SetText($text);
		}
		function GetType()
		{
			return ($this->Type === null)?Button::Normal:$this->Type;
		}
		function SetType($type)
		{
			$this->Type = ($type == Button::Normal?null:$type);
			NolohInternal::SetProperty("type", $type, $this);
		}
		function SetText($newText)
		{
			parent::SetText($newText);
			NolohInternal::SetProperty("value", $newText, $this);
		}
		/**
		* @ignore
		*/
		function Show()
		{
			$initialProperties = parent::Show();
			//$initialProperties .= ",'type','$this->Type'";
			NolohInternal::Show("INPUT", $initialProperties, $this);
		}
	}
?>