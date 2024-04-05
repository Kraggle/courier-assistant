import { Loader } from '@googlemaps/js-api-loader';
import { AddressFinder } from '@ideal-postcodes/address-finder';
import K, { timed } from './K.js';

const update = timed(),
	_K = window._K || {
		str: {},
		route: {}
	};

/**
 * @type {Object}
 */
const Google = {
	addingMarkers: false,
	buttons: {},
	markers: [],
	hasLoc: !!K.urlParam('lat'),
	center: {
		lat: Number(K.urlParam('lat') || 53.27640),
		lng: Number(K.urlParam('lng') || -3.22189),
	},
	hasZoom: !!K.urlParam('zoom'),
	zoom: Number(K.urlParam('zoom') || 14)
};

(async () => {
	const keys = await fetchJSON('/google'),
		loader = new Loader({
			apiKey: keys.api,
			version: "weekly",
		});

	Google.core = await loader.importLibrary('core');
	Google.maps = await loader.importLibrary('maps');
	Google.marker = await loader.importLibrary('marker');
	// Google.geocoding = await loader.importLibrary('geocoding');
	Google.places = await loader.importLibrary('places');
	Google.infos = await fetchJSON('/info');
	// console.log(Google);

	Google.map = new Google.maps.Map(document.getElementById('map'), {
		center: Google.center,
		zoom: Google.zoom,
		mapId: keys.id,
		mapTypeId: Google.maps.MapTypeId.ROADMAP,
		streetViewControl: false,
		mapTypeControl: false,
	});

	Google.infoWindow = new Google.maps.InfoWindow();

	const wrap = $('<div />', {
		class: 'flex flex-col justify-center items-center rounded-sm shadow-[#0000004d_0px_1px_4px_-1px] bg-white mr-2.5'
	});
	Google.buttons.add = createControl('fa-regular fa-location-plus text-xl border-b', 'Add marker!');
	Google.buttons.pos = createControl('fa-regular fa-circle-dot text-base', 'Geolocation!');
	wrap.append(Google.buttons.add);
	wrap.append(Google.buttons.pos);

	Google.map.controls[Google.core.ControlPosition.RIGHT_TOP].push(wrap[0]);

	Google.map.addListener('click', e => {
		if (!Google.addingMarkers) return;
		$('#infoBtn').trigger('click');
		$('#lat').val(e.latLng.lat().toFixed(5));
		$('#lng').val(e.latLng.lng().toFixed(5));
		placeMarker(e.latLng);
	});

	/* Run function on zoom change */
	Google.core.event.addListener(Google.map, 'zoom_changed', zoomChanged);

	/* Set url lat and lng on dragend */
	Google.core.event.addListener(Google.map, 'dragend', function() {
		K.addURLParam('lat', Google.map.center.lat().toFixed(5));
		K.addURLParam('lng', Google.map.center.lng().toFixed(5));
	});

	Google.infos.forEach(info => {
		placeMarker(new Google.core.LatLng(info.position.lat, info.position.lng), info, false);
	});

	Google.buttons.add.on('click', toggleMarker);
	Google.buttons.pos.on('click', toGeoLocation);
	!Google.hasLoc && toGeoLocation();
	zoomChanged();

	const user = new Google.marker.AdvancedMarkerElement({
		map: Google.map,
		content: $('<i />', {
			class: 'fa-duotone fa-circle-dot text-blue-500 text-lg'
		})[0]
	});

	trackLocation({
		onSuccess: ({
			coords: {
				latitude: lat,
				longitude: lng
			}
		}) => user.position = new Google.core.LatLng(lat, lng)
	});


	// placeMarker(new Google.core.LatLng(52.78141, -3.21912))

	// initAutoComplete();
	initAddressFinder();

	// const marker = new Google.marker.AdvancedMarkerElement({
	// 	map: Google.map,
	// 	position: Google.center,
	// 	content: linkContent(Google.center, '99 fake address')
	// });
	// marker.address = '99 fake address';
	// markerHandlers(marker);
})();

function generateLinks(coords) {
	if (K.type(coords.lat) == 'number')
		coords = new Google.core.LatLng(coords.lat, coords.lng);

	return {
		google: `https://www.google.com/maps/search/?api=1&query=${coords.lat()},${coords.lng()}`,
		apple: `https://maps.apple.com/?ll=${coords.lat()},${coords.lng()}&z=10&q=result`,
		waze: `https://www.waze.com/ul/?ll=${coords.lat()},${coords.lng()}&z=10`,
	};
}

