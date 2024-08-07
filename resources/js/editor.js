import { basicSetup, EditorView } from 'codemirror';
import { EditorState, Compartment } from '@codemirror/state';
import { html } from '@codemirror/lang-html';
import { barf } from 'thememirror';
import K from './K.js'

let language = new Compartment, tabSize = new Compartment

const onChange = EditorView.updateListener.of(v => {
	if (v.docChanged) {
		const view = v.state.doc.toString();
		$('#viewer, #content').html(view);
	}
});

let state = EditorState.create({
	extensions: [
		basicSetup,
		language.of(html()),
		tabSize.of(EditorState.tabSize.of(2)),
		barf, // Add tomorrowDracula theme to CodeMirror
		onChange,
		EditorView.lineWrapping,
	]
})

let view = new EditorView({
	state,
	parent: $('#editor')[0],
})

view.dispatch({
	changes: { from: 0, insert: $('#viewer').html() }
});

$(() => {
	// console.log(CodeMirror);

	const $title = $('#title'),
		$slug = $('#slug');

	$title.on('input', function() {
		if ($slug.hasClass('user-slug')) return;

		$slug.val(K.toSlug(($(this).val() || '').toString()));
	});

	$slug.on('keyup', function() {
		$(this).addClass('user-slug');
	});
});

// document.addEventListener("trix-before-initialize", () => {
// 	// Change Trix.config if you need
// 	// Trix.config.blockAttributes.default.tagName = 'p';
// 	console.log(Trix.config);
// });