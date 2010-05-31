<?php
/**
 * Listener class
 * 
 * The Listener Component gives the developer the ability to listen to some specified source of data (such as a file, database, some function, etc...) 
 * and react to changes to them real-time. More specifically, the Event stored in its Update property will be triggered.
 * In this way, it faithfully implements what is sometimes known as "push technology" or "Comet."
 * 
 * @package Controls/Core
 */
class Listener extends Component
{
	/**
	 * A possible (and default) value for the Listener's Transport property, Listener::LongPoll means that the Listener will try to establish one long-lived connection to the server (per Update) and while remaining connected, to poll the data server-side until an update is detected. After the update, the connection is closed, and the process starts over with the new connection.
	 */
	const LongPoll = 'LongPoll';
	/**
	 * A possible value for the Listener's Transport property, Listener::Stream means that the Listener will try to establish one persistent, long-lived connection to the server and while remaining connected, to poll the data server-side until an update is detected. Please note that anything that interferes with the server's output stream (e.g., compression) might possibly break this Transport.
	 */
	const Stream = 'Stream';
	/**
	 * A possible value for the Listener's Transport property, Listener::Poll means that the Listener will try to periodically poll the data by establishing a new connection to the server every time.
	 */
	const Poll = 'Poll';
	/**
	 * Within the context of your Update Event function, Listener::$Data will hold the updated, most recent data found in the data Source.
	 * @var mixed
	 */
	public static $Data;
	/**
	 * When enabled, NOLOH will automatically start and close the session when it is needed. This has the effect of making an application with a Listener instance generally more responsive to the user's ServerEvents, but might in turn slow down the Listener's own processes.
	 * The name is intended to remind the developer of automatic, sliding doors, opening and closing on-demand.
	 * @var boolean
	 */
	public $SlidingSession = true;
	
	private $Source;
	private $GetDataMethod;
	private $Interval;
	private $Transport;
	private $Enabled;
	private $SavedData;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Listener.
	 * @param File|DataCommand|ServerEvent $source
	 * @param Event $update
	 * @param Listener::Stream|Listener::LongPoll|Listener::Poll $transport
	 * @param integer $interval
	 * @return Listener
	 */
	function Listener($source, $update = null, $transport = self::LongPoll, $interval = 500)
	{
		parent::Component();
		$this->SetSource($source);
		if($update)
			$this->SetUpdate($update);
		$this->Transport = $transport;
		$this->Interval = $interval;
	}
	/**
	 * Returns the Listener's Source of data.
	 * @return File|DataCommand|ServerEvent|string
	 */
	function GetSource()
	{
		return $this->Source;
	}
	/**
	 * Sets the Listener's Source of data.
	 * @param File|DataCommand|ServerEvent|string $source
	 */
	function SetSource($source)
	{
		$this->Source = $source;
		$this->GetDataMethod = 'GetDataFrom' . (is_object($source) ? get_class($source) : 
			(is_string($source) ? 'String' : BloodyMurder('Invalid type (' . gettype($source) . ') passed in as a Listener Source.')));
		$this->SavedData = $this->GetData();
	}
	/**
	 * Returns the Listener's Transport, or in other words, how the server and client will actually implement the concept. Different Transports have different performance and compatibility advantages, which are individually documented.
	 * @return Listener::Poll|Listener::LongPoll|Listener::Stream
	 */
	function GetTransport()
	{
		return $this->Transport;
	}
	
