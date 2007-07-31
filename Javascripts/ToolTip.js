function TransferPaneAdd(objFrom, objTo)
{
	FromPane = document.getElementById(objFrom);
	ToPane = document.getElementById(objTo);
	while(FromPane.selectedIndex >= 0)
	{
		ToPane.options.add(new Option(FromPane.options[FromPane.selectedIndex].text, FromPane.options[FromPane.selectedIndex].value));
	    FromPane.remove(FromPane.selectedIndex);
	}
}