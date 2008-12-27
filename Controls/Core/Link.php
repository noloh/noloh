<?php
/**
 * Link class
 *
 * A Control for a conventional web link. A Link is a string of Text or Control (e.g., an Image) that can redirect a user to a different Destination,
 * either an external URL or by setting URL Tokens of your applcation, indicated by a Destination value of URL::Tokens. By default,
 * a text Link is blue and underlined, which has a familiar psychological appeal to anyone who has browsed the World Wide Web.
 * 
 * You can use the Link as follows
 * <pre>
 * function Foo()
 * {
 *	$tempLink = new Link(null, 0, 0);
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
	private $Control;
	private $Tokens;
	private $RemoveSubsequents;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Link
 	* Example
 	*	<pre> $tempVar = new Link(null, 0, 0, 80, 24);</pre>
 	* @param mixed $destination Either a URL string or URL::Tokens if you want the link to change tokens
 	* @param string|Control $textOrControl
	* @param integer $left The left coordinate of this element
	* @param integer $top The top coordinate of this element
	* @param integer $width The width of this element
	* @param integer $height The height of this element
	*/
	function Link($destination='', $textOrControl='', $left = 0, $top = 0, $width = 83, $height = 20)  
	{
		if(is_object($textOrControl))
		{
			if($textOrControl instanceof Control)
			{
				parent::Label(null, $left, $top, $width, $height);
				$this->SetControl($textOrControl);
			}
			else
				BloodyMurder('Invalid type passed into the 2nd parameter of Link constructor. Must be either a string or Control.');
		}
		else 
			parent::Label($textOrControl, $left, $top, $width, $height);
		$this->SetDestination($destination);
		$this->Tokens = array();
		$this->RemoveSubsequents = array();
	}
	/**
	 * Returns the destination for the Link, i.e., where the link will redirect the user after it is clicked. A
	 * value of '#' or null can be used for not redirecting anywhere but still having the look of a Link, useful for Click Events.
	 * A value of URL::Tokens indicates that this Link will point to specific application Tokens, which can be set by the SetToken function.
	 * @return mixed
	 */
	function GetDestination()
	{
		return $this->Destination;
	}
	/**
	 * Sets the destination for the Link, i.e., where the link will redirect the user after it is clicked. A
	 * value of '#' or null can be used for not redirecting anywhere but still having the look of a Link, useful for Click Events.
	 * A value of URL::Tokens indicates that this Link will point to specific application Tokens, which can be set by the SetToken function.
	 * @param mixed $destination
	 */
	function SetDestination($destination)
	{
		$this->Destination = $destination;
		NolohInternal::SetProperty('href', $destination===null?'#':$destination, $this);
	}
	/**
	 * Gets the value of a particular URL token to which the Link points. If that token has not been set, then $defaultValue will be returned
	 * @param string $tokenName
	 * @param string $defaultValue
	 * @return string
	 */
	function GetToken($tokenName, $defaultValue=null)
	{
		return isset($this->Tokens[$tokenName]) && $GLOBALS['_NURLTokenMode'] ? $this->Tokens[$tokenName] : $defaultValue;
	}
	/**
	 * Sets the value of a particular URL token to which the Link points.
	 * @param string $tokenName
	 * @param string $tokenValue
	 * @param boolean $removeSubsequentTokens If true, every token appearing after the current one will be removed
	 * @return string The value passed in
	 */
	function SetToken($tokenName, $tokenValue, $removeSubsequentTokens=false)
	{
		if($GLOBALS['_NURLTokenMode'] && (!isset($this->Tokens[$tokenName]) || $this->Tokens[$tokenName]!=$tokenValue))
		{
			/*if($tokenValue === null)
				unset($this->Tokens[$tokenName]);
			else*/
				$this->Tokens[$tokenName] = $tokenValue;
				$this->RemoveSubsequents[$tokenName] = $removeSubsequentTokens;
			/*
			if($removeSubsequentTokens)
			{
				reset($this->Tokens);
				for($position=1; key($this->Tokens)!=$tokenName; ++$position)
					next($this->Tokens);
				array_splice($this->Tokens, $position);
			}*/
			$this->UpdateTokens();
		}
		return $tokenValue;
	}
	/**
	 * @ignore
	 */
	function UpdateTokens()
	{
		NolohInternal::SetProperty('href', $this->Destination===null?('#/'.URL::TokenString($this->Tokens)):$this->Destination, $this);
		//NolohInternal::SetProperty('href', $destination===null?('#/'.URL::TokenString($this->Tokens)):$destination, $this);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === 'Click' && $this->Control !== null && $this->Target === null)
		{
			if($this->Destination === null)
				return '_NSetURL("' . URL::TokenString($this->Tokens) . '","' . $this->Id . '");' . parent::GetEventString($eventTypeAsString) . 'this.blur();';
			else
				return parent::GetEventString($eventTypeAsString) . 'location="' . $this->Destination . '";';
		}
		return parent::GetEventString($eventTypeAsString);
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
		$this->UpdateEvent('Click');
	}
	/**
	 * Returns the Control to be used as the Link, instead of a string of text.
	 * @return Control
	 */
	function GetControl()
	{
		return $this->Control;
	}
	/**
	 * Sets the Control to be used as the Link, instead of a string of text.
	 * @param Control $control
	 */
	function SetControl($control)
	{
		if($this->Control == null)
			$this->CSSClass .= ' NLnkCtrl';
		else 
			$this->Control->SetParentId(null);
		$control->SetParentId($this->Id);
		$this->Control = $control;
		unset($_SESSION['_NFunctionQueue'][$this->Id]['_NAWH']);
		NolohInternal::SetProperty('style.width', '', $this);
		NolohInternal::SetProperty('style.height', '', $this);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		if($this->Control != null)
		{
			$this->Control->SetParentId(null);
			Control::SetWidth($this->Width);
			Control::SetHeight($this->Height);
		}
		$this->CSSClass = str_replace('NLnkCtrl', '', $this->CSSClass);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function GetWidth()
	{
		return $this->Control == null ? parent::GetWidth() : $this->Control->GetWidth();
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		return $this->Control == null ? parent::GetHeight() : $this->Control->GetHeight();
	}
	/**
	 * @ignore
	 */
	function SetAllTokens()
	{
		foreach($this->Tokens as $key => $val)
			URL::SetToken($key, $val, $this->RemoveSubsequents[$key]);
		unset($GLOBALS['_NTokenUpdate'], $GLOBALS['_NInitialURLTokens']);
	}
	/**
	* @ignore
	*/	
	function Show()
	{
		//$initialProperties = Control::Show();
		//$initialProperties .= ",'style.wordWrap','break-word','style.overflow','hidden'";
		NolohInternal::Show('A', Control::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShow($indent);
		if($str !== false)
		{
			echo $indent, '<A href="', $this->Destination===null?($_SERVER['PHP_SELF'].'?'.URL::TokenString($this->Tokens)):$this->Destination, '" ', $str, '>';
			if($this->Control)
			{
				echo "\n";
				$this->Control->NoScriptShow($indent);
				echo $indent;
			}
			else 
				echo $this->Text;
			echo "</A>\n";
		}
	}
}
?>