_N.Upload = [];
_N.Upload.FileUploadObjIds = [];
function _NRdyBox(id)
{
	if(_N.Upload.FileUploadObjIds.length > 0)
	{
		_N(id).UploadComplete = true;
		for(var i=0; i<_N.Upload.FileUploadObjIds.length; ++i)
			if(_N(_N.Upload.FileUploadObjIds[i]).UploadComplete == false)
				return;
		_NServer(_N.Upload.EventType, _N.Upload.Id, _N.Upload.event);
		_N.Upload = [];
		_N.Upload.FileUploadObjIds = [];
	}
}
function _NServerWUpl(eventType, id, fileUploadObjIds, event)
{
	_N.Upload.EventType = eventType;
	_N.Upload.Id = id;
	_N.Upload.FileUploadObjIds = fileUploadObjIds;
	_N.Upload.event = event;
	for(var i=0; i<fileUploadObjIds.length; ++i)
	{
		iFrame = _N(fileUploadObjIds[i]);
		iFrame.UploadComplete = false;
		iFrame.contentWindow.document.getElementById("frm").submit();
	}
}