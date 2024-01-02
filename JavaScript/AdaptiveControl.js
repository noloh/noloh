var AdaptiveControl = function(labelId, controlId, adaptiveLabel = true)
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
			if (adaptiveLabel)
			{
				label.addClass("AdaptiveLabelFocused");
			}
			else
			{
				label.css('visibility', 'hidden');
			}
		}
		else if (!label.attr('data-always-focused'))
		{
			if (adaptiveLabel)
			{
				label.removeClass("AdaptiveLabelFocused");
			}
			else
			{
				label.css('visibility', 'visible');
			}
		}
	};
	input.on('input change', checkClass);
};