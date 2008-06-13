<?php
/**
 * TextArea class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Core
 */
class TextArea extends Control 
{
	private $MaxLength;
	private $Scrolling;	
	
	function TextArea($text = null, $left = 0, $top = 0, $width = 200, $height = 100, $maxLength = -1)  
	{
		parent::Control($left, $top, $width, $height);
		$this->SetMaxLength($maxLength);
		if($text != null)
			$this->SetText($text);
	}
	
	function GetMaxLength()
	{
		return $this->MaxLength;
	}
	
	function SetMaxLength($newMaxLength)
	{
		$this->MaxLength = $newMaxLength;
		NolohInternal::SetProperty('MaxLength', $newMaxLength, $this);
	}
	
	function SetText($text)
	{
		parent::SetText($text);
		QueueClientFunction($this, 'SetTextAreaText', array('\''.$this->Id.'\'', '\''.preg_replace("(\r\n|\n|\r)", '<Nendl>', addslashes($text)).'\''));
	}
	/*function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}*/
    function SetScrollLeft($scrollLeft)
    {
    	$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
        if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollLeft\'', $scrollLeft), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
        $this->ScrollLeft = $scrollLeft;
    }
    /*function GetScrollTop()
    {
    	return $this->ScrollTop;
    }*/
    function SetScrollTop($scrollTop)
    {
    	$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
    	if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
        $this->ScrollTop = $scrollTop;
    }
	function GetScroll()							{return $this->GetEvent('Scroll');}
	function SetScroll($newScroll)					{$this->SetEvent($newScroll, 'Scroll');}
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === null)
			return ',\'onchange\',\''.$this->GetEventString('Change').'\',\'onfocus\',\''.$this->GetEventString('Focus')/*."','onblur','".$this->GetEventString('LoseFocus')*/."'" .
				(GetBrowser()=='ie'
				?
					',\'onkeypress\',\'doKeyPress("'.$this->Id.'",this.MaxLength);\'' .
					',\'onpaste\',\'doPaste("'.$this->Id.'",this.MaxLength);\''
				:
					',\'onkeypress\',\'doKeyPress(event);\'');

		$preStr = '';
        switch($eventTypeAsString)
        {
			case 'Click':
            case 'Change':
			case 'DoubleClick':
			case 'LoseFocus':
                $preStr = '_NSave("'.$this->Id.'","value");';
                break;
            case 'Focus':
                $preStr = '_NFocus="'.$this->Id.'";';
                break;
            /*case 'LoseFocus':
                $preStr = "_NFocus=null;";
                break;*/
        }
		return $preStr . parent::GetEventString($eventTypeAsString);
	}
	function GetScrolling()
	{
		return $this->Scrolling;
	}
	function SetScrolling($scrollType)
	{
		$this->Scrolling = $scrollType;
		$tmpScroll = null;
		if($scrollType == System::Auto)
			$tmpScroll = 'auto';
		elseif($scrollType == System::Full)
			$tmpScroll = 'visible';
		elseif($scrollType === null)
			$tmpScroll = '';
		elseif($scrollType)
			$tmpScroll = 'scroll';
		else//if(!$scrollType)
			$tmpScroll = 'hidden';
		//Alert($tmpScroll);
		NolohInternal::SetProperty('style.overflow', $tmpScroll, $this);
	}
    function GetSelectedText()
    {
        return Event::$FocusedComponent == $this->Id ? Event::$SelectedText : '';
    }
	function Show()
	{
		$initialProperties = parent::Show();
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/".(GetBrowser() == "ie"?"IE":"Mozilla")."TextAreaScripts.js");
		$initialProperties .= $this->GetEventString(null);
		AddNolohScriptSrc('TextArea.js', true);
		NolohInternal::Show('TEXTAREA', $initialProperties, $this);
		if(GetBrowser() != 'ie')
			AddScript('_N(\''.$this->Id.'\').addEventListener(\'input\',doInput,false)');
	}
}

?>