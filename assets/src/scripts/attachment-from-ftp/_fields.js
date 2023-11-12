import { RichText } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

export const TitleField = ({ classNameBase, post }) => {
    const { api } = attachment_from_ftp;
    const { id, title } = post;
    const [fieldValue, setFieldValue] = useState(title.rendered);
    const [success, setSuccess] = useState(false);
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

        console.info(`${api.root}wp/v2/media/${id}`);

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
                console.log(data);
                setSuccess(true);
                setTimeout(() => {
                    setSuccess(false);
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
                    dangerouslySetInnerHTML={{ __html: fieldValue }}
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
            {success && (
                <div
                    className={`${classNameBase}__fieldwrap-message ${classNameBase}__fieldwrap-message--success`}
                >
                    Success!
                </div>
            )}
        </div>
    );
};
