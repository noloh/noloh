<?
/**
 * @package Web.UI.Controls
 */
class TreeNode extends Panel
{
	public $PlusMinus;
	public $Nodes;
	public $NodeElement;
	public $NodeItem;
	public $NodeIcon;
	public $NodePanel;
	public $NodeString;
	private $LeafSrc;
	private $CloseSrc;
	private $OpenSrc;
	private $ParentNodeId;
	private $Selected;
	
	public static function GetDefaultLeafSrc()
	{
		return NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/document.gif';
	}
	public static function GetDefaultCloseSrc()
	{
		return NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/folder_close.gif';
	}
	public static function GetDefaultOpenSrc()
	{
		return NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/folder_open.gif';
	}
	
	function TreeNode($element)
	{
		parent::Panel(20, 0, 0, null);
		$this->SetScrolling(System::Full);
		$this->SetLayoutType(1);
		if(GetBrowser() == 'ie')
			NolohInternal::SetProperty('style.marginTop','6px',$this);
		$this->PlusMinus = new PlusMinusSwitch(0, 0);
		$this->PlusMinus->SetLayoutType(1);
		$this->PlusMinus->SetClientVisible(false);
		if(is_object($element))
		{
			if($element instanceof Control)
				$this->NodeElement = &GetComponentById($element->Id);
			elseif($element instanceof Item)
			{
				$this->NodeItem = &$element;
				$this->NodeElement = new Label($element->Text, 0, 0, System::Auto, 18);
			}
		}
		else
		{
			$this->NodeString = $element;
			$this->NodeElement = new Label($element, 0, 0, System::Auto, 15);
		}
		$this->NodeElement->SetCursor(Cursor::Hand);
		$this->NodeElement->SetLeft(40);
		$this->NodeIcon = new Image(TreeNode::GetDefaultLeafSrc(), 8, 3, 16, 15);
		$this->NodeIcon->SetLayoutType(1);
		$this->NodePanel = new Panel(25, 20, null, null, $this);
		$this->NodePanel->SetScrolling(System::Full);
		$this->NodePanel->SetLayoutType(2);
		$this->NodePanel->SetVisible(0);
		$this->Nodes = &$this->NodePanel->Controls;
		$this->Nodes->AddFunctionName = 'AddNode';
		$this->Nodes->InsertFunctionName = 'InsertNode';
		$this->Nodes->RemoveAtFunctionName = 'RemoveNodeAt';
		$this->PlusMinus->Change = new ClientEvent('PlusMinusChange("'.$this->NodePanel->Id.'","'.$this->NodeIcon->Id.'","' . $this->Id . '")');
		$this->Controls->Add($this->PlusMinus);
		$this->Controls->Add($this->NodeElement);
		$this->Controls->Add($this->NodeIcon);
		$this->Controls->Add($this->NodePanel);
	}
	private function AddNodeHelper($node, $lastNodeId=null)
	{
		$node->SetWidth($this->Width-20);
		if($this->TreeListId != null)
		{
			$node->SetTreeListId($this->TreeListId);
			$node->ParentNodeId = $this->Id;
			$node->TellChildren($this->TreeListId);
		}
		if($this->NodePanel->Controls->Count() == 0)
		{
			$this->PlusMinus->ClientVisible = true;
			if($this->NodePanel->ClientVisible === true)
				$this->NodeIcon->Src = $this->OpenSrc!=null ? $this->OpenSrc : TreeNode::GetDefaultOpenSrc();
			else 
				$this->NodeIcon->Src = $this->CloseSrc!=null ? $this->CloseSrc : TreeNode::GetDefaultCloseSrc();
		}
	}
	function AddNode(TreeNode $node)
	{
		$this->AddNodeHelper($node);
		$this->NodePanel->Controls->Add($node, true, true);
		return $node;
	}
	function InsertNode(TreeNode $node, $index)
	{
		if(isset($this->NodePanel->Controls->Item[$index]))
		{
			$this->AddNodeHelper($node);
			$this->NodePanel->Controls->Insert($node, $index, true);
		}
		else
		{
			$this->AddNodeHelper($node);
			$this->NodePanel->Controls->Add($node, true, true);
		}
		return $node;
	}
	function RemoveNodeAt($idx)
	{
		$this->NodePanel->Controls->Item[$idx]->Remove();
	}
	function Remove()
	{
		$tList = GetComponentById($this->TreeListId);
		if($this->ParentNodeId != null)
		{
			$parentNode = $this->GetParentNode();
			$parentNode->NodePanel->Controls->Remove($this, true);
			if($parentNode->NodePanel->Controls->Count() == 0)
			{
				$parentNode->PlusMinus->ClientVisible = false;
				$parentNode->NodeIcon->Src = TreeNode::GetDefaultLeafSrc();
			}
		}
		else 
			$tList->Nodes->Remove($this, true);
		$this->ForgetListDeeply();
	}

