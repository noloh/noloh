<?php
/**
 * TreeNode class
 *
 * A TreeNode is a Control Auxiliary to TreeList. For more information, see TreeList.
 * 
 * A TreeNode can have other TreeNodes as sub-nodes, in which case it can either be open or closed.
 * If it does not have sub-nodes, then it is called a "leaf." The three different states: leaf, 
 * open, or closed, each have different Icon Srcs that visually indicate to the user information
 * about the TreeNode. These Srcs have defaults but can also be customized.
 * 
 * A TreeNode can also be Selected or not. A TreeList, in turn, has many functions that allow one to
 * return information about the Selected TreeNodes.
 * 
 * @package Controls/Auxiliary
 */
class TreeNode extends Panel
{
	/**
	 * The ArrayList of all inner TreeNodes.
	 * @var ArrayList
	 */
	public $TreeNodes;
	/**
	 * The RolloverImage which looks like a plus/minus switch that toggles whether the TreeNode is open.
	 * @var RolloverImage
	 */
	public $PlusMinus;
	/**
	 * The Image next to the TreeNode that visually suggests whether the TreeNode is a leaf node, or if not, if it is open or closed.
	 * @var Image
	 */
	public $Icon;
	/**
	 * The Panel holding the sub-nodes of the TreeNode which is shown or hidden depending on whether the TreeNode is open or closed.
	 * @var Panel
	 */
	public $ChildrenPanel;
	private $Element;
	private $LeafSrc;
	private $CloseSrc;
	private $OpenSrc;
	private $ParentNodeId;
	private $Selected;
	private $Value;
	private $TreeListId;
	private $SelectBackColor;
	private $DeselectBackColor;
	private $SelectColor;
	private $DeselectColor;
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
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends TreeNode
 	 * Example
 	 *	<pre> $node = new TreeNode('Some Text');</pre>
	 * @param mixed $element
	 * @param integer $left
	 * @return TreeNode
	 */
	function __construct($element, $left=10)
	{
		parent::__construct(0, 0, null, null);
		$this->SetScrolling(System::Full);
		$this->SetLayout(Layout::Relative);
		if($left != null)
			$this->CSSMarginLeft = $left . 'px';
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
	private function ForgetListDeeply()
	{
		$this->SetTreeListId(null);
		$controlCount = $this->ChildrenPanel->Controls->Count();
		$elements = $this->ChildrenPanel->Controls->Elements;
		for($i=0; $i<$controlCount; ++$i)
			$elements[$i]->ForgetListDeeply();
	}
	/**
	 * Returns the TreeNode's element that is displayed as the caption. This object is most typically a Label.
	 * @return Control
	 */
	function GetElement()
	{
		return $this->Element;
	}
	/**
	 * Sets the TreeNode's element that is displayed as the caption. This object is most typically a Label.
	 * @param Control $element
	 */
	function SetElement($element)
	{
		if(is_object($element))
		{
			if($element instanceof Control)
			{
				$this->Element = &GetComponentById($element->Id);
				$this->Element->SetLeft(40);
//				$this->Element->SetTop(-1);
				$this->Element->SetTop(0);
			}
			elseif($element instanceof Item)
			{
				$this->Value = $element->Value;
//				$this->Element = new Label($element->Text, 40, -1, System::Auto, System::Auto);
				$this->Element = new Label($element->Text, 40, 0, System::Auto, System::Auto);
			}
		}
		else
//			$this->Element = new Label($element, 40, -1, System::Auto, System::Auto);
			$this->Element = new Label($element, 40, 0,  System::Auto, System::Auto);
		if(GetBrowser() != 'ie')
		{
			$this->Element->CSSMargin = '5px';
			$this->Element->CSSMarginLeft = '0px';
		}
		$this->Element->SetLayout(Layout::Relative);
		$this->Element->SetCursor(Cursor::Hand);
		ClientScript::Set($this, 'El', $this->Element, '_N');
	}
	/**
	* Sets the color of the TreeNode's Text when this TreeNode is selected, 
	* defaults to white.
	* 
	* @param string $color
	*/
	function SetSelectColor($color)
	{
		ClientScript::Set($this, 'SlClr', $color, '_N');
		$this->SelectColor = $color;	
	}
	/**
	* Gets the color of the TreeNode's Text when this TreeNode is selected, 
	* defaults to white.
	*/
	function GetSelectColor()	{return $this->SelectColor?$this->SelectColor:'#FFFFFF';}
	/**
	* Sets the BackColor of the TreeNode's Text when this TreeNode is selected, 
	* defaults to #316AC5.
	* 
	* @param string $color
	*/
	function SetSelectBackColor($color)
	{
		ClientScript::Set($this, 'SlBkClr', $color, '_N');	
		$this->SelectBackColor = $color;
	}
	/**
	* Gets the BackColor of the TreeNode's Text when this TreeNode is selected, 
	* defaults to #316AC5.
	*/
	function GetSelectBackColor()	{return $this->SelectBackColor?$this->SelectBackColor:'#316AC5';}
	/**
	 * Returns the TreeNode, if any, having this TreeNode as a sub-node.
	 * @return TreeNode
	 */
	function GetParentNode()
	{
		return GetComponentById($this->ParentNodeId);
	}
	/**
	 * @ignore
	 */
	function GetRightBottomChildId()
	{
		if($this->ChildrenPanel->Controls->Count() > 0)
			return $this->ChildrenPanel->Controls->Elements[$this->ChildrenPanel->Controls->Count() -1]->GetRightBottomChildId();
		else 
			return $this->Id;
	}
	/**
	 * Returns the number of TreeNodes that are its sub-nodes recursively. Note: The legacy is strict; a TreeNode is not considered in its own legacy.
	 * @return integer
	 */
	function GetLegacyLength()
	{
		$legacyLength = 0;
		$childCount = $this->ChildrenPanel->Controls->Count();
		for($i=0; $i<$childCount; $i++)
			$legacyLength += $this->ChildrenPanel->Controls->Elements[$i]->GetLegacyLength();
		return $legacyLength + $childCount;
	}
	/**
	 * Expands the TreeNode, and if specified, all of its sub-nodes recursively.
	 * @param boolean $deep
	 */
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
	/**
	 * Expands the Parent TreeNodes recursively so that this TreeNode is displayed. If successful, returns this TreeNode, but if it fails, returns false.
	 * @return boolean|TreeNode
	 */
	function ExpandToShow()
	{
		if($this->ParentNodeId)
		{
			$parentNode = GetComponentById($this->ParentNodeId);
			$parentNode->Expand();
			if($parentNode->ExpandToShow())
				return $this;
		}
		return false;
	}
	/**
	 * Returns the Id of the TreeList, if any, that has this TreeNode as one of its sub-nodes, recursively.
	 * @return string
	 */
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
//		$this->Element->Click['_N'] = new ClientEvent('_NTreeClick("'.$this->Id.'","'.$this->Element->Id.'");');
		$this->Element->Click['_N'] = new ClientEvent('_NTreeClick', $this->Id);
	}
	/**
	 * @ignore
	 */
	function GetClick()
	{
		if(UserAgent::GetName() === UserAgent::IPad)
			return parent::GetClick();
		else
		{
			$click = $this->Element->Click;
			if(!isset($click['_N']))
				$click['_N'] = new ClientEvent('');
			return $this->Element->Click;
		}
		
	}
	/**
	 * @ignore
	 */
	function SetClick($newClick)
	{
		if(UserAgent::GetName() === UserAgent::IPad)
			return parent::SetClick($newClick);
		else
		{
			$this->Element->Click = new Event(array(), array(array($this->Element->Id,'Click')));
			$this->Element->Click['_N'] = $this->TreeListId==null 
				? new ClientEvent('')
	//			: new ClientEvent('_NTreeClick("'.$this->Id.'","'.$this->Element->Id.'");');
				: new ClientEvent('_NTreeClick', $this->Id);
			$this->Element->Click[] = $newClick;
		}
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
	/**
	 * Returns the Value of the TreeNode, or, if it does not exist, the Text of the TreeNode.
	 * @return string
	 */
	function GetValue()
	{
		return $this->Value ? $this->Value : $this->Text;
	}
	/**
	 * Sets the Value of the TreeNode.
	 * @param string $value
	 */
	function SetValue($value)
	{
		$this->Value = $value;
	}
	/**
	 * Returns the Src of the TreeNode Icon when the TreeNode has no sub-nodes.
	 * @return string
	 */
	function GetLeafSrc()
	{
		return $this->LeafSrc;
	}
	/**
	 * Sets the Src of the TreeNode Icon when the TreeNode has no sub-nodes.
	 * @param string $src
	 */
	function SetLeafSrc($src)
	{
		if($src == null)
			$src = TreeNode::GetDefaultLeafSrc();
		$this->LeafSrc = $src;
		if($this->ChildrenPanel->Controls->Count() == 0)
			$this->Icon->Src = $src;
	}
	/**
	 * Returns the Src of the TreeNode Icon when the TreeNode has sub-nodes and is closed.
	 * @return string
	 */
	function GetCloseSrc()
	{
		return $this->CloseSrc;
	}
	/**
	 * Sets the Src of the TreeNode Icon when the TreeNode has sub-nodes and is closed.
	 * @param string $src
	 */
	function SetCloseSrc($src)
	{
		if($src == null)
			$src = TreeNode::GetDefaultCloseSrc();
		$this->CloseSrc = $src;
		NolohInternal::SetProperty('CloseSrc', $src, $this);
		if($this->ChildrenPanel->Controls->Count() != 0 && $this->ChildrenPanel->Visible !== true)
			$this->Icon->SetSrc($src);
	}
	/**
	 * Returns the Src of the TreeNode Icon when the TreeNode has sub-nodes and is open.
	 * @return string
	 */
	function GetOpenSrc()
	{
		return $this->OpenSrc;
	}
	/**
	 * Sets the Src of the TreeNode Icon when the TreeNode has sub-nodes and is open.
	 * @param string $src
	 */
	function SetOpenSrc($src)
	{
		if($src == null)
			$src = TreeNode::GetDefaultOpenSrc();
		$this->OpenSrc = $src;
		NolohInternal::SetProperty('OpenSrc', $src, $this);
		if($this->ChildrenPanel->Controls->Count() != 0 && $this->ChildrenPanel->Visible === true)
			$this->Icon->SetSrc($src);
	}
	/**
	 * Returns whether or not the TreeNode is Selected.
	 * @return boolean
	 */
	function GetSelected()
	{
		return $this->TreeListId !== null && in_array($this, GetComponentById($this->TreeListId)->GetSelectedTreeNodes(), true);
	}
	/**
	 * Sets whether or not the TreeNode is Selected.
	 * @param boolean $bool
	 */
	function SetSelected($bool)
	{
		if($this->TreeListId !== null)
		{
			$selectedTreeNodes = &GetComponentById($this->TreeListId)->_NGetSelectedTreeNodeIds();
			$pos = array_search($this->Id, $selectedTreeNodes, true);
			//if($bool)
			if($bool === ($pos===false))
			{
				if($bool)
				{
					$selectedTreeNodes[] = $this->Id;
					$this->ExpandToShow();
				}
				else
					array_splice($selectedTreeNodes, $pos, 1);
				//GetComponentById($this->TreeListId)->SetSelectedTreeNode($this);
//				QueueClientFunction($this, '_NTreeSlctTgl', array('\''.$this->Id.'\'', '\''.$this->Element->Id.'\''), false);
				ClientScript::Queue($this, '_NTreeSlctTgl', array($this->Id), false);
			//GetComponentById($this->TreeListId)->;
			//if($bool)
			//	QueueClientFunction($this, '_NTreeSlct', array('\''.$this->Id.'\'', '\''.$this->Element->Id.'\'', 'Object()'));
			//$this->Click->Exec();
			}
		}
		else
			BloodyMurder('You must add the TreeNode to the TreeList before selecting it.');
	}
	/**
	 * @ignore
	 */
	function AddShift($shift)
	{
		//if(!isset($this->MouseDown['_N']))
//			$this->MouseDown['_N'] = new ClientEvent('_NTreeSlctOne', $this->Id, $this->Element->Id);
			$this->MouseDown['_N'] = new ClientEvent('_NTreeSlctOne', $this->Id);
		parent::AddShift($shift);
	}
}

?>