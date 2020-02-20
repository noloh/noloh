var AdaptiveControl = function(labelId, controlId)
{
	var label = $('#' + labelId),
		control = $('#' + controlId),
		input = control.is('textarea') ?
			control :
			control.find('input');

	label.css('width', '').css('height', '');

	var checkClass = function(e)
	{
		var el = $(e.target);
		if ((el.val() || el.val() === 0) && !(el.val() == -1 && el.attr('data-role') == 'combobox'))
		{
			label.addClass("AdaptiveLabelFocused");
		}
		else if (!label.attr('data-always-focused'))
		{
			label.removeClass("AdaptiveLabelFocused");
		}
	};
	input.on('input change', checkClass);
};