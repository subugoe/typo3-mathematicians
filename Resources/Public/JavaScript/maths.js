var term = "";
var refresh = false;

function ow(name) {
	var url = "?eID=mathematicians_ow";
	var param = name;
	$(".owResult").html('Loading data ...');
	$.ajax({
		url: url,
		data: {person: param},
		dataType: "html",
		timeout: 3000,
		success: function(data) {
			$(".owResult").replaceWith(data);
		},
		error: function(xhr, err, e) {
			var message;
			if (err == 'timeout') {
				message = "Sorry, database not available."
			}
			else {
				message = err;
			}
			$(".owResult").replaceWith('<span>' + message + '</span>');
		}
	});
}


function genealogy(name) {
	var param = name;
	$("#genResult").html('Loading data ...');
	$.ajax({
		url: "?eID=mathematicians_gen",
		data: {name: param},
		dataType: "html",
		timeout: 8000,
		success: function(data) {
			$("#genResult").html(data);
			return;
		},
		error: function(xhr, err, e) {
			var message;
			if (err == 'timeout') {
				message = "Sorr, database not available."
			}
			else {
				message = err;
			}
			$("#genResult").html('<span>' + message + '</span>');
			return;
		}
	});
}


function mactut(name) {
	var param = name;
	$.ajax({
		url: "?eID=mathematicians_mactut",
		data: {person: param},
		dataType: "html",
		timeout: 2000,
		success: function(data) {
			$('#mactutResult').html(data)
		},
		error: function(xhr, err, e) {
			alert(err);
			var message;
			if (err == 'timeout') {
				message = "Die Datenbank ist zur Zeit leider nicht verfügbar."
			}
			else {
				message = err;
			}
			$("#macutResult").html('<span>' + message + '</span>');
		}
	});
}


function mycarousel_itemLoadCallback(carousel, state) {
	if (!refresh) {
		if (carousel.has(carousel.first, carousel.last)) {
			return;
		}
	}
	jQuery.get(
		'?eID=mathematicians_ow',
		{
			person: term,
			first: carousel.first,
			last: carousel.last
		},
		function(xml) {
			mycarousel_itemAddCallback(carousel, carousel.first, carousel.last, xml);
		},
		'xml'
	);
	refresh = false;
};

function mycarousel_callback(carousel, state) {
	carousel.reload();
}


function mycarousel_itemAddCallback(carousel, first, last, xml) {
	// Set the size of the carousel
	carousel.size(parseInt(jQuery('total', xml).text()));
	jQuery('link', xml).each(function(i) {
		carousel.add(first + i, jQuery(this).text());
	});
};

/**
 * Item html creation helper.
 */
function mycarousel_getItemHTML(url) {
	return '<img src="' + url + '" height="110" alt="" />';
};

jQuery(document).ready(function() {

	$("#simpleSearch").submit(function(event) {
		event.preventDefault();
		$('#mycarousel').html('<ul class="owResult"></ul>');
		refresh = true;
		term = $("input[name='person']").val();
		jQuery('#mycarousel').jcarousel({
			scroll: 1,
			visible: 3,
			size: 8,
			start: 1,
			itemLoadCallback: mycarousel_itemLoadCallback
		});
		genealogy(term);
		ow(term);
	});

});
