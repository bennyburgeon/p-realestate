// Livewire bundles and boots Alpine.js itself (via @livewireScripts), so we
// deliberately don't import/start a separate Alpine instance here — doing so
// causes "multiple instances of Alpine running" and breaks Livewire's DOM morphing.

import $ from 'jquery';

window.$ = window.jQuery = $;

// select2's UMD build exports a factory (root, jQuery) => void instead of
// attaching itself automatically — it must be invoked explicitly here.
import select2 from 'select2';
select2(window, $);

import 'select2/dist/css/select2.min.css';
