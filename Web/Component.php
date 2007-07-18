<?php
/**
 * Component class file.
 */
 
/**
 * Component class
 *
 * Component is the base class for all NOLOH components.
 * A component, an instance of the Component class or its descendent class, is
 * a basic building-block in a NOLOH application.
 *
 * A component has
 * - properties, which can be accessed by other components or functions.
 *
 * Properties are inheritable, but can be redefined.
 *
 * A component has a <b>Parent</b>, which establishes
 * a tree based on the parent-child relationship.
 *
 * Each component has an <b>DistinctId</b> that uniquely identifies itself among
 * all other components. 
 *
 * Properties
 * - <b>DistinctId</b>, string, read-only
 *   <br>Gets the DistinctId of this Component
 * - <b>Opacity</b>, integer,
 *   <br>Gets or Sets the Opacity of this Component
 * - <b>Parent</b>, Component, read-only
 *   <br>Gets the component's parent component in the Application.
 *   Note, a webpage has no parent and it's Parent property is null.
 * - <b>ParentId</b>, string,
 *   <br>Gets or Sets the DistinctId of the Parent of this Component
 * - <b>ScrollLeft</b>, integer,
 *   <br>Gets or Sets the ScrollLeft ot this Component
 * - <b>ScrollTop</b>, integer,
 *   <br>Gets or Sets the DiScrollTop of this Component
 * - <b>ServerVisible</b>, boolean,
 *   <br>Gets or Sets the ServerVisible of this Component
 * - <b>ZIndex</b>, integer,
 *   <br>Gets or Sets the ZIndex of this Component
 *
 */

class Component extends Object
{
	//private $PublicProperties = array();

	/**
	*Determines whether the Component is drawn on the Client
	*@var boolean
	*/
	public $ServerVisible;
	/**
	 * DistinctId of the component
	 * @var string
	 */
	public $DistinctId;
	/**
	 * ParentId of the component
	 * @var string
	 */
	private $ParentId;
	private $ShowStatus;

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
	
	function GetParentId() {return $this->ParentId;}
	function SetParentId($whatParentId)
	{
		$bool = $whatParentId != null;
		if($bool)
			$this->ParentId = $whatParentId;
		$_SESSION['NOLOHControlQueue'][$this->DistinctId] = $bool;
	}
	/**
	 * Constructor.
	 *
	 * for inherited components, be sure to call the parent constructor first
	 * so that the component properties and events are defined.
	 */
	function Component()
	{
		$this->ShowStatus = 0;
		$this->DistinctId = "N" . ++$_SESSION['NOLOHNumberOfComponents'];
		global $OmniscientBeing;
		$OmniscientBeing[$this->DistinctId] = &$this;
	}
	/**
	 * Shows the Component.
	 * Should not be called under most circumstances, should only be called in overriding the Show() of custom components.
	 * @return boolean
	 */
	function AddEventHandler($whatFunctionAsString)
	{
		return new ServerEvent($this, $whatFunctionAsString);
	}
	/**
	 * Gets Parent of this Component, or Parent based on $GenerationsAbove paramater 
	 * <br> Can also be called as a property
	 * <code>$this->SomeComponent->Parent</code>
	 * @param integer|specifies whatlevel of Parent to get.
	 */
	function GetParent($GenerationsAbove = 1)
	{
		if($this->ParentId == "")
			return null;
		if(is_int($GenerationsAbove))
		{
			if($GenerationsAbove == 1)
				return GetComponentById($this->ParentId);
			elseif($GenerationsAbove > 1)
				return GetComponentById($this->ParentId)->GetParent($GenerationsAbove-1);
			else 
				return $this;
		}
		elseif(is_string($GenerationsAbove))
		{
			$parent = GetComponentById($this->ParentId);
			return $parent instanceof $GenerationsAbove ? $parent : $parent->GetParent($GenerationsAbove);
		}
		elseif(is_array($GenerationsAbove))
		{
			$count = count($GenerationsAbove);
			for($i=0; $i<$count; $i++)
				if($this instanceof $GenerationsAbove[$i])
					return $this;
			return GetComponentById($this->ParentId)->GetParent($GenerationsAbove);
		}
		return null;
	}
	function GetAddId()
	{
		return $this->GetParent()->GetAddId();
	}
	/*
	function __isset($nm)
	{
		return isset($this->PublicProperties[$nm]);
	}
	
	function __unset($nm)
	{
		unset($this->PublicProperties[$nm]);
	}*/
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
	function RestoreValues()
	{
		//$vars = get_object_vars($this);
		$vars = (array)$this;
		foreach ($vars as $key => &$val)
			if(is_object($val))
			{
				if($val instanceof Pointer)
					//eval('$this->'.$key.' = &$val->Dereference();');
					$val = $val->Dereference();
				elseif($val instanceof ArrayList)
					$val->RestoreValues();
			}
			elseif(is_array($val))
				ArrayRestoreValues($val);
	}
	
	function Equals(Component &$obj)
	{
		global $OmniscientBeing;
		$OmniscientBeing[$this->DistinctId] = $obj;
	}
	
	function SetPropertyByReference($whatPropertyNameAsString, &$whatValue)
	{
		$this->$whatPropertyNameAsString = $whatValue;
	}
	
	function __toString()
	{
		return $this->DistinctId;
	}
	
	function __destruct()
	{
		if(isset($GLOBALS["NOLOHGarbage"]))
		{
			$id = $this->DistinctId;
			unset($_SESSION['NOLOHControlQueue'][$id],
				$_SESSION['NOLOHFunctionQueue'][$id],
				$_SESSION['NOLOHPropertyQueue'][$id]);
			$_SESSION["NOLOHGarbage"][$id] = "";
		}
	}
	
	function Show()
	{	
		$this->ShowStatus = null;
		if(isset($_SESSION['NOLOHControlQueue'][$this->DistinctId]))
			unset($_SESSION['NOLOHControlQueue'][$this->DistinctId]);
	}
	
	function Hide()
	{
		$this->ShowStatus = 2;
		if(isset($_SESSION['NOLOHControlQueue'][$this->DistinctId]))
		{
			unset($_SESSION['NOLOHControlQueue'][$this->DistinctId]);
			$this->ParentId = null;
		}
		return true;
	}
	
	function Resurrect()
	{
		$this->ShowStatus = null;
		if(isset($_SESSION['NOLOHControlQueue'][$this->DistinctId]))
			unset($_SESSION['NOLOHControlQueue'][$this->DistinctId]);
		return true;
	}
	
	/*
	function __wakeup()
	{
		/*
		$VarNames = array_keys(GetDeepClassVars(get_class($this)));
		//$VarNames = array_keys((array)($this));
		$VarNums = count($VarNames);
		for($i=0; $i<$VarNums; $i++)
			if($VarNames[$i] != "PublicProperties")
				eval('unset($this->'.$VarNames[$i].');');
		
		$vars = get_object_vars($this);
		//var_dump($vars);
		foreach ($vars as $key => $val)
			if(is_object($val) && get_class($val) == "Pointer")
			//if(IsPointer($val))
				eval('$this->'.$key.' = $val->Dereference(); echo " -".get_class($this->'.$key.')."- ";');
				//eval('$this->'.$key.' = DereferencePointer($val);');
	}
	*/
}

?>