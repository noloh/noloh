function _NRlImgTgl(id, state)
{
	var img = _N(id);
	if(!img.Cur)
		img.Cur = 'Out';
	if(img.Selected && state != 'Slct' || img.Cur == state)
		return;
		
	/*if(state == 'Slct' && img.Cur != 'Slct' && img.Group)
	{
		var prevImg = img.Group.PrevSelectedElement;
		if(prevImg != null)
			_NSet(prevImg, 'Selected', false);
	}*/
	img.src = img[state];
	img.Cur = state;
	if(img.onchange)
		img.onchange();
}