	function TellChildren($treeListId)
	{
		$nodesCount = $this->Nodes->Count();
		for($i=0; $i<$nodesCount; ++$i)
		{
			$node = &$this->Nodes[$i];
			$node->SetTreeListId($treeListId);
		}
	}
	
	private function ForgetListDeeply()
	{
		$this->SetTreeListId(null);
		$controlCount = $this->NodePanel->Controls->Count();
		$item = $this->NodePanel->Controls->Item;
		for($i=0; $i<$controlCount; ++$i)
			$item[$i]->ForgetListDeeply();
	}

	function GetParentNode()
	{
		return GetComponentById($this->ParentNodeId);
	}

	function GetRightBottomChildId()
	{
		if($this->NodePanel->Controls->Count() > 0)
			return $this->NodePanel->Controls->Item[$this->NodePanel->Controls->Count() -1]->GetRightBottomChildId();
		else 
			return $this->Id;
	}
	// Note: The legacy is strict; a node is not considered in its own legacy
	function GetLegacyLength()
	{
		$legacyLength = 0;
		$nodePanelCount = $this->NodePanel->Controls->Count();
		for($i=0; $i<$nodePanelCount; $i++)
			$legacyLength += $this->NodePanel->Controls->Item[$i]->GetLegacyLength();
		return $legacyLength + $nodePanelCount;
	}

	function Expand($deep = false)
	{
		$this->PlusMinus->Src = NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/minus.gif';
		$this->NodePanel->ClientVisible = true;
		if($deep)
		{
			$NodeCount = $this->NodePanel->Controls->Count();
			for($i=0; $i<$NodeCount; $i++)
				$this->NodePanel->Controls->Item[$i]->Expand(true);
		}
	}
	
	function GetTreeListId()
	{
		return $this->TreeListId;
	}
	
	function SetTreeListId($newId)
	{
		$this->TreeListId = $newId;
		NolohInternal::SetProperty('ListId', $newId, $this);
		$this->NodeElement->Click['_N'] = new ClientEvent("SelectNode('$this->Id','".$this->NodeElement->Id."',".(GetBrowser()=='ie'?'window.':'').'event);');
	}

	function GetClick()
	{
		$click = $this->NodeElement->Click;
		if(!isset($click['_N']))
			$click['_N'] = new ClientEvent('');
		return $this->NodeElement->Click;
	}

	function SetClick($newClick)
	{
		$this->NodeElement->Click = new Event(array(), array(array($this->NodeElement->Id,'Click')));
		$this->NodeElement->Click['_N'] = $this->TreeListId==null 
			? new ClientEvent("")
			: new ClientEvent("SelectNode('$this->Id','".$this->NodeElement->Id."',".(GetBrowser()=='ie'?'window.':'').'event);');
		$this->NodeElement->Click[] = $newClick;
	}
	
	function GetText()
	{
		return $this->NodeElement == null ? null : $this->NodeElement->GetText();
	}
	
	function SetText($text)
	{
		if($this->NodeElement != null)
			$this->NodeElement->SetText($text);
	}
	
	function GetValue()
	{
		return $this->NodeItem ? $this->NodeItem->Value : $this->Text;
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
		if($this->NodePanel->Controls->Count() == 0)
			$this->NodeIcon->Src = $newSrc;
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
		if($this->NodePanel->Controls->Count() != 0 && $this->NodePanel->ClientVisible !== true)
			$this->NodeIcon->SetSrc($newSrc);
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
		if($this->NodePanel->Controls->Count() != 0 && $this->NodePanel->ClientVisible === true)
			$this->NodeIcon->SetSrc($newSrc);
	}
	
	function AddShift($shift)
	{
		$this->MouseDown[] = new ClientEvent("document.getElementById('{$this->NodeElement->Id}').onclick.call(" . (GetBrowser()=='ie'?'':'this, event') . ')');
		parent::AddShift($shift);
	}
}

?>