function initAddressFinder() {
	const $search = $('#map-search'),
		$card = $('#search-card'),
		marker = new Google.marker.AdvancedMarkerElement();
	markerHandlers(marker);

	Google.map.controls[Google.core.ControlPosition.TOP_LEFT].push($card[0]);

	AddressFinder.setup({
		apiKey: 'ak_luen9mkza3ZJx0FpImxZ0BKxKqOMe',
		inputField: '#map-search',

		/**
		 * @param {Object} a
		 */
		onAddressRetrieved: a => {
			marker.map = null;
			console.log(a);

			const pos = new Google.core.LatLng(a.latitude, a.longitude),
				parts = [
					a.line_1 || '',
					a.line_2 || '',
					a.line_3 || '',
					a.post_town,
					a.postcode
				];
			let formatted = '';
			parts.forEach(p => {
				formatted += `${p ? `${formatted ? ', ' : ''}${p}` : ''}`;
			});

			$search.val(formatted);

			Google.map.setCenter(pos);
			Google.map.setZoom(17);

			marker.content = linkContent(pos, formatted);
			marker.position = pos;
			marker.map = Google.map;
			marker.address = formatted;
		},
		containerClass: 'relative',
		mainClass: 'absolute shadow-sm z-50 mt-2 w-full rounded-md border border-gray-300 bg-white overflow-hidden',
		listClass: 'max-h-40 overflow-y-auto [&>li]:cursor-pointer [&>li:not(:last-child)]:border-b [&>li]:border-gray-300 [&>li]:px-4 [&>li]:py-1 [&>li]:text-base hover:[&>li]:bg-gray-100',
		messageClass: '!cursor-default italic hover:!bg-transparent',
		toolbarClass: 'flex items-center justify-end border-t border-gray-300 bg-gray-100 px-3 py-1',
		countryToggleClass: 'hover-first flex cursor-pointer items-center gap-2 whitespace-nowrap text-sm [&>*:first-child]:block [&>*:first-child]:overflow-hidden [&>*:first-child]:transition-all [&>*:last-child]:bg-gray-100 [&>*:last-child]:text-base [&>*:last-child]:font-bold [&>*]:align-baseline',
	});

}

/**
 * Initializes the Google Maps Autocomplete and adds the search box to the map.
 */
function initAutoComplete() {
	const $search = $('#map-search'),
		$card = $('#search-card').detach(),
		autocomplete = new Google.places.Autocomplete($search[0], {
			strictBounds: false,
			fields: ['formatted_address', 'geometry', 'name']
		}),
		marker = new Google.marker.AdvancedMarkerElement();

	Google.map.controls[Google.core.ControlPosition.TOP_LEFT].push($card[0]);
	$card.removeClass('hidden');

	autocomplete.addListener('place_changed', () => {
		marker.map = null;

		const place = autocomplete.getPlace();
		if (!place.geometry || !place.geometry.location) return;

		if (place.geometry.viewport) {
			Google.map.fitBounds(place.geometry.viewport);
		} else {
			Google.map.setCenter(place.geometry.location);
			Google.map.setZoom(17);
		}

		marker.position = place.geometry.location;
		marker.map = Google.map;


		// console.log(place);
	});
}

/**
 * Fetches a JSON object from a given URL.
 * @param {string} url - The URL to fetch the JSON object from.
 * @returns {Promise<Object>} - A Promise that resolves to the JSON object.
 */
async function fetchJSON(url) {
	const res = await fetch(url);
	const json = await res.json();
	return json;
}

/**
 * Changes the visibility of markers on zoom and saves it for reload.
 */
function zoomChanged() {
	const zoom = Google.map.getZoom().toFixed(2);
	K.addURLParam('zoom', zoom);
	K.each(Google.markers, (i, marker) => marker.map = zoom >= 16 ? Google.map : null);
}

/**
 * Toggles the marker mode.
 */
function toggleMarker() {
	if (Google.addingMarkers) {
		// Disable marker mode.
		Google.map.setOptions({
			draggableCursor: ''
		});
		Google.buttons.add.removeClass('text-blue-500').addClass('text-blue-700');
		Google.addingMarkers = false;
	} else {
		// Enable marker mode.
		Google.map.setOptions({
			draggableCursor: `url(${$('#markerSrc').attr('src')}), auto`
		});
		Google.buttons.add.addClass('text-blue-500').removeClass('text-blue-700');
		Google.addingMarkers = true;
	}
}

/**
 * Move the map to the user's location.
 */
function toGeoLocation() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition((position) => {
			const pos = new Google.core.LatLng(position.coords.latitude,
				position.coords.longitude);

			Google.map.setCenter(pos);
			Google.map.setZoom(17);

		}, () => {
			handleLocationError(true, Google.infoWindow.getPosition());
		});
	} else
		handleLocationError(false, Google.map.getCenter());
}

/**
 * Creates a marker on the map with the given position and optional data.
 * 
 * @param {google.maps.LatLng} position The position of the marker.
 * @param {Object} data The data associated with the marker, if any.
 * @param {boolean} [adding=true] Whether the marker is being added via a mouse click.
 */
