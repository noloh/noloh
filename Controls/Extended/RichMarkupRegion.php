<?php
/**
 * RichMarkupRegion class
 *
 * A RichMarkupRegion is an enhanced MarkupRegion that allows for static text to communicate with your NOLOH application. A RichMarkupRegion is unique in that its objects can be created dynamically from NOLOH, in addition to being able to have these objects communicate with NOLOH via ServerEvents. 
 * 
 * For more information and for a better understanding of the proper uses and techniques associated with RichMarkupRegion, please refer to the RichMarkupRegion article under the articles section of the documentation.
 * 
 * @package Controls/Extended
 */
class RichMarkupRegion extends MarkupRegion
{
	/**
	 * @ignore
	 */
	public $ComponentSpace;
	private $Eventees;
	private $EventSpace;
	private $Larvae;
	private $TempString;
	private $ItemCount;
	/**
	 * Constructor
	 * 
	 * @param string $markupStringOrFile A string of text or the path to a file you wish to parse and display in your RichMarkupRegion
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function RichMarkupRegion($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		$this->ComponentSpace = array();
		parent::MarkupRegion($markupStringOrFile, $left, $top, $width, $height);
	}
	/**
	 * @ignore
	 */
	function SetText($markupStringOrFile)
	{
		$this->ItemCount = 0;
		$this->Eventees = array();
		$this->Larvae = array();
		foreach($this->ComponentSpace as $component)
			$component->SecondGuessParent();
		$this->ComponentSpace = array();
		//$this->MarkupString = $markupStringOrFile;
        Control::SetText($markupStringOrFile);
        if($markupStringOrFile != null)
        {
        	//file_put_contents('/tmp/WeirdString', $markupStringOrFile);
	        if(is_file($markupStringOrFile))
				$text = file_get_contents($markupStringOrFile);
			else
				$text = &$markupStringOrFile;
			if(isset($this->InnerCSSClass))
 				$text = "<div id = 'Inner{$this->Id}' class = '{$this->InnerCSSClass}'>$text</div>";
//			 	$text = '<div class = \''. $this->InnerCSSClass . '\'>' . $text . '</div>';
			$fullString = &$this->ParseItems($text);
//			System::Log($fullString);
//			$text = &str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $tmpFullString);
			$text = str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $fullString);
        }
        else
        	$text = '';
		//		$this->AutoWidthHeight($tmpFullString);
		if($this->GetShowStatus()!==0)
//			ClientScript::Queue($this, '_NMkupSet', array($this, $text), true, Priority::High);
			AddScript('_NMkupSet(\'' . $this->Id. '\',\'' . $text. '\')', Priority::High);
		else
			$this->TempString = $text;
//		file_put_contents('/tmp/snakeinthegrass2', var_export($this->Eventees, true));
		
	}
	private function ParseItems($text)
	{
		$tmpText = preg_replace_callback(
			'!<n:(.*?)(\s+.*?)?\s*descriptor\s*=\s*(["\'])([^:]+?)(?::([^"\']+))?\3(.*?)(?:/\s*>|(?:>(.*?)</n:\1>))!is',
        	array(&$this, 'MarkupReplace'),
			$text
		);
  		return $tmpText;
	}
	private function MarkupReplace($matches)
	{
		++$this->ItemCount;
		$id = $this->Id . 'i' . $this->ItemCount;
	
		$innerContent = isset($matches[7])?$matches[7]:'';
		if(strcasecmp($matches[1], 'larva') === 0)
		{
			$this->Larvae[$id] = array($matches[4], $matches[5]);
			return '<div class=<NQt2>NLarva<NQt2> id=<NQt2>' . $id . '<NQt2>' . $matches[2].$matches[6].'>'.$innerContent.'</div>';
		}
		else 
		{
			$this->Eventees[$id] = array($matches[1], $matches[4], $matches[5]);
			return '<'.$matches[1].$matches[2]. 'id=<NQt2>'.$id.'<NQt2>'.$matches[6].'>'.$innerContent.'</'.$matches[1].'>';
		}
	}
	/**
	 * Returns an array of Eventee objects
	 * @return array
	 */
	public function GetEventees($byValue=null)
	{
		$eventees = array();
		if($byValue===null)
			foreach($this->Eventees as $id => $info)
			{
//				if($info[1] == "tools_services")
//					Alert("Blah: " . $this->Eventees["N30i1"][2]);
				if(isset($info[0]))
					$eventees[] = new Eventee($id, $info[0], $info[1], $info[2], $this->Id);
//				else
//					file_put_contents('/tmp/snakeinthegrass', var_export($info, true), FILE_APPEND);
			}	
		else 
			foreach($this->Eventees as $id => $info)
				if($info[1] == $byValue)
					$eventees[] = new Eventee($id, $info[0], $info[1], $info[2], $this->Id);
		return $eventees;
	}
	/**
	 * Returns an array of Larva objects
	 * @return array
	 */
	public function GetLarvae($byValue=null)
	{
		$larvae = array();

		if($byValue===null)
			foreach($this->Larvae as $id => $info)
				$larvae[] = new Larva($id, $info[0], $info[1], $this->Id);
		else 
			foreach($this->Larvae as $id => $info)
				if($info[0] == $byValue)
					$larvae[] = new Larva($id, $info[0], $info[1], $this->Id);
		return $larvae;
	}
	/**
	 * Returns an array of both Eventee and Larva objects
	 * @return array
	 */
	public function GetMarkupItems($byValue=null)
	{
		return array_merge($this->GetEventees($byValue), $this->GetLarvae($byValue));
	}
	/**
	 * @ignore
	 */
	public function UpdateEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::UpdateEvent($eventType);
		
		NolohInternal::SetProperty(Event::$Conversion[$eventType], $eventType, $eventeeId);
	}
	/**
	 * @ignore
	 */
	public function GetEvent($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::GetEvent($eventType);
		
		if(!isset($this->EventSpace[$eventeeId]))
			$this->EventSpace[$eventeeId] = array();
		if(!isset($this->EventSpace[$eventeeId][$eventType]))
			$this->EventSpace[$eventeeId][$eventType] = new Event(array(), array(array(array($this->Id, $eventeeId), $eventType)));
		return $this->EventSpace[$eventeeId][$eventType];
	}
	/**
	 * @ignore
	 */
	public function SetEvent($eventObj, $eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::SetEvent($eventObj, $eventType);
		
		if(!isset($this->EventSpace[$eventeeId]))
			$this->EventSpace[$eventeeId] = array();		
		$this->EventSpace[$eventeeId][$eventType] = $eventObj;
		//$pair = array(array($this->PanelId, $eventeeId), $eventType);
		$pair = array(array($this->Id, $eventeeId), $eventType);
		if($eventObj != null && !in_array($pair, $eventObj->Handles, true))
			$eventObj->Handles[] = $pair;
		$this->UpdateEvent($eventType, $eventeeId);
	}
	/**
	 * @ignore
	 */
	public function GetEventString($eventType, $eventeeId=null)
	{
		if($eventeeId == null)
			return parent::GetEventString($eventType);
		
		return $this->EventSpace[$eventeeId][$eventType]->GetEventString($eventType, $eventeeId);
	}
	/**
	 * @ignore
	 */
	public function ExecEvent($eventType, $eventeeId)
	{
		return $this->EventSpace[$eventeeId][$eventType]->Exec($execClientEvents=false);
	}
	/**
	 * @ignore
	 */
	public function Show()
	{
		parent::Show();
		ClientScript::Add('_NMkupSet(\''.$this->Id.'\',\''.$this->TempString.'\');', Priority::High);
		$this->TempString = null;
	}
	/**
	 * @ignore
	 */
	public function SearchEngineShow()
	{
		$markupStringOrFile = Control::GetText();
		if($markupStringOrFile != null)
        {
	        if(is_file($markupStringOrFile))
				$text = file_get_contents($markupStringOrFile);
			else
				$text = &$markupStringOrFile;
			$fullString = &$this->ParseItems($text);
//			$text = &str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $tmpFullString);
			$text = str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), $fullString);
        }
        $tag = $this->GetSearchEngineTag();
		echo '<', $tag, Control::SearchEngineShow(true),'>', preg_replace(array('/<Nendl>/', '/<NQt2>/', '/<NQt1>/', '/<([^<>]* )target\s*=([\'"])\w+\2\s*([^<>]*)>/'), array("\n", "\"", "'", '<$1$3>'), $text);
		foreach($this->ComponentSpace as $component)
			if($component instanceof Component)
				$component->SearchEngineShow();
		echo '</', $tag, '>';
	}
	/**
	 * @ignore
	 */
	function NoScriptShow($indent)
	{
		// Needs to parse TempString to get the Larvae, check if it is morphed in ComponentSpace
		// and show the Component inside the tag!
		$str = Control::NoScriptShowIndent($indent);
		if($str !== false)
		{
			$text = str_replace(array('<Nendl>', '<NQt2>', '<NQt1>'), array("\n", "\"", "'"), $this->TempString);
			echo $indent, '<DIV ', $str, ">\n", $text, "\n", $indent, "</DIV>\n";
		}
	}
}
?>