import { _x } from '@wordpress/i18n';

export const App = ({ element }) => {
    const { dataset } = element;
    const { attachmentFromFtpFilename } = element.dataset;

    return <button className='button button-primary'>{attachmentFromFtpFilename}</button>;
};
