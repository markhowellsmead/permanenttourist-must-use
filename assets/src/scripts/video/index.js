const createVideoPreview = (url, callback) => {
    const video = document.createElement('video');
    video.src = url;
    //video.crossOrigin = 'anonymous'; // Needed if the video is from a different origin
    video.muted = true; // Required for auto-play on some browsers

    video.addEventListener('loadeddata', event => {
        video.currentTime = 20; // Seek to 20 seconds
    });

    video.addEventListener('loadedmetadata', event => {
        const assumedFrameRate = 30;
        const duration = video.duration;
        event.target.dataset.totalFrames = Math.floor(duration * assumedFrameRate);
    });

    video.addEventListener('seeked', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        callback(canvas.toDataURL('image/jpeg')); // Returns the preview as a data URL
    });

    // Start video playback to trigger events
    video.load();
    video.play();
    // video.stop();
};

const videos = document.querySelectorAll('.wp-block-video video');

if (videos.length) {
    videos.forEach(video => {
        if (!!video.hasAttribute('poster')) {
            return;
        }

        createVideoPreview(video.src, previewImage => {
            // previewImage is a base64-encoded image. add it to the page
            video.setAttribute('poster', previewImage);

            // const image = document.createElement('img');
            // image.src = previewImage;
            // video.parentNode.insertBefore(image, video);
        });
    });
}

const playButtons = document.querySelectorAll(
    '.wp-block-video .play-button, .wp-block-image .play-button, .shp-video-play-button'
);

if (playButtons.length) {
    playButtons.forEach(button => {
        button.addEventListener('click', event => {
            if (window._paq) {
                // add click handler to ping matomo analytics with a custom event.
                const button = event.currentTarget;
                if (button.dataset?.attributeUrl) {
                    window._paq.push(['trackEvent', 'Video', 'Play', button.dataset.attributeUrl]);
                    return;
                }

                const root = button.closest('[data-attribute-url]');

                if (root && root.dataset?.attributeUrl) {
                    window._paq.push(['trackEvent', 'Video', 'Play', root.dataset.attributeUrl]);
                    return;
                }

                return;
            }
        });
    });
}
