// a list of available pre-installed buttons ("special") for your reference
// please see the documentation for details

// -------------------------------------------------------------
// use an image as your button, instead of a text label

	edImgButtons[edImgButtons.length] = new edImgButton('qt-somebutton', 'button.png', '<tag>', '</tag>', 'Button');

// -------------------------------------------------------------
// start a new button row

	edButtons[edButtons.length] = new edButton('qt-spacer-br');

// -------------------------------------------------------------
// add a space inbetween buttons

	edButtons[edButtons.length] = new edButton('qt-spacer');

// -------------------------------------------------------------
// insert a link

	edButtons[edButtons.length] = new edButton('qt-link', 'link', '', '', 'Link');

// -------------------------------------------------------------
// insert an image

	edButtons[edButtons.length] = new edButton('qt-image', 'img', '', '', 'Image');

// -------------------------------------------------------------
// a button that will close any and all open tags

	edButtons[edButtons.length] = new edButton('qt-close', 'Close All Tags', '', '', 'Close any open tags');

// -------------------------------------------------------------
// ties upm_quicktags to upm_img_popper
// you must have upm_img_popper installed and activated for this to work

	edButtons[edButtons.length] = new edButton('qt-img-popper', 'img', '', '', 'Image');

// -------------------------------------------------------------
// dictionary lookup

	edButtons[edButtons.length] = new edButton('qt-spell', 'Dictionary', '', '', 'Lookup Word Definition');