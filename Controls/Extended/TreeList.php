<?php
/**
 * TreeList class
 *
 * A TreeList is a Control that allows data to be displayed organized in a tree structure. A
 * typical example of a TreeList would be a representation of a computer's file system, with 
 * folders within folders as "branch" TreeNodes and the files as the "leaf" TreeNodes.
 * 
 * <pre> 
 * // Instantiates a new TreeList
 * $treeList = new TreeList();
 * // Instantiates a new TreeNode and Adds it to the TreeList
 * $treeList->TreeNodes->Add(new TreeNode('First Node'));
 * // Adding a string will automatically instantiate a new TreeNode for you
 * $treeList->TreeNodes->Add('Second Node');
 * </pre>
 *
 * @package Controls/Extended
 */
class TreeList extends Panel 
{
	/**
	 * The ArrayList of all inner TreeNodes
	 * @var ArrayList
	 */
	public $TreeNodes;
	private $SelectedTreeNodes;
	private $OpenSrc;
	private $CloseSrc;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends TreeList
	 * @param integer $left
	 * @param integer $top
	 * @param integer $width
	 * @param integer $height
	 * @return TreeList
	 */
	function TreeList($left=0, $top=0, $width=200, $height=500)
	{
		parent::Panel($left, $top, $width, $height, $this);
		$this->SelectedTreeNodes = array();
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
		//$node->SetWidth($this->Width-20);
		$this->Controls->Add($node, true);
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
		//$node->SetWidth($this->Width-20);
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
	/**
	 * Clears the TreeList. Also equivalent to 
	 * <pre>$treeObject->TreeNodes->Clear();</pre>
	 */
	function Clear()
	{
		$this->Controls->Clear(true);
	}
	/**
	 * Returns the first instance of TreeNode that is Selected.
	 * @return TreeNode
	 */
	function GetSelectedTreeNode()
	{
		return count($this->SelectedTreeNodes)==0 ? null : GetComponentById($this->SelectedTreeNodes[0]);
	}
	/**
	 * Sets a TreeNode object to be Selected, as well as Expands it.
	 * @param TreeNode $treeNode
	 */
	function SetSelectedTreeNode($treeNode)
	{
		$this->SelectedTreeNodes = array($treeNode->Id);
		QueueClientFunction($treeNode, '_NTreeSlctOne', array('\''.$treeNode->Id.'\'', '\''.$treeNode->Element->Id.'\''));
		$treeNode->ExpandToShow();
	}
	/**
	 * Returns an array, indexed numerically, of all of the TreeNodes that are Selected.
	 * @return array
	 */
	function GetSelectedTreeNodes()
	{
		$ret = array();
		$selectedNodesCount = count($this->SelectedTreeNodes);
		for($i = 0; $i < $selectedNodesCount; ++$i)
			$ret[] = GetComponentById($this->SelectedTreeNodes[$i]);
		return $ret;
	}
	/**
	 * Returns the Value of the first Selected TreeNode.
	 * @return string
	 */
	function GetSelectedValue()
	{
		$selectedNode = $this->GetSelectedTreeNode();
		return $selectedNode ? $selectedNode->GetValue() : null;
	}
	/**
	 * Sets the first TreeNode having the specified Value to be Selected.
	 * @param string $value
	 */
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
		$node->Selected = ($node->GetValue() == $value);
		//if($node->Selected = ($node->GetValue() == $value))
		//	$this->SelectedTreeNodes[] = $node->Id;
		foreach($node->TreeNodes as $treeNode)
			self::SetSelectedValueHelper($value, $treeNode);
	}
	/**
	 * Returns the Text of the first Selected TreeNode.
	 * @return string
	 */
	function GetSelectedText()
	{
		$selectedNode = $this->GetSelectedTreeNode();
		return $selectedNode ? $selectedNode->GetText() : null;
	}
	/**
	 * Sets the first TreeNode having the specified Text to be Selected.
	 * @param string $text
	 */
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
		$node->Selected = ($node->GetText() == $text);
		//if($node->Selected = ($node->GetText() == $text))
		//	$this->SelectedTreeNodes[] = $node->Id;
		foreach($node->TreeNodes as $treeNode)
			self::SetSelectedTextHelper($text, $treeNode);
	}
	/**
	 * @ignore
	 */
	function &_NGetSelectedTreeNodeIds()
	{
		return $this->SelectedTreeNodes;
	}
	/**
	 * Sets the Src of each internal TreeNode that has sub-nodes and is open to the specified location.
	 * @param string $openSrc
	 */
	function SetOpenSrc($openSrc)
	{
		NolohInternal::SetProperty('OpenSrc', $openSrc, $this);
		foreach($this->Controls->Elements as $treeNode)
			self::OpenSrcHelper($openSrc, $treeNode);
	}
	/**
	 * @ignore
	 */
	static function OpenSrcHelper($openSrc, $node)
	{
		if($node->GetOpenSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->Visible === true)
			$this->Icon->SetSrc($openSrc);
		foreach($this->Controls->Elements as $treeNode)
			self::OpenSrcHelper($openSrc, $treeNode);
	}
	/**
	 * Sets the Src of each internal TreeNode that has sub-nodes and is closed to the specified location.
	 * @param string $closedSrc
	 */
	function SetCloseSrc($closeSrc)
	{
		NolohInternal::SetProperty('CloseSrc', $closeSrc, $this);
		foreach($this->Controls->Elements as $treeNode)
			self::CloseSrcHelper($closeSrc, $treeNode);
	}
	/**
	 * @ignore
	 */
	static function CloseSrcHelper($closeSrc, $node)
	{
		if($node->GetCloseSrc() == null && $node->ChildrenPanel->Controls->Count() != 0 && $node->ChildrenPanel->Visible !== true)
			$this->NodeIcon->SetSrc($closeSrc);
		$nodeCount = $this->TreeNodes->Count;
		foreach($this->Controls->Elements as $treeNode)
			self::CloseSrcHelper($closeSrc, $treeNode);
	}
	/**
	 * Expands all of the inernal TreeNodes. 
	 */
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
		AddScript('_NTreeInit(\''.$this->Id.'\')');
		parent::Show();
	}
}
?>