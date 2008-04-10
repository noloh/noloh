function _NGetGroupSelectedIndex()
{
	var elementsLength = this.Elements.length;
	for(var i=0; i<elementsLength; ++i)
		if(document.getElementById(this.Elements[i]).Selected)
			return i;
	return -1;
}

function _NGetGroupSelectedElement()
{
	var selectedIndex = this.GetSelectedIndex();
	return selectedIndex == -1 ? null : this.Elements[selectedIndex];
}

function Group()
{
	this.Elements = new Array();
	this.GetSelectedIndex = _NGetGroupSelectedIndex;
	this.GetSelectedElement = _NGetGroupSelectedElement;
}