<?php
/**
 * @internal
 */
class Paginator extends RichMarkupRegion implements Countable
{
	const First = '{first}', Last = '{last}', Prev = '{prev}', Next = '{next}', Pages = '{pages}', Status = '{status}',
	CurrentPage = '{currentPage}', LastPage = '{lastPage}';
	
	private $First;
	private $Last;
	private $Prev;
	private $Next;
	private $Pages;
	private $Status;
	private $NumResults;
	private $Limit;
	private $CurrentOffset;
	private $ResultsPanel;
	private $CurrentPage;
	private $Template;
	private $DataSource;
	private $RowCallback;
	private $MaxPages = 5;
	private $PageClass;
	private $PageSelectedClass;
	private $ShowsEmpty;
	
	function Paginator($resultsPanel=null, $left=0, $top=0, $width=300, $height=25)
	{
		parent::RichMarkupRegion(null, $left, $top, $width, $height);
		$this->Init($resultsPanel);
	}
	private function Init($resultsPanel)
	{
		$this->Scrolling = false;
		$this->Visible = false;
		$this->SetResultsPanel($resultsPanel);
		$template = '{first} {prev} {status} {pages} {next} {last}';
//		$template = '{first}{prev}{next}{last}';
		$this->SetTemplate($template);
	}
	function SetEmptyShow($bool)
	{
		if($bool & $this->Visible !== true)
			$this->Visible = true;
	}
	function GetShowsEmpty()	{return $this->ShowEmpty;}
	function GetNext()	{return $this->Next;}
	function GetPrev()	{return $this->Prev;}
	function GetLast()	{return $this->Last;}
	function GetFirst()	{return $this->First;}
	function GetStatus(){return $this->Status;}
	function GetOffset()	
	{
		if(is_array($this->CurrentOffset))
			return $this->CurrentOffset[1];
		else
			return $this->CurrentOffset;
	}
	function GetPageChange()
	{
		return parent::GetEvent('PageChange');
	}
	function SetPageChange($event)
	{
		return parent::SetEvent($event, 'PageChange');
	}
	function SetMaxPages($num)
	{
		$this->MaxPages = $num;
	}
	function GetMaxPages()	{return $this->MaxPages;}
	function SetPageClass($class)
	{
		$prevClass = $this->PageClass;
		$this->PageClass = $class;
		if($this->Pages)
			foreach($this->Pages->Controls as $page)
			{
				if($prevClass)
					$page->CSSClass = preg_replace("/\s*$\s*/i", '', $prevClass);
				$page->CSSClass .= ' ' . $class;
			}
	}
	function GetCurrentPage()	{return $this->CurrentPage;}
	function CreatePage($number)
	{
		return new Link('#', $number, 0, 0, null, null);
	}
	function CreateFirst()
	{
		return new Link('#', '<< first', 0, 0, null, null);
	}
	function CreatePrev()
	{
		return new Link('#', '< prev', 0, 0, null, null);
	}
	function CreateStatus()
	{
		return new Label('0 of 0', 0, 0, null, null);
	}
	function CreateNext()
	{
		return new Link('#', 'next >', 0, 0, null, null);
	}
	function CreateLast()
	{
		return new Link('#', 'last >>', 0, 0, null, null);
	}
	function SetResultsPanel($resultsPanel)
	{
		if($resultsPanel instanceof Panel || $resultsPanel instanceof Container || $resultsPanel instanceof ArrayList)
			$this->ResultsPanel = $resultsPanel;
		else
			BloodyMurder('ResultsPanel must be an instance of a Panel, Container, or ArrayList, you passed in an ' . get_class($resultsPanel));
	}
	function SetTemplate($string)
	{
		$template = str_replace(array('{first}', '{last}', '{prev}', '{next}', '{pages}', '{status}'), 
		array('<n:larva style=descriptor="first"/>', '<n:larva descriptor="last"/>', '<n:larva descriptor="prev"/>',
		'<n:larva descriptor="next"/>', '<n:larva descriptor="pages"/>', '<n:larva descriptor="status"/>'), $string);
		
		$this->SetText($template);
		$larva = $this->GetLarvae();
		foreach($larva as $part)
		{
			$object = null;
			switch($part->Keyword)
			{
				case 'first':
					$object = $this->First = $this->CreateFirst();
					$object->CSSClass = 'NPag';
					$object->Click = new ServerEvent($this, 'SetPage', 1);		 
					break;
				case 'last':  
					$object = $this->Last = $this->CreateLast();
					$object->CSSClass = 'NPag';
					$object->Click = new ServerEvent($this, 'SetPage', 'last');	
					break;
				case 'prev':  
					$object = $this->Prev = $this->CreatePrev();
					$object->CSSClass = 'NPag';
					$object->Click = new ServerEvent($this, 'PrevPage');	
					break;
				case 'next':  
					$object = $this->Next = $this->CreateNext();
					$object->CSSClass = 'NPag';
					$object->Click = new ServerEvent($this, 'NextPage');
					break;
				case 'pages':  $object = $this->Pages = &new Panel(0, 0, null, null);
							   $object->CSSDisplay = 'inline';
					break;
				case 'status': $object = $this->Status = $this->CreateStatus();
							   $object->CSSDisplay = 'inline';
					break;
			}
			if($object)
				$part->Morph($object);
		}
	}
	function GetTemplate()
	{
		return str_replace(array('<n:larva descriptor="first"/>', '<n:larva descriptor="last"/>', '<n:larva descriptor="prev"/>',
		'<n:larva descriptor="next"/>', '<n:larva descriptor="pages"/>', '<n:larva descriptor="status"/>'), 
		array('{first}', '{last}', '{prev}', '{next}', '{pages}', '{status}') , $this->Text);
	}
	function SetLimit($num)
	{
		$this->Limit = $num;
	}
	function GetLimit()	
	{
		if(is_array($this->Limit))
			return $this->Limit[1];
		else
			return $this->Limit;
	}
	function GetNumResults()	{return $this->NumResults;}
	function SetPage($num, $bind=true)
	{
		$count = $this->Count();
		if($num == 'last')
			$num = $count;
		if($num > 0 && $num <= $count)
		{
			if(is_array($this->CurrentOffset))
				$this->CurrentOffset[1] = ($num - 1) * $this->GetLimit();
			else
				$this->CurrentOffset = ($num - 1) * $this->GetLimit();
			$this->CurrentPage = $num;
			if($bind)
				$this->Bind();
						
			if($num === 1)
			{
				if($this->Prev)
					$this->Prev->Visible = System::Vacuous;
				if($this->First)
					$this->First->Visible = System::Vacuous;		
			}
			else
			{
				if($this->Prev && $this->Prev->Visible !== true)
					$this->Prev->Visible = true;
				if($this->First && $this->First->Visible !== true)
					$this->First->Visible = true;
			}		
			if($num == $count)
			{
				if($this->Next)
					$this->Next->Visible = System::Vacuous;
				if($this->Last)
					$this->Last->Visible = System::Vacuous;
			}
			else
			{
				if($this->Next && $this->Next->Visible !== true)
					$this->Next->Visible = true;
				if($this->Last && $this->Last->Visible !== true)
					$this->Last->Visible = true;	
			}
			if($this->Status)
				$this->Status->Text = $num . ' of ' . $count;
				
			if($this->Pages)
			{
				$pageCount = count($this->Pages->Controls);
				
				if($pageCount == 0 || $num >= $this->MaxPages || $this->Pages->Controls[0]->Text != 1)
				{
					$index = 1;
					if($pageCount != 0 && ($num >= $this->MaxPages || $this->Pages->Controls[0]->Text != 1))
					{
						$this->Pages->Controls->Clear();
						if($num >= $this->MaxPages)
							$index = ($num + 2) - $this->MaxPages;
					}
					for($i=0; $index <= $count && (!$this->MaxPages || $i<$this->MaxPages); ++$i, ++$index)
					{
						$this->Pages->Controls->Add($page = $this->CreatePage($index));
						if($this->PageClass)
							$page->CSSClass .= ' ' . $this->PageClass;
						$page->Click = new ServerEvent($this, 'SetPage', $index);
						$page->Layout = Layout::Relative;
						$page->CSSClass .= ' NPag';
					}
					
				}	
			}
			return $num;
		}
		else
			return false;
	}
	function PrevPage()
	{	
		return($this->SetPage($this->CurrentPage - 1));
	}
	function NextPage()
	{
		return($this->SetPage($this->CurrentPage + 1));
	}
	function Count()
	{
		$limit = is_array($this->Limit)?$this->Limit[1]:$this->Limit;
		if($this->NumResults && $limit)
			return (ceil((float)$this->NumResults / $limit));
		else
			return 0;
	}
	/**
	 * @ignore
	 */
	function GetCount()
	{
		return $this->Count();
	}
	/**
	 * @ignore
	 */
	public function Bind($dataSource=null, $rowCallback=null, $limit=10, $offset=0, $cache = false)
	{
		$data = null;
		if($dataSource)
		{
			$countCommand = null;
			if(is_array($dataSource))
			{
				if(isset($dataSource[0]) && ($dataSource[0] instanceof DataCommand))
				{
					$countCommand = $dataSource[0];
					if(isset($dataSource[1]) && ($dataSource[1] instanceof DataCommand))
						$bindCommand = $dataSource[1];
				}
			}
			elseif($dataSource instanceof DataCommand)
				$bindCommand = $dataSource;
			
			if($bindCommand instanceof DataCommand)
			{
				if($countCommand)
				{
					$countCommand = $countCommand;
					$hasCountCommand = true;
				}
				else
				{
					$countCommand = $bindCommand;
					$hasCountCommand = false;
				}
//				$countCommand = ($countCommand)?$countCommand:$bindCommand;
				$sql = preg_replace('/(.*?);*?\s*?\z/i', '$1', $countCommand->GetSqlStatement());
				$numRows = new DataCommand($countCommand->GetConnection(), 'SELECT count(1) FROM (' . $sql . ') as sub_query ', Data::Num);
				$numRows = $numRows->Execute();
				$numRows = $numRows->Data[0][0];
				
				if($hasCountCommand)
					$sql = preg_replace('/(.*?);*?\s*?\z/i', '$1', $bindCommand->GetSqlStatement());
				
				$this->NumResults = $numRows;
				
				if(is_array($limit) || is_array($offset))
					$sql .= ';';
				else
					$sql = 'SELECT * FROM (' . $sql . ') as sub_query ';
				
				$this->DataSource = new DataCommand($bindCommand->GetConnection(), $sql, $bindCommand->ResultType);
				$this->Limit = $limit;
				$callBack = true;
				$this->SetPage(1, false);
				$this->Visible = true;
			}
		}
		else
		{
			$callBack = false;
			$offset = $this->CurrentOffset;
			$limit = $this->Limit;
			
		}
		if($this->DataSource instanceof DataCommand)
		{
			if(!$loadIntoMemory)
			{
				if(is_array($limit) || is_array($offset))
				{
					if(is_array($limit))
						$this->DataSource->ReplaceParam($limit[0], $limit[1]);
					if(is_array($offset))
						$this->DataSource->ReplaceParam($offset[0], $offset[1]);
				}
				else
				{
					$result = preg_replace('/(.*?)\s*(?:(?:OFFSET\s*\d*)|(?:LIMIT\s*\d*)|\s)*?\s*;/i', '$1', $this->DataSource->GetSqlStatement());
					$result .= ' LIMIT ' . $limit . ' OFFSET ' . $offset . ';';
				
					$this->DataSource->SetSqlStatement($result);
				}
				if($callBack && $rowCallback instanceof ServerEvent)
				{
					if($rowCallback instanceof ServerEvent)
						$this->RowCallback = $rowCallback;
					
					$this->DataSource->Callback($this, 'AddControl');
				}
				if($this->ResultsPanel instanceof ArrayList)
					$this->ResultsPanel->Clear();
				else
					$this->ResultsPanel->Controls->Clear();
				$data = $this->DataSource->Execute();
			}
			if(is_array($offset))
			{
				$this->CurrentOffset = $offset;
				$this->CurrentOffset[1] = $this->CurrentOffset[1] + $limit[1];
			}
			else
				$this->CurrentOffset = $offset + $limit;
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
	}
	function AddControl($data)
	{
		$previousBound = Event::$BoundData;
		Event::$BoundData = $data;
		$control = $this->RowCallback->Exec();
		if($control instanceof Control || $control instanceof Component)
		{
				if($this->ResultsPanel instanceof ArrayList)
					$this->ResultsPanel->Add($control);
				else
					$this->ResultsPanel->Controls->Add($control);
		}
		else
			BloodyMurder('Attempting to add ' . get_class($control) . ' to Paginator\'s ResultsPanel. 
			Paginator can only add Controls or Components to your ResultsPanel');
		Event::$BoundData = $previousBound;		
	}
}?>