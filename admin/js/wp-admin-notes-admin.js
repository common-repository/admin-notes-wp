(function ($) {
	'use strict';
	let bounce_effect;
	$(function () {
		if ($('body').hasClass('wpan-animate-help-tab')) {
			$('#contextual-help-link').click(function () {
				clearInterval(bounce_effect);
				$('body').removeClass('wpan-animate-help-tab');
				var data = {
					'action': 'update_seen_help_tab_animation',
					nonce: wpan_ajax_object.nonce,
				};
				// We can also pass the url value separately from ajaxurl for front end AJAX implementations
				jQuery.post(wpan_ajax_object.ajax_url, data, function (response) {
					console.log('has been animated help tab user meta updated.');
				});
			});
			bounce_effect = setInterval(function () {
				$('#contextual-help-link').effect("bounce", {direction: 'down', times: 5}, 1000);
			}, 3000);
		}

	});
})(jQuery);


document.addEventListener("DOMContentLoaded", function () {
	let wpan_toolbar_screen_id_link = document.querySelector("#wp-admin-bar-wpan-screen-id-helper a");
	let wpan_toolbar_screen_id_click_link = document.querySelector("#wp-admin-bar-wpan-screen-id-display a");

	let wpan_toolbar_screen_id = wpan_toolbar_screen_id_click_link.querySelector(".wpan_toolbar_screen_id");
	let wpan_toolbar_screen_id_click_label = wpan_toolbar_screen_id_click_link.querySelector(".wpan_toolbar_screen_id_click_label");
	wpan_toolbar_screen_id_link.addEventListener("click", function (event) {
		event.preventDefault();
	});

	wpan_toolbar_screen_id_click_link.addEventListener("click", function (event) {
		event.preventDefault();
		navigator.permissions.query({name: "clipboard-write"}).then(result => {
			if (result.state == "granted" || result.state == "prompt") {
				/* write to the clipboard now */
				navigator.clipboard.writeText(wpan_toolbar_screen_id.innerHTML).then(function () {
					wpan_toolbar_screen_id_click_label.innerHTML = wpan_labels.copied;
					setTimeout(function () {
						wpan_toolbar_screen_id_click_label.innerHTML = wpan_labels.click_to_copy;
					}, 1000);
				}, function () {
					/* clipboard write failed */
				});
			}
		});

	});

	let wpan_help_tab_links = document.querySelectorAll(".contextual-help-tabs  > ul > li > a");
	wpan_help_tab_links.forEach(item => {

		item.addEventListener('click', event => {
			let panel_id = event.target.getAttribute('href');
			let elements = document.querySelectorAll(panel_id + " iframe" + ',' + panel_id + " img");
			wpan_setSrcAttributeFromData(elements);

		});
	});

	let contextual_help_link = document.querySelector("#contextual-help-link");
	contextual_help_link.addEventListener('click', event => {
		let elements = document.querySelectorAll(".help-tab-content.active iframe" + ',' + ".help-tab-content.active img");
		wpan_setSrcAttributeFromData(elements);
	});
});

function wpan_setSrcAttributeFromData(elements) {
	elements.forEach(element => {
		wpan_setElementSrc(element);
	});
}

function wpan_setElementSrc(element) {
	if (!element.classList.contains("wpan-is-lazy-loaded")) {
		let data_src = element.dataset.src;
		element.setAttribute("src", data_src);
		element.classList.add("wpan-is-lazy-loaded");
	}
}


