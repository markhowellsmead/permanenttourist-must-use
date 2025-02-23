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
        outerStyles['--sht-teaser-cover-aspect-ratio'] = attributes.aspectRatioMobile;
    }

    if (attributes?.aspectRatioTablet) {
        outerStyles['--sht-teaser-cover-aspect-ratio--tablet'] = attributes.aspectRatioTablet;
    }

    if (attributes?.aspectRatioDesktop) {
        outerStyles['--sht-teaser-cover-aspect-ratio--desktop'] = attributes.aspectRatioDesktop;
    }

    return outerStyles;
};
