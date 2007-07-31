function TransferPaneAdd(objFrom, objTo)
{
	FromPane = document.getElementById(objFrom);
	ToPane = document.getElementById(objTo);
	while(FromPane.selectedIndex >= 0)
	{
		AddOptionAndSave(objTo, new Option(FromPane.options[FromPane.selectedIndex].text, FromPane.options[FromPane.selectedIndex].value));
		//ToPane.options.add(new Option(FromPane.options[FromPane.selectedIndex].text, FromPane.options[FromPane.selectedIndex].value));
		RemoveOptionAndSave(objFrom, FromPane.selectedIndex);
		//FromPane.remove(FromPane.selectedIndex);
	}
}