	/**
	 * Sets the Listener's Transport, or in other words, how the server and client will actually implement the concept. Different Transports have different performance and compatibility advantages, which are individually documented.
	 * @param Listener::Poll|Listener::LongPoll|Listener::Stream $transport
	 */
	function SetTransport($transport)
	{
		$this->Transport = $transport;
		if($this->GetShowStatus() === 1 && $this->GetParentId())
			self::Adopt();
		return $transport;
	}
	/**
	 * Returns the Listener's Interval, which is the number of miliseconds in between polls. Note that what is meant by the term "poll" depends on the Transport that is used, and doesn't necessarily imply that the browser will poll the server; sometimes, the server polls itself, and then the Interval property would still be relevant. 
	 * @return $integer
	 */
	function GetInterval()
	{
		return $this->Interval;
	}
	/**
	 * Sets the Listener's Interval, which is the number of miliseconds in between polls. Note that what is meant by the term "poll" depends on the Transport that is used, and doesn't necessarily imply that the browser will poll the server; sometimes, the server polls itself, and then the Interval property would still be relevant. 
	 * @param integer $interval
	 */
	function SetInterval($interval)
	{
		$this->Interval = $interval;
		if($this->GetShowStatus() === 1 && $this->GetParentId())
			self::Adopt();
		return $interval;
	}
	private function GetData()					{return $this->{$this->GetDataMethod}();}
	private function GetDataFromDataCommand()	{return $this->Source->Execute();}
	private function GetDataFromFile()			{return $this->Source->GetContent();}
	private function GetDataFromServerEvent()	{return $this->Source->Exec();}
	private function GetDataFromString()		{return file_get_contents($this->Source);}
	private function CheckData()
	{
		$data = $this->GetData();
		if($data != $this->SavedData)
		{
			if($this->SlidingSession)
				@session_start();
			Listener::$Data = $data;
			$this->GetUpdate()->Exec();
			$this->SavedData = $data;
			Listener::$Data = null;
			return true;
		}
		return false;
	}
	/**
	 * @ignore
	 */
	public static function Process($postVal)
	{
		ob_end_clean();
		//@apache_setenv('no-gzip', 1);
		//ini_set('zlib.output_compression', 0);
	    //ini_set('implicit_flush', 1);
	    //for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
	    ob_implicit_flush(1);
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		//if(!UserAgent::IsIE())
		//	header('Transfer-Encoding: chunked');
		//else
			header('Content-Type: text/html; charset=UTF-8');
		//flush();
		/*ob_start();
		ob_end_flush();
		flush();
		usleep(50000);*/
	    
		/*echo str_repeat(' ', 4096);
		ob_flush();
		flush();*/
		
		$arr = explode(',', $postVal);
		$count = count($arr);
		$listener = Component::Get($arr[0]);
		$transport = $listener->Transport;
		$interval = $listener->Interval;
		$slidingSession = $listener->SlidingSession;
		for($i=1; $i<$count; ++$i)
		{
			unset($listener);
			$listener = Component::Get($arr[$i]);
			if($interval > $listener->Interval)
				$interval = $listener->Interval;
			$slidingSession = $slidingSession || $listener->SlidingSession; 
		}
		
		
		
		if($transport === self::LongPoll || $transport === self::Stream)
		{
			$microInterval = $interval * 1000;
			++$_SESSION['_NVisit'];
			$gzip = defined('FORCE_GZIP');
			$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($GLOBALS['OmniscientBeing']),1) : serialize($GLOBALS['OmniscientBeing']);
			//ini_set('session.use_cookies', 0);
			if($slidingSession)
				session_write_close();
			ignore_user_abort(true);
			$updated = false;
			$loop = true;
			do
			{
				$updates = false;
				for($i=0; $i<$count; ++$i)
					$updates = Component::Get($arr[$i])->CheckData() || $updates;
				if($updates)
				{
					if(isset($GLOBALS['_NTokenUpdate']) && (!isset($_POST['_NSkeletonless']) || !UserAgent::IsIE()))
						URL::UpdateTokens();
					NolohInternal::Queues();
					if($transport === self::Stream && UserAgent::IsIE())
//						echo str_repeat(' ', 1024), $_SESSION['_NScriptSrc'] . '/*_N*/' . $_SESSION['_NScript'][0] . $_SESSION['_NScript'][1] . $_SESSION['_NScript'][2] . '/*_N2*/';
						echo str_repeat(' ', 1024), str_replace(array("\\", "\n"), array("\\\\", "\\n"), htmlspecialchars($_SESSION['_NScriptSrc'] . '/*_N*/' . $_SESSION['_NScript'][0] . $_SESSION['_NScript'][1] . $_SESSION['_NScript'][2] . '/*_N2*/'));
//						echo str_repeat(' ', 1024), str_replace(array("\\", "\n"), array("\\\\", "\\n"), htmlentities($_SESSION['_NScriptSrc'])), '/*_N*/', htmlentities($_SESSION['_NScript'][0]), htmlentities($_SESSION['_NScript'][1]), htmlentities($_SESSION['_NScript'][2]), '/*_N2*/';
					else
						echo $_SESSION['_NScriptSrc'], '/*_N*/', $_SESSION['_NScript'][0], $_SESSION['_NScript'][1], $_SESSION['_NScript'][2], '/*_N2*/';
					ob_flush();
					flush();
					$_SESSION['_NScriptSrc'] = '';
					$_SESSION['_NScript'] = array('', '', '');
					//$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($GLOBALS['OmniscientBeing']),1) : serialize($GLOBALS['OmniscientBeing']);
					if($slidingSession)
					{
						$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($GLOBALS['OmniscientBeing']),1) : serialize($GLOBALS['OmniscientBeing']);
						session_write_close();
					}
					if($transport === self::LongPoll || connection_aborted())
						$loop = false;
					$updated = true;
				}
				else
				{
					echo ' ';
					ob_flush();
					flush();
					if(connection_aborted())
						$loop = false;
					else
						usleep($microInterval);
				}
			}while($loop);
			if(!$slidingSession && isset($updated))
				$_SESSION['_NOmniscientBeing'] = $gzip ? gzcompress(serialize($GLOBALS['OmniscientBeing']),1) : serialize($GLOBALS['OmniscientBeing']);
			//session_write_close();
			/*$GLOBALS['_NGarbage'] = true;
			unset($OmniscientBeing, $GLOBALS['OmniscientBeing']);
			unset($GLOBALS['_NGarbage']);*/
			exit();
		}
		elseif($transport === 'Poll')
			for($i=0; $i<$count; ++$i)
				Component::Get($arr[$i])->CheckData();
	}
	/**
	 * Returns the Evesnt associated with the Listener detecting that the data has been updated
	 * @return Event
	 */
	function GetUpdate()
	{
		return $this->GetEvent('Update');
	}
	/**
	 * Sets the Event associated with the Listener detecting that the data has been updated
	 * @param Event $change
	 */
	function SetUpdate($update)
	{
		return $this->SetEvent($update, 'Update');
	}
	/**
	 * Returns whether the Listener is Enabled or disabled. A disabled Listener will not listen, and therefore, never trigger its Update Event. 
	 * @return boolean
	 */
	function GetEnabled()
	{
		return $this->Enabled === null;
	}
	/**
	 * Sets whether the Listener is Enabled or disabled. A disabled Listener will not listen, and therefore, never trigger its Update Event.
	 * @param boolean $enabled
	 */
	function SetEnabled($enabled)
	{
		if($enabled)
		{
			$this->Enabled = null;
			$enabledStr = 'true';
		}
		else
		{
			$this->Enabled = false;
			$enabledStr = 'false';
		}
		
		//ClientScript::Set($this, 'Enabled', $enabled, null, null);
		QueueClientFunction($this, '_NChangeByObj', array('_N.'.$this->Id, '\'Enabled\'', $enabledStr), false);
	}
	/**
	 * @ignore
	 */
	function UpdateEvent($eventType)
	{
		QueueClientFunction($this, '_NChangeByObj', array('_N.'.$this->Id, '\'Update\'', '\''.$this->GetEvent($eventType)->GetEventString($eventType,$this->Id).'\''));
	}
    /**
     * @ignore
     */
    function Show()
    {
        parent::Show();
        AddNolohScriptSrc('Listener.js', true);
        $this->Reshow();
    }
    /**
     * @ignore
     */
    function Reshow()
    {
        if($this->GetShowStatus() === 1 && ($parentId = $this->GetParentId()))
            ClientScript::Queue($this, 'new _NListener', array($parentId, $this->Id, $this->Transport, $this->Interval), true, Priority::High);
    }
    /**
     * @ignore
     */
    function Bury()
    {
        ClientScript::Add('_N.'.$this->Id.'.Destroy();', Priority::High);
        parent::Bury();
    }
    /**
     * @ignore
     */
    function Adopt()
    {
        ClientScript::Add('_N.'.$this->Id.'.Destroy();', Priority::High);
        $this->Reshow();
    }
    /**
     * @ignore
     */
    function Resurrect()
    {
        parent::Resurrect();
        $this->Reshow();
    }
}
?>