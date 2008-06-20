function TransferPanelAdd(objFrom, objTo)
{
	FromPane = _N(objFrom);
	ToPane = _N(objTo);
	while(FromPane.selectedIndex >= 0)
	{
		AddOptionAndSave(objTo, new Option(FromPane.options[FromPane.selectedIndex].text, FromPane.options[FromPane.selectedIndex].value));
		//ToPane.options.add(new Option(FromPane.options[FromPane.selectedIndex].text, FromPane.options[FromPane.selectedIndex].value));
		RemoveOptionAndSave(objFrom, FromPane.selectedIndex);
		//FromPane.remove(FromPane.selectedIndex);
	}
}