<?php
/**
 * @package Web.UI.Controls
 */
class TreeList extends Panel 
{
	public $TreeNodesList;
	public $Nodes;
	
	function TreeList($left=0, $top=0, $width=200, $height=500)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->TreeNodesList = new ListBox();
		$this->TreeNodesList->SetClientVisible(GetBrowser()=='ie' ? false : 0);
		$this->Controls->Add($this->TreeNodesList, true, true);
		$this->SetScrolling(System::Auto);
		//$this->AutoScroll = true;
		$this->Nodes = &$this->Controls;
		$this->Nodes->AddFunctionName = 'AddNode';
		$this->Nodes->InsertFunctionName = 'InsertNode';
		$this->Nodes->RemoveAtFunctionName = 'RemoveNodeAt';
		$this->Nodes->ClearFunctionName = 'Clear';
	}
	function AddNode(TreeNode $node)
	{
		$node->SetWidth($this->Width-20);
		$this->Controls->Add($node, true, true);
		$this->AddNodeHelper($node);
		return $node;
	}
	private function AddNodeHelper($node)
	{
		$node->SetTreeListId($this->Id);
		$node->SetListIndex($this->TreeNodesList->Items->Count());
		$this->TreeNodesList->Items->Add(new Item($node->Id, $node->NodeElement->Id));
		$nodeControlCount = $node->NodePanel->Controls->Count();
		for($i = 0; $i < $nodeControlCount; ++$i)
			$this->AddNodeHelper($node->NodePanel->Controls->Item[$i]);
	}
	function InsertNode(TreeNode $node, $index)
	{
		$node->SetWidth($this->Width-20);
		$nodesCount = $this->Nodes->Count();
		$nodesDown = $node->GetLegacyLength()+1;
		for($i=++$index; $i<$nodesCount; ++$i)
			$this->Nodes->Item[$i]->MoveListIndexRecursively($nodesDown);
		$this->Controls->Insert($node, $index, true);
		$this->InsertNodeHelper($node, --$index);
		return $node;
	}
	private function InsertNodeHelper($node, &$index)
	{
		$node->SetTreeListId($this->Id);
		$node->SetListIndex($index);
		$this->TreeNodesList->Items->Insert(new Item($node->Id, $node->NodeElement->Id), $index);
		$nodeControlCount = $node->NodePanel->Controls->Count();
		for($i = 0; $i < $nodeControlCount; ++$i)
			$this->InsertNodeHelper($node->NodePanel->Controls->Item[$i], ++$index);
	}
	function RemoveNodeAt($index)
	{
		$this->Controls->Item[$index]->Remove();
	}
	function Clear()
	{
		$this->Controls->Clear(true);
		$this->TreeNodesList->Items->Clear();
		$this->Controls->Add($this->TreeNodesList, true, true);
	}
	function GetNodeByIndex($NodeId)
	{
		return GetComponentById($this->TreeNodesList->Items->Item[$NodeId]->Value);
	}
	function GetSelectedNode()
	{
		if($this->TreeNodesList->SelectedIndex != -1)
			return GetComponentById($this->TreeNodesList->Items->Item[$this->TreeNodesList->SelectedIndex]->Value);
		return null;
	}
	function GetSelectedElement()
	{
		$ret = null;
		if($this->TreeNodesList->SelectedIndex != -1 && $this->TreeNodesList->SelectedIndex != null)
		{
			$SelectedNode = $this->GetSelectedNode();
			//$SelectedNode = &GetComponentById($this->TreeNodesList->Items->Item[$this->TreeNodesList->SelectedIndex]->Value);
			if($SelectedNode->NodeItem != null)
				$ret = $SelectedNode->NodeItem;
			elseif($SelectedNode->NodeString != null)
				$ret = $SelectedNode->NodeString;
			else
				$ret = GetComponentById($this->TreeNodesList->Items->Item[$this->TreeNodesList->SelectedIndex]->Text);
		}
		return $ret;
	}
	function GetSelectedValue()
	{
//		$El = $this->GetSelectedElement();
//		if(is_object($El))
//		{
//			if(get_class($El) == "Item")
//				return $El->Value;
//			elseif($El instanceof Control)
//				return $El->Text;
//		}
//		if($El == null)
//			return "";
//		return $El;
		$el = $this->GetSelectedElement();
		if($el == null)
			return "";
		if(is_object($el))
		{
			if($el instanceof Item)
				return $el->Value;
			elseif($el instanceof Control)
				return $el->Text;
		}
		return $el;
	}
	function ExpandAll()
	{
		$nodeCount = $this->Controls->Count();
		for($i=1; $i<$nodeCount; ++$i)
			$this->Controls->Item[$i]->Expand(true);
	}
	function Show()
	{
		AddNolohScriptSrc('TreeList.js');
		//AddScript("document.getElementById('" . $this->Id . "').treeNodesList = '" . $this->TreeNodesList->Id . "';");
		NolohInternal::SetProperty('treeNodesList', $this->TreeNodesList->Id, $this);
		NolohInternal::SetProperty('OpenSrc', /*$this->OpenSrc!=null?$this->OpenSrc:*/TreeNode::GetDefaultOpenSrc(), $this);
		NolohInternal::SetProperty('CloseSrc', /*$this->CloseSrc!=null?$this->CloseSrc:*/TreeNode::GetDefaultCloseSrc(), $this);
		parent::Show();
	}
}
?>