export const FeaturedImage = ({
    postData,
    resolution = 'large',
    classNameBase = '',
    className = '', // Generated class name incl. e.g. 3x2 style
}) => {
    const empty = (
        <figure
            className={`${className} ${classNameBase}__figure ${classNameBase}__figure--empty`}
        />
    );

    if (!postData?.featured_image) {
        return empty;
    }

    const { media_details } = postData.featured_image;

    if (!media_details) {
        return empty;
    }

    const src =
        media_details.sizes[resolution]?.source_url || media_details.sizes?.thumbnail?.source_url;

    if (!src) {
        return empty;
    }

    return (
        <figure className={`${className} ${classNameBase}__figure`}>
            <img
                className={`${classNameBase}__image ${classNameBase}__image--${resolution}`}
                src={src}
                alt={media_details.alt_text || postData.title.rendered}
            />
        </figure>
    );
};
