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
	/**
	 * Constructor for ListViewItem
	 * @param mixed $objOrText An optional initial SubItem for the ListViewItem.
	 * This can also be an Array of SubItems.
	 * @param integer $height The height of the ListViewItem within the ArrayList
	 */	
	function ListViewItem($objOrText = null, $height=20)
	{
		parent::Panel(null, null, '100%', $height, $this);
		$this->SetLayout(Layout::Relative);
		$this->SubItems = &$this->Controls;
		$this->SubItems->AddFunctionName = 'AddSubItem';
		if($objOrText != null)
			$this->AddSubItem($objOrText);
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
				$i = $j = 0;
				foreach($objOrText as $val)
				{
					if($cols[$j] == $i++)
					{
						$this->CreateSubItem($val);
						++$j;
					}
				}
			}
			else
			{
				foreach($objOrText as $val)
					$this->CreateSubItem($val);
			}
		}
		else
			$this->CreateSubItem($objOrText);
		if($this->ListViewId != null)
			GetComponentById($this->ListViewId)->Update($this);
	}
	private function CreateSubItem($objectOrText)
	{
		if(!is_object($objectOrText) || $objectOrText == null)
			$object = new Label($objectOrText, null, 0, null, '100%');
		else
		{
			$object = new Panel(null, 0, 1, '100%');
			$object->Controls->Add($objectOrText);
			if(($height = $objectOrText->GetHeight()) > $this->GetHeight())
				$this->SetHeight($height);
		}
		$this->SubItems->Add($object, true);
		$object->SetCSSClass('NLVSubItem');
		return $object;
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
}		
?>