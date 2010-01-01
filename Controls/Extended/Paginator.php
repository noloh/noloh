<?php
/**
 * Paginator class
 *
 * Paginators are used to page through data and present results accordingly. Paginators are completely customizeable and can be bound to various data sources.
 * 
 * Basic example of instantiation and using a Paginator:
 * <pre>
 * //We first create a Panel to display the paged objects
 * $results = new Panel();
 * //then we construct our Paginator and pass in $results
 * $pages = new Paginator($results);
 * /*we can then Bind the Paginator to a DataCommand
 *   and set the callback for each row of data{@*}
 * $command = Data::Links->MyDB1->ExecSQL('SELECT * FROM people');
 * $pages->Bind($command, new ServerEvent($this, 'CreatePerson');
 * //Create the callback function
 * function CreatePerson()
 * {
 *     //The data for each row of data is stored in Event::$BoundData
 *     $person = Event::$BoundData;
 *     $name = new Label($person['firstname'] . ' ' . $person['lastname']);
 *     /*We must return an object for each callback. Paginator will automatically
 *       add this object to your results panel. You can return any object that extends
 *       Control{@*}
 *     return $name;
 * }
 * </pre>
 * @package Controls/Extended
 */
class Paginator extends RichMarkupRegion implements Countable
{
	const First = '{first}', Last = '{last}', Prev = '{prev}', Next = '{next}', Pages = '{pages}', Status = '{status}',
	Current = '{current}', LastPage = '{lastPage}';
	const NonEmpty = 'non-empty';
	
