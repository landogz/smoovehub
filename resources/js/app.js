import './modules/landing/landing.js';
import { initAuth } from './modules/auth/auth.js';
import { initSiteForms } from './modules/site/forms.js';
import { initPrototype } from './modules/prototype/index.js';

document.addEventListener('DOMContentLoaded', () => {
    initAuth();
    initSiteForms();
    initPrototype();
});
