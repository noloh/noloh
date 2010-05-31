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
	 * A possible and default value for the Target property, Self indicates that the Link will open in the same window.
	 */
	const Self = '_self';
	/**
	 * A possible value for the Target property, NewWindow indicates that the Link will open either in a new browser window or a new tab, depending on the user's browser settings.
	 */
	const NewWindow = '_blank';
	
	private $Destination;
	private $Target;
	private $Control;
	private $Tokens;
	private $TokenChain;
	private $RemoveSubsequents;
	private $RemoveSubsequents2;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Link
 	* Example
 	*	<pre> $link = new Link(URL::Tokens, 'Click here', 0, 0, 80, 24);</pre>
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
		$this->RemoveSubsequents2 = array();
	}
	/**
	 * Returns the destination for the Link, i.e., where the link will redirect the user after it is clicked. A
	 * value of '#' or null can be used for not redirecting anywhere but still having the look of a Link, useful for Click Events.
	 * A value of URL::Tokens indicates that this Link will point to specific application Tokens, which can be set by the SetToken function.
	 * @return mixed
	 */
	function GetDestination()
	{
		return $this->Destination===null && $GLOBALS['_NURLTokenMode'] ? (System::FullAppPath().'#/'.$this->TokenString()) : $this->Destination;
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
			$this->Tokens[$tokenName] = $tokenValue;
			$this->RemoveSubsequents[$tokenName] = $removeSubsequentTokens;
			$this->QueueUpdateTokens();
		}
		return $tokenValue;
	}
	/**
	 * @ignore
	 */
	function GetChainToken($index, $defaultValue=null)
	{
		return isset($this->TokenChain[$index]) && $GLOBALS['_NURLTokenMode'] ? $this->TokenChain[$index] : $defaultValue;
	}
	/**
	 * @ignore
	 */
	function SetChainToken($index, $tokenValue, $removeSubsequentTokens=false)
	{
		$chain = &$this->GetTokenChain()->Elements;
		if($GLOBALS['_NURLTokenMode'] && (!isset($chain[$index]) || $chain[$index]!=$tokenValue))
		{
			$chain[$index] = $tokenValue;
			$this->RemoveSubsequents2[$index] = $removeSubsequentTokens;
			$this->QueueUpdateTokens();
		}
		return $tokenValue;
	}
	/**
	 * @ignore
	 */
	function GetTokenChain()
	{
		if(!$this->TokenChain)
			$this->TokenChain = new ImplicitArrayList($this, 'AddChainToken', 'RemoveChainTokenAt', 'ClearChainTokens');
		return $this->TokenChain;
	}
	/**
	 * @ignore
	 */
	function AddChainToken($value)
	{
		$this->QueueUpdateTokens();
		$this->TokenChain->Add($value, true);
	}
	/**
	 * @ignore
	 */
	function RemoveChainTokenAt($index)
	{
		$this->QueueUpdateTokens();
		$this->TokenChain->RemoveAt($index, true);
	}
	/**
	 * @ignore
	 */
	function ClearChainTokens()
	{
		$this->QueueUpdateTokens();
		$this->TokenChain->Clear(true);
	}
	/**
	 * @ignore
	 */
	function QueueUpdateTokens()
	{
		$GLOBALS['_NQueuedLinks'][$this->Id] = true;
	}
	/**
	 * @ignore
	 */
	function UpdateTokens()
	{
		NolohInternal::SetProperty('href', $this->Destination===null && $GLOBALS['_NURLTokenMode'] ? ('#/'.$this->TokenString()) : $this->Destination, $this);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function TokenString()
	{
		$tokens = array_merge($_SESSION['_NTokens']);
		foreach($this->Tokens as $key => $val)
			URL::SetTokenHelper($tokens, $key, $val, $this->RemoveSubsequents[$key]);
		$chain = array_merge(URL::$TokenChain->Elements);
		if($this->TokenChain)
			foreach($this->TokenChain as $key => $val)
				URL::SetTokenHelper($chain, $key, $val, $this->RemoveSubsequents2[$key]);
		return URL::TokenString($chain, $tokens);
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
			parent::SetCSSClass('NLnkCtrl ' . parent::GetCSSClass());
		else 
			$this->Control->SetParentId(null);
		$control->SetOpacity(parent::GetOpacity());
		$control->SetParentId($this->Id);
		$this->Control = $control;
		unset($_SESSION['_NFunctionQueue'][$this->Id]['_NAWH']);
		NolohInternal::SetProperty('style.width', '', $this);
		NolohInternal::SetProperty('style.height', '', $this);
		NolohInternal::SetProperty(UserAgent::IsIE() ? 'style.filter' : 'style.opacity', '', $this);
		//Control::SetOpacity(null);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		parent::SetText($text);
		if($this->Control)
		{
			$this->Control->SetParentId(null);
			Control::SetWidth($this->Width);
			Control::SetHeight($this->Height);
			Control::SetOpacity($this->Opacity);
			$this->Control = null;
		}
		parent::SetCSSClass(str_replace('NLnkCtrl', '', parent::GetCSSClass()));
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function SetCSSClass($class = '')
	{
		if($this->Control)
			parent::SetCSSClass('NLnkCtrl ' . str_replace('NLnkCtrl', '', $class));
		else
			parent::SetCSSClass($class);
	}
	/**
	 * @ignore
	 */
	function GetWidth()
	{
		return $this->Control === null ? parent::GetWidth() : $this->Control->GetWidth();
	}
	/**
	 * @ignore
	 */
	function GetHeight()
	{
		return $this->Control === null ? parent::GetHeight() : $this->Control->GetHeight();
	}
	/**
	 * @ignore
	 */
	function GetOpacity()
	{
		return $this->Control === null ? parent::GetOpacity() : $this->Control->GetOpacity();
	}
	/**
	 * @ignore
	 */
	function SetOpacity($opacity)
	{
		return $this->Control === null ? parent::SetOpacity($opacity) : $this->Control->SetOpacity($opacity);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === 'Click' && ($this->Control !== null || $this->Text !== null) && $this->Target === null)
		{
			return parent::GetEventString($eventTypeAsString) .
				($this->Destination === '#' ? 'return false;' :
				($this->Destination === null ? '_NSetTokens("'.$this->TokenString().'","'.$this->Id.'");this.blur();'
					: ('_NSetURL("'.$this->Destination.'","'.$this->Id.'");')));
		}
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */	
	function Show()
	{
		NolohInternal::Show('A', Control::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		if($this->Destination !== '#')
			echo '<A href="', $this->Destination===null && $GLOBALS['_NURLTokenMode'] ? ($_SERVER['PHP_SELF'].'?'.$this->TokenString()) : $this->Destination, '">', $this->Control ? $this->Control->SearchEngineShow() : $this->Text, '</A>';
		else 
		{
			parent::SearchEngineShow();
			if($this->Control)
				$this->Control->SearchEngineShow();
		}
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		$str = Control::NoScriptShow($indent);
		if($str !== false)
		{
			echo $indent, '<A href="', $this->Destination===null && $GLOBALS['_NURLTokenMode'] ? ($_SESSION['_NURL'].'?'.$this->TokenString()) : $this->Destination, '" ', $str, '>';
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