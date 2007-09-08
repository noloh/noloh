<?php
/**
 * @package Web.UI.Controls
 */
class TreeList extends Panel 
{
	public $Nodes;
	private $SelectedNodes;
	private $OpenSrc;
	private $CloseSrc;
	
	function TreeList($left=0, $top=0, $width=200, $height=500)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->SetScrolling(System::Auto);
		$this->Nodes = &$this->Controls;
		$this->Nodes->AddFunctionName = 'AddNode';
		$this->Nodes->InsertFunctionName = 'InsertNode';
		$this->Nodes->RemoveAtFunctionName = 'RemoveNodeAt';
		NolohInternal::SetProperty('OpenSrc', TreeNode::GetDefaultOpenSrc(), $this);
		NolohInternal::SetProperty('CloseSrc', TreeNode::GetDefaultCloseSrc(), $this);
	}
	function AddNode(TreeNode $node)
	{
		$node->SetWidth($this->Width-20);
		$this->Controls->Add($node, true, true);
		$node->SetTreeListId($this->Id);
		$node->TellChildren($this->Id);
		return $node;
	}
	function InsertNode(TreeNode $node, $index)
	{
		$node->SetWidth($this->Width-20);
		$this->Controls->Insert($node, $index, true);
		$node->SetTreeListId($this->Id);
		$node->TellChildren($this->Id);
		return $node;
	}
	function RemoveNodeAt($index)
	{
		$this->Controls->Item[$index]->Remove();
	}
	function Clear()
	{
		$this->Controls->Clear(true);
	}
	function GetSelectedNode()
	{
		return count($this->SelectedNodes)==0 ? null : GetComponentById($this->SelectedNodes[0]);
	}
	function GetSelectedNodes()
	{
		$ret = array();
		$selectedNodesCount = count($this->SelectedNodes);
		for($i = 0; $i < $selectedNodesCount; ++$i)
			$ret[] = GetComponentById($this->SelectedNodes[$i]);
		return $ret;
	}
	function GetSelectedValue()
	{
		$selectedNode = $this->GetSelectedNode();
		if($selectedNode != null)
			if($selectedNode->NodeItem != null)
				return $selectedNode->NodeItem->Value;
			else 
				return $selectedNode->NodeElement->Text;
		else 
			return null;
	}
	function GetSelectedText()
	{
		$selectedNode = $this->GetSelectedNode();
		return $selectedNode ? $selectedNode->NodeElement->Text : null;
	}
	function SetOpenSrc($openSrc)
	{
		NolohInternal::SetProperty('OpenSrc', $openSrc, $this);
		$nodeCount = $this->Nodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::OpenSrcHelper($openSrc, $this->Nodes[$i]);
	}
	static function OpenSrcHelper($openSrc, $node)
	{
		if($node->GetOpenSrc() == null && $node->NodePanel->Controls->Count() != 0 && $node->NodePanel->ClientVisible === true)
			$this->NodeIcon->SetSrc($openSrc);
		$nodeCount = $this->Nodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::OpenSrcHelper($openSrc, $this->Nodes[$i]);
	}
	function SetCloseSrc($closeSrc)
	{
		NolohInternal::SetProperty('CloseSrc', $closeSrc, $this);
		$nodeCount = $this->Nodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::CloseSrcHelper($openSrc, $this->Nodes[$i]);
	}
	static function CloseSrcHelper($closeSrc, $node)
	{
		if($node->GetCloseSrc() == null && $node->NodePanel->Controls->Count() != 0 && $node->NodePanel->ClientVisible !== true)
			$this->NodeIcon->SetSrc($closeSrc);
		$nodeCount = $this->Nodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::CloseSrcHelper($closeSrc, $this->Nodes[$i]);
	}
	function ExpandAll()
	{
		$nodeCount = $this->Controls->Count();
		for($i=1; $i<$nodeCount; ++$i)
			$this->Controls->Item[$i]->Expand(true);
	}
	function Set_NSelectedNodes($selectedNodes)
	{
		$this->SelectedNodes = explode('~d2~', $selectedNodes);
	}
	function Show()
	{
		AddNolohScriptSrc('TreeList.js');
		AddScript("InitTreeList('$this->Id')");
		parent::Show();
	}
}
?>