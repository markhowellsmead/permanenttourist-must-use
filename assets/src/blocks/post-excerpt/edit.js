import { withSelect } from '@wordpress/data';
import { Component } from '@wordpress/element';

class Edit extends Component {
	render() {
		const { excerpt, className, attributes, setAttributes } = this.props;

		if (excerpt !== attributes.excerpt) {
			setAttributes({ excerpt });
		}

		return (
			<section className={className}>
				{excerpt && <p>{excerpt}</p>}
				{!excerpt && (
					<p>
						<em>This post has no excerpt.</em>
					</p>
				)}
			</section>
		);
	}
}

export default withSelect(function (select) {
	let currentExcerpt = select('core/editor').getEditedPostAttribute('excerpt');
	return {
		excerpt: currentExcerpt,
	};
})(Edit);
