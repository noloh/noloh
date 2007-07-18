<?
class TreeNode extends Panel
{
	public $PlusMinus;
	public $Nodes;
	public $NodeElement;
	public $NodeItem;
	public $NodeIcon;
	public $NodePanel;
	public $NodeString;
	private $TreeListId;
	private $ListIndex;
	private $LeafSrc;
	private $CloseSrc;
	private $OpenSrc;
	private $ParentNodeId;
	//private $AltClick;
	
	private static function GetDefaultLeafSrc()
	{
		return NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/document.gif";
	}
	private static function GetDefaultCloseSrc()
	{
		return NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/folder_close.gif';
	}
	private static function GetDefaultOpenSrc()
	{
		return NOLOHConfig::GetNOLOHPath().'Web/UI/Controls/Images/folder_open.gif';
	}
	function TreeNode($element)
	{
		parent::Panel(20, 0, 0, null);
		$this->SetScrolling(System::Full);
		$this->SetPositionType(1);
		if(GetBrowser() == "ie")
			NolohInternal::SetProperty("style.marginTop","6px",$this);
		$this->PlusMinus = new PlusMinusSwitch(0, 0);
		$this->PlusMinus->SetPositionType(1);
		$this->PlusMinus->SetClientVisible(false);
		if(is_object($element))
		{
			if($element instanceof Control)
				$this->NodeElement = &GetComponentById($element->Id);
			elseif($element instanceof Item)
			{
				$this->NodeItem = &$element;
				// Used to be null, then set text. Not sure if you need that but don't see why you would.
	   			//$this->NodeElement = new Label($element->Text, 0, 0, null, 18);
				$this->NodeElement = new Label($element->Text, 0, 0, System::Auto, 18);
	   			//$this->NodeElement->Text = $element->Text;
			}
		}
		else//if(is_string($element))
		{
			$this->NodeString = $element;
			//$this->NodeElement = new Label($element, 0, 0, null, 15);
			$this->NodeElement = new Label($element, 0, 0, System::Auto, 15);
		}
		$this->NodeElement->SetCursor = Cursor::Arrow;
		$this->NodeElement->SetLeft(40);
		$this->NodeIcon = new Image(TreeNode::GetDefaultLeafSrc(), 8, 3, 16, 15);
		//$this->NodeIcon = new Image(null, 17, 3, 16, 15);
		$this->NodeIcon->SetPositionType(1);
		$this->NodePanel = new Panel(25, 20, null, null, $this);
		$this->NodePanel->SetScrolling(System::Full);
		$this->NodePanel->SetPositionType(2);
		$this->NodePanel->SetClientVisible("NoDisplay");
		$this->Nodes = &$this->NodePanel->Controls;
		$this->Nodes->AddFunctionName = "AddNode";
		//$this->Nodes->RemoveFunctionName = "RemoveNode";
		$this->PlusMinus->Change = new ClientEvent('PlusMinusChange("'.$this->NodePanel->Id.'","'.$this->NodeIcon->Id.'")');
		$this->Controls->Add($this->PlusMinus);
		$this->Controls->Add($this->NodeElement);
		$this->Controls->Add($this->NodeIcon);
		$this->Controls->Add($this->NodePanel);
	}
	function AddNode(TreeNode $node)
	{
		$node->SetWidth($this->Width-20);
		if($this->TreeListId != null)
		{
			$node->SetTreeListId($this->TreeListId);
			$listNodes = &GetComponentById($this->TreeListId)->TreeNodesList->Items;
			$lastNodeId = $this->GetRightBottomChildId();
			//for($node->SetListIndex(1); $listNodes->Item[$node->GetListIndex()-1]->Value!=$lastNodeId; $node->SetListIndex($node->GetListIndex()+1));
			for($tmpLI=0; $listNodes->Item[$tmpLI]->Value!=$lastNodeId; $tmpLI++);
			$node->SetListIndex($tmpLI+1);
			$listNodesCount = $listNodes->Count();
			$nodeLegacyLength = $node->GetLegacyLength()+1;
			for($i=$node->ListIndex; $i<$listNodesCount; ++$i)
			{
				$tempNode = &GetComponentById($listNodes->Item[$i]->Value);
				$tempNode->SetListIndex($tempNode->GetListIndex() + $nodeLegacyLength);
			}
			$node->ParentNodeId = $this->Id;
			$listNodes->Insert(new Item($node->Id, $node->NodeElement->Id), $node->ListIndex);
			$node->TellChildren($this->TreeListId, $listNodes);
		}
		if($this->NodePanel->Controls->Count() == 0)
		{
			$this->PlusMinus->ClientVisible = true;
			if($this->NodePanel->ClientVisible === true)
				$this->NodeIcon->Src = $this->OpenSrc!=null ? $this->OpenSrc : TreeNode::GetDefaultOpenSrc();
			else 
				$this->NodeIcon->Src = $this->CloseSrc!=null ? $this->CloseSrc : TreeNode::GetDefaultCloseSrc();
		}
		//if(func_num_args()==1)
			$this->NodePanel->Controls->Add($node, true, true);
		return $node;
	}
	function RemoveNode($idx)
	{
		$this->NodePanel->Controls->Items[$idx]->Remove();
	}
	function Remove()
	{
		$tList = GetComponentById($this->TreeListId);
		$legacyLength = $this->GetLegacyLength();
		if($this->ParentNodeId != null)
		{
			$parentNode = $this->GetParentNode();
			$parentNode->NodePanel->Controls->RemoveItem($this);
			if($parentNode->NodePanel->Controls->Count() == 0)
			{
				$parentNode->PlusMinus->ClientVisible = false;
				$parentNode->NodeIcon->Src = TreeNode::GetDefaultLeafSrc();
			}
		}
		else 
			$tList->Nodes->RemoveItem($this);
		$listNodes = $tList->TreeNodesList->Items;
		for($i=0; $i<=$legacyLength; ++$i)
			$listNodes->RemoveAt($this->ListIndex);
		$listNodesCount = $listNodes->Count();
		for($i=$this->ListIndex; $i<$listNodesCount; ++$i)
		{
			$tmpNode = &GetComponentById($listNodes->Item[$i]->Value);
			$tmpNode->SetListIndex($tmpNode->GetListIndex() - $legacyLength - 1);
		}
		$this->ForgetListDeeply();
	}

