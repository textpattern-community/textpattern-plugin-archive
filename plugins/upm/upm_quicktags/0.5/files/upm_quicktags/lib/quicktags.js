// Originally,
// JS QuickTags version 1.1
// Copyright 2002-2004 Alex King
// http://alexking.org/

// Licensed under the LGPL license
// http://gnu.org/copyleft/lesser.html

// adjustments made by
// Patrick Woods <http://www.hakjoon.com/>
// Mary <http://utterplush.com/>

// -------------------------------------------------------------

	var edImgButtons = new Array();
	var edButtons = new Array();
	var edLinks = new Array();
	var edOpenTags = new Array();

	var xhtml = 'yes';
	var showLinks = 'yes';

// -------------------------------------------------------------

	function upm_quicktags_toolbar(area)
	{
		if (!document.getElementById || !document.getElementsByTagName)
		{
			return;
		}

		var toolbar = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'div') :
			document.createElement('div');

		toolbar.setAttribute('id', 'upm_quicktags');

		area.parentNode.insertBefore(toolbar, area);

		for (var i = 0; i < edImgButtons.length; i++)
		{
			var thisImgButton = edShowImgButton(edImgButtons[i]);
			toolbar.appendChild(thisImgButton);
		}

		for (var i = 0; i < edButtons.length; i++)
		{
			var thisButton = edShowButton(edButtons[i]);
			toolbar.appendChild(thisButton);
		}

		if (showLinks == 'yes')
		{
			var linkList = edShowLinks();
			toolbar.appendChild(linkList);
		}
	}

// -------------------------------------------------------------

	function edImgButton(id, src, tagStart, tagEnd, tooltip)
	{
		// used to name the toolbar button
		this.id = id;

		// image to use on button
		this.src = src;

		// open tag
		this.tagStart = tagStart;

		// close tag
		this.tagEnd = tagEnd;

		// tooltip text
		this.tooltip = tooltip;
	}

// -------------------------------------------------------------

	function edButton(id, label, tagStart, tagEnd, tooltip)
	{
		// used to name the toolbar button
		this.id = id;

		// label on button
		this.label = label;

		// open tag
		this.tagStart = tagStart;

		// close tag
		this.tagEnd = tagEnd;

		// tooltip text
		this.tooltip = tooltip;
	}

// -------------------------------------------------------------

	function edLink(text, url, target, rel, tooltip)
	{
		// link text
		this.text = text;

		// link address
		this.url = url;

		// link target window
		this.target = target;

		// link rel
		this.rel = rel;

		// tooltip text
		this.tooltip = tooltip;
	}

