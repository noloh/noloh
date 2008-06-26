function ReadyBox(id)
{
	if(NOLOHUpload.FileUploadObjIds.length > 0)
	{
		_N(id).UploadComplete = true;
		for(var i=0; i<NOLOHUpload.FileUploadObjIds.length; ++i)
			if(_N(NOLOHUpload.FileUploadObjIds[i]).UploadComplete == false)
				return;
		PostBack(NOLOHUpload.EventType, NOLOHUpload.ID);
		NOLOHUpload = new Object();
		NOLOHUpload.FileUploadObjIds = [];
	}
}

function PostBackWithUpload(EventType, ID, FileUploadObjIds)
{
	clearInterval(_NURLCheck);
	NOLOHUpload.EventType = EventType;
	NOLOHUpload.ID = ID;
	NOLOHUpload.FileUploadObjIds = FileUploadObjIds;
	for(var i=0; i<FileUploadObjIds.length; ++i)
	{
		iFrame = _N(FileUploadObjIds[i]);
		iFrame.UploadComplete = false;
		iFrame.contentWindow.document.getElementById("frm").submit();
	}
}