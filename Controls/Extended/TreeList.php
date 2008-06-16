<?php
/**
 * TreeList class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Extended
 */
class TreeList extends Panel 
{
	public $TreeNodes;
	private $SelectedTreeNodes;
	private $OpenSrc;
	private $CloseSrc;
	
	function TreeList($left=0, $top=0, $width=200, $height=500)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->SetScrolling(System::Auto);
		$this->TreeNodes = &$this->Controls;
		$this->TreeNodes->AddFunctionName = 'AddTreeNode';
		$this->TreeNodes->InsertFunctionName = 'InsertTreeNode';
		$this->TreeNodes->RemoveAtFunctionName = 'RemoveTreeNodeAt';
		NolohInternal::SetProperty('OpenSrc', TreeNode::GetDefaultOpenSrc(), $this);
		NolohInternal::SetProperty('CloseSrc', TreeNode::GetDefaultCloseSrc(), $this);
	}
	function AddTreeNode($node)
	{
		if(!($node instanceof TreeNode))
			$node = new TreeNode($node);
		$node->SetWidth($this->Width-20);
		$this->Controls->Add($node, true, true);
		$node->SetTreeListId($this->Id);
		$node->TellChildren($this->Id);
		return $node;
	}
	function InsertTreeNode($node, $index)
	{
		if(!($node instanceof TreeNode))
			$node = new TreeNode($node);
		$node->SetWidth($this->Width-20);
		$this->Controls->Insert($node, $index, true);
		$node->SetTreeListId($this->Id);
		$node->TellChildren($this->Id);
		return $node;
	}
	function RemoveTreeNodeAt($index)
	{
		$this->Controls->Elements[$index]->Remove();
	}
	function Clear()
	{
		$this->Controls->Clear(true);
	}
	function GetSelectedTreeNode()
	{
		return count($this->SelectedTreeNodes)==0 ? null : GetComponentById($this->SelectedTreeNodes[0]);
	}
	function SetSelectedTreeNode($treeNode)
	{
		$this->SelectedTreeNodes = array($treeNode->Id);
		QueueClientFunction($treeNode, 'SelectNode', array('\''.$treeNode->Id.'\'', '\''.$treeNode->Element->Id.'\'', 'Object()'));
		$treeNode->ExpandToShow();
	}
	function GetSelectedTreeNodes()
	{
		$ret = array();
		$selectedNodesCount = count($this->SelectedTreeNodes);
		for($i = 0; $i < $selectedNodesCount; ++$i)
			$ret[] = GetComponentById($this->SelectedTreeNodes[$i]);
		return $ret;
	}
	function GetSelectedValue()
	{
		$selectedNode = $this->GetSelectedTreeNode();
		return $selectedNode ? $selectedNode->GetValue() : null;
	}
	function GetSelectedText()
	{
		$selectedNode = $this->GetSelectedTreeNode();
		return $selectedNode ? $selectedNode->GetText() : null;
	}
	function SetOpenSrc($openSrc)
	{
		NolohInternal::SetProperty('OpenSrc', $openSrc, $this);
		$nodeCount = $this->TreeNodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::OpenSrcHelper($openSrc, $this->TreeNodes[$i]);
	}
	static function OpenSrcHelper($openSrc, $node)
	{
		if($node->GetOpenSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->ClientVisible === true)
			$this->Icon->SetSrc($openSrc);
		$nodeCount = $this->TreeNodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::OpenSrcHelper($openSrc, $this->TreeNodes[$i]);
	}
	function SetCloseSrc($closeSrc)
	{
		NolohInternal::SetProperty('CloseSrc', $closeSrc, $this);
		$nodeCount = $this->TreeNodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::CloseSrcHelper($closeSrc, $this->TreeNodes[$i]);
	}
	static function CloseSrcHelper($closeSrc, $node)
	{
		if($node->GetCloseSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->ClientVisible !== true)
			$this->NodeIcon->SetSrc($closeSrc);
		$nodeCount = $this->TreeNodes->Count;
		for($i=0; $i<$nodeCount; ++$i)
			self::CloseSrcHelper($closeSrc, $this->TreeNodes[$i]);
	}
	function ExpandAll()
	{
		$nodeCount = $this->Controls->Count();
		for($i=1; $i<$nodeCount; ++$i)
			$this->Controls->Elements[$i]->Expand(true);
	}
	function Set_NSelectedNodes($selectedNodes)
	{
		$this->SelectedTreeNodes = explode('~d2~', $selectedNodes);
	}
	function Show()
	{
		AddNolohScriptSrc('TreeList.js');
		AddScript('InitTreeList(\''.$this->Id.'\')');
		parent::Show();
	}
}
?>