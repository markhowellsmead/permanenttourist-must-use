import { RichText } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

export const TitleField = ({ classNameBase, post }) => {
    const { api } = attachment_from_ftp;
    const { id, title } = post;
    const [fieldValue, setFieldValue] = useState(title.rendered);
    // const [success, setSuccess] = useState(false);
    const [updating, setUpdating] = useState(false);

    const updateTitle = () => {
        if (!fieldValue.length) {
            return;
        }

        setUpdating(true);

        const data = {
            title: fieldValue.replace(/<\/?[^>]+(>|$)/g, ''),
        };

        fetch(`${api.root}wp/v2/media/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': api.nonce,
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                // setSuccess(true);
                setUpdating(false);
                setTimeout(() => {
                    // setSuccess(false);
                }, 5000);
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                setUpdating(false);
            });
    };

    const empty_class = !fieldValue || !fieldValue.length ? `${classNameBase}__field--empty` : '';

    return (
        <div className={`${classNameBase}__fieldwrap ${classNameBase}__fieldwrap--title`}>
            <RichText
                tagName='div'
                className={`${classNameBase}__title ${classNameBase}__field ${classNameBase}__field--title ${empty_class}`}
                value={fieldValue}
                onChange={value => {
                    setFieldValue(value);
                }}
            />
            <button
                disabled={!fieldValue.length || updating}
                className='button button-primary'
                onClick={updateTitle}
            >
                Update
            </button>
        </div>
    );
};

export const KeywordField = ({ classNameBase, post }) => {
    const { api } = attachment_from_ftp;
    const { id, meta, media_details } = post;
    const { image_meta } = media_details;
    // Assume meta.keywords is an array of keywords, fallback to empty array
    const initialKeywords = Array.isArray(image_meta?.keywords)
        ? image_meta.keywords.join(', ')
        : '';

    const [fieldValue, setFieldValue] = useState(initialKeywords);
    const [updating, setUpdating] = useState(false);

    const updateKeywords = () => {
        if (!fieldValue.length) {
            return;
        }

        setUpdating(true);

        // Split by comma, trim whitespace, filter out empty
        const keywordsArray = fieldValue
            .split(',')
            .map(k => k.trim())
            .filter(Boolean);

        const data = {
            meta: {
                keywords: keywordsArray,
            },
        };

        fetch(`${api.root}wp/v2/media/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': api.nonce,
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                setUpdating(false);
            })
            .catch(error => {
                console.error('Error updating keywords:', error);
            })
            .finally(() => {
                setUpdating(false);
            });
    };

    const empty_class = !fieldValue || !fieldValue.length ? `${classNameBase}__field--empty` : '';

    return (
        <div className={`${classNameBase}__fieldwrap ${classNameBase}__fieldwrap--keywords`}>
            <input
                type='text'
                className={`${classNameBase}__keywords ${classNameBase}__field ${classNameBase}__field--keywords ${empty_class}`}
                value={fieldValue}
                onChange={e => setFieldValue(e.target.value)}
                placeholder='Enter keywords, separated by commas'
            />
            <button
                disabled={true}
                //disabled={!fieldValue.length || updating}
                className='button button-primary'
                onClick={updateKeywords}
            >
                Update
            </button>
        </div>
    );
};

export const CreateButton = ({ classNameBase, attachment_id }) => {
    const { api } = attachment_from_ftp;
    const api_create = `${api.root}mhm/v1/photo-from-attachment/${attachment_id}`;
    const [disabled, setDisabled] = useState(false);
    const [newPost, setNewPost] = useState(null);

    const createPost = () => {
        setDisabled(true);
        fetch(api_create, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': api.nonce,
            },
        })
            .then(response => response.json())
            .then(data => {
                console.log('Post created:', data);
                setDisabled(false);
                setNewPost(data.data || false);
            })
            .catch(error => {
                console.error('Error creating post:', error);
                setDisabled(false);
            });
    };

    return (
        <>
            {newPost && (
                <div className={`${classNameBase}__new-post`}>
                    <a href={newPost.link} target='_blank' rel='noopener noreferrer'>
                        {newPost.id}
                    </a>
                </div>
            )}
            {!newPost && (
                <button
                    className={`button button-primary`}
                    onClick={createPost}
                    disabled={disabled}
                >
                    Create photo post
                </button>
            )}
        </>
    );
};