function placeMarker(position, data = null, adding = true) {

	const marker = new Google.marker.AdvancedMarkerElement({
		position,
		map: Google.map,
		content: buildContent(data)
	});
	marker.data = data;
	markerHandlers(marker);

	Google.markers.push(marker);

	if (adding) {
		toggleMarker();
		Google.map.panTo(position);
	}
}

/**
 * Toggles the highlighting of a marker.
 * 
 * @param marker - The marker to highlight or unhighlight.
 */
function toggleHighlight(marker) {
	if (marker.content.classList.contains("highlight")) {
		marker.content.classList.remove("highlight");
		marker.zIndex = null;
	} else {
		marker.content.classList.add("highlight");
		marker.zIndex = 1;
	}
}

function markerHandlers(marker) {

	marker.addListener('click', (e) => {
		const $src = $(e.domEvent.srcElement),
			data = marker.data || null;

		/**
		 * @type {Object}
		 */
		let modal = {};

		if ($src.hasClass('link'))
			return;

		if ($src.hasClass('edit')) {
			modal = {
				'title.text': _K.str.editLocation,
				'form.action': _K.route.infoUpdate,
				'method.value': 'PATCH',
				'id.value': data.id,
				'lat.value': data.position.lat,
				'lng.value': data.position.lng,
				'address.value': data.address,
				'name.value': data.name,
				'year.value': data.year,
				'note.value': data.note,
				'destroy.removeclass': 'hidden',
				'destroy.data': {
					'modal': {
						'id.value': data.id,
					},
				},
				'submit.text': _K.str.save
			};
			if (!K.isIn(data.creator, [_K.str.username, 'Kraig Larner'])) {
				modal['destroy.addclass'] = 'hidden';
				modal['destroy.data'] = {
					'modal': {
						'id.value': '',
					}
				};

				delete modal['destroy.removeclass'];
			}

			$('#editBtn').data('modal', modal).trigger('click');
			return;
		}

		if ($src.hasClass('name')) {
			toClipboard($src.data('name'));
			return;
		}

		if ($src.hasClass('move')) {
			if (marker.gmpDraggable) {
				marker.content.classList.remove("moving");
				marker.gmpDraggable = false;
			} else {
				marker.content.classList.add("moving");
				marker.gmpDraggable = true;
			}
		}

		if ($src.hasClass('save')) {
			modal = {
				'title.text': _K.str.addLocation,
				'form.action': _K.route.infoAdd,
				'method.value': 'PUT',
				'id.value': '',
				'lat.value': marker.position.lat,
				'lng.value': marker.position.lng,
				'address.value': marker.address,
				'name.value': '',
				'year.value': '',
				'note.value': '',
				'destroy.addclass': 'hidden',
				'submit.text': _K.str.add
			};

			$('#infoBtn').data('modal', modal).trigger('click');
			return;
		}

		if ($src.hasClass('changes')) {
			$('.changesBtn').data('modal', {
				'tbody.changes': data.changes,
			}).trigger('click');
			return;
		}

		toggleHighlight(marker);

	});

	marker.addListener('dragend', (e) => {
		const data = marker.data || null;

		if (data && marker.content.classList.contains("moving")) {
			update.run(() => {
				$.ajax({
					url: _K.route.infoLocation,
					method: 'POST',
					data: {
						id: data.id,
						lat: e.latLng.lat().toFixed(5),
						lng: e.latLng.lng().toFixed(5),
						_method: 'PATCH',
						_token: $('[name="_token"]').first().val()
					}
				}).done(function(response) {
					// console.log(response);
				});
			}, 2000);
		}
	});
}

/**
 * Copies the given text to the clipboard.
 * 
 * @param {string} text - The text to copy to the clipboard.
 */
async function toClipboard(text) {
	try {
		await navigator.clipboard.writeText(text);
		console.log('Copied to clipboard');
	} catch (err) {
		console.error('Could not copy text: ', err);
	}
}

/**
 * Makes a marker with links to other maps.
 * 
 * @param {Object} position The data associated with the marker, if any.
 */
