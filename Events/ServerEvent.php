<?php
/**
 * ServerEvent class
 *
 * A ServerEvent is a kind of Event that is executed on the server. This is probably one of the most-used classes in an application.
 * They are capable of calling a function on a particular object with particular parameters, or alternatively, a static function of
 * a class with particular parameters.
 * 
 * <pre>
 * // Instantiate a new Button
 * $btn = new Button("Click Me");
 * // Give the button a Click Event, so that when it is clicked, ButtonClicked("FirstParam", 2) will be called on the $this object
 * $btn->Click = new ServerEvent($this, "ButtonClicked", "FirstParam", 2);
 * </pre>
 * 
 * In addition, they are capable of setting particular FileUpload objects to upload when the event is launched. For example:
 * <pre>
 * class UploadForm extends Panel
 * {
 * 	function UploadForm($left, $top, $width, $height)
 * 	{
 * 		parent::Panel($left, $top, $width, $height);
 * 		// Instantiates a new FileUpload box
 * 		$fileUpload = new FileUpload();
 * 		// Instantiates a new Button below the FileUpload box
 * 		$btn = new Button("Submit", 0, 30);
 * 		// Gives the button a Click Event that passes the fileUpload as a parameter
 * 		$btn->Click = new ServerEvent($this, "ButtonClicked", $fileUpload);
 * 		// Tells the button's click to also upload the file
 * 		$btn->Click->Uploads->Add($fileUpload);
 * 	}
 * 	function ButtonClicked($fileUpload)
 * 	{
 * 		// Alerts the size of the uploaded file
 * 		Alert("Size of file: " . $fileUpload->File->GetSize() . " bytes");	
 * 	}
 * }
 * </pre>
 * See also:
 * @see FileUpload
 * @see File
 * 
 * @package Events
 */
class ServerEvent extends Event
{
	/**
	 * @ignore
	 */
	private $Owner;
	/**
	 * An ArrayList holding the FileUpload objects that the Event will upload when launched from the client
	 * @access public
	 * @var ArrayList
	 */
	private $Uploads;
	/**
	 * @ignore
	 */
	public $Parameters;
	
	/**
	 * @ignore
	 */
	static function GenerateString($eventType, $objId, $uploadArray)
	{
		return count($uploadArray) === 0
			? 'PostBack("' . $eventType . '","' . $objId . '",event);'
			: 'PostBackWithUpload("' . $eventType . '","' . $objId . '",[' . implode(',', $uploadArray) . '],event);';
	}
	/**
	 * Constructor
	 * @param string|object $objOrClassName Can be either a class name as a string such as "SpecialPanel" or an object such as $this
	 * @param string $functionAsString The name of the function as a string
	 * @param mixed $parametersAsDotDotDot An unlimited number of parameters that will in turn be passed as parameters into the specified function
	 * @return ServerEvent
	 */
	function ServerEvent($objOrClassName, $functionAsString, $parametersAsDotDotDot = null)
	{
		parent::Event($functionAsString);
		$this->Owner = is_object($objOrClassName) && $objOrClassName instanceof Component
			? new Pointer($objOrClassName)
			: $objOrClassName;
		$this->Uploads = new ArrayList();
		$this->Parameters = array_slice(func_get_args(), 2);
	}
	/**
	 * @ignore
	 */
	function GetUploadIds()
	{
		$arr = array();
		foreach($this->Uploads as $ul)
			$arr[] = "\\\"" . $ul->Id . "\\\"";
		return $arr;
	}
	/**
	 * @ignore
	 */
	function GetInfo(&$arr, &$onlyClientEvents)
	{
		$onlyClientEvents = false;
		return $this->Uploads->Count()==0 ? $arr : array_splice($arr[1], -1, 0, $this->GetUploadIds());
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventType, $objsId)
	{
		return $this->GetEnabled()
			? ServerEvent::GenerateString($eventType, $objsId, $this->GetUploadIds())
			: '';
	}
	/**
	 * @ignore
	 */
	function Blank()
	{
		return false;
	}
	/**
	 * Launches the particular event. That is, the specified function will be called on the specified object or class 
	 * using the specified parameters.
	 * <pre>
	 * // Instantiate a new Button
	 * $btn = new Button("Click Me");
	 * // Give it a Click ServerEvent
	 * $btn->Click = new ServerEvent($this, "ButtonClicked", "FirstParam", 2);
	 * // The following two lines are identical:
	 * $btn->Exec();
	 * $this->ButtonClicked("FirstParam", 2);
	 * </pre>
	 * @param boolean $execClientEvents Indicates whether client-side code will execute. <br>
	 * Modifying this parameter is highly discouraged as it may lead to unintended behavior.<br>
	 */
	function Exec(&$execClientEvents=true)
	{
		if($GLOBALS['_NQueueDisabled'] || $this->Enabled===false)
			return;
		$execClientEvents = true;		
		
		$runThisString = 'return ';
		if(is_object($this->Owner))
			if($this->Owner instanceof Pointer)
				$runThisString .= '$this->Owner->Dereference()->';
			else 
				$runThisString .= '$this->Owner->';
		elseif(is_string($this->Owner))
			$runThisString .= $this->Owner . '::';
			
		$runThisString .= $this->ExecuteFunction;
		
		if(strpos($this->ExecuteFunction,'(') === false)
		{
			$parameterCount = count($this->Parameters);
			$runThisString .= '(';
			for($i = 0; $i < $parameterCount - 1; ++$i)
				$runThisString .= '$this->Parameters['.$i.'],';
			$runThisString .= $parameterCount > 0 ? '$this->Parameters['.$i.']);' : ');';
		}
		else 
			$runThisString .= ';';
		
		$source = Event::$Source;
		$handles = array();
		$this->GetDeepHandles($handles);
		//Alert(count($handles));
		if(count($handles) == 1)
			Event::$Source = &$handles[0];
		else
			Event::$Source = $handles;
		$return = eval($runThisString);
		Event::$Source = &$source;
		return $return;
	}
	/**
	 * @ignore
	 */
	function __get($nm)
	{
		if($nm == 'Uploads')
		{
			if($this->Uploads == null)
				$this->Uploads = new ArrayList();
			return $this->Uploads;
		}
		else 
			return parent::__get($nm);
	}
}

?>