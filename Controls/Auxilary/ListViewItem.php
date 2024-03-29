<?php	
/**
 * ListViewItem class
 * 
 * A ListViewItem represents a row in a ListView. Similar to an Item, a ListViewItem also has a Value that
 * can be used to store a value associated with this row. A possible use for this is to store the id of a row
 * retrieved from a database whose contents are populated in this ListViewItem.
 * 
 * @package Controls/Auxiliary
 */
class ListViewItem extends Panel //extends Component
{
	/**
	 * @ignore
	 */
	public $Checked;
	/**
	 * SubItems make up the contents of a row. You can think of a SubItem
	 * as equivalent to a column of the Row.
	 * 
	 * The following is an example of how SubItems are used in association
	 * with this ListViewItem and a ListView.
	 * <pre>
	 * 	   $listView = new ListView();
	 *     $listView->Columns->AddRange('Car Brand', 'Color');
	 *     $listViewItem = new ListViewItem();
	 *     $listViewItem->SubItems->AddRange('Toyota', 'Red');
	 *     $listView->ListViewItems->Add($listViewItem);
	 * </pre>
	 *    
	 * Please also note that an array of SubItems can be passed in
	 * either at the instantiation stage, or through the ArrayList AddRange
	 * <pre>
	 *     $listViewItem = new ListViewItem(array('Toyota', 'Red'));
	 *     //Or
	 *     $listViewItem = nw ListViewItem();
	 *     $listViewItem->AddRange(array('Toyota', 'Red'));
	 * </pre>
	 * @var ArrayList
	 */
	public $SubItems;
	private $ListViewId;
	private $Value;
	private $SubItemsHack = array();
	//Possible Alternatives Excess, Extra
//	private $Surplus;
	/**
	 * Constructor for ListViewItem
	 * @param mixed $objOrText An optional initial SubItem for the ListViewItem.
	 * This can also be an Array of SubItems.
	 * @param integer $height The height of the ListViewItem within the ArrayList
	 */	
	function __construct($objOrText = null, $height=20)
	{
		parent::__construct(null, null, '100%', null, $this);
		$this->CSSClasses->Add('NLVRow');
		$this->SetLayout(Layout::Relative);
		$this->SubItems = new ImplicitArrayList($this, 'AddSubItem');
		$this->SubItems->RemoveFunctionName = 'RemoveSubItem';
//		$this->SubItems->ParentId = $this->Id;
		$this->SetHeight($height);
		$this->Scrolling = false;
		if($objOrText != null)
			$this->AddSubItem($objOrText);
	}
	/**
	* @ignore
	*/
	function SetHeight($height)
	{
		parent::SetHeight($height);
		if(!$height)
			$this->CSSClasses->Add('NLVWrap');
		else
			$this->CSSClasses->Remove('NLVWrap');
	}
	/**
	 * Returns the ListView that this ListViewItem is associated with
	 * @return ListView
	 */
	function GetListView()
	{
		if($this->ListViewId != null)
			return GetComponentById($this->ListViewId);
	}
	/**
	 * Sets the ListView that this ListViewItem is associated with
	 * @param ListView $listView
	 */
	function SetListView($listView)	{$this->ListViewId = $listView->Id;}
	/**
	 * Adds a SubItem to the SubItems ArrayList of this ListViewItem. 
	 * It's unnecessary to call this function directly as SubItems should be
	 * added through the SubItems ArrayList.
	 * 
	 * <pre>
	 *	   //If within a ListViewItem class
	 *     $this->SubItems->Add('This is a test');
	 *     
	 *     //Otherwise
	 *     $listViewItem = new ListViewItem();
	 *     $listViewItem->SubItems->Add('This is a test');
	 * </pre>
	 * @param string|array|Control $objOrText
	 */
	function AddSubItem($objOrText=null)
	{
		if(is_array($objOrText))
		{
			if(isset($GLOBALS['_NLVCols']))
			{
				$cols = $GLOBALS['_NLVCols'];
//				return System::Log('ListViewItem', $objOrText, $cols);
				foreach($cols as $column)
					{
					if(array_key_exists($column, $objOrText))
						$subItem = $this->CreateSubItem($objOrText[$column]);
					else
						$subItem = $this->CreateSubItem(null);
				}
			}
			else
			{
				foreach($objOrText as $val)
					$subItem = $this->CreateSubItem($val);
			}
		}
		else
			$subItem = $this->CreateSubItem($objOrText);
			
		if($this->ListViewId != null)
			$this->GetListView()->Update($this, $this->SubItems->Count() - 1);
		return $subItem;
	}
	/**
	* @ignore
	*/
	function RemoveSubItem($element)
	{
		$this->Controls->Remove($element);
		$this->SubItems->Remove($element, true);
		ClientScript::Queue($this, '_NRem', array($element->Id . '_W'), false);
	}
	/**
	* @ignore
	*/
	private function CreateSubItem($objectOrText)
	{
		if(!is_object($objectOrText) || $objectOrText == null)
//			$object = new Label($objectOrText, null, 0, null, '100%');
			$object = new Label($objectOrText, null, null, null, null);
		else
			$object = $objectOrText;
			
		$object->Layout = Layout::Relative;
//		$object->CSSClasses->Add('NLVSubItem');
//		$this->ShowSubItem($object);
		$this->SubItems->Add($object, true);
		return $object;
	}
	/**
	* @ignore
	*/
	function ShowSubItem($subItem)
	{
		if($this->GetShowStatus()!==0)
		{
			$initial = "style.position','relative'";
			NolohInternal::Show('DIV', $initial, $this, $this->Id, $subItem->Id . '_W');
		}
		else
			$this->SubItemsHack[] = $subItem;
		$this->Controls->Add($subItem, true);
	}
	/**
	* @ignore
	*/
	function GetAddId($obj)	
	{
//		return $obj instanceof Container?$obj->GetId() . '_W':$obj->GetId();
		return $obj->Id . '_W';
	}
	/**
	 * Sets the Value of this ListViewItem.
	 * @param mixed $value
	 */
	function SetValue($value)	{$this->Value = $value;}
	/**
	 * Returns the Value of this ListViewItem
	 * @return mixed
	 */
	function GetValue()			{return $this->Value;}
	/**
	 * Removes this ListViewItem from the ListView that it's in.
	 */
	function Remove()
	{
		$this->GetListView()->ListViewItems->Remove($this);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString === 'Click') 
			return '_NLVSlct("' . $this->Id . '");' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		parent::Show();
//		$initial = "'className','NLVWrap', 'style.position','relative'";
		$initial = "'style.position','relative'";
//		$initial = "'className','NLVWrap'";
		foreach($this->SubItemsHack as $subItem)
			NolohInternal::Show('DIV', $initial, $this, $this->Id, $subItem->Id . '_W');
		unset($this->SubItemsHack);
	}
}		
?>
