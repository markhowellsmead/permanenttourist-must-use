import { Spinner } from '@wordpress/components';

import classnames from 'classnames';

import { apiGet } from './_api';
import { TitleField } from './_fields';

import './index.scss';

export const App = ({ element }) => {
    const classNameBase = 'c-attachment-from-ftp';
    const { dataset } = element;
    const { api } = attachment_from_ftp;

    const { data, loading, error } = apiGet(
        `${api.root}/wp/v2/media/?per_page=100&media_type=image`,
        api.nonce
    );

    if (loading) {
        return <Spinner />;
    }

    if (!data.length) {
        return 'No images found.';
    }

    return (
        <div className={classNameBase}>
            <div className={`${classNameBase}__entries`}>
                {data.map(item => {
                    const { id, title, media_details } = item;
                    const { image_meta } = media_details;
                    const { width, height } = media_details;
                    const { photo_posts } = item.pt;

                    console.log(item);

                    const className = classnames({
                        [`${classNameBase}__entry`]: true,
                        [`${classNameBase}__entry--disabled`]: photo_posts.length,
                        [`${classNameBase}__entry--no-title`]: !title.rendered,
                    });

                    return (
                        <figure key={id} className={className}>
                            <div className={`${classNameBase}__imagewrap`}>
                                <a href={item.link} target='_blank'>
                                    <img
                                        src={
                                            media_details.sizes.medium?.source_url ||
                                            media_details.sizes.full?.source_url
                                        }
                                        width={width}
                                        height={height}
                                        alt={title.rendered}
                                    />
                                </a>
                            </div>
                            <figcaption className={`${classNameBase}__figure`}>
                                <TitleField classNameBase={classNameBase} post={item} />
                                {image_meta?.keywords && <p>{image_meta?.keywords.join(', ')}</p>}
                                <p>Connected to {photo_posts.length} photo posts</p>
                            </figcaption>
                        </figure>
                    );
                })}
            </div>
        </div>
    );
};
