import { Loader } from '@googlemaps/js-api-loader';
import { PostcodeLookup } from '@ideal-postcodes/postcode-lookup';
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
	console.log(Google);

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
		class: 'flex flex-col justify-center items-center rounded-sm shadow-sm bg-white mr-2'
	});
	Google.buttons.add = createControl('fa-regular fa-location-plus text-xl border-b', 'Add marker!');
	Google.buttons.pos = createControl('fa-regular fa-circle-dot text-base', 'Geolocation!');
	wrap.append(Google.buttons.add);
	wrap.append(Google.buttons.pos);

	Google.map.controls[Google.core.ControlPosition.RIGHT_CENTER].push(wrap[0]);

	Google.map.addListener('click', e => {
		if (!Google.addingMarkers) return;
		$('#infoBtn').trigger('click');
		$('#lat').val(e.latLng.lat());
		$('#lng').val(e.latLng.lng());
		placeMarker(e.latLng);
	});

	/* Change markers on zoom */
	Google.core.event.addListener(Google.map, 'zoom_changed', zoomChanged);

	/* Change markers on zoom */
	Google.core.event.addListener(Google.map, 'dragend', function() {
		K.addURLParam('lat', Google.map.center.lat());
		K.addURLParam('lng', Google.map.center.lng());
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
		}) => {
			user.position = {
				lat,
				lng
			};
		}
	});

	initAutoComplete();

	// placeMarker(new Google.core.LatLng(52.78141, -3.21912))

	// initPostcodeLookup();
})();

function initPostcodeLookup() {
	PostcodeLookup.setup({
		apiKey: 'ak_luen9mkza3ZJx0FpImxZ0BKxKqOMe',
		context: '#lookup-field',
		onAddressPopulated: address => {
			console.log(address);
		}
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


		console.log(place);
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
	const zoom = Google.map.getZoom();
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

	Google.markers.push(marker);

	if (adding) {
		toggleMarker();
		Google.map.panTo(position);
	}

	/**
	 * @type {Object}
	 */
	let modal = {};
	if (data) {
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
	}

	marker.addListener('click', (e) => {
		const $src = $(e.domEvent.srcElement);

		if ($src.hasClass('edit')) {
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

		if ($src.hasClass('changes')) {
			$('.changesBtn').data('modal', {
				'tbody.changes': data.changes,
			}).trigger('click');
			return;
		}

		toggleHighlight(marker);

	});

	marker.addListener('dragend', (e) => {
		if (data && marker.content.classList.contains("moving")) {
			update.run(() => {
				$.ajax({
					url: _K.route.infoLocation,
					method: 'POST',
					data: {
						id: data.id,
						lat: e.latLng.lat(),
						lng: e.latLng.lng(),
						_method: 'PATCH',
						_token: $('[name="_token"]').first().val()
					}
				}).done(function(response) {
					console.log(response);
				});
			}, 2000);
		}
	});
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
 * Builds the content of a marker.
 * 
 * @param {Object} data The data associated with the marker, if any.
 */
function buildContent(data = null) {
	const $wrap = $('<div />', {
		class: 'marker'
	});

	let icon = $('<i />');
	if (data && data.name && data.year)
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
				text: data.address
			}).appendTo($detail);
		}

		if (data.name && data.year) {
			const $name = $('<div />', {
				class: 'name-wrap'
			}).appendTo($detail);

			$('<div />', {
				class: 'name',
				data: {
					name: data.name
				},
				html: '<span class="pointer-events-none">' + data.name + '</span><i class="pointer-events-none fa-regular fa-copy">'
			}).appendTo($name);

			$('<div />', {
				class: 'year',
				text: data.year
			}).appendTo($name);
		}

		if (data.note) {
			$('<div />', {
				class: 'note',
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
