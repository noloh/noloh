<?php
/**
 * ListView class
 *
 * ListViews are used to present information in a tabular, sortable, grid. ListView's rows are made up of ListViewItems which can be popoulated with any object. In addition, a ListView can be directly bound to data sources using its Bind functionality.
 * 
 * See the Data Binding article for more information.
 * 
 * Basic example of instantiation and using a ListView:
 * <pre>
 * $listView = new ListView();
 * $listView->Columns->AddRange('First Name', 'Last Name', 'Address')
 * 
 * // Lets assume that people is an associative array containing a list of people
 * $people = array(...);
 * 
 * foreach($people as $person)
 * {
 *     $row = new ListViewItem();
 *     $row->SubItems->AddRange($person['fname'], $person['lname'], $person['address']);
 *     $listView->ListViewItems->Add($row);
 * }
 * </pre>
 * @package Controls/Extended
 */
class ListView extends Panel
{
	static $Ordered;
	/**
	 * @ignore
	 */
	const Ascending = true; 
	/**
	 * @ignore
	 */
	const Descending = false;
	/**
	 * An ArrayList containing the ListView's ListViewItems. ListViewitems should be added to this ArrayList
	 * @var ArrayList
	 */
	public $ListViewItems;
	private $Columns;
	private $CurrentOffset;
	private $BodyPanelsHolder;
	private $SelectedRows;
	private $Selectable;
	private $SelectCSS;
	private $DataSource;
	private $Limit;
	private $SortedBy;
	private $StoredInMemory;
	private $RowCallback;
	private $ApproxCount;
	private $HeightSpacer;
	private $DataColumns;
	private $PrevColumn;
	private $ExcessSubItems;
	private $ColumnLookup;
	private $_InnerOffset;
//	private $Loader;
	/**
	 * @ignore
	 */
	protected $ColumnsPanel;
/*	/**
	 * @ignore
	 */
//	protected $LVItemsQueue = array();
	/**
	 * @ignore
	 */
	protected $InnerPanel;
	/**
	 * @ignore
	 */
	protected $Line;
	/**
	 * Returns the Panel containing the ListView's ColumnHeaders
	 * @return Panel
	 */
	function GetColumnPanel(){return $this->ColumnsPanel;}
	/**
	 * Constructor
	 * 
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
	function ListView($left=0, $top=0, $width=300, $height=200)
	{
		parent::Panel($left, $top, $width, null)/*, $this)*/;
		$this->Scrolling = false;
		$this->ColumnsPanel = new Panel(0, 0, $width, 22, $this);
		$this->ColumnsPanel->CSSBackgroundImage = "url(". System::ImagePath() . "Std/HeadBlue.gif)";
		$this->ColumnsPanel->CSSBackgroundRepeat = "repeat-x";
		$this->ColumnsPanel->Controls->AddFunctionName = "AddColumn";
		$this->Columns = &$this->ColumnsPanel->Controls;
		$this->BodyPanelsHolder = new Panel(0, 0, $width, $height - $this->ColumnsPanel->Height);
		$this->BodyPanelsHolder->SetScrolling(System::Auto);
		
