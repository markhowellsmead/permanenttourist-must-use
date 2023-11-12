import { createRoot } from '@wordpress/element';
import { App } from './_app';

const buttons = document.querySelectorAll('[data-attachment-from-ftp]');

if (buttons.length) {
    buttons.forEach(button => {
        const root = createRoot(button);
        root.render(<App element={button} />);
    });
}
