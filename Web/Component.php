<?php
/**
 * @package Web
 * Component class file.
 */
 
/**
 * Component class
 *
 * A component is a basic building-block in a NOLOH application. It is an abstract class, so you may not instantiate a new Component, only write classes that extend this class.<br>
 * Each component has an <b>Id</b> that uniquely identifies itself among all other components.<br>
 * A component may have a <b>Parent</b>, which establishes a tree based on the parent-child relationship.<br>
 *
 * @property-read Component::(NotShown\Shown\Buried) $ShowStatus Whether it is Shown, NotShown, Buried
 * @property-read Component $Parent The Parent Component
 */

abstract class Component extends Object
{
	const NotShown = 0;
	const Shown = 1;
	const Buried = 2;
	/**
	 * Id of the component
	 * @var string
	 */
	public $Id;
	/**
	 * ParentId of the component
	 * @access private
	 * @var string
	 */
	private $ParentId;
	/**
	 * Whether it is not shown, shown, or in the graveyard
	 * @access private
	 * @var integer
	 */
	private $ShowStatus;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Component.
	 */ 
	function Component()
	{
		$this->ShowStatus = 0;
		global $OmniscientBeing;
		$OmniscientBeing[$this->Id = 'N' . ++$_SESSION['NOLOHNumberOfComponents']] = &$this;
	}
	/**
	 * Whether the component has never been shown, has been shown, or has been shown and removed
	 * @return Component::(NotShown\Shown\Buried)
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
			unset($_SESSION['NOLOHControlQueue'][$id]);
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
		$bool = $parentId != null;
		if($bool)
			$this->ParentId = $parentId;
		$_SESSION['NOLOHControlQueue'][$this->Id] = $bool;
	}
	/**
	 * Gets Parent of this Component, or Parent based on the $generation paramater as follows:<br>
	 * If $generation is an integer, it will return that number of Parents above, e.g., GetParent(2) will return the grandparent.<br>
	 * If $generation is a string, it will return the closest ancestor that is an instance of the class passed in.
	 * <code>
	 * // Sets $parentPanel to the Panel that is the closest ancestor of $btn
	 * $parentPanel = $btn->GetParent('Panel');
	 * </code>
	 * @param integer|string $generation
	 * @return Component
	 */
	function GetParent($generation = 1)
	{
		if($this->ParentId == null)
			return null;
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
			$count = count($generation);
			for($i=0; $i<$count; $i++)
				if($this instanceof $generation[$i])
					return $this;
			return GetComponentById($this->ParentId)->GetParent($generation);
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
	 * Shows the Component.
	 * Should not be called under most circumstances. Should only be called in overriding the Show() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is first shown on the client.
	 */
	function Show()
	{
		$this->ShowStatus = null;
		if(isset($_SESSION['NOLOHControlQueue'][$this->Id]))
			unset($_SESSION['NOLOHControlQueue'][$this->Id]);
		return "'id','$this->Id'";
	}
	/**
	 * The opposite of Showing. If the Component has a client-side aspect, it will be removed from the client.
	 * Should not be called under most circumstances. Should only be called in overriding the Bury() of advanced, custom components.
	 * Overriding this function allows you to have code execute when the Component is removed from the client.
	 */
	function Bury()
	{
		$this->ShowStatus = 2;
		if(isset($_SESSION['NOLOHControlQueue'][$this->Id]))
		{
			unset($_SESSION['NOLOHControlQueue'][$this->Id]);
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
		if(isset($_SESSION['NOLOHControlQueue'][$this->Id]))
			unset($_SESSION['NOLOHControlQueue'][$this->Id]);
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
		if(isset($GLOBALS['NOLOHGarbage']))
		{
			$id = $this->Id;
			unset($_SESSION['NOLOHControlQueue'][$id],
				$_SESSION['NOLOHFunctionQueue'][$id],
				$_SESSION['NOLOHPropertyQueue'][$id]);
			$_SESSION['NOLOHGarbage'][$id] = '';
		}
	}
}

?>