		$this->ColumnsPanel->Layout = $this->BodyPanelsHolder->Layout = Layout::Relative;
		$this->InnerPanel = new Panel(0, 0, $this->Width, null, $this);
		$this->InnerPanel->SetLayout(Layout::Web);
		$this->BodyPanelsHolder->SetScrolling(System::Auto);
		//ListViewItems
		$this->ListViewItems = &$this->InnerPanel->Controls;
		$this->ListViewItems->AddFunctionName = 'AddListViewItem';
		$this->ListViewItems->RemoveFunctionName = 'RemoveListViewItem';
		$this->ListViewItems->ClearFunctionName = 'ClearListViewItems';
		$this->ListViewItems->InsertFunctionName = 'InsertListViewItem';
		$this->BodyPanelsHolder->Controls->Add($this->InnerPanel);
		//Shift With Outer Panel
		$this->ColumnsPanel->Shifts[] = Shift::WidthWith($this, Shift::Width);
		$this->BodyPanelsHolder->Shifts[] = Shift::WidthWith($this, Shift::Width);
		$this->SetHeight($height);
		//Line
		$this->Line = new Label('', 0, 0, 3, '100%');
		$this->Line->Visible = false;
		$this->Line->BackColor = '#808080';
		$this->Line->ParentId = $this->Id;
		$this->Controls->AddRange($this->ColumnsPanel, $this->BodyPanelsHolder);
		$this->ModifyScroll();
	}
	/*function HandleColumns()
	{
		$args = func_get_args();
	    $invocation = InnerSugar::$Invocation;
	    $tail = InnerSugar::$Tail;
	    if($invocation == InnerSugar::Get)
	    {
	        switch($tail)
	        {
	            case 'Everything':
	                return 42;
	                break;
	            default: throw new SugarException();
	        }
	    }
	}*/
	/**
	 * Returns an ArrayList containing the ColumnHeaders of the ListView. ColumnHeaders should be added to this ArrayList
	 * @return ArrayList
	 */
	function GetColumns(){return $this->Columns;}
	/**
	 * @ignore
	 */
	function SetHeight($height)
	{
		parent::SetHeight($height);
		$columnHeight = $this->ColumnsPanel->Height;
		if($height >= $columnHeight)
			$this->BodyPanelsHolder->Height = $height - $columnHeight;
	}
	/**
	 * @ignore
	 */
	function AddColumn($text, $width = System::Auto)
	{
//		$column = (is_string($text) || is_int($text))
//			? new ColumnHeader($text, 0, $width, $this->ColumnsPanel->GetHeight())
		$column = ($text instanceof ColumnHeader)? $text: new ColumnHeader($text, 0, $width, $this->ColumnsPanel->GetHeight());
		
		if($column)
		{
			$this->Columns->Add($column, true);
			$column->SetLayout(Layout::Relative);
			$column->CSSFloat = 'left';
			$this->BondColumn($column);
			$column->SetListView($this->Id);
			$column->ShiftStart = new ClientEvent('_NLVResizeStart', $this->Line->GetId(), $column->GetId(), $this->InnerPanel->GetId());
			$column->ShiftStop = new ClientEvent('_NLVResizeEnd');
			$this->Line->Shifts[] = Shift::LeftWith($column, Shift::Width);
			ClientScript::Queue($column, '_NHndlClmn', array($column, $this->InnerPanel));
		}
		if(isset($this->ExcessSubItems))
		{
			foreach($this->ExcessSubItems as $key => $listViewItem)
			{
				if($listViewItem->GetListView())
					$this->Update($listViewItem);
			}
		}	
		return $column;
	}
	private function BondColumn($object, $column=null/*, $shift=true*/)
	{
		if($object instanceof Control)
		{
			$object->Toggle = System::Continuous;
			$select = $object->GetSelect();
			$object->Select = null;
			$object->Select['LV'] = new ServerEvent($this, 'Sort', $column === null?$object:$column);
			$object->Select['LV'][] = $select;	
			
			$this->InnerPanel->Shifts[] = Shift::WidthWith($object);
		}
	}
	/**
	 * @ignore
	 */
	function AddListViewItem($listViewItem)
	{
		if(!$listViewItem instanceof ListViewItem)
		{
			if(is_array($listViewItem))
			{
//				System::Log($listViewItem);
				if(isset($this->DataColumns))
			{
				$previousCols = isset($GLOBALS['_NLVCols'])?$GLOBALS['_NLVCols']:null;
				$GLOBALS['_NLVCols'] = $this->DataColumns;
				}
				if($this->RowCallback)
				{
					$previousBound = Event::$BoundData;
				Event::$BoundData = $listViewItem;
					$listViewItem = $this->RowCallback->Exec();
				Event::$BoundData = $previousBound;
				if(is_array($listViewItem))
					{
						if(isset($this->DataColumns))
							$GLOBALS['_NLVCols'] = $previousCols;
					return $this->InsertListViewItem($listViewItem[0], $listViewItem[1]);		
			}
				}
				else
					$listViewItem = new ListViewItem($listViewItem);
					
				if(isset($this->DataColumns))
					$GLOBALS['_NLVCols'] = $previousCols;	
			}
			else
				$listViewItem = new ListViewItem($listViewItem);
		}
		$this->ListViewItems->Add($listViewItem, true);
		$listViewItem->SetListView($this);
		return $this->SetItemProperties($listViewItem);
	}
	function RemoveListViewItem($listViewItem)
	{
		$this->ListViewItems->Remove($listViewItem, true);
		$listViewItem->SetListView(null);
	}
	/**
	 * @ignore
	 */
	function InsertListViewItem($listViewItem, $idx)
	{
		$this->ListViewItems->Insert($listViewItem, $idx, true);
		$listViewItem->SetListView($this);
		return $this->SetItemProperties($listViewItem);
	}
	/**
	 * @ignore
	 */
	function Update(ListViewItem $listViewItem=null, $startColumn=null)
	{
		$this->SetItemProperties($listViewItem, $startColumn);
		return true;
	}
	//Function Consolidates adding parts of Update() and AddListViewItem()
	private function SetItemProperties(ListViewItem $listViewItem, $startColumn = null)
	{
		$subItemCount = $listViewItem->SubItems->Count();
		$colCount = $this->Columns->Count();
//		System::Log('SubItemCount', $subItemCount, 'ColCount', $colCount);
		$start = $startColumn !== null?$startColumn:$listViewItem->Controls->Count();
		if($subItemCount > $colCount)
		{
			if(!isset($this->ExcessSubItems))
				$this->ExcessSubItems = array();
			if(!isset($this->ExcessSubItems[$listViewItem->Id]))
				$this->ExcessSubItems[$listViewItem->Id] = $listViewItem;
		}
		elseif(isset($this->ExcessSubItems[$listViewItem->Id]))
			unset($this->ExcessSubItems[$listViewItem->Id]);
			
		if($colCount > 0 && $subItemCount > 0)
		{
			$max = ($colCount > $subItemCount)?$subItemCount:$colCount;
			for($i=$start; $i < $max; ++$i)
			{
//				System::Log('start', $start, 'max', $max, 'i', $i);
				if(!isset($listViewItem->Controls->Elements[$i]))
				{
					$column = $this->Columns->Elements[$i];
					$colId = $column->GetId();
					$subItem = $listViewItem->SubItems->Elements[$i];
					$listViewItem->ShowSubItem($subItem);
					
					if(isset($_SESSION['_NFunctionQueue'][$colId]) && isset($_SESSION['_NFunctionQueue'][$colId]['_NLVSet']))
					{
						$_SESSION['_NFunctionQueue'][$colId]['_NLVSet'][0][] = ClientScript::ClientFormat($subItem);
//						System::Log($_SESSION['_NFunctionQueue'][$colId]['_NLVSet']);
					}
					else
					{
						ClientScript::Queue($column, '_NLVSet', array($column, $subItem), true);
					}
				}
			}
		}
		if($this->Selectable)
		{
			$listViewItem->UpdateEvent('Click');
			ClientScript::Set($listViewItem, 'SelCls', $this->SelectedCSS);
		}
		return $listViewItem;
	}
	private function ModifyScroll()
	{
		$this->BodyPanelsHolder->Scroll = new ClientEvent("_NLVModScroll('{$this->BodyPanelsHolder->Id}', '{$this->ColumnPanel->Id}', '{$this->InnerPanel->Id}');");
	}
	/**
	 * @ignore
	 */
	public function ClearListViewItems()
	{
		ClientScript::Set($this->InnerPanel, 'SelectedRows', null, null);
		$this->ListViewItems->Clear(true);
		if(isset($this->ExcessSubItems))
			$this->ExcessSubItems = array();
//		$this->LVItemsQueue = array();
//		$this->LVItemsQueue = array();
	}
	/**
	 * Clears both the Columns and ListViewItems of the ListView
	 */
	public function Clear()
	{
		$this->ClearListViewItems();
		$this->Columns->Clear();
		$this->ColumnLookup = null;
	}
	/**
	 * @ignore
	 */
	function GetDataFetch()
	{
		//$this->SetCursor($this->BodyPanelsHolder->GetCursor());
		return $this->GetEvent('DataFetch');
	}	
	/**
	 * @ignore
	 */
	function SetDataFetch($newEvent)
	{
		$this->SetEvent($newEvent, 'DataFetch');
	}
	/**
	 * @ignore
	 */
	function Show()
	{
		ClientScript::AddNOLOHSource('ListView.js');
		ClientScript::AddNOLOHSource('Dimensions.js', true);
		parent::Show();
	}
	/**
	 * Returns the number of rows of the dataset the ListView is bound to.
	 * @return integer
	 */
	public function GetBoundCount()	{return $this->ApproxCount;}
	/**
	 * @ignore
	 */
	public function GetApproxCount()	{return $this->ApproxCount;}
	/**
	 * Binds a ListView to a data source. See the Data Binding article for more information.
	 * 
	 * Rather than execute our database function directly, we'll create a 
	 * command to be used with our Bind. While we can pass in a result set 
	 * from an ExecFunction, passing a command is more efficient since Bind 
	 * will only retrieve the necessary rows instead of all resulting rows.
	 * <pre>
	 * $people = Data::$Links->MyDB->CreateCommand('sp_get_people', 10065);
	 * //Instantiates a ListView
	 * $listView = new ListView();
	 * $listView->Bind($people, array('firstname', 'lastname', 'phone'));
	 * </pre>
	 * 
	 * @param mixed $dataSource The data source you wish to bind to. In most cases this is a DataCommand object
	 * @param array $constraints An array of constraints to be bound to
	 * @param integer $limit The number of rows to return at a time
	 * @param integer $offset The offset of the first row
	 * @param ServerEvent $rowCallback The ServerEvent to be called for each row, this allows increased control of your row
	 */
	public function Bind($dataSource=null, $constraints=null, $limit=50, $offset=0, $rowCallback=null/*, $storeInMemory = false*/)
	{
		$data = null;
		if($dataSource != null)
		{
			$this->Clear();
			$this->DataSource = $dataSource;
			$sql = preg_replace('/(.*?);*?\s*?\z/i', '$1', $dataSource->GetSQL());
			//Row Count
			$connection = $dataSource->GetConnection();
			$numRows = new DataCommand($connection, 'SELECT count(1) FROM (' . $sql . ') as sub_query ', Data::Num);
			$numRows = $numRows->Execute();
			$numRows = $numRows->Data[0][0];
			//Columns
			if($constraints)
			{
				$columns = new DataCommand($connection, 'SELECT * FROM (' . $sql . ') AS sub_query LIMIT 1', Data::Assoc);
				$columns = $columns->Execute();
				if(!empty($columns->Data))
					$this->ColumnLookup = array_flip(array_keys($columns->Data[0]));
			}
			if($this->HeightSpacer)
				$this->HeightSpacer->SetHeight($numRows * 20);
			else
			{
				$this->HeightSpacer = new Label('', 0, 0, 1, $numRows * 20);
				$this->HeightSpacer->ParentId = $this->BodyPanelsHolder->Id;
			}
			$this->ApproxCount = $numRows;
			$sql = 'SELECT * FROM (' . $sql . ') as sub_query ';
			$this->DataSource = new DataCommand($dataSource->GetConnection(), $sql, $dataSource->ResultType);
			$this->Limit = $limit;
			$callBack = true;
		}
		else
		{
			$callBack = false;
			/*Finds the difference between the scrollTop and the InnerOffset and
			and divides it by the average row height to determine the needed
			limit*/
			$difference = $this->_InnerOffset['scrollTop'] - $this->_InnerOffset['innerOffset'];
			$calcLimit = ceil(($difference + ($this->BodyPanelsHolder->GetHeight() * 2))/20);
//			System::Log('CalcLimit', $calcLimit);
			//
//			$this->Loader->Visible = false;
			$offset = $this->CurrentOffset;
			$limit = $this->Limit > $calcLimit?$this->Limit:$calcLimit;
		}
		if(isset($constraints))
		{
			$this->DataColumns = array();
			$columns = array();
			$count = count($constraints);
			for($i=0; $i < $count; ++$i)
			{
				$properties = array(null, null, System::Auto);
				if(is_array($constraints[$i]))
				{
					$currentProperty = 0;
					//0=>column, 1=>title, 2=>width
					foreach($constraints[$i] as $constraint => $value)
					{
						if(is_string($constraint))
						{
							$constraint = strtolower($constraint);
							if(strtolower($constraint) == 'name')
								$properties[0] = $value;
							elseif(strtolower($constraint) == 'title')
								$properties[1] = $value;
							elseif(strtolower($constraint) == 'width')
								$properties[2] = $value;
						}
						else
							$properties[$currentProperty++] = $value;
					}
				}
				else
					$properties[0] = $properties[1] = $constraints[$i];

				if($properties[0] || $properties[1])
				{
					if($properties[1] !== false)
					{
							$title = ($properties[1] != false)?$properties[1]:$properties[0];
							$this->AddColumn($title, $properties[2]);
						$this->DataColumns[] = $properties[0];
					}
					if($properties[0])
						$columns[] = $properties[0];
				}			
			}
		}
		if($this->DataSource instanceof DataCommand)
		{
			if(!$loadIntoMemory)
			{
				$sql = $this->DataSource->GetSQL();
//				return System::Log($sql);
				$result = preg_replace('/^(.*?)(?:\s+(?:OFFSET\s+\d+)|(?:LIMIT\s+\d+)|\s)*?;$/si', '$1', $sql, 1);
//				$result = preg_replace('/^(.*?)\s*(?:(?:OFFSET\s*\d*)|(?:LIMIT\s*\d*)|\s)*?\s*;/si', '$1', $sql, 1);
//				$result = preg_replace('/(.*?)\s*(?:(?:OFFSET\s*\d*)|(?:LIMIT\s*\d*)|\s)*?\s*;/si', '$1', $sql);
//				return System::Log('failed', self::pcre_error_decode(preg_last_error()));
				$result .= ' LIMIT ' . $limit . ' OFFSET ' . $offset . ';';
//				return System::Log($result);
				$this->DataSource->SetSQL($result);
				if($callBack)
				{
					if($rowCallback instanceof ServerEvent)
						$this->RowCallback = $rowCallback;
					if($constraints)
						$this->DataSource->Callback(new DataConstraint($columns), $this, 'AddListViewItem');
					else
						$this->DataSource->Callback($this, 'AddListViewItem');
				}
				$data = $this->DataSource->Execute();
				if(count($data->Data) < $limit)
					$this->DataFetch['Bind']->Enabled = false;
				elseif($this->GetDataFetch('Bind')->Blank())
				{
					$this->DataFetch['Bind'] = new ServerEvent($this, 'Bind');
//					$this->SetLoader();
				}
				else
					$this->DataFetch['Bind']->Enabled = true;
			}
			$this->CurrentOffset = $offset + $limit;
		}
		elseif(is_array($dataSource))
		{
		}
		elseif(is_file($dataSource))
		{
			/*if xml
			elseif JSON
			elseif CSV*/
		}
		elseif(false/*URL*/)
		{
			
		}
		if(!isset($constraints) && isset($data->Data[0]) && $callBack/* && !$rowCallback*/)
		{
			$this->Columns->AddRange(array_keys($data->Data[0]));
	}
	}
	/**
	 * Sorts the ListView on a particular column
	 * @param integer|ColumnHeader $column Either the index of the Column or the actual ColumnHeader object you wish to sort on 
	 * @param boolean $ascending 
	 */
	public function Sort($column, $ascending=true)
	{
		//TODO Allow Specifying of Property you want to sort on a per column basis.
		if($column instanceof Control)
			$index = $this->Columns->IndexOf($column);
		elseif(is_int($column))
			$index = $column;
		else return;

		if(isset($this->PrevColumn) && $column != $this->PrevColumn)
			$this->PrevColumn->SetSelected(false);
			
		if(isset($this->SortedBy) && $this->SortedBy['column'] === $column->Id)
			$ascending = $this->SortedBy['sorted'] = ($this->SortedBy['sorted'] == self::Ascending)?self::Descending:self::Ascending;
		else
			$this->SortedBy = array('column' => $column->Id, 'sorted' => $ascending);
		
		self::$Ordered = $ascending;
		
		$this->PrevColumn = $column;

		if($this->DataSource != null && !$this->StoredInMemory && $this->DataFetch['Bind']->Enabled)
		{
			$result = preg_replace('/^(.*?)(?:\s+(?:OFFSET\s+\d+)|(?:LIMIT\s+\d+)|\s)*?;$/si', '$1', $this->DataSource->GetSQL());
//			$result = preg_replace('/(.*?)\s*(?:(?:OFFSET\s*\d*)|(?:LIMIT\s*\d*)|\s)*?\s*;/si', '$1', $this->DataSource->GetSQL());
			$result = preg_replace('/sub_query ORDER BY (?:[\w"]+(?: ASC| DESC)?(?:, ?)?)+/', '', $result, 1, $count);
			
			$callBack = $this->DataSource->GetCallback();
			if(isset($callBack['constraint']) && is_array($this->ColumnLookup))
			{
//				$columnName = $callBack['constraint']->Columns[$this->DataColumns[$index]];
				$columnName = $this->DataColumns[$index];
				$sortColumn = isset($this->ColumnLookup[$columnName])?$this->ColumnLookup[$columnName] + 1:$columnName;
			}
			else
				$sortColumn = $index + 1;
			if($count > 0)
				$result .= 'sub_query';
			$result .= ' ORDER BY ' . $sortColumn;
			if(!$ascending)
				$result .= ' DESC';
			
			$this->DataSource->SetSQL($result);
			$this->ListViewItems->Clear();
			$this->CurrentOffset = 0;
			$this->DataFetch['Bind']->Enabled = true;
			$this->DataFetch['Bind']->Exec();
			return;
		}		
		$rows = array();
		
		foreach($this->ListViewItems->Elements as $key => $listViewItem)
			$rows[$key] = isset($listViewItem->SubItems[$index])?$listViewItem->SubItems[$index]->GetText():null;	
		if(!$ascending)
			asort($rows);
		else
			arsort($rows);
		
		if(count($rows) > 0)
		{
		foreach($rows as $key => $val)
			$clientArray[] = $this->ListViewItems->Elements[$key]->GetId();
		
		ClientScript::Queue($this, '_NLVSort', array($this->InnerPanel, $clientArray));
	}
	}
	/**
	 * @ignore
	 */
	public function Set_NSelectedRows($rows)
	{
		$this->SelectedRows = explode('~d2~', rtrim($rows, '~d2'));
	}
	/**
	 * Returns an array of the currently Selected ListViewItems
	 * @return array
	 */
	public function GetSelectedListViewItems()
	{
		$listViewItems = array();
		$count = count($this->SelectedRows);
		for($i=0; $i < $count; ++$i)
			$listViewItems[] = GetComponentById($this->SelectedRows[$i]);
			
		return $listViewItems;
	}
	/**
	 * Returns an array of values for the currently Selected ListViewItems
	 * @return array
	 */
	public function GetSelectedValues()
	{
		$values = array();
		$count = count($this->SelectedRows);
		for($i=0; $i < $count; ++$i)
			$values[] = GetComponentById($this->SelectedRows[$i])->GetValue();
			
		return $values;
	}
	/**
	 * Sets whether Rows are Selectable. This allows for row selecting, in addition to ctrl click functionality.
	 * @param bool $mode Whether the ListView is Selectable
	 * @param string $cssClass The CSS class you wish to use on the row when selected.
	 */
	public function SetSelectable($mode, $cssClass = 'NLVSelect')
	{
		if($mode)
		{
			foreach($this->ListViewItems as $listViewItem)
			{
				$listViewItem->UpdateEvent('Click');
				ClientScript::Set($listViewItem, 'SelCls', $cssClass);
			}
			$this->Selectable = $mode;
			$this->SelectCSS = $cssClass;
		}
	}
	/**
	* @ignore
	*/
	function Set_InnerOffset($values)
	{
		$values = explode(',', $values);
		$this->_InnerOffset = array('scrollTop' => $values[0], 'innerOffset' => $values[1]);
	}
