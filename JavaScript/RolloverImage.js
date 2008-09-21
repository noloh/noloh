function _NTglRlOvrImg(id, state)
{
	var img = _N(id);
	if(img.Cur != state && (img.Selected == null || !img.Selected))
	{
		img.Cur = state;
		if(state == 'Slct')
		{
			var group = img.Group;
			if(group != null)
			{
				var prevImg = group.GetSelectedElement();
				if(prevImg != null)
				{
					prevImg = _N(prevImg);
					prevImg.src = prevImg['Out'];
					prevImg.Selected = false;
					_NSave(prevImg.id, 'Selected');
					prevImg.Cur = 'Out';
				}
			}
			_NSetProperty(id, 'Selected', true);
			if(img.Select != null)
				img.Select.call();	
		}
		img.src = img[state];
		if(img.onchange != null)
			img.onchange.call();
		
	}
	else if(state == 'Slct' && (img.Tgl))
	{
		img.src = img['Out'];
		_NSetProperty(id, 'Selected', false);
		if(img.onchange != null)
			img.onchange.call();
		img.Cur = 'Out';
	}
}