	private $First;
	private $Last;
	private $Prev;
	private $Next;
	private $Pages;
	private $Status;
	private $Current;
	private $LastPage;
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
	private $ShowNonEmpty;
	/**
	 * Constructor
	 * 
	 * @param Panel|ArrayList $resultsPanel The container that's populated with paged results.
	 * @param integer $left The Left coordinate of this element
	 * @param integer $top The Top coordinate of this element
	 * @param integer $width The Width dimension of this element
	 * @param integer $height The Height dimension of this element
	 */
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
	/**
	 * Sets whether the Control is Visible. Can be either a boolean value, System::Vacuous, or Paginator::NonEmpty. The difference between false and
	 * System::Vacuous only comes into play when a Layout::Web is used. Invisible Controls still take up space, whereas Vacuous
	 * Controls do not.
	 * 
	 * Paginator::NonEmpty is used for when you only want the Paginator to show with data.
	 *
	 * @param mixed $visibility
	 */
	function SetVisible($visibility)
	{
		if($visibility === Paginator::NonEmpty)
			$this->ShowNonEmpty = true;
		else
			parent::SetVisible($visibility);
	}
	/**
	 * @ignore 
	 */
	/*function GetVisible()
	{
		
	}*/
	/**
	 * Returns the Next object of the Paginator when Paginator::Next is used in Template.
	 * By default this is a Link.
	 * @return Control|Container
	 */
	function GetNext()	{return $this->Next;}
	/**
	 * Returns the Prev object of the Paginator when Paginator::Prev is used in Template.
	 * By default this is a Link.
	 * @return Control|Container
	 */
	function GetPrev()	{return $this->Prev;}
	/**
	 * Returns the Last object of the Paginator when Paginator::Last is used in Template.
	 * By default this is a Link.
	 * @return Control|Container
	 */
	function GetLast()	{return $this->Last;}
	/**
	 * Returns the First object of the Paginator when Paginator::First is used in Template.
	 * By default this is a Link.
	 * @return Control|Container
	 */
	function GetFirst()	{return $this->First;}
	/**
	 * Returns the Status object of the Paginator when Paginator::Status is used in Template.
	 * By default this is a Label.
	 * @return Control|Container
	 */
	function GetStatus(){return $this->Status;}
	/**
	 * Returns the Current object of the Paginator when Paginator::Current is used in Template.
	 * By default this is a Label.
	 * @return Control|Container
	 */
	function GetCurrent() {return $this->Current;}
	/**
	 * Returns the LastPage object of the Paginator when Paginator::LastPage is used in Template.
	 * By default this is a Label.
	 * @return Control|Container
	 */
	function GetLastPage(){return $this->LastPage;}
	/**
	 *
	 * The PageChange Event, which gets launched when the CurrentPage of the Paginator changes.
	 * @return Event
	 */
	function GetPageChange()
	{
		return parent::GetEvent('PageChange');
	}
	/**
	 * The PageChange Event, which gets launched when the CurrentPage of the Paginator changes.
	 * @param Event $event
	 */
	function SetPageChange($event)
	{
		return parent::SetEvent($event, 'PageChange');
	}
	/**
	 * Sets the maximum number of pages. Data that lives on a page greater than MaxPages will be ignored.
	 * @param object $num
	 * @return 
	 */
	function SetMaxPages($num)
	{
		$this->MaxPages = $num;
	}
	/**
	 * Returns the maximum number of pages.
	 * @return 
	 */
	function GetMaxPages()	{return $this->MaxPages;}
	/**
	 * Assigns a CSS class to the Pages element's Controls. 
	 * This is useful when your template consists of the Paginator::Pages element.
	 * 
	 * @param string $class
	 * @return 
	 */
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
	/**
	 * Returns the current page number of the Paginator
	 * @return integer
	 */
	function GetCurrentPage()	{return $this->CurrentPage;}
	/**
	 * A factory method to create the individual Page objects when Paginator::Pages is used.
	 * Extend Paginator and override for custom implementation. 
	 * @param integer $number page number
	 * @return Link
	 */
	function CreatePage($number)
	{
		return new Link('#', $number, 0, 0, null, null);
	}
	/**
	 * A factory method to create the First object Paginator::First is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Link
	 */
	function CreateFirst()
	{
		return new Link('#', '<< first', 0, 0, null, null);
	}
	/**
	 * A factory method to create the First object when Paginator::First is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Link
	 */
	function CreatePrev()
	{
		return new Link('#', '< prev', 0, 0, null, null);
	}
	/**
	 * A factory method to create the First object when Paginator::Status is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Label
	 */
	function CreateStatus()
	{
		return new Label('0 of 0', 0, 0, null, null);
	}
	/**
	 * A factory method to create the Next object when Paginator::Next is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Link
	 */
	function CreateNext()
	{
		return new Link('#', 'next >', 0, 0, null, null);
	}
	/**
	 * A factory method to create the Last object when Paginator::Last is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Link
	 */
	function CreateLast()
	{
		return new Link('#', 'last >>', 0, 0, null, null);
	}
	/**
	 * A factory method to create the Current object when Paginator::Current is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Label
	 */
	function CreateCurrent()
	{
		return new Label('#', '0', 0, 0, null, null);
	}
	/**
	 * A factory method to create the LastPage object when Paginator::LastPage is used.
	 * Extend Paginator and override for custom implementation. 
	 * @return Label
	 */
	function CreateLastPage()
	{
		return new Label('#', '0', 0, 0, null, null);
	}
	/**
	 * Assigns the container to be used for the Paginator's results.
	 * If the $resultPanel is a Panel then the result of the callback function
	 * will be added to your Panel. If the $resultPanel is an ArrayList then
	 * the Add function will be called with the result of the callback, adding
	 * the element to your ArrayList. 
	 * @param Panel|ArrayList $resultsPanel
	 * @return 
	 */
	function SetResultsPanel($resultsPanel)
	{
		if($resultsPanel instanceof Panel || $resultsPanel instanceof Container || $resultsPanel instanceof ArrayList)
			$this->ResultsPanel = $resultsPanel;
		else
			BloodyMurder('ResultsPanel must be an instance of a Panel, Container, or ArrayList, you passed in an ' . get_class($resultsPanel));
	}
	/**
	 * Assigns the template that determines what elements show in the Paginator.
	 * Possible elements are Paginator::First, Paginator::Last, Paginator::Prev, Paginator::Next,
	 * Paginator::Pages, Paginator::Status, Paginator::Current, and Paginator::LastPage.
	 * 
	 * For example, the default template is:
	 *     Paginator::First . Paginator::Prev . Paginator::Status . Paginator::Pages .
	 *     Paginator::Next . Paginator ::Last
	 * This can also be written as:
	 *     '{first} {prev} {status} {pages} {next} {last}';
	 * You can intermix other charachters in your template, and re-order any elements you like, for example:
	 * <pre>
	 *     /*This will result in a Paginator with only a prev, next, 
	 *     and status, but displayed according to the template order.{@*}
	 *     $paginator->Template = '{prev}{next} Viewing {status}'; 
	 * </pre>
	 * 
	 * Please note that text for the actual Paginator elements can be modified by accessing the object directly.
	 * See the respective element's property for more details.
	 * @param string $string
	 */
	function SetTemplate($string)
	{
		$template = str_replace(array('{first}', '{last}', '{prev}', '{next}', '{pages}', '{status}', '{current}', '{lastPage}'), 
		array('<n:larva style=descriptor="first"/>', '<n:larva descriptor="last"/>', '<n:larva descriptor="prev"/>',
		'<n:larva descriptor="next"/>', '<n:larva descriptor="pages"/>', '<n:larva descriptor="status"/>',
		'<n:larva descriptor="current"/>', '<n:larva descriptor="lastPage"/>'), $string);
		
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
				case 'current':
					$object = $this->Current = $this->CreateCurrent();
					$object->CSSClass = 'NPag';
					$object->CSSDisplay = 'inline';
					break;
				case 'lastPage':
					$object = $this->LastPage = $this->CreateLastPage();
					$object->CSSClass = 'NPag';
					$object->CSSDisplay = 'inline';
					break;
				case 'pages':  
					$object = $this->Pages = &new Panel(0, 0, null, null);
					$object->CSSDisplay = 'inline';
					break;
				case 'status': 
					$object = $this->Status = $this->CreateStatus();
					$object->CSSDisplay = 'inline';
					break;
			}
			if($object)
				$part->Morph($object);
		}
	}
	/**
	 * Returns the template that determines what elements show in the Paginator.
	 * @return string
	 */
	function GetTemplate()
	{
		return str_replace(array('<n:larva descriptor="first"/>', '<n:larva descriptor="last"/>', '<n:larva descriptor="prev"/>',
		'<n:larva descriptor="next"/>', '<n:larva descriptor="pages"/>', '<n:larva descriptor="status"/>', 
		'<n:larva descriptor="current"/>', '<n:larva descriptor="lastPage"/>'), 
		array('{first}', '{last}', '{prev}', '{next}', '{pages}', '{status}', '{current}', '{lastPage}'), $this->Text);
	}
	/**
	 * Assigns the number of elements to be displayed per page.
	 * If $num is an array then the first index corresponds to
	 * the number of the parameter of a database stored procedure,
	 * with the second index corresponding to the value.
	 * 
	 * For example:
	 * <pre>
	 * 	   //Sets a maximum of 10 elements per page
	 *     $paginator->Limit = 10;
	 * </pre>
	 * Using an Array
	 * <pre>
	 *     ...
	 *     //Assuming your command will be the Database function:
	 *     $command = Data::$Links->MyDB1->ExecFunction('sp_get_all_people', $limit, $offset);
	 *     ... 
	 *     $paginator->Limit = array(1, 10);
	 *     //Alternatively you can also use negatives
	 *     //This means to set the second to last paramater to 10
	 *     $paginator->Limit = array(-2, 10);
	 * </pre>
	 * @param integer|array $num
	 * @return 
	 */
	function SetLimit($num)
	{
		$this->Limit = $num;
	}
	/**
	 * Returns the number of elements to be displayed per page.
	 * @return integer
	 */
	function GetLimit()	
	{
		if(is_array($this->Limit))
			return $this->Limit[1];
		else
			return $this->Limit;
	}
	/**
	 * Returns the current data offset.
	 * @return integer
	 */
	function GetOffset()	
	{
		if(is_array($this->CurrentOffset))
			return $this->CurrentOffset[1];
		else
			return $this->CurrentOffset;
	}
	/**
	 * Returns the number of rows of the dataset the Paginator is bound to.
	 * @return integer
	 */
	function GetBoundCount()	{return $this->NumResults;}
	/**
	 * Sets the Page of the Paginator to a particular page number.
	 * @param integer $num Page number to change to
	 * @param boolean $bind
	 * @return 
	 */
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
			if($this->LastPage)
				$this->LastPage->Text = $count;
			if($this->Current)
				$this->Current->Text = $num;
				
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
			$pageChange = $this->GetPageChange();
			if(!$pageChange->Blank())
				$pageChange->Exec();
			return $num;
		}
		else
			return false;
	}
	/**
	 * Makes the Paginator go back one page
	 * @return 
	 */
	function PrevPage()
	{	
		return($this->SetPage($this->CurrentPage - 1));
	}
	/**
	 * Make the Paginator go forward one page
	 * @return 
	 */
	function NextPage()
	{
		return($this->SetPage($this->CurrentPage + 1));
	}
	/*
	 * Returns the total number of Pages
	 * @return integer
	 */
	function Count()
	{
		$limit = is_array($this->Limit)?$this->Limit[1]:$this->Limit;
		if($this->NumResults && $limit)
			return (ceil((float)$this->NumResults / $limit));
		else
			return 0;
	}
	/*
	 * Returns the total number of Pages
	 * @return integer
	 */
	function GetCount()
	{
		return $this->Count();
	}
	/**
	 * 
	 * @param object $dataSource [optional]
	 * @param object $rowCallback [optional]
	 * @param object $limit [optional]
	 * @param object $offset [optional]
	 * @param object $cache [optional]
	 * @return 
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
	/**
	 * @ignore
	 */
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