//	function GetLoader()
//	{
//		
//	}
//	/**
//	* @ignore
//	* 
//	*/
//	function SetLoader($loadObj=null)
//	{
//		$loadPanel = new Panel(0, $this->ColumnsPanel->GetHeight(), $this->BodyPanelsHolder->Width, $this->BodyPanelsHolder->Height);
//		$loadPanel->CSSClass = 'NLVLoad';
//		if(!$loadObj)
//		{
//			$loader = new Image(System::ImagePath() . 'lv_loader.gif');
//			$loadText = new Label('Loading...', 0, 0, '100%', null);
//			$loadObj = new Panel(0, 0, $loader->GetWidth(), null);
//			$loadObj->Controls->AddRange($loader, $loadText);
//			$loadObj->Controls->AllLayout = Layout::Relative;
//		}
//		$loadPanel->Controls->Add($loadObj);
//		ClientScript::AddNOLOHSource('Layout.js');
//		ClientScript::Queue($loadPanel, 'HAlign', array($loadObj));
//		ClientScript::Queue($loadPanel, 'VAlign', array($loadObj));
//		$this->Loader = $loadPanel;
//		$this->Loader->ParentId = $this->Id;
//		$this->Loader->SetVisible(false);
//		ClientScript::Set($this, 'Loader', $this->Loader, '_N');
//	}
	/*function pcre_error_decode($errcode)
	{
	  switch ($errcode)
	  {
	    case PREG_NO_ERROR:
	      return 'PREG_NO_ERROR';
	    case PREG_INTERNAL_ERROR:
	      return 'PREG_INTERNAL_ERROR';
	    case PREG_BACKTRACK_LIMIT_ERROR:
	      return 'PREG_BACKTRACK_LIMIT_ERROR';
	    case PREG_RECURSION_LIMIT_ERROR:
	      return 'PREG_RECURSION_LIMIT_ERROR';
	    case PREG_BAD_UTF8_ERROR:
	      return 'PREG_BAD_UTF8_ERROR';
	    case PREG_BAD_UTF8_OFFSET_ERROR:
	      return 'PREG_BAD_UTF8_OFFSET_ERROR';
	    default:
	      return 'Unknown error code';
	  }
	}*/
}
?>