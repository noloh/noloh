<?php
/**
 * @package UI
 * @subpackage Controls
 */
class TreeList extends Panel 
{
	public $TreeNodesList;
	public $Multiple;
	public $Nodes;
	
	function TreeList($whatLeft=0, $whatTop=0, $whatWidth=200, $whatHeight=500)
	{
		parent::Panel($whatLeft, $whatTop, $whatWidth, $whatHeight, $this);
		$this->TreeNodesList = new ListBox();
		$this->TreeNodesList->SetClientVisible(false);
		$this->Controls->Add($this->TreeNodesList, true, true);
		$this->SetScrolling(System::Auto);
		//$this->AutoScroll = true;
		$this->Nodes = &$this->Controls;
		$this->Nodes->AddFunctionName = "AddNode";
		//$this->Nodes->RemoveFunctionName = "RemoveNode";
	}
	function AddNode(TreeNode $node)
	{
		$node->SetWidth($this->Width-20);
		$this->Controls->Add($node, true, true);
		$this->AddNodeHelper($node);
		return $node;
	}
	private function AddNodeHelper(TreeNode $node)
	{
		$node->SetTreeListId($this->Id);
		$node->ListIndex = $this->TreeNodesList->Items->Count();
		$this->TreeNodesList->Items->Add(new Item($node->Id, $node->NodeElement->Id));
		$nodeControlCount = $node->NodePanel->Controls->Count();
		for($i = 0; $i < $nodeControlCount; ++$i)
			$this->AddNodeHelper($node->NodePanel->Controls->Item[$i]);
	}
	function Clear()
	{
		$this->Controls->Clear();
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
		AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/TreeListScripts.js");
		//AddScript("document.getElementById('" . $this->Id . "').treeNodesList = document.getElementById('" . $this->TreeNodesList->Id . "');");
		AddScript("document.getElementById('" . $this->Id . "').treeNodesList = '" . $this->TreeNodesList->Id . "';");
		parent::Show();
	}
}
?>