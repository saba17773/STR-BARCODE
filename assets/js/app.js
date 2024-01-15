// @ts-nocheck
// @ts-ignore
jQuery(document).ready(function($) {
	// @ts-ignore
	// FastClick.attach(document.body);

	// check session or cookie expire?
	// xpire();

	$("ul.dropdown-menu [data-toggle=dropdown]").on("click", function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(this)
			.parent()
			.siblings()
			.removeClass("open");
		$(this)
			.parent()
			.toggleClass("open");
	});

	$(".inputs").keydown(function(e) {
		if (e.which === 13) {
			var index = $(".inputs").index(this) + 1;
			$(".inputs")
				.eq(index)
				.focus();
		}
	});

	gojax("get", base_url + "/api/menu/generate").done(function(data) {
		var temp = "";
		$("#menu-loader").html("");
		for (var i = 0; i < data.length; i++) {
			if (data[i].Link.indexOf("?show=0") === -1) {
				temp +=
					'<li><a href="' +
					base_url +
					data[i].Link +
					'">' +
					data[i].Description +
					"</a></li>";
			}
		}
		$("#menu-loader").append(temp);
	});
});

function setInt(selector) {
	return $(selector).maskMoney({ precision: 0, thousands: "" });
}

function inputWeight(selector) {
	return $(selector).maskMoney({ precision: 1, thousands: '' });
}

function remove_from_array(array, element) {
	// if (!Array.prototype.indexOf) {
	// 	//
	// } else {
	// 	const index = array.indexOf(element);
	// 	if (index !== -1) {
	// 		array.splice(index, 1);
	// 	}
	// }
}
