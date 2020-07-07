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

	edButtons[edButtons.length] = new edButton('qt-ol', 'ol', '<ol>\n', '\n</ol>', 'Numbered List');
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
// "close tags"

	edButtons[edButtons.length] = new edButton('qt-close', 'Close All Tags', '', '', 'Close any open tags'); // special

// -------------------------------------------------------------
// show link dropdown
// yes or no

	showLinks = 'no';

// -------------------------------------------------------------
// have img button output xhtml
// yes or no

	xhtml = 'yes';

// -------------------------------------------------------------
// width to wrap the Quicktags toolbar buttons

	toolbar_width = '368px';
