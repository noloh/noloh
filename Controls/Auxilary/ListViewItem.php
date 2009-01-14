<?php	
/**
 * ListViewItem class
 * 
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class ListViewItem extends Panel //extends Component
{
	public $Checked;
	public $SubItems;
	private $ListViewId;
	private $Value;
		
	function ListViewItem($objOrText = null, $height=20)
	{
		parent::Panel(null, null, '100%', $height, $this);
		$this->SetLayout(Layout::Relative);
		$this->SubItems = &$this->Controls;
		$this->SubItems->AddFunctionName = 'AddSubItem';
		if($objOrText != null)
			$this->AddSubItem($objOrText);
	}
	function GetListView()
	{
		if($this->ListViewId != null)
			return GetComponentById($this->ListViewId);
	}
	function SetListView($listView)	{$this->ListViewId = $listView->Id;}
	/**
	 * @ignore
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
		if(is_string($objectOrText) || $objectOrText == null)
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
	function SetValue($value)	{$this->Value = $value;}
	function GetValue()			{return $this->Value;}
	/**
	 * @ignore
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
		if($eventTypeAsString == 'Click') 
			return '_NLVSlct("' . $this->Id . '"' . (!UserAgent::IsIE()?', event':'') . ');' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
}		
?>