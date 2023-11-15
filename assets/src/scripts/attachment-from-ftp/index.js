import { createRoot } from '@wordpress/element';
import { App } from './_app';

const element = document.querySelector('[data-attachmentfromftppublish-app]');

if (element) {
    const root = createRoot(element);
    root.render(<App element={element} />);
}
