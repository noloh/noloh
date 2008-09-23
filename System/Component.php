<?php
/**
 * Component class
 *
 * A component is a basic building-block in a NOLOH application. It is an abstract class, so you may not instantiate a new Component, only write classes that extend this class.<br>
 * Each component has an <b>Id</b> that uniquely identifies itself among all other components.<br>
 * A component may have a <b>Parent</b>, which establishes a tree based on the parent-child relationship.<br>
 * 
 * @package System
 */

abstract class Component extends Object
{
	/**
	 * A possible ShowStatus for the Component. NotShown indicates that the Component has never been shown.
	 */
	const NotShown = 0;
	/**
	 * A possible ShowStatus for the Component. Shown indicates that the Component is present on the client.
	 */
	const Shown = 1;
	/**
	 * A possible ShowStatus for the Component. Buried indicates that the Component is has been shown and then removed.
	 */
	const Buried = 2;
	
	private $EventSpace;
	/**
	 * A unique Id for the component
	 * @var string
	 */
	public $Id;
	private $ParentId;
	private $ShowStatus;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Component.
	 */ 
	function Component()
	{
		$this->ShowStatus = 0;
		global $OmniscientBeing;
		$OmniscientBeing[$this->Id = 'N' . ++$_SESSION['_NNumberOfComponents']] = &$this;
		if($this instanceof Singleton)
		{
			$class = get_class($this);
			if(isset($_SESSION['_NSingletons'][$class]))
				BloodyMurder('Cannot create more than one instance of a ' . $class . ' class because it is a Singleton.');
			else
				$_SESSION['_NSingletons'][$class] = $this->Id;
		}
	}
	/**
	 * Whether the component has never been shown, has been shown, or has been shown and removed
	 * @return mixed
	 */
	function GetShowStatus()
	{
		return $this->ShowStatus === null ? 1 : $this->ShowStatus;
	}
	/**
	* @ignore
	*/
	function SecondGuessShowStatus()
	{
		if($this->ParentId != null && GetComponentById($this->ParentId) == null)
			$this->ShowStatus = 0;
	}
	/**
	 * @ignore
	 */
	function SecondGuessParent()
	{
		if($this->ParentId != null && GetComponentById($this->ParentId) == null)
		{
			$this->ParentId = null;
			unset($_SESSION['_NControlQueue'][$this->Id]);
			//unset($_SESSION['_NControlQueue'][$id]);
		}
	}
	/**
	 * Gets the Id of the immediate Parent Component
	 * @return string 
	 */
	function GetParentId() 
	{
		return $this->ParentId;
	}
	/**
	 * Sets the ParentId of the Component. The Component whose Id is passed in will become the new Parent of this Component. <br>
	 * Note that an ArrayList with a ParentId will automatically do this for you. {@link ArrayList::ParentId}
	 * @param string $parentId The Id of the Parent Component
	 */
	function SetParentId($parentId)
	{
		if($this->ParentId !== $parentId)
		{
			if($parentId === null)
				if($this->ShowStatus===null)
					$_SESSION['_NControlQueueRoot'][$this->Id] = false;
				else 
					unset($_SESSION['_NControlQueueRoot'][$this->Id], $_SESSION['_NControlQueueDeep'][$this->ParentId][$this->Id]);
			else 
			{
				if($this->ParentId !== null)
					unset($_SESSION['_NControlQueueRoot'][$this->Id], $_SESSION['_NControlQueueDeep'][$this->ParentId][$this->Id]);
				if(GetComponentById($parentId)->ShowStatus !== 0)
					$_SESSION['_NControlQueueRoot'][$this->Id] = true;
				else 
					if(isset($_SESSION['_NControlQueueDeep'][$parentId]))
						$_SESSION['_NControlQueueDeep'][$parentId][$this->Id] = true;
					else 
						$_SESSION['_NControlQueueDeep'][$parentId] = array($this->Id => true);
			}
			$this->ParentId = $parentId;
		}
	}
	/**
	 * @ignore
	 */
	function SetMorphedParentId($parentId)
	{
		$this->ParentId = $parentId;
		$regionId = substr($parentId, 0, strpos($parentId, 'i'));
		if(GetComponentById($regionId)->GetShowStatus()!==0)
			$_SESSION['_NControlQueueRoot'][$this->Id] = true;
		else 
			if(isset($_SESSION['_NControlQueueDeep'][$regionId]))
				$_SESSION['_NControlQueueDeep'][$regionId][$this->Id] = true;
			else 
				$_SESSION['_NControlQueueDeep'][$regionId] = array($this->Id => true);
	}
	/**
	 * Gets Parent of this Component, or Parent based on the $generation paramater as follows:<br>
	 * If $generation is an integer, it will return that number of Parents above, e.g., GetParent(2) will return the grandparent.<br>
	 * If $generation is a string, it will return the closest ancestor that is an instance of the class passed in.
	 * <pre>
	 * // Sets $parentPanel to the Panel that is the closest ancestor of $btn
	 * $parentPanel = $btn->GetParent('Panel');
	 * </pre>
	 * @param integer|string $generation
	 * @return Component
	 */
	function GetParent($generation = 1)
	{
		if($this->ParentId !== null)
			if(is_int($generation))
			{
				if($generation == 1)
					return GetComponentById($this->ParentId);
				elseif($generation > 1)
					return GetComponentById($this->ParentId)->GetParent($generation-1);
				else 
					return $this;
			}
			elseif(is_string($generation))
			{
				$parent = GetComponentById($this->ParentId);
				return $parent instanceof $generation ? $parent : $parent->GetParent($generation);
			}
			elseif(is_array($generation))
			{
				$parent = GetComponentById($this->ParentId);
				$count = count($generation);
				for($i=0; $i<$count; $i++)
					if($parent instanceof $generation[$i])
						return $parent;
				return $parent->GetParent($generation);
			}
		return null;
	}
	/**
	* @ignore
	*/
	function GetAddId($obj)
	{
		return $this->GetParent()->GetAddId($obj);
	}
	/**
	 * Gets an Event with a particular name. Should be called by your own custom Events, as this enables certain functionality, e.g., the use of array square bracket notation for appending to an event.
	 * @param string $eventType
	 * @return Event
	 */
	function GetEvent($eventType)
	{
		if($this->EventSpace === null)
			$this->EventSpace = array();
		return isset($this->EventSpace[$eventType]) 
			? $this->EventSpace[$eventType]
			: new Event(array(), array(array($this->Id, $eventType)));
	}
	/**
	 * Sets an Event with a particular name. Should be called by your own custom Events, as this enables certain functionality, e.g., the use of array square bracket notation for appending to an event.
	 * @param Event $eventObj The Event object
	 * @param string $eventType The name of the event
	 * @return Event The Event Object that was passed in
	 */
	function SetEvent($eventObj, $eventType)
	{
		if($this->EventSpace === null)
			$this->EventSpace = array();
		$this->EventSpace[$eventType] = $eventObj;
		$pair = array($this->Id, $eventType);
		if($eventObj != null && !in_array($pair, $eventObj->Handles, true))
			$eventObj->Handles[] = $pair;
		$this->UpdateEvent($eventType);
		return $eventObj;
	}
	/**
	 * @ignore
	 */
	function UpdateEvent($eventType)
	{
		NolohInternal::SetProperty($eventType, array($eventType, null), $this);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventType)
	{
		return isset($this->EventSpace[$eventType])
			? $this->EventSpace[$eventType]->GetEventString($eventType, $this->Id)
			: '';
	}
	/**
	 * Returns the only instance of a specified class that implements Singleton. Should not be called directly outside of your own That() methods.
	 * @param string $className The name of the class, as a string.
	 */
	static function That($className)
	{
		return isset($_SESSION['_NSingletons'][$className]) ? $_SESSION['_NSingletons'][$className] : null;
	}
	/**
	 * Shows the Component.
	 * Should not be called under most circumstances. Should only be called in overriding the Show() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is first shown on the client.
	 */
	function Show()
	{
		$this->ShowStatus = null;
		if(isset($_SESSION['_NControlQueueRoot'][$this->Id]))
			unset($_SESSION['_NControlQueueRoot'][$this->Id]);
		return '\'id\',\''.$this->Id.'\'';
	}
	
