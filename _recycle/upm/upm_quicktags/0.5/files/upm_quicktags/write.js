// see plugin documentation before modifying

// -------------------------------------------------------------
// headings

	edButtons[edButtons.length] = new edButton('qt-h1', 'h1', '<h1>', '</h1>', 'Level 1 Heading');
	edButtons[edButtons.length] = new edButton('qt-h2', 'h2', '<h2>', '</h2>', 'Level 2 Heading');
	edButtons[edButtons.length] = new edButton('qt-h3', 'h3', '<h3>', '</h3>', 'Level 3 Heading');

	edButtons[edButtons.length] = new edButton('qt-p', 'p', '<p>', '</p>', 'Paragraph');

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// lists

	edButtons[edButtons.length] = new edButton('qt-ol', 'ol', '<ol>\n', '\</ol>', 'Numbered List');
	edButtons[edButtons.length] = new edButton('qt-ul', 'ul', '<ul>\n', '\n</ul>', 'Bullet List');
	edButtons[edButtons.length] = new edButton('qt-li', 'li', '<li>', '</li>\n', 'List Item');

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// definition lists

	edButtons[edButtons.length] = new edButton('qt-dl', 'dl', '<dl>\n', '\n</dl>', 'Definition List');
	edButtons[edButtons.length] = new edButton('qt-dt', 'dt', '<dt>', '</dt>', 'Definition Term');
	edButtons[edButtons.length] = new edButton('qt-dd', 'dd', '<dd>', '</dd>', 'Definition');

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// image

	edButtons[edButtons.length] = new edButton('qt-image', 'img', '', '', 'Image'); // special

// -------------------------------------------------------------
// link

	edButtons[edButtons.length] = new edButton('qt-link', 'link', '', '', 'Link'); // special

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// emphasize

	edButtons[edButtons.length] = new edButton('qt-em', 'em', '<em>', '</em>', 'Emphasize');
	edButtons[edButtons.length] = new edButton('qt-str', 'str', '<strong>', '</strong>', 'Strongly Emphasize');

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// quotes

	edButtons[edButtons.length] = new edButton('qt-quote', '\u201cquote\u201d', '&#8220;', '&#8221;', 'Quote');
	edButtons[edButtons.length] = new edButton('qt-bq', 'b-quote','<blockquote>\n', '\n</blockquote>', 'Blockquote');

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// code

	edButtons[edButtons.length] = new edButton('qt-code', 'code', '<code>', '</code>', 'Inline Code');
	edButtons[edButtons.length] = new edButton('qt-bc', 'b-code', '<pre><code>\n', '\n</code></pre>', 'Code Block');

// -------------------------------------------------------------
// "close tags"

	edButtons[edButtons.length] = new edButton('qt-close', 'Close All Tags', '', '', 'Close any open tags'); // special

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// dictionary lookup

	edButtons[edButtons.length] = new edButton('qt-spell', 'Dictionary', '', '', 'Lookup Word Definition'); // special

// -------------------------------------------------------------
// spacer

	edButtons[edButtons.length] = new edButton('qt-spacer'); // special

// -------------------------------------------------------------
// links in link dropdown

	edLinks[edLinks.length] = new edLink('Alex King', 'http://www.alexking.org/', '');
	edLinks[edLinks.length] = new edLink('Textpattern', 'http://textpattern.com/', '', '', 'I &#9829; Textpattern');

// -------------------------------------------------------------
// show link dropdown
// yes or no

	showLinks = 'yes';

// -------------------------------------------------------------
// have img button output xhtml
// yes or no

	xhtml = 'yes';
