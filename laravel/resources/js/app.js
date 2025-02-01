import './bootstrap';

import Chart from 'chart.js/auto';
window.Chart = Chart;

// Following lines commented out 2023.10.29 after error
// Uncaught TypeError: window.Alpine.cloneNode is not a function
// reported in Console by developer tools, in response to
// comment on https://laracasts.com/discuss/channels/livewire/livewire-3-error-alpineclonenode-is-not-a-function

//import Alpine from 'alpinejs';

//window.Alpine = Alpine;

//Alpine.start();

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Drag-and-drop sorting
import sort from '@alpinejs/sort'
Alpine.plugin(sort) 
 
Livewire.start()
