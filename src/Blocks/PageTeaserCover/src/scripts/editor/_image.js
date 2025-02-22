export const Image = ({ props }) => {
    const { classNameBase, postId, pageData, imageSize } = props;

    if (
        postId &&
        pageData &&
        pageData?.featured_image?.media_details?.sizes[imageSize]?.source_url
    ) {
        return (
            <figure className={`${classNameBase}__figure`}>
                <img
                    className={`${classNameBase}__image`}
                    src={pageData.featured_image.media_details.sizes[imageSize].source_url}
                    alt={pageData.featured_image.alt_text || ''}
                    width={pageData.featured_image.media_details.sizes[imageSize].width}
                    height={pageData.featured_image.media_details.sizes[imageSize].height}
                />
            </figure>
        );
    }
    return <></>;
};
