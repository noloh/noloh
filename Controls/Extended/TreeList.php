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
	/**
	 * An ArrayList of all the TreeNodes
	 * @var ArrayList
	 */
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
	/**
	 * @ignore
	 */
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
	/**
	 * @ignore
	 */
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
	/**
	 * @ignore
	 */
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
	function SetSelectedValue($value)
	{
		foreach($this->Controls->Elements as $treeNode)
			self::SetSelectedValueHelper($value, $treeNode);
	}
	/**
	 * @ignore
	 */
	function SetSelectedValueHelper($value, $node)
	{
		if($node->GetValue() == $value)
			$node->Selected = true;
		foreach($node->TreeNodes as $treeNode)
			self::SetSelectedValueHelper($value, $treeNode);
	}
	function GetSelectedText()
	{
		$selectedNode = $this->GetSelectedTreeNode();
		return $selectedNode ? $selectedNode->GetText() : null;
	}
	function SetSelectedText($text)
	{
		foreach($this->Controls->Elements as $treeNode)
			self::SetSelectedTextHelper($text, $treeNode);
	}
	/**
	 * @ignore
	 */
	function SetSelectedTextHelper($text, $node)
	{
		if($node->GetText() == $text)
			$node->Selected = true;
		foreach($node->TreeNodes as $treeNode)
			self::SetSelectedTextHelper($text, $treeNode);
	}
	function SetOpenSrc($openSrc)
	{
		NolohInternal::SetProperty('OpenSrc', $openSrc, $this);
		foreach($this->Controls->Elements as $treeNode)
			self::OpenSrcHelper($openSrc, $treeNode);
	}
	static function OpenSrcHelper($openSrc, $node)
	{
		if($node->GetOpenSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->ClientVisible === true)
			$this->Icon->SetSrc($openSrc);
		foreach($this->Controls->Elements as $treeNode)
			self::OpenSrcHelper($openSrc, $treeNode);
	}
	function SetCloseSrc($closeSrc)
	{
		NolohInternal::SetProperty('CloseSrc', $closeSrc, $this);
		foreach($this->Controls->Elements as $treeNode)
			self::CloseSrcHelper($closeSrc, $treeNode);
	}
	static function CloseSrcHelper($closeSrc, $node)
	{
		if($node->GetCloseSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->ClientVisible !== true)
			$this->NodeIcon->SetSrc($closeSrc);
		$nodeCount = $this->TreeNodes->Count;
		foreach($this->Controls->Elements as $treeNode)
			self::CloseSrcHelper($closeSrc, $treeNode);
	}
	function ExpandAll()
	{
		foreach($this->Controls->Elements as $element)
			$element->Expand(true);
	}
	/**
	 * @ignore
	 */
	function Set_NSelectedNodes($selectedNodes)
	{
		$this->SelectedTreeNodes = explode('~d2~', $selectedNodes);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		AddNolohScriptSrc('TreeList.js');
		AddScript('InitTreeList(\''.$this->Id.'\')');
		parent::Show();
	}
}
?>