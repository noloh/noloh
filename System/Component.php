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
abstract class Component extends Base
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
	 * Gets a Component by its Id
	 * @param string $id
	 * @return Component
	 */
	static function &Get($id)
	{
		return $GLOBALS['OmniscientBeing'][$id];
	}
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Component.
	 */
	function __construct()
	{
		$this->ShowStatus = 0;
		global $OmniscientBeing;
		$OmniscientBeing[$this->Id = 'N' . ++$_SESSION['_NNumberOfComponents']] = &$this;
		if($this instanceof Singleton)
		{
			$parent = new ReflectionClass(get_class($this));
			do
			{
				$class = $parent;
				$parent = $class->getParentClass();
			}while($parent && $parent->implementsInterface('Singleton'));
			$class = $class->getName();
			if(isset($_SESSION['_NSingletons'][$class]) && Component::Get($id = $_SESSION['_NSingletons'][$class]))
			{
				$lastTrash = is_array($_SESSION['_NGarbage']) ? count($_SESSION['_NGarbage']) : 0;
				$GLOBALS['_NGarbage'] = true;
				unset($OmniscientBeing[$id], $GLOBALS['_NGarbage']);
				if($lastTrash === (is_array($_SESSION['_NGarbage']) ? count($_SESSION['_NGarbage']) : 0))
					BloodyMurder('Cannot create more than one instance of a ' . $class . ' class because it is a Singleton.');
					//System::Log($lastTrash, (is_array($_SESSION['_NGarbage']) ? end($_SESSION['_NGarbage']) : null));
			}
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
	function SecondGuessParent()
	{
		if($this->ParentId != null && GetComponentById($this->ParentId) == null)
		{
			$regionId = substr($this->ParentId, 0, strpos($this->ParentId, 'i'));
			if(GetComponentById($regionId))
				unset($_SESSION['_NControlQueueDeep'][$regionId][$this->Id]);
			unset($_SESSION['_NControlQueueRoot'][$this->Id]);
			$this->ParentId = null;
			$this->ShowStatus = 0;
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
				if(isset($_SESSION['_NControlQueueRoot'][$this->Id]))
					unset($_SESSION['_NControlQueueRoot'][$this->Id]);
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
				return $parent ? ($parent instanceof $generation ? $parent : $parent->GetParent($generation)) : null;
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
		$handle = array($this->Id, $eventType);
		if($this->EventSpace === null)
			$this->EventSpace = array();
		elseif(!empty($this->EventSpace[$eventType]))
		{
			$handles = &$this->EventSpace[$eventType]->Handles;
			if(($index = array_search($handle, $handles)) !== false);
				array_splice($handles, $index, 1);
		}
		$this->EventSpace[$eventType] = $eventObj;
		if($eventObj != null && !in_array($handle, $eventObj->Handles, true))
			$eventObj->Handles[] = $handle;
		$this->UpdateEvent($eventType);
		return $eventObj;
	}
	/**
	 * @ignore
	 */
	function UpdateEvent($type)
	{
		if(UserAgent::GetName()===UserAgent::IPad)
		{
			if($type === 'MouseOver')
				return;
			Event::$Conversion['MouseDown'] = 'ontouchstart';
		}
		NolohInternal::SetProperty(isset(Event::$Conversion[$type])?Event::$Conversion[$type]:$type, array('GetEvPrStr', $type), $this);
	}
	/**
	 * @ignore
	 */
	function GetEvPrStr($type)
	{
		if(isset(Event::$Conversion[$type]))
			return '\''.$this->GetEventString($type).'\'';
		else
			return '_NEvent(\'' . $this->GetEventString($type) . '\',\'' . $this->Id . '\')';
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
	static function That()
	{
		if (func_num_args() === 1)
		{
			$className = func_get_arg(0);
		}
		elseif (function_exists('get_called_class'))
		{
			$className = get_called_class();
		}
		else
		{
			BloodyMurder('::That cannot get defining key');
		}
		
		if (!is_string($className)) {
			BloodyMurder('Parameter to That() must be a valid string');
		}
		return isset($_SESSION['_NSingletons'][$className]) ? GetComponentById($_SESSION['_NSingletons'][$className]) : null;
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
	}
	/**
	 * @ignore
	 */
	function SearchEngineShowChildren()
	{
		//$this->Show();
		if(!empty($_SESSION['_NControlQueueDeep'][$this->Id]))
			foreach($_SESSION['_NControlQueueDeep'][$this->Id] as $id => $show)
			{
				$obj = GetComponentById($id);
				if($show && $obj)
					$obj->SearchEngineShow();
			}
	}
	/**
	 * @ignore
	 */
	function NoScriptShowChildren($indent)
	{
		if(!empty($_SESSION['_NControlQueueDeep'][$this->Id]))
			foreach($_SESSION['_NControlQueueDeep'][$this->Id] as $id => $show)
			{
				$obj = GetComponentById($id);
				if($show && $obj)
					$obj->NoScriptShow($indent);
			}
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
	 * Removes the Component from its parent Collection. For example, if this object is a Button in a Panel, then calling $object->Leave would remove the Button from the Panel. 
	 */
	function Leave()
	{	
		$parent = $this->GetParent();
		if(isset($parent))
		{
            if($parent->HasProperty('Controls'))
            {
            	$controls = $parent->Controls;
            	if($controls instanceof ArrayList && $controls->ParentId === $parent->Id && $controls->Remove($this))
			        return true;
            }
            if($parent instanceof Iterator && $parent->HasMethod('Remove') && $parent->Remove($this))
				return true;
				
			$parentClass = get_class($parent);
			do
			{
				$reflect = new ReflectionClass($parentClass);
	            $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC + ReflectionProperty::IS_PROTECTED + ReflectionProperty::IS_PRIVATE);
		        foreach ($properties as $prop) 
				{
					$propName = $prop->getName();
					if($parent->HasProperty($propName))
					{
						$propValue = $parent->$propName;
						if($propValue instanceof ArrayList && $propValue->ParentId === $parent->Id && $propValue->Remove($this))
							return true;
					}
				}
				$parentClass = $reflect->getParentClass();
			}while($parentClass);
			$this->ParentId = null;
			return true;
		}
		return false;
	}
	/**
	 * Re-shows an object for one parent that was once shown under another parent but not removed.
	 * Should not be called under most circumstances. Should only be called in overriding the Adopt() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is shown on the client after being added to another parent.
	 */
	function Adopt()
	{
		NolohInternal::Adoption($this);
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
	function NoScriptShow($indent)	{}
	/**
	 * @ignore
	 */
	function GetSecure()
	{
		// Might be necessary for more advanced features, but should currently be off.
		return false;
	}
	/**
	 * @ignore
	 */
	function __wakeup()
	{

	}
	/**
	 * @ignore
	 */
	function __sleep()
	{
		/*if(isset($GLOBALS['_NChunking']))
			$GLOBALS['_NControlChunk'][$this->Id] = &$this;*/

		$vars = (array)$this;
		//global $OmniscientBeing;
		$keys = array();
		foreach ($vars as $key => $val)
		//{
        	//if(is_null($val))
        	if($val === null)
				unset($vars[$key]);
			else
				$keys[] = $key;
           /* elseif (is_object($val))
            	if($val instanceof Component)
					$this->$key = new Pointer($val);*/
        //}
		return $keys;
//		return array_keys($vars);
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