	function TellChildren($treeListId, $listNodes)
	{
		$index = $this->ListIndex;
		$nodesCount = $this->Nodes->Count();
		for($i=0; $i<$nodesCount; ++$i)
		{
			$node = &$this->Nodes[$i];
			$node->SetListIndex($index+$i+1);
			$node->SetTreeListId($treeListId);
			$listNodes->Insert(new Item($node->Id, $node->NodeElement->Id), $node->ListIndex);
			$index += $node->TellChildren($treeListId, $listNodes);
		}
		return $i;
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
		$this->PlusMinus->Src = NOLOHConfig::GetNOLOHPath()."Web/UI/Controls/Images/minus.gif";
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
		/*
		if(!$this->NodeElement->Click->Blank())
		{
			$e = $this->NodeElement->Click;
			
		}
		*/
		//$this->NodeElement->Click = new ClientEvent("SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);");
		//DOESN'T WORK ANYMORE - ASHER
		//Alert("about to");
		$this->NodeElement->Click["_N"] = new ClientEvent("SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);");
		//$this->SetClick($this->AltClick);
		//$this->SetClick(null);
	}

	function GetListIndex()
	{
		return $this->ListIndex;
	}

	function SetListIndex($newListIndex)
	{
		$this->ListIndex = $newListIndex;
		NolohInternal::SetProperty("ListIndex", $newListIndex, $this);
	}

	function GetClick()
	{
		$click = $this->NodeElement->Click;
		if(!isset($click["_N"]))
			$click["_N"] = new ClientEvent("");
		return $this->NodeElement->Click;
	}

