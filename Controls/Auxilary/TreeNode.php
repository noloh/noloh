<?php
/**
 * TreeNode class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class TreeNode extends Panel
{
	public $TreeNodes;
	public $PlusMinus;
	public $Icon;
	public $ChildrenPanel;
	private $Element;
	private $LeafSrc;
	private $CloseSrc;
	private $OpenSrc;
	private $ParentNodeId;
	private $Selected;
	private $Value;
	private $TreeListId;
	/**
	 * @ignore
	 */
	public static function GetDefaultLeafSrc()
	{
		return System::ImagePath() . 'document.gif';
	}
	/**
	 * @ignore
	 */
	public static function GetDefaultCloseSrc()
	{
		return System::ImagePath() . 'folder_close.gif';
	}
	/**
	 * @ignore
	 */
	public static function GetDefaultOpenSrc()
	{
		return System::ImagePath() . 'folder_open.gif';
	}
	
	function TreeNode($element, $left=10)
	{
		parent::Panel($left, 0, 0, null);
		$this->SetScrolling(System::Full);
		$this->SetLayout(1);
		if(GetBrowser() == 'ie')
			NolohInternal::SetProperty('style.marginTop','6px',$this);
		//$this->PlusMinus = new PlusMinusSwitch(0, 3);
		$this->PlusMinus = new RolloverImage(System::ImagePath() . 'plus.gif', null, 0, 3);
		$this->PlusMinus->SetSelectedSrc(System::ImagePath() . 'minus.gif');
		$this->PlusMinus->SetTogglesOff(true);
		
		$this->PlusMinus->SetVisible(false);
		$this->SetElement($element);
		$this->Icon = new Image(TreeNode::GetDefaultLeafSrc(), 17, 0, 16, 15);
		$this->ChildrenPanel = new Panel(25, 20, null, null, $this);
		$this->ChildrenPanel->SetScrolling(System::Full);
		$this->ChildrenPanel->SetLayout(2);
		$this->ChildrenPanel->SetVisible(null);
		$this->TreeNodes = &$this->ChildrenPanel->Controls;
		$this->TreeNodes->AddFunctionName = 'AddTreeNode';
		$this->TreeNodes->InsertFunctionName = 'InsertTreeNode';
		$this->TreeNodes->RemoveAtFunctionName = 'RemoveTreeNodeAt';
		$this->PlusMinus->Change = new ClientEvent('_NTreeTgl("'.$this->ChildrenPanel->Id.'","'.$this->Icon->Id.'","' . $this->Id . '");');
		$this->Controls->Add($this->PlusMinus);
		$this->Controls->Add($this->Icon);
		$this->Controls->Add($this->Element);
		$this->Controls->Add($this->ChildrenPanel);
	}
	/**
	 * @ignore
	 */
	private function AddNodeHelper($node)
	{
		//$node->SetWidth($this->Width-20);
		if($this->TreeListId != null)
		{
			$node->SetTreeListId($this->TreeListId);
			$node->ParentNodeId = $this->Id;
			$node->TellChildren($this->TreeListId);
		}
		if($this->ChildrenPanel->Controls->Count() == 0)
		{
			$this->PlusMinus->Visible = true;
			if($this->ChildrenPanel->Visible === true)
				$this->Icon->Src = $this->OpenSrc!=null ? $this->OpenSrc : TreeNode::GetDefaultOpenSrc();
			else 
				$this->Icon->Src = $this->CloseSrc!=null ? $this->CloseSrc : TreeNode::GetDefaultCloseSrc();
		}
	}
	/**
	 * @ignore
	 */
	function AddTreeNode($node)
	{
		if(!($node instanceof TreeNode))
			$node = new TreeNode($node);
		$this->AddNodeHelper($node);
		$this->ChildrenPanel->Controls->Add($node, true);
		return $node;
	}
	/**
	 * @ignore
	 */
	function InsertTreeNode($node, $index)
	{
		if(!($node instanceof TreeNode))
			$node = new TreeNode($node);
		if(isset($this->ChildrenPanel->Controls->Elements[$index]))
		{
			$this->AddNodeHelper($node);
			$this->ChildrenPanel->Controls->Insert($node, $index, true);
		}
		else
		{
			$this->AddNodeHelper($node);
			$this->ChildrenPanel->Controls->Add($node, true);
		}
		return $node;
	}
	/**
	 * @ignore
	 */
	function RemoveTreeNodeAt($idx)
	{
		$this->ChildrenPanel->Controls->Elements[$idx]->Remove();
	}
	/**
	 * @ignore
	 */
	function Remove()
	{
		$tList = GetComponentById($this->TreeListId);
		if($this->ParentNodeId != null)
		{
			$parentNode = $this->GetParentNode();
			$parentNode->ChildrenPanel->Controls->Remove($this, true);
			if($parentNode->ChildrenPanel->Controls->Count() == 0)
			{
				$parentNode->PlusMinus->Visible = false;
				$parentNode->Icon->Src = TreeNode::GetDefaultLeafSrc();
			}
		}
		else 
			$tList->TreeNodes->Remove($this, true);
		$this->ForgetListDeeply();
	}
	/**
	 * @ignore
	 */
	function TellChildren($treeListId)
	{
		$nodesCount = $this->TreeNodes->Count();
		for($i=0; $i<$nodesCount; ++$i)
		{
			$node = &$this->TreeNodes[$i];
			$node->SetTreeListId($treeListId);
		}
	}
	/**
	 * @ignore
	 */
	private function ForgetListDeeply()
	{
		$this->SetTreeListId(null);
		$controlCount = $this->ChildrenPanel->Controls->Count();
		$elements = $this->ChildrenPanel->Controls->Elements;
		for($i=0; $i<$controlCount; ++$i)
			$elements[$i]->ForgetListDeeply();
	}

	function GetElement()
	{
		return $this->Element;
	}
	
	function SetElement($element)
	{
		if(is_object($element))
		{
			if($element instanceof Control)
			{
				$this->Element = &GetComponentById($element->Id);
				$this->Element->SetLeft(40);
				$this->Element->SetTop(-1);
			}
			elseif($element instanceof Item)
			{
				$this->Value = $element->Value;
				$this->Element = new Label($element->Text, 40, -1, System::Auto, System::Auto);
			}
		}
		else
			$this->Element = new Label($element, 40, -1, System::Auto, System::Auto);
		if(GetBrowser() != 'ie')
		{
			$this->Element->CSSMargin = '5px';
			$this->Element->CSSMarginLeft = '0px';
		}
		$this->Element->SetLayout(1);
		$this->Element->SetCursor(Cursor::Hand);
	}
	
	function GetParentNode()
	{
		return GetComponentById($this->ParentNodeId);
	}

	function GetRightBottomChildId()
	{
		if($this->ChildrenPanel->Controls->Count() > 0)
			return $this->ChildrenPanel->Controls->Elements[$this->ChildrenPanel->Controls->Count() -1]->GetRightBottomChildId();
		else 
			return $this->Id;
	}
	// Note: The legacy is strict; a node is not considered in its own legacy
	function GetLegacyLength()
	{
		$legacyLength = 0;
		$childCount = $this->ChildrenPanel->Controls->Count();
		for($i=0; $i<$childCount; $i++)
			$legacyLength += $this->ChildrenPanel->Controls->Elements[$i]->GetLegacyLength();
		return $legacyLength + $childCount;
	}

	function Expand($deep = false)
	{
		$this->PlusMinus->SetSelected(true);
		//$this->PlusMinus->Src = System::ImagePath() . 'minus.gif';
		//$this->ChildrenPanel->Visible = true;
		if($deep)
		{
			$nodeCount = $this->ChildrenPanel->Controls->Count();
			for($i=0; $i<$nodeCount; ++$i)
				$this->ChildrenPanel->Controls->Elements[$i]->Expand(true, false);
		}
	}
	
	function ExpandToShow()
	{
		if($this->ParentNodeId)
		{
			$parentNode = GetComponentById($this->ParentNodeId);
			$parentNode->Expand();
			$parentNode->ExpandToShow();
		}
	}
	
	function GetTreeListId()
	{
		return $this->TreeListId;
	}
	/**
	 * @ignore
	 */
	function SetTreeListId($newId)
	{
		$this->TreeListId = $newId;
		NolohInternal::SetProperty('ListId', $newId, $this);
		$this->Element->Click['_N'] = new ClientEvent('_NTreeSlct("'.$this->Id.'","'.$this->Element->Id.'");');
	}
	/**
	 * @ignore
	 */
	function GetClick()
	{
		$click = $this->Element->Click;
		if(!isset($click['_N']))
			$click['_N'] = new ClientEvent('');
		return $this->Element->Click;
	}
	/**
	 * @ignore
	 */
	function SetClick($newClick)
	{
		$this->Element->Click = new Event(array(), array(array($this->Element->Id,'Click')));
		$this->Element->Click['_N'] = $this->TreeListId==null 
			? new ClientEvent('')
			: new ClientEvent('_NTreeSlct("'.$this->Id.'","'.$this->Element->Id.'");');
		$this->Element->Click[] = $newClick;
	}
	/**
	 * @ignore
	 */
	function GetText()
	{
		return $this->Element ? $this->Element->GetText() : null;
	}
	/**
	 * @ignore
	 */
	function SetText($text)
	{
		if($this->Element != null)
			$this->Element->SetText($text);
	}
	
	function GetValue()
	{
		return $this->Value ? $this->Value : $this->Text;
	}
	
	function SetValue($value)
	{
		$this->Value = $value;
	}
	
	function GetLeafSrc()
	{
		return $this->LeafSrc;
	}
	
	function SetLeafSrc($newSrc)
	{
		if($newSrc == null)
			$newSrc = TreeNode::GetDefaultLeafSrc();
		$this->LeafSrc = $newSrc;
		if($this->ChildrenPanel->Controls->Count() == 0)
			$this->Icon->Src = $newSrc;
	}
	
	function GetCloseSrc()
	{
		return $this->CloseSrc;
	}
	
	function SetCloseSrc($newSrc)
	{
		if($newSrc == null)
			$newSrc = TreeNode::GetDefaultCloseSrc();
		$this->CloseSrc = $newSrc;
		NolohInternal::SetProperty('CloseSrc', $newSrc, $this);
		if($this->ChildrenPanel->Controls->Count() != 0 && $this->ChildrenPanel->Visible !== true)
			$this->Icon->SetSrc($newSrc);
	}
	
	function GetOpenSrc()
	{
		return $this->OpenSrc;
	}
	
	function SetOpenSrc($newSrc)
	{
		if($newSrc == null)
			$newSrc = TreeNode::GetDefaultOpenSrc();
		$this->OpenSrc = $newSrc;
		NolohInternal::SetProperty('OpenSrc', $newSrc, $this);
		if($this->ChildrenPanel->Controls->Count() != 0 && $this->ChildrenPanel->Visible === true)
			$this->Icon->SetSrc($newSrc);
	}
	
	function GetSelected()
	{
		return $this->TreeListId != null && in_array($this, GetComponentById($this->TreeListId)->GetSelectedTreeNodes(), true);
	}
	
	function SetSelected($bool)
	{
		if($this->TreeListId !== null)
		{
			if($bool)
				GetComponentById($this->TreeListId)->SetSelectedTreeNode($this);
			//GetComponentById($this->TreeListId)->;
			//if($bool)
			//	QueueClientFunction($this, '_NTreeSlct', array('\''.$this->Id.'\'', '\''.$this->Element->Id.'\'', 'Object()'));
			//$this->Click->Exec();
		}
		else
			BloodyMurder('You must add the TreeNode to the TreeList before selecting it.');
	}
	/**
	 * @ignore
	 */
	function AddShift($shift)
	{
		$this->MouseDown[] = new ClientEvent('_N(\''.$this->Element->Id.'\').onclick();');
		parent::AddShift($shift);
	}
}

?>