	function NoScriptShowChildren($indent)
	{
		if(!empty($_SESSION['_NControlQueueDeep'][$this->Id]))
			foreach($_SESSION['_NControlQueueDeep'][$this->Id] as $id => $true)
				GetComponentById($id)->NoScriptShow($indent);
	}
	/**
	 * The opposite of Showing. If the Component has a client-side aspect, it will be removed from the client.
	 * Should not be called under most circumstances. Should only be called in overriding the Bury() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is removed from the client.
	 */
	function Bury()
	{
		$this->ShowStatus = 2;
		if(isset($_SESSION['_NControlQueueRoot'][$this->Id]))
		{
			unset($_SESSION['_NControlQueueRoot'][$this->Id]);
			$this->ParentId = null;
		}
	}
	/**
	 * Re-shows an object that was once shown, then buried.
	 * Should not be called under most circumstances. Should only be called in overriding the Resurrect() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is shown on the client after the first time.
	 */
	function Resurrect()
	{
		$this->ShowStatus = null;
		if(isset($_SESSION['_NControlQueueRoot'][$this->Id]))
			unset($_SESSION['_NControlQueueRoot'][$this->Id]);
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()		{}
	/**
	* @ignore
	*/
	function __sleep()
	{
		/*if(isset($GLOBALS['_NChunking']))
			$GLOBALS['_NControlChunk'][$this->Id] = &$this;*/
			
		$vars = (array)$this;
		//global $OmniscientBeing;
		foreach ($vars as $key => $val)
		//{			
        	//if(is_null($val))
        	if($val === null)
				unset($vars[$key]);
           /* elseif (is_object($val))
            	if($val instanceof Component)
					$this->$key = new Pointer($val);*/
        //}
		return array_keys($vars);
	}
	/**
	 * @ignore
	 *
	function GetChunk()
	{
		$GLOBALS['_NChunking'] = true;
		$GLOBALS['_NControlChunk'] = array();
		serialize($this);
		$arr = $GLOBALS['_NControlChunk'];
		unset($GLOBALS['_NChunking'], $GLOBALS['_NControlChunk']);
		return $arr;
	}
	/**
	* @ignore
	*/
	function __toString()
	{
		return $this->Id;
	}
	/**
	* @ignore
	*/
	function __destruct()
	{
		if(isset($GLOBALS['_NGarbage']))
		{
			$id = $this->Id;
			unset($_SESSION['_NControlQueueRoot'][$id],
				$_SESSION['_NControlQueueDeep'][$id],
				$_SESSION['_NFunctionQueue'][$id],
				$_SESSION['_NPropertyQueue'][$id]);
			$_SESSION['_NGarbage'][$id] = true;
		}
	}
}

?>