	function SetClick($newClick)
	{
		//parent::SetClick($newClick);
		//$this->Click->ServerVisible = false;
		//$this->NodeElement->Click = new ClientEvent("SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);");
		
		//if($newClick instanceof Event)
		//	$this->NodeElement->Click->Plus = new ClientEvent($this->GetEventJS("Click"));
		//$this->AltClick = $newClick;
		/*
		if($this->TreeListId != null)
		{
			/*
			$eventString = "SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);";
			if($newClick != null)
				$eventString .= str_replace("\\\"", "'", $newClick->GetEventString("Click", $this->Id));
			//Alert($eventString);
			$this->NodeElement->Click = new ClientEvent($eventString.";");
			*
			$this->NodeElement->Click = new Event(array(), array(array($this->NodeElement->Id,"Click")));
			$this->NodeElement->Click["SN"] = new ClientEvent("SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);");
			$this->NodeElement->Click[] = $newClick;
		}
		*/
		$this->NodeElement->Click = new Event(array(), array(array($this->NodeElement->Id,"Click")));
		$this->NodeElement->Click["_N"] = $this->TreeListId==null 
			? new ClientEvent("alert('hi kids');")
			: new ClientEvent("SelectNode('$this->Id', '$this->TreeListId', ".(GetBrowser()=="ie"?"window.":"")."event);");
		$this->NodeElement->Click[] = $newClick;
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
		NolohInternal::SetProperty("CloseSrc", $newSrc, $this->NodeIcon);
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
		NolohInternal::SetProperty("OpenSrc", $newSrc, $this->NodeIcon);
		if($this->NodePanel->Controls->Count() != 0 && $this->NodePanel->ClientVisible === true)
			$this->NodeIcon->SetSrc($newSrc);
	}
	/*
	private function UpdateNodeElementClick()
	{
		$this->NodeElement->Click = new ClientEvent("SelectNode('" . $this->Id . "', '" . $this->TreeListId . "', event);");
		if(isset($this->Click))
			$this->NodeElement->Click->Plus = new ClientEvent($this->GetEventJS("Click"));
	}
	*/
	function Show()
	{
		NolohInternal::SetProperty("OpenSrc", $this->OpenSrc!=null?$this->OpenSrc:TreeNode::GetDefaultOpenSrc(), $this->NodeIcon);
		NolohInternal::SetProperty("CloseSrc", $this->CloseSrc!=null?$this->CloseSrc:TreeNode::GetDefaultCloseSrc(), $this->NodeIcon);
		//if($this->Click instanceof Event)
		//{
		//	$this->NodeElement->Click->Plus = new ClientEvent($this->GetEventJS("Click"));
		//	$this->Click->ServerVisible = false;
		//}
		
		/*
		if($this->NodePanel->Controls->Count() != 0)
		{
			AddScript('document.getElementById("'.$this->NodeIcon->Id.'").CloseSrc="' . 
				(isset($this->CloseSrc) ? $this->CloseSrc : TreeNode::GetDefaultCloseSrc()).'"');
			AddScript('document.getElementById("'.$this->NodeIcon->Id.'").OpenSrc="' . 
				(isset($this->OpenSrc) ? $this->OpenSrc : TreeNode::GetDefaultOpenSrc()).'"');			
			if($this->NodeIcon->Src == null)
				if(isset($this->CloseSrc))
					$this->NodeIcon->Src = $this->CloseSrc;
				else
					$this->NodeIcon->Src = $this->CloseSrc : TreeNode::GetDefaultCloseSrc();
			$this->PlusMinus->ClientVisible = true;
		}
		else 
		{
			if(!isset($this->NodeIcon->Src))
				if(!isset($this->LeafSrc))
					$this->NodeIcon->Src = $this->CloseSrc : TreeNode::GetDefaultLeafSrc();
				else 
					$this->NodeIcon->Src = $this->LeafSrc;
			$this->PlusMinus->ClientVisible = false;
		}
		*/
		parent::Show();
		return true;
	}
}

?>