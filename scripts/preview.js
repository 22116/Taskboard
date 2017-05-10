$(window).load(function ()
{
	$('#preview').hide();
	$('input[value="Preview"]').click(function ()
	{
		$('.wrapper').append('<div id="bgGray"></div>');
		$('#preview .caption > h3:nth-of-type(1)').text($('input[name="name"]').val());
		$('#preview .caption > h3:nth-of-type(2)').text($('input[name="mail"]').val());
		$('#preview .caption > p').text($('textarea[name="content"]').val());
		$('#preview').show();
	});

	$('#preview input[type="button"]').click(function ()
	{
		$('#preview').hide();
		$('#bgGray').remove();
	});

	function handleFileSelect(evt)
	{
		var file = evt.target.files;
		var f = file[0];

		if (!f.type.match('image.*'))
		{
			alert("Image only please....");
		}
		var reader = new FileReader();

		reader.onload = (function(theFile)
		{
			return function(e)
			{
				var span = document.createElement('span');
				span.innerHTML = ['<img style="width:320px;height:240px" class="thumb" title="', escape(theFile.name), '" src="', e.target.result, '" />'].join('');
				document.getElementById('output').insertBefore(span, null);
			};
		})(f);

		reader.readAsDataURL(f);
	}

	document.getElementById('file').addEventListener('change', handleFileSelect, false);
});
