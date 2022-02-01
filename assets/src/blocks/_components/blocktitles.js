/**
 * Block Title for Say Hello components
 * mark@sayhello.ch 26.8.2019
 *
 * Usage:
	<BlockTitle
		title={attributes.title}
		setAttributes={setAttributes}
		/>
 * OR:
	<BlockTitle
		tagName="h1"
		title={attributes.title}
		setAttributes={setAttributes}
		/>
 */

const { Component } = wp.element;
const { RichText } = wp.blockEditor;
const { _x } = wp.i18n;

export class BlockTitle extends Component {
	constructor(props) {
		super(...arguments);
		this.props = props;
	}

	render() {
		const { className, tagName, title, setAttributes } = this.props;

		let tag_name = tagName || 'h2';
		let class_name = className || 'c-block__title';

		return (
			<RichText
				tagName={tag_name}
				format='string'
				allowedFormats={[]}
				formattingControls={[]}
				placeholder={_x('Add a title', 'Field placeholder', 'sha')}
				className={class_name}
				multiline={false}
				value={title}
				keepPlaceholderOnFocus={true}
				onChange={value => {
					setAttributes({ title: value.replace(/<\/?[^>]+(>|$)/g, '') });
				}}
			/>
		);
	}
}

export class BlockSubtitle extends Component {
	constructor(props) {
		super(...arguments);
		this.props = props;
	}

	render() {
		const { attributes, className, tagName, setAttributes } = this.props;

		let tag_name = tagName || 'p';
		let class_name = className || 'c-block__subtitle';

		return (
			<RichText
				tagName={tag_name}
				format='string'
				allowedFormats={[]}
				formattingControls={[]}
				placeholder={_x('Add a subtitle', 'Field placeholder', 'sha')}
				className={class_name}
				multiline={false}
				value={attributes.subtitle}
				keepPlaceholderOnFocus={true}
				onChange={value => {
					setAttributes({ subtitle: value.replace(/<\/?[^>]+(>|$)/g, '') });
				}}
			/>
		);
	}
}
