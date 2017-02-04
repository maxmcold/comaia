jQuery.gvalidation.errors = jQuery.extend(jQuery.gvalidation.errors, {
	required: "To polje ne sme biti prazno!",
	alpha: "Vpišete lahko samo črke.",
	alphanum: "Vpišete lahko samo črke in številke.",
	nodigit: "Številke niso dovoljene.",
	digit: "Prosimo vpišite samo celotna števila.",
	digitmin: "Število mora biti min %1",
	digitltd: "Vrednost mora biti med %1 in %2",
	number: "Prosimo vpišite število.",
	email: "Prosimo vpišite veljaven e-mail naslov: <br /><span>npr. vašeime@domena.com</span>",
	image : 'To polje lahko vsebuje samo slike',
	phone: "Prosimo vpišite veljavno telefonsko številko.",
	url: "Prosimo vpišite veljaven URL: <br /><span>npr. http://www.vašadomena.com</span>",
	
	confirm: "To polje se razlikuje od %1",
	differs: "Ta vrednost mora biti drugačna od %1",
	length_str: "Dolžina mora biti med %1 in %2",
	length_fix: "Dolžina mora biti točno %1 znakov",
	lengthmax: "Dolžina je lahko največ %1",
	lengthmin: "Dolžina mora biti najmanj %1",
	words_min : "To polje lahko vsebuje %1 besed, trenutno: %2 besed",
	words_range : "To polje mora vsebovati %1-%2 besed, trenutno: %3 besed",
	words_max : "To polje lahko vsebuje največ %1 besed, trenutno: %2 besed",
	checkbox: "Prosimo obkljukajte",
	group : 'Prosimo obkljukajte vsaj %1 stvar(i)',
	custom: "Prosimo izberite",
	select: "Prosimo izberite vrednost",
	select_multiple : "Prosimo izberite vsaj eno vrednost"
});