function _NGroup() 
{
	this.Elements = [];
}
_NGroup.prototype.GetSelectedIndex = function()
{
	var groupee;
	var elementsLength = this.Elements.length;
	for(var i=0; i<elementsLength; ++i)
		if((groupee = _N(this.Elements[i])) && groupee.Selected)
			return i;
	return -1;
};
_NGroup.prototype.GetSelectedElement = function()
{
	var selectedIndex = this.GetSelectedIndex();
	return selectedIndex == -1 ? null : this.Elements[selectedIndex];
};
_NGroup.prototype.Remove = function(id)
{
	var elementsLength = this.Elements.length, i;
	for(i=0; i<elementsLength; ++i)
		if(id == this.Elements[i])
		{
			this.Elements.splice(i, 1);
			return i;
		}
};