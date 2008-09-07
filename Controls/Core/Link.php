<?php
/**
 * Link class
 *
 * A Control for a conventional web link. A Link is a string of Text or Image that can redirect a user to a different Destination,
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
	private $Image;
	private $Tokens;
	private $RemoveSubsequents;
	/**
	* Constructor.
	* Be sure to call this from the constructor of any class that extends Link
 	* Example
 	*	<pre> $tempVar = new Link(null, 0, 0, 80, 24);</pre>
 	* @param mixed $destination Either a URL string or URL::Tokens if 
 	* @param string|Image $textOrImage
	* @param integer $left The left coordinate of this element
	* @param integer $top The top coordinate of this element
	* @param integer $width The width of this element
	* @param integer $height The height of this element
	*/
	function Link($destination='', $textOrImage='', $left = 0, $top = 0, $width = 83, $height = 20)  
	{
		if(is_object($textOrImage))
		{
			parent::Label(null, $left, $top, $width, $height);
			$this->SetImage($textOrImage);
		}
		else 
			parent::Label($textOrImage, $left, $top, $width, $height);
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
		NolohInternal::SetProperty('href', $destination===null?('#/'.URL::TokenString($this->Tokens)):$destination, $this);
		$this->UpdateEvent('Click');
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString == 'Click' && $this->Destination === null)
			return '_NSetURL("' . URL::TokenString($this->Tokens) . '","' . $this->Id . '");' . parent::GetEventString($eventTypeAsString);
			//return 'location="#/' . URL::TokenString($this->Tokens) . '";' . parent::GetEventString($eventTypeAsString);
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
	}
	/**
	 * Returns the Image to be used as the Link, instead of a string of text.
	 * @return Image
	 */
	function GetImage()
	{
		return $this->Image;
	}
	/**
	 * Sets the Image to be used as the Link, instead of a string of text.
	 * @param Image $image
	 */
	function SetImage($image)
	{
		if($this->Image != null)
			$this->Image->SetParentId(null);
		$image->SetParentId($this->Id);
		$this->Image = $image;
		if(Control::GetWidth() === System::Auto)
			Control::SetWidth($image->GetWidth());
		if(Control::GetHeight() === System::Auto)
			Control::SetHeight($image->GetHeight());
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
		echo $indent, '<A href="', $this->Destination, '" ', $str, '>';
		if($this->Image)
		{
			echo "\n";
			$this->Image->NoScriptShow($indent);
			echo $indent;
		}
		else 
			echo $this->Text;
		echo "</A>\n";
	}
}
?>