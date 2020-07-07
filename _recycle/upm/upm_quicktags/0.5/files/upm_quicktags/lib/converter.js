// much thanks to Guido <guido at linuxfocus dot org>
// http://main.linuxfocus.org/~guido/javascript/convert.html
// slightly modified

// -------------------------------------------------------------

	function initArray()
	{
		this.length = initArray.arguments.length;

		for (var i = 0; i < this.length; i++)
		{
			this[i] = initArray.arguments[i];
		}
	}

// -------------------------------------------------------------

	function toUnicode(value)
	{
		var tmpnum;
		var retval = '';
		var ConvArray = new initArray(0,1,2,3,4,5,6,7,8,9,'A','B','C','D','E','F');
		var i = 0;

		var intnum = parseInt(value, 10);

		if (isNaN(intnum))
		{
			retval = 'NaN';
		}

		else
		{
			while (intnum > 0.9)
			{
				i++;
				tmpnum = intnum;
				retval = ConvArray[tmpnum % 16] + retval;
				intnum = Math.floor(tmpnum / 16);

				if (i > 100)
				{
					// break infinite loops
					retval = 'NaN';
					break;
				}
			}
		}

		retval += '';

		while (retval.length < 4)
		{
			retval = '0' + retval;
		}

		retval = '\u005cu' + retval.toLowerCase();

		return retval;
	}

// -------------------------------------------------------------

	function toAscii(string)
	{
		return string.charCodeAt(0);
	}

// -------------------------------------------------------------

	function fromChar()
	{
		symbol = document.getElementById('symbol');
		symbol_out = document.getElementById('symbol_out');

		symbol_out.value = toUnicode(toAscii(symbol.value));
	}

// -------------------------------------------------------------

	function fromAsc()
	{
		asc = document.getElementById('asc');
		asc_out = document.getElementById('asc_out');

		asc_out.value = toUnicode(asc.value);
	}