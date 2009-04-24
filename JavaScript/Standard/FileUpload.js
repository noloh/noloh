_N.Uploads = [];
function _NRdyBox(id)
{
	if(_N.Uploads.length)
	{
		_N(id).UploadComplete = true;
		for(var i=0; i<_N.Uploads.length; ++i)
			if(_N(_N.Uploads[i]).UploadComplete == false)
				return;
		event = _N.UploadEvent;
		_NServer();
		delete _N.UploadEvent;
		_N.Uploads = [];
	}
}
function _NServerWUpl()
{
	_N.UploadEvent = event;
	var iFrame, i;
	for(i=0; i<_N.Uploads.length; ++i)
	{
		iFrame = _N(_N.Uploads[i]);
		iFrame.UploadComplete = false;
		iFrame.contentWindow.document.getElementById("frm").submit();
	}
}