export const contentStylesCalc = style => {
    const contentStyles = {};

    if (style?.spacing?.padding) {
        if (style?.spacing?.padding?.top) {
            if (style?.spacing?.padding?.top.indexOf('var:preset') !== -1) {
                const size = style.spacing.padding.top.split('|')[2];
                contentStyles['paddingTop'] = 'var(--wp--preset--spacing--' + size + ')';
            } else {
                contentStyles['paddingTop'] = style.spacing.padding.top;
            }
        }

        if (style?.spacing?.padding?.right) {
            if (style?.spacing?.padding?.right.indexOf('var:preset') !== -1) {
                const size = style.spacing.padding.right.split('|')[2];
                contentStyles['paddingRight'] = 'var(--wp--preset--spacing--' + size + ')';
            } else {
                contentStyles['paddingRight'] = style.spacing.padding.right;
            }
        }

        if (style?.spacing?.padding?.bottom) {
            if (style?.spacing?.padding?.bottom.indexOf('var:preset') !== -1) {
                const size = style.spacing.padding.bottom.split('|')[2];
                contentStyles['paddingBottom'] = 'var(--wp--preset--spacing--' + size + ')';
            } else {
                contentStyles['paddingBottom'] = style.spacing.padding.bottom;
            }
        }

        if (style?.spacing?.padding?.left) {
            if (style?.spacing?.padding?.left.indexOf('var:preset') !== -1) {
                const size = style.spacing.padding.left.split('|')[2];
                contentStyles['paddingLeft'] = 'var(--wp--preset--spacing--' + size + ')';
            } else {
                contentStyles['paddingLeft'] = style.spacing.padding.left;
            }
        }
    }

    return contentStyles;
};

export const innerStylesCalc = style => {
    const innerStyles = {};

    if (style?.spacing?.blockGap) {
        if (style.spacing.blockGap.indexOf('var:preset') !== -1) {
            const size = style.spacing.blockGap.split('|')[2];
            innerStyles['--wp--style--block-gap'] = 'var(--wp--preset--spacing--' + size + ')';
        } else {
            innerStyles['--wp--style--block-gap'] = style.spacing.blockGap;
        }
    }
    return innerStyles;
};

export const outerStylesCalc = attributes => {
    const outerStyles = {};

    if (attributes?.aspectRatioMobile) {
        if (attributes?.aspectRatioMobile === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio'] = attributes.aspectRatioMobile;
        }
    }

    if (attributes?.aspectRatioTablet) {
        if (attributes?.aspectRatioTablet === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio--tablet'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio--tablet'] = attributes.aspectRatioTablet;
        }
    }

    if (attributes?.aspectRatioLaptop) {
        if (attributes?.aspectRatioLaptop === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio--laptop'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio--laptop'] = attributes.aspectRatioLaptop;
        }
    }

    if (attributes?.aspectRatioDesktop) {
        if (attributes?.aspectRatioDesktop === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio--desktop'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio--desktop'] = attributes.aspectRatioDesktop;
        }
    }

    if (attributes?.aspectRatioLargeDesktop) {
        if (attributes?.aspectRatioLargeDesktop === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio--large-desktop'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio--large-desktop'] =
                attributes.aspectRatioLargeDesktop;
        }
    }

    if (attributes?.aspectRatioXLargeDesktop) {
        if (attributes?.aspectRatioXLargeDesktop === 'stretch') {
            outerStyles['--sht-teaser-cover-aspect-ratio--xlarge-desktop'] = 'auto';
            outerStyles['--sht-teaser-cover-height'] = '100%';
        } else {
            outerStyles['--sht-teaser-cover-aspect-ratio--xlarge-desktop'] =
                attributes.aspectRatioXLargeDesktop;
        }
    }

    return outerStyles;
};
