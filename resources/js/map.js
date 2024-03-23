
import { Loader } from '@googlemaps/js-api-loader';

const loader = new Loader({
	apiKey: "AIzaSyCqTypOLWYZmhGxqyZzrOzr0xCcNNNLjcU",
	version: "weekly",
	libraries: ['marker']
});

let map, google, infoWindow, addingMarkers = false;

loader.load().then(res => {
	google = res;
	console.log(google);

	map = new google.maps.Map(document.getElementById('map'), {
		center: {
			lat: 53.27640,
			lng: -3.22189
		},
		zoom: 14,
		mapId: '6c1f907fb68e659b'
	});

	infoWindow = new google.maps.InfoWindow();

	const wrap = $('<div />', {
		class: 'flex flex-col justify-center items-center rounded-sm shadow-sm bg-white mr-2'
	}),
		addBtn = createControl('far fa-location-plus border-b', 'Add marker!'),
		posBtn = createControl('far fa-circle-dot', 'Geolocation!');
	wrap.append(addBtn);
	wrap.append(posBtn);

	map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(wrap[0]);

	addBtn.on('click', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active text-violet-500').addClass('text-gray-600');
			addingMarkers = false;
		} else {
			$(this).addClass('active text-violet-500').removeClass('text-gray-600');
			addingMarkers = true;
		}
	});

	map.addListener('click', e => {
		if (!addingMarkers) return;
		placeMarker(e.latLng, map);
	});

	posBtn.on('click', function() {
		if (navigator.geolocation) {
			console.log(navigator.geolocation);
			navigator.geolocation.getCurrentPosition((position) => {
				const pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};

				map.setCenter(pos);
			}, () => {
				handleLocationError(true, infoWindow.getPosition());
			});
		} else
			handleLocationError(false, map.getCenter());
	});


}).catch(err => console.error(err));


function placeMarker(position, map) {
	const marker = new google.maps.marker.AdvancedMarkerElement({
		position,
		map
	});

	addingMarkers = false;

	map.panTo(position);
}

function createControl(icon, title) {
	const $btn = $('<button />', {
		class: icon + ' text-gray-600 text-center hover:text-violet-500 text-xl w-10 h-10 transition',
		title,
		type: 'button'
	});

	return $btn;
}

function handleLocationError(browserHasGeolocation, pos) {
	infoWindow.setPosition(pos);
	infoWindow.setContent(
		browserHasGeolocation
			? 'Error: The Geolocation service failed.'
			: 'Error: Your browser doesn\'t support geolocation.'
	);
	infoWindow.open(map);
}