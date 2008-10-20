<?php
/**
 * Table class
 *
 * A Table is a Control for a conventional web table. It has an ArrayList of Rows, each of which has an ArrayList of
 * Columns. It is discouraged to use Tables simply for organizing your website, which is a common practice in designing
 * with HTML. Instead, you should use absolute positioning in most cases. A Table should be used when it makes sense
 * semantically to have a Table.
 * 
 * @package Controls/Core
 */
class Table extends Control
{
	/**
	 * An ArrayList of Rows
	 * @var ArrayList
	 */
	public $Rows;
	private $BuiltMatrix;
	private $ScrollLeft;
	private $ScrollTop;
	/**
	 * Constructor.
	 * Be sure to call this from the constructor of any class that extends Table
	 * @param integer $left The left coordinate of this element
	 * @param integer $top The top coordinate of this element
	 * @param integer $width The width coordinate of this element
	 * @param integer $height The height coordinate of this element
	 * @return Table
	 */
	function Table($left=0, $top=0, $width=500, $height=500)
	{
		parent::Control($left, $top, $width, $height);
		$this->Rows = new ArrayList();
		$this->Rows->ParentId = $this->Id;
	}
	// Evals don't work in encrypted kernels! Needs to be fixed! And params as a string??? Dotdotdot it!
	/**
	 * @ignore
	 */
	function BuildTable($numRows, $numCols, $typeAsString, $params='')
	{
		$this->BuiltMatrix = array(array());
		for($i = 0; $i < $numRows; ++$i)
		{
			$this->Rows->Add(new TableRow());
			for($j = 0; $j < $numCols; ++$j)
			{				
				eval('$this->Rows->Elements[$i]->Columns->Add(new TableColumn(new '.$typeAsString.'(' . $params . ')));');
				$this->BuiltMatrix[$i][$j] = &$this->Rows->Elements[$i]->Columns->Elements[$j];
				$this->BuiltMatrix[$i][$j]->Controls->Elements[0]->SetWidth($this->BuiltMatrix[$i][$j]->GetWidth());
			}
		}
		return $this->BuiltMatrix;
	}
	// Ignored until BuildTable is fixed.
	/**
	 * @ignore
	 */
	function GetBuiltMatrix()
	{
		return $this->BuiltMatrix;
	}
	/**
	 * Returns the position of the horizontal scrollbar
	 * @param integer $scrollTop
	 */
	function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}
	/**
	 * Sets the position of the horinzontal scrollbar
	 * @param integer $scrollTop
	 */
    function SetScrollLeft($scrollLeft)
    {
    	$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
        if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollLeft\'', $scrollLeft), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
        $this->ScrollLeft = $scrollLeft;
    }
    /**
	 * Returns the position of the vertical scrollbar
	 * @param integer $scrollTop
	 */
    function GetScrollTop()
    {
    	return $this->ScrollTop;
    }
    /**
	 * Sets the position of the vertical scrollbar
	 * @param integer $scrollTop
	 */
    function SetScrollTop($scrollTop)
    {
    	$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
    	if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
        $this->ScrollTop = $scrollTop;
    }
    /**
     * @ignore
     */
	function Show()
	{
		$initialProperties = parent::Show();
		$id = $this->Id;
		$initialProperties .= ",'style.overflow','auto'";
		NolohInternal::Show('DIV', $initialProperties, $this);
		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','100%','style.height','100%'";
//		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		NolohInternal::Show('TABLE', $initialProperties, $this, $id);
		$initialProperties = "'id','{$id}InnerTBody', 'style.position','relative'";
		NolohInternal::Show('TBODY', $initialProperties, $this, $id.'InnerTable');
	}
	/**
	 * @ignore
	 */
	function SearchEngineShow()
	{
		foreach($this->Rows as $row)
			$row->SearchEngineShow();
	}
}
?>