function linkContent(position, address) {
	const $wrap = $('<div />', {
		class: 'marker font-sans'
	});

	$wrap.append($('<div />', {
		class: 'icon',
		html: $('<i />', {
			class: 'fa-solid fa-location-crosshairs'
		})
	}));

	const $detail = $('<div />', {
		class: 'details'
	}).appendTo($wrap);

	$('<div />', {
		class: 'title',
		text: _K.str.mapLinks
	}).appendTo($detail);

	$('<div />', {
		class: 'address',
		html: splitAddress(address)
	}).appendTo($detail);

	const $foot = $('<div />', {
		class: 'links btns flex justify-between items-center text-3xl gap-3'
	}).appendTo($detail);

	/**
	 * @type {Object}
	 */
	const links = generateLinks(position);
	K.each(links, (at, link) => {
		$('<a />', {
			href: link,
			target: '_blank',
			html: `<i class="link fa-brands fa-${at} cursor-pointer"></i>`
		}).appendTo($foot);
	});

	$('<i />', {
		class: 'save fa-regular fa-floppy-disk cursor-pointer text-orange-400 text-lg self-end',
	}).appendTo($foot);

	return $wrap[0];
}

function splitAddress(address) {
	if (address.length <= 30)
		return address;

	let middle = Math.floor(address.length / 2);
	const before = address.lastIndexOf(' ', middle),
		after = address.indexOf(' ', middle + 1);

	if (before == -1 || (after != -1 && middle - before >= after - middle))
		middle = after;
	else middle = before;

	return `${address.substr(0, middle)}<br>${address.substr(middle + 1)}`;
}

/**
 * Builds the content of a marker.
 * 
 * @param {Object} data The data associated with the marker, if any.
 */
function buildContent(data = null) {
	const $wrap = $('<div />', {
		class: 'marker font-sans',
		data: data
	});

	let icon = $('<i />');
	if (data && data.note && data.note.bMatch(/\b\d{4}\b/))
		icon.addClass('fa-solid fa-user');
	else
		icon.addClass('fa-solid fa-note');
	$wrap.append($('<div />', {
		class: 'icon',
		html: icon
	}));

	if (data) {
		const $detail = $('<div />', {
			class: 'details'
		}).appendTo($wrap);
		$('<div />', {
			class: 'title',
			text: _K.str.locationInformation
		}).appendTo($detail);

		if (data.address) {
			$('<div />', {
				class: 'address',
				html: splitAddress(data.address)
			}).appendTo($detail);
		}

		// if (data.name && data.year) {
		// 	const $name = $('<div />', {
		// 		class: 'name-wrap'
		// 	}).appendTo($detail);

		// 	$('<div />', {
		// 		class: 'name',
		// 		data: {
		// 			name: data.name
		// 		},
		// 		html: '<span class="pointer-events-none">' + data.name + '</span><i class="pointer-events-none fa-regular fa-copy">'
		// 	}).appendTo($name);

		// 	$('<div />', {
		// 		class: 'year',
		// 		text: data.year
		// 	}).appendTo($name);
		// }

		if (data.note) {
			$('<div />', {
				class: 'note whitespace-pre-wrap',
				text: data.note
			}).appendTo($detail);
		}

		const $foot = $('<div />', {
			class: 'footer'
		}).appendTo($detail);

		const $btns = $('<div />', {
			class: 'btns text-lg sm:text-sm flex gap-4 sm:gap-2'
		}).appendTo($foot);

		$('<i />', {
			class: 'edit fa-regular fa-edit cursor-pointer text-amber-500'
		}).appendTo($btns);

		$('<i />', {
			class: 'move fa-regular fa-arrows-up-down-left-right cursor-pointer text-emerald-500'
		}).appendTo($btns);

		if (data.changes.length) {
			$('<i />', {
				class: 'changes far fa-rotate cursor-pointer text-sky-500'
			}).appendTo($btns);
		}

		$('<div />', {
			class: 'creator',
			text: data.creator
		}).appendTo($foot);
	}

	return $wrap[0];
}

/**
 * Create a control button for the map.
 * 
 * @param {string} icon The icon to use for the button.
 * @param {string} title The title of the button.
 */
function createControl(icon, title) {
	const $btn = $('<button />', {
		class: icon + ' text-center text-gray-500 hover:text-gray-700 w-10 h-10 transition',
		title,
		type: 'button'
	});

	return $btn;
}

/**
 * Handles the error that occurs when trying to get the user's location.
 * 
 * @param {boolean} browserHasGeolocation Whether the browser supports geolocation.
 * @param {google.maps.LatLng} pos The position to show the error on the map.
 */
function handleLocationError(browserHasGeolocation, pos) {
	Google.infoWindow.setPosition(pos);
	Google.infoWindow.setContent(
		browserHasGeolocation ?
			'Error: The Geolocation service failed.' :
			'Error: Your browser doesn\'t support geolocation.'
	);
	Google.infoWindow.open(Google.map);
}

/**
 * Starts tracking the user's location.
 * 
 * @param {Object} options The options for the tracking.
 */
function trackLocation({
	onSuccess,
	onError = () => { }
}) {
	if ('geolocation' in navigator === false)
		return onError(new Error('Geolocation is not supported by this browser.'));

	return navigator.geolocation.watchPosition(onSuccess, onError);
}
