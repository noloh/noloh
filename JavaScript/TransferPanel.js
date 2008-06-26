function TransferPanelAdd(objFrom, objTo)
{
	FromPanel = _N(objFrom);
	ToPanel = _N(objTo);
	while(FromPanel.selectedIndex >= 0)
	{
		ToPanel.options.add(new Option(FromPanel.options[FromPanel.selectedIndex].text, FromPanel.options[FromPanel.selectedIndex].value));
		FromPanel.remove(FromPanel.selectedIndex);
	}
	_NSave(objFrom, "_NItems", ImplodeOptions(FromPanel.options));
	_NSave(objTo, "_NItems", ImplodeOptions(ToPanel.options));
}