import './bootstrap';

import Alpine from 'alpinejs';

import.meta.glob('../images/**', { eager: true });

window.Alpine = Alpine;

Alpine.start();
