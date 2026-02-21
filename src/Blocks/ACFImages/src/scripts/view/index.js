const videos = document.querySelectorAll('.wp-block-sht-primary-media__figure--video');

if (videos.length) {
    const clickHandler = event => {
        const button = event.currentTarget,
            figure = button.closest('.wp-block-sht-primary-media__figure'),
            template = figure.parentNode.querySelector('template');

        const iframe = template.content.querySelector('iframe');

        // add autoplay and no cookie to the iframe src
        let iframeSrc = iframe.getAttribute('src') + '&rel=0&showinfo=0&autoplay=1';
        iframeSrc = iframeSrc.replace('www.youtube.com', 'www.youtube-nocookie.com');

        // set the new iframe src including autoplay true.
        iframe.setAttribute('src', iframeSrc);
        iframe.setAttribute('allow', 'autoplay');

        // remove all children of the figure
        while (figure.firstChild) {
            figure.removeChild(figure.firstChild);
        }

        // append the iframe to the figure
        figure.appendChild(iframe);
    };

    videos.forEach(video => {
        const button = video.querySelector('.shp-video-play-button');
        if (button) {
            button.addEventListener('click', clickHandler);
        }
    });
}
