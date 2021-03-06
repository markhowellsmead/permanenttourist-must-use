import { Component } from '@wordpress/element';

export const getLazySrcs = async function (imageId, size = 'full') {
    return new Promise((resolve, reject) => {
        wp.apiFetch({
            path: `/hello-roots/v1/lazy-image/${imageId}/?size=${size}`,
        })
            .then(resp => {
                resolve(resp);
            })
            .catch(resp => {
                console.log('reject', resp);
                reject(resp);
            });
    });
};

export class LazyImage extends Component {
    constructor(props) {
        super(...arguments);
        this.props = props;
    }

    render() {
        const { admin, background, className, focalPoint, image, objectFocalPoint } = this.props;

        if (image === undefined) {
            return <p>Kein Bild gefunden</p>;
        }

        let srcset = [];
        Object.keys(image.srcset).forEach(size => {
            srcset.push(`${image.srcset[size]} ${size}w`);
        });
        srcset = srcset.reverse().join(', ');

        let classNameOut = `${className} o-lazyimage`;

        if (background === true) {
            classNameOut = `${classNameOut} o-lazyimage--background`;
        }

        if (image.svg) {
            classNameOut = `${classNameOut} o-lazyimage--svg`;
        }

        let style_orig = {};
        let style_pre = {};

        if (!!objectFocalPoint) {
            style_orig.objectPosition = `${objectFocalPoint.x * 100}% ${objectFocalPoint.y * 100}%`;
            style_pre.objectPosition = `${objectFocalPoint.x * 100}% ${objectFocalPoint.y * 100}%`;
        }

        if (background === true) {
            style_orig.backgroundImage = "url('${image.org[0]}')";
            style_pre.backgroundImage = "url('${image.pre}')";

            if (!!focalPoint) {
                style_orig.backgroundPosition = `${focalPoint.x * 100}% ${focalPoint.y * 100}%`;
                style_pre.backgroundPosition = `${focalPoint.x * 100}% ${focalPoint.y * 100}%`;
            }

            if (admin) {
                return (
                    <figure className={classNameOut}>
                        <div
                            {...image.attributes}
                            className='o-lazyimage__image o-lazyimage__image--lazyloaded'
                            style={style_orig}
                        />
                    </figure>
                );
            }

            return (
                <figure className={classNameOut}>
                    {!image.svg && <div className='o-lazyimage__preview' style={style_pre} />}
                    <div
                        {...image.attributes}
                        className='o-lazyimage__image o-lazyimage__image--lazyload'
                        style={style_orig}
                        data-bgset={srcset}
                    />
                    <noscript>
                        <div
                            {...image.attributes}
                            className='o-lazyimage__image'
                            style={style_orig}
                        />
                    </noscript>
                </figure>
            );
        }

        if (admin) {
            return (
                <figure className={classNameOut}>
                    <img
                        {...image.attributes}
                        className='o-lazyimage__image o-lazyimage__image--lazyloaded'
                        src={image.org[0]}
                        srcset={srcset}
                        style={style_orig}
                    />
                </figure>
            );
        }

        return (
            <figure className={classNameOut}>
                {!image.svg && (
                    <img className='o-lazyimage__preview' src={image.pre} style={style_pre} />
                )}
                <img
                    {...image.attributes}
                    className='o-lazyimage__image o-lazyimage__image--lazyload'
                    data-sizes='auto'
                    src={image.pre}
                    data-srcset={srcset}
                    style={style_orig}
                />
                <noscript>
                    <img
                        {...image.attributes}
                        className='o-lazyimage__image'
                        src={image.org[0]}
                        srcset={srcset}
                        style={style_orig}
                    />
                </noscript>
            </figure>
        );
    }
}
