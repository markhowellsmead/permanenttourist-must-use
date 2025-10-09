import './_index.scss';

(function () {
    // Only run on archive pages
    if (!document.body.classList.contains('post-type-archive-photo')) {
        return;
    }

    // Helper to get post ID from classList
    function getPostIdFromClassList(classList) {
        for (const cls of classList) {
            if (cls.startsWith('post-')) {
                return parseInt(cls.replace('post-', ''), 10);
            }
        }
        return null;
    }

    // Get nonce from a meta tag or inline script (adjust selector as needed)
    const { nonce } = wpApiSettings;
    if (!nonce) {
        console.error('Nonce not found');
        return;
    }

    // Get all entries as an array and loop in reverse
    const entries = Array.from(document.querySelectorAll('.wp-block-post.type-photo'));
    const seenSrcs = new Set();
    for (let i = entries.length - 1; i >= 0; i--) {
        const entry = entries[i];
        const img = entry.querySelector('img');
        if (img && img.src) {
            if (seenSrcs.has(img.src)) {
                entry.style.border = '2px solid red';
            }
            seenSrcs.add(img.src);
        }
    }

    // Now add the delete buttons and tooltips
    entries.forEach(entry => {
        const postId = getPostIdFromClassList(entry.classList);
        if (!postId) {
            return;
        }

        // add tooltip to each entry which contains the src of the image
        const img = entry.querySelector('img');
        if (img && img.src) {
            entry.querySelector('a').setAttribute('title', img.src);
        }

        // Create the X button
        const btn = document.createElement('button');
        btn.textContent = '×';
        btn.setAttribute('type', 'button');
        btn.setAttribute('aria-label', 'Delete post');
        btn.className = 'pt-photo-delete-btn';

        // Position the button relative to the entry
        entry.style.position = 'relative';
        entry.appendChild(btn);

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            btn.disabled = true;
            fetch(`/wp-json/wp/v2/photo/${postId}`, {
                method: 'DELETE',
                headers: {
                    'X-WP-Nonce': nonce,
                    'Content-Type': 'application/json',
                },
            })
                .then(res => {
                    if (res.ok) {
                        entry.style.opacity = '0.25';
                        btn.remove();
                    } else {
                        btn.disabled = false;
                        alert('Failed to delete post.');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    alert('Failed to delete post.');
                });
        });
    });
})();
