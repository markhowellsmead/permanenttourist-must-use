import { RichText } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

export const TitleField = ({ classNameBase, post }) => {
    const { api } = attachment_from_ftp;
    const { id, title } = post;
    const [fieldValue, setFieldValue] = useState(title.rendered);
    // const [success, setSuccess] = useState(false);
    const [updating, setUpdating] = useState(false);
    const [editing, setEditing] = useState(false);

    const updateTitle = () => {
        if (!fieldValue.length) {
            return;
        }

        setUpdating(true);

        const data = {
            title: fieldValue,
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
                setEditing(false);
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
            {!editing && (
                <h2
                    className={`${classNameBase}__title`}
                    dangerouslySetInnerHTML={{ __html: fieldValue || 'NO TITLE' }}
                    onClick={() => setEditing(true)}
                />
            )}
            {editing && (
                <>
                    <RichText
                        tagName='h2'
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
                    <button className='button button-secondary' onClick={() => setEditing(false)}>
                        Cancel
                    </button>
                </>
            )}
            {/* {success && (
                <div
                    className={`${classNameBase}__fieldwrap-message ${classNameBase}__fieldwrap-message--success`}
                >
                    Success!
                </div>
            )} */}
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
