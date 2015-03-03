<?php
/**
 * Multimedia class
 * 
 * An Multimedia is a Control used for showing various multimedia, e.g., a Flash animation.
 * 
 * @package Controls/Core
 */
class Multimedia extends Control
{
	private $Data;
	private $Type;
	private $ClassId;
	private $IsMovie;
	/**
	 * Parameters for the Multimedia. Each parameter should be an Item. They correspond to PARAM tags for EMBED tags.
	 * @var ArrayList
	 */
	public $Parameters;
	/**
	 * Vars passed to Flash animations. Each var should be an Item.
	 * @var ArrayList
	 */
	public $FlashVars;
	/**
	 * Multimedia Controls to be used should this one fail.
	 * @var ArrayList
	 */
	public $InnerMultimedia;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Multimedia
	 * @param string $data The URL of the Multimedia
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width of this element
	 * @param integer $height The height of this element
	 * @param boolean $isMovie
	 * @return Multimedia
	 */
	public function Multimedia($data = null, $left = 0, $top = 0, $width = 100, $height = 100, $isMovie=false)
	{
		parent::Control($left, $top, null, null);
		$this->SetWidth($width);
		$this->SetHeight($height);
		$this->Parameters = new ImplicitArrayList($this, 'AddParameter', 'RemoveParameter', 'ClearParameters');
		$this->FlashVars = new ImplicitArrayList($this, 'AddFlashVar', 'RemoveFlashVar', 'ClearFlashVars');
		$this->InnerMultimedia = new ArrayList();
		$this->InnerMultimedia->ParentId = $this->Id;
		$this->SetData($data);
		$this->IsMovie = ($isMovie)?true:null;
		$this->BackColor = 'white';
	}
	/**
	 * Returns the URL of the Multimedia data
	 * @return string
	 */
	function GetData()
	{
		return $this->Data;
	}
	/**
	 * Sets the URL of the Multimedia data
	 * @param string $data
	 */
	function SetData($data)
	{
		if($data == null)
			return;
		$this->Data = $data;
		$splitString = explode('?', $data, 2);
		$splitString = explode('.', $splitString[0]);
		$extension = $splitString[count($splitString)-1];
		if($extension == 'swf')
		{
			$this->SetType('application/x-shockwave-flash');
			//$this->SetClassId('clsid:D27CDB6E-AE6D-11cf-96B8-444553540000');
//			$this->Parameters->Add (new Item('wmode', 'window'));
			$this->Parameters->Add (new Item('wmode', 'window'));
			$this->Parameters->Add(new Item('movie', $this->Data));
		}
		$this->QueueResetInnerString();
	}
	/**
	 * Returns the implementation URI for the Multimedia
	 * @return string
	 */
	function GetClassId()
	{
		return $this->ClassId;
	}
	/**
	 * Sets the implementation URI for the Multimedia
	 * @param string $classId
	 */
	function SetClassId($classId)
	{
		$this->ClassId = $classId;
		$this->QueueResetInnerString();
	}
	/**
	 * Returns the Content-type used for the Multimedia
	 * @return string
	 */
	function GetType()
	{
		return $this->Type;
	}
	/**
	 * Sets the Content-type used for the Multimedia
	 * @param string $type
	 */
	function SetType($type)
	{
		$this->Type = $type;
		$this->QueueResetInnerString();
	}
	/**
	 * @ignore
	 */
	function AddParameter(Item $item)
	{
//		if($this->GetShowStatus())
//		{
//			$initialProperties = ''name','$item->Text','value','$item->Value'';
//			NolohInternal::Show('PARAM', $initialProperties, $this, $this->Id);
//		}
		$this->QueueResetInnerString();
		$this->Parameters->Add($item, true);
	}
	/**
	 * @ignore
	 */
	function RemoveParameter(Item $item)
	{
		$this->QueueResetInnerString();
		$this->Parameters->Remove($item, true);
	}
	/**
	 * @ignore
	 */
	function ClearParameters()
	{
		$this->QueueResetInnerString();
		$this->Parameters->Clear(true);
	}
	/**
	 * @ignore
	 */
	function AddFlashVar($flashVar)
	{
		$this->QueueResetInnerString();
		$this->FlashVars->Add($flashVar, true);
	}
	/**
	 * @ignore
	 */
	function RemoveFlashVar($flashVar)
	{	
		$this->QueueResetInnerString();
		$this->FlashVars->Remove($flashVar, true);
	}
	/**
	 * @ignore
	 */
	function ClearFlashVars()
	{
		$this->QueueResetInnerString();
		$this->FlashVars->Clear(true);
	}
/*	function SetWidth($width)
	{
		parent::SetWidth($width);
		//QueueClientFunction($this, '_NChange', array("'".$this->Id . "I'", "'style.width'", "'100%'"), false);
//		QueueClientFunction($this, '_NChange', array("'".$this->Id . "I'", "'style.width'", "'".$width ."px'"), false);
		//QueueClientFunction($this, '_NChange', array("'".$this->Id . "E'", "'width'", "'".$width ."'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}
	function SetHeight($height)
	{
		parent::SetHeight($height);
		//QueueClientFunction($this, '_NChange', array("'".$this->Id . "I'", "'style.height'", "'100%'"), false);
//		QueueClientFunction($this, '_NChange', array("'".$this->Id . "I'", "'style.height'", "'".$height ."px'"), false);
		//NolohInternal::SetProperty("innerHTML", $this, $this);
	}*/
	/**
	 * @ignore
	 */
	function GetInnerString()
	{
		//AddScript('_NClearMM(\'' . $this->Id . '\');', Priority::High);
		$tmpStr = '<OBJECT name="'.$this->Id.'I" id="'.$this->Id.'I" style="width:100%;height:100%" ';
		if($this->Type != null)
			$tmpStr.='type="'.$this->Type.'" ';
		if($this->Data != null)
			$tmpStr.='data="'.$this->Data.'"';
		if($this->ClassId != null)
			$tmpStr.='classid = "'.$this->ClassId.'" ';
		$tmpStr.='>';
//		$paramCount = $this->Parameters->Count();
		foreach($this->Parameters as $item)
			$tmpStr .='<PARAM name = "'.$item->Text.'" value = "'.$item->Value.'">';
		$embedFlashVars='';
		if(count($this->FlashVars) > 0)
		{
			$flashVars = '';
			foreach($this->FlashVars as $var)
				$flashVars .= $var->Text . '=' . $var->Value . '&';
			$flashVars = rtrim($flashVars, '&');
			$tmpStr .= '<PARAM name = "FlashVars" value = "'. $flashVars . '">';
			$embedFlashVars = ' FlashVars="'. $flashVars . '" ';
		}
		if($this->IsMovie /*&& !UserAgent::IsIE()*/)/*$this->Type == 'application/x-shockwave-flash')*/
		{
//			$tmpStr .= "<EMBED name=\"{$this->Id}I\" type=\"$this->Type\" src=\"$this->Data\" $embedFlashVars width=\"$this->Width\" height=\"$this->Height\"></EMBED>";	
			$tmpStr .= "<EMBED name=\"{$this->Id}I\" type=\"$this->Type\" src=\"$this->Data\" $embedFlashVars width=100% height=100%></EMBED>";	
		}
//		$InnerMultimediaCount = $this->InnerMultimedia->Count();
//		for($i=0; $i < $InnerMultimediaCount; $i++)
//			$this->InnerMultimedia->Elements[$i]->Show($this->IndentLevel + 1);	
		//print(str_repeat("  ", $IndentLevel) . "</OBJECT></DIV>\n");
		
		$tmpStr .= '</OBJECT>';
		//Terrible way of doing this, but width and height gets reset
		return '\'' . str_replace("'", "\\'", $tmpStr) . '\'';
	}
	/**
	 * @ignore
	 */
	function QueueResetInnerString()
	{
		//QueueClientFunction($this, '_NSetInnerMMString', array('\''.$this->Id.'\'', ));
		NolohInternal::SetProperty('innerHTML', array('GetInnerString'), $this);
	}
	/**
	 * Returns a Talk Event, which gets launched when a Flash object attempts to talk to NOLOH. The arguments are stored in Event::$FlashArgs
	 * @return Event
	 */
	function GetTalk()				{return $this->GetEvent('Talk');}
	/**
	 * Sets a Talk Event, which gets launched when a Flash object attempts to talk to NOLOH. The arguments are stored in Event::$FlashArgs
	 * @param Event $event
	 */
	function SetTalk($event)		{$this->SetEvent($event, 'Talk');}
	/**
	 * Calls a function of the Multimedia, e.g., a function defined in Flash
	 * @param string $function The name of the function
	 * @param mixed,... $argsDotDotDot An unlimitted list of parameters to be passed into the function
	 */
	function InvokeFunction($function, $argsDotDotDot=null)
	{
//		$params = array_slice(func_get_args(), 1);
		$params = func_get_args();
		array_splice($params, 0, 0, $this->Id);
		$count = count($params);
		for($i=0; $i<$count; ++$i)
			$params[$i] = ClientEvent::ClientFormat($params[$i]);
		QueueClientFunction($this, '_NFlashInvoke', $params, false);
	}
	/**
	 * @ignore
	 */
	public function Show()
	{	
		AddNolohScriptSrc('Multimedia.js');
		NolohInternal::Show('DIV', parent::Show(), $this);
	}
	/**
	 * @ignore
	 */
	function __call($name, $args)
	{
		array_splice($args, 0, 0, $name);
		call_user_func_array(array($this, 'InvokeFunction'), $args);
	}
}
?>