// -------------------------------------------------------------

	function edShowImgButton(button)
	{
		if (button.id == 'qt-spacer')
		{
			var theButton = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'span') :
			document.createElement('span');

			theButton.setAttribute('class', 'qt-spacer');
			theButton.setAttribute('className', 'qt-spacer'); // stupid ie
			theButton.appendChild(document.createTextNode(' '));

			return theButton;
		}

		var theButton = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'input') :
			document.createElement('input');

		theButton.setAttribute('type', 'image');
		theButton.setAttribute('src', 'upm_quicktags/img/' + button.src);
		theButton.setAttribute('id', button.id);
		theButton.setAttribute('class', 'qt-button');
		theButton.setAttribute('className', 'qt-button');

		if (button.tooltip)
		{
			theButton.setAttribute('title', button.tooltip);
		}

		theButton.tagStart = button.tagStart;
		theButton.tagEnd = button.tagEnd;
		theButton.open = button.open;

		if (theButton.getAttribute('id') == 'qt-img-popper')
		{
			var w = 650;
			var h = 450;

			var t = (screen.height) ? (screen.height - h) / 2 : 0;
			var l = (screen.width) ? (screen.width - w) / 2 : 0;

			theButton.onclick = function ()
			{
				var upm_img_popper = window.open('index.php?event=upm_img_popper&bm=true', 'upm_img_popper', 'top='+t+',left='+l+',width='+w+',height='+h+',toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,copyhistory=no,resizable=yes');
				upm_img_popper.focus();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-image')
		{
			theButton.onclick = function ()
			{
				edInsertImage();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-link')
		{
			theButton.onclick = function ()
			{
				edInsertLink(this);
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-close')
		{
			theButton.onclick = function ()
			{
				edCloseAllTags();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-spell')
		{
			theButton.onclick = function ()
			{
				edSpell();
				return false;
			};
		}

		else
		{
			theButton.onclick = function ()
			{
				edInsertTag(this);
				return false;
			};
		}

		return theButton;
	}

// -------------------------------------------------------------

	function edShowButton(button)
	{
		if (button.id == 'qt-spacer')
		{
			var theButton = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'span') :
			document.createElement('span');

			theButton.setAttribute('class', 'qt-spacer');
			theButton.setAttribute('className', 'qt-spacer');
			theButton.appendChild(document.createTextNode(' '));

			return theButton;
		}

		if (button.id == 'qt-spacer-br')
		{
			var theButton = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'br') :
			document.createElement('br');

			return theButton;
		}

		var theButton = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'input') :
			document.createElement('input');

		theButton.setAttribute('type', 'button');
		theButton.setAttribute('id', button.id);
		theButton.setAttribute('class', 'qt-button');
		theButton.setAttribute('className', 'qt-button');

		if (button.tooltip)
		{
			theButton.setAttribute('title', button.tooltip);
		}

		theButton.setAttribute('value', button.label);

		theButton.tagStart = button.tagStart;
		theButton.tagEnd = button.tagEnd;
		theButton.open = button.open;

		if (theButton.getAttribute('id') == 'qt-img-popper')
		{
			var w = 650;
			var h = 450;

			var t = (screen.height) ? (screen.height - h) / 2 : 0;
			var l = (screen.width) ? (screen.width - w) / 2 : 0;

			theButton.onclick = function ()
			{
				var upm_img_popper = window.open('index.php?event=upm_img_popper&bm=true', 'upm_img_popper', 'top='+t+',left='+l+',width='+w+',height='+h+',toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,copyhistory=no,resizable=yes');
				upm_img_popper.focus();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-image')
		{
			theButton.onclick = function ()
			{
				edInsertImage();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-link')
		{
			theButton.onclick = function ()
			{
				edInsertLink(this);
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-close')
		{
			theButton.onclick = function ()
			{
				edCloseAllTags();
				return false;
			};
		}

		else if (theButton.getAttribute('id') == 'qt-spell')
		{
			theButton.onclick = function ()
			{
				edSpell();
				return false;
			};
		}

		else
		{
			theButton.onclick = function ()
			{
				edInsertTag(this);
				return false;
			};
		}

		return theButton;
	}

// -------------------------------------------------------------

	function edShowLinks()
	{
		var sel = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'select') :
			document.createElement('select');

		sel.setAttribute('class', 'qt-links');
		sel.setAttribute('className', 'qt-links');

		sel.onchange = function ()
		{
			edQuickLink(this.options[this.selectedIndex].value, this);
		};

		var opt = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'option') :
			document.createElement('option');

		opt.setAttribute('value', -1);
		opt.setAttribute('selected', 'selected');

		opt.appendChild(document.createTextNode('(Quick Links)'));

		sel.appendChild(opt);

		for (var i = 0; i < edLinks.length; i++)
		{
			var thisOpt = (document.createElementNS) ?
			document.createElementNS('http://www.w3.org/1999/xhtml', 'option') :
			document.createElement('option');

			thisOpt.setAttribute('value', i);

			if (edLinks[i].tooltip)
			{
				thisOpt.setAttribute('title', edLinks[i].tooltip);
			}

			thisOpt.appendChild(document.createTextNode(edLinks[i].text));

			sel.appendChild(thisOpt);
		}

		return sel;
	}

// -------------------------------------------------------------

	function edAddTag(button)
	{
		if (button.tagEnd != '')
		{
			edOpenTags[edOpenTags.length] = button;
			button.value = '/' + button.value;
		}
	}

// -------------------------------------------------------------

	function edRemoveTag(button)
	{
		for (i = 0; i < edOpenTags.length; i++)
		{
			if (edOpenTags[i] == button)
			{
				edOpenTags.splice(i, 1);
				button.value = button.value.replace('/', '');
			}
		}
	}

// -------------------------------------------------------------

	function edCheckOpenTags(button)
	{
		var tag = 0;

		for (var i = 0; i < edOpenTags.length; i++)
		{
			if (edOpenTags[i] == button)
			{
				tag++;
			}
		}

		if (tag > 0)
		{
			return true;
		}

		else
		{
			return false;
		}
	}

// -------------------------------------------------------------

	function edCloseAllTags()
	{
		var count = edOpenTags.length;

		for (var o = 0; o < count; o++)
		{
			edInsertTag(edOpenTags[edOpenTags.length - 1]);
		}
	}

// -------------------------------------------------------------

	function edQuickLink(i, thisSelect)
	{
		if (i > -1)
		{
			var tempStr = '<a';

			if (edLinks[i].rel)
			{
				tempStr += ' rel="' + edLinks[i].rel + '"';
			}

			tempStr += ' href="' + edLinks[i].url + '"';

			if (edLinks[i].target)
			{
				tempStr += ' target="' + edLinks[i].target + '"';
			}

			if (edLinks[i].tooltip)
			{
				tempStr += ' title="' + edLinks[i].tooltip + '"';
			}

			tempStr += '>' + edLinks[i].text + '</a>';

			thisSelect.selectedIndex = 0;
			edInsertContent(tempStr);
		}

		else
		{
			thisSelect.selectedIndex = 0;
		}
	}

// -------------------------------------------------------------

	function edSpell()
	{
		var word = '';
		var myField = document.getElementById(current);


		if (document.selection)
		{
			myField.focus();

			var sel = document.selection.createRange();

			if (sel.text.length > 0)
			{
				word = sel.text;
			}
		}

		else if (myField.selectionStart || myField.selectionStart == '0')
		{
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;

			if (startPos != endPos)
			{
				word = myField.value.substring(startPos, endPos);
			}
		}

		if (word == '')
		{
			word = prompt('Dictionary:', '');
		}

		if (word != '')
		{
			window.open('http://dictionary.reference.com/search?q=' + escape(word));
		}
	}

// -------------------------------------------------------------

	function edInsertTag(button)
	{
		var myField = document.getElementById(current);

		// IE support
		if (document.selection)
		{
			myField.focus();

			var sel = document.selection.createRange();

			if (sel.text.length > 0)
			{
				sel.text = button.tagStart + sel.text + button.tagEnd;
			}

			else
			{
				if (!edCheckOpenTags(button) || button.tagEnd == '')
				{
					sel.text = button.tagStart;
					edAddTag(button);
				}

				else
				{
					sel.text = button.tagEnd;
					edRemoveTag(button);
				}
			}

			myField.focus();
		}

		// Mozilla/Netscape support
		else if (myField.selectionStart || myField.selectionStart == '0')
		{
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var cursorPos = endPos;
			var scrollTop = myField.scrollTop;

			if (startPos != endPos)
			{
				myField.value = myField.value.substring(0, startPos)
					+ button.tagStart + myField.value.substring(startPos, endPos) + button.tagEnd
					+ myField.value.substring(endPos, myField.value.length);

				cursorPos += button.tagStart.length + button.tagEnd.length;
			}

			else
			{
				if (!edCheckOpenTags(button) || button.tagEnd == '')
				{
					myField.value = myField.value.substring(0, startPos) + button.tagStart + myField.value.substring(endPos, myField.value.length);
					edAddTag(button);
					cursorPos = startPos + button.tagStart.length;
				}

				else
				{
					myField.value = myField.value.substring(0, startPos) + button.tagEnd + myField.value.substring(endPos, myField.value.length);
					edRemoveTag(button);
					cursorPos = startPos + button.tagEnd.length;
				}
			}

			myField.focus();
			myField.selectionStart = cursorPos;
			myField.selectionEnd = cursorPos;
			myField.scrollTop = scrollTop;
		}

		else
		{
			if (!edCheckOpenTags(button) || button.tagEnd == '')
			{
				myField.value += button.tagStart;
				edAddTag(button);
			}

			else
			{
				myField.value += button.tagEnd;
				edRemoveTag(button);
			}

			myField.focus();
		}
	}

// -------------------------------------------------------------

	function edInsertContent(myValue)
	{
		var myField = document.getElementById(current);

		// IE support
		if (document.selection)
		{
			myField.focus();

			var sel = document.selection.createRange();
			sel.text = myValue;

			myField.focus();
		}

		// Mozilla/Netscape support
		else if (myField.selectionStart || myField.selectionStart == '0')
		{
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;

			myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);

			myField.focus();
			myField.selectionStart = startPos + myValue.length;
			myField.selectionEnd = startPos + myValue.length;
		}

		else
		{
			myField.value += myValue;
			myField.focus();
		}
	}

// -------------------------------------------------------------

	function edInsertLink(i)
	{
		if (!edCheckOpenTags(i))
		{
			var url = prompt('Enter the URL', 'http://');

			if (url)
			{
				i.tagStart = '<a';

				var rel = prompt('Link relationship (rel attribute)','');

				if (rel)
				{
					i.tagStart += ' rel="' + rel + '"';
				}

				if (url)
				{
					i.tagStart += ' href="' + url + '"';
				}

				var tooltip = prompt('Tooltip','');

				if (tooltip)
				{
					i.tagStart += ' title="' + tooltip + '"';
				}

				var target = prompt('Target','');

				if (target)
				{
					i.tagStart += ' target="' + target + '"';
				}

				i.tagStart += '>';

				i.tagEnd = '</a>';
			}
		}

		edInsertTag(i);
	}

// -------------------------------------------------------------

	function edInsertImage()
	{
		var img = prompt('Enter the URL of the image:','http://');

		if (img)
		{
			img = '<img src="' + img + '"';

			var width = prompt('Image width:','');

			if (width)
			{
				img += ' width="' + width + '"';
			}

			var height = prompt('Image height:','');

			if (height)
			{
				img += ' height="' + height + '"';
			}

			var alt = prompt('Alternate text for non-visual browsers:','');

			if (alt)
			{
				img += ' alt="' + alt + '"';
			}

			var title = prompt('Image Tooltip:','');

			if (title)
			{
				img += ' title="' + title + '"';
			}

			if (xhtml == 'yes')
			{
				img += ' />';
			}

			else
			{
				myValue += '>';
			}

			edInsertContent(img);
		}
	}

// -------------------------------------------------------------
// just in case you're using IE 5
// Adds and/or removes elements from an array
// Cezary Tomczak www.gosu.pl

	if (!Array.prototype.splice)
	{
		Array.prototype.splice = function(index, howMany)
		{
			elements = [], removed = [], i;

			for (i = 2; i < arguments.length; ++i)
			{
				elements.push(arguments[i]);
			}

			for (i = index; (i < index + howMany) && (i < this.length); ++i)
			{
				removed.push(this[i]);
			}

			for (i = index + howMany; i < this.length; ++i)
			{
				this[i - howMany] = this[i];
			}

			this.length -= removed.length;

			for (i = this.length + elements.length - 1; i >= index + elements.length; --i)
			{
				this[i] = this[i - elements.length];
			}

			for (i = 0; i < elements.length; ++i)
			{
				this[index + i] = elements[i];
			}

			return removed;
		};
	}