export const Image = ({ props }) => {
    const { classNameBase, postId, pageData, imageSize, focalPoint } = props;

    if (
        postId &&
        pageData &&
        pageData?.featured_image?.media_details?.sizes[imageSize]?.source_url
    ) {
        const objectPosition = focalPoint
            ? `${focalPoint.x * 100}% ${focalPoint.y * 100}%`
            : 'center center';

        return (
            <figure className={`${classNameBase}__figure`}>
                <img
                    style={{ objectPosition }}
                    className={`${classNameBase}__image`}
                    src={pageData.featured_image.media_details.sizes[imageSize].source_url}
                    alt={pageData.featured_image.alt_text || ''}
                    width={pageData.featured_image.media_details.sizes[imageSize].width}
                    height={pageData.featured_image.media_details.sizes[imageSize].height}
                />
            </figure>
        );
    }

    return <figure className={`${classNameBase}__figure ${classNameBase}__figure--empty`} />;
};
