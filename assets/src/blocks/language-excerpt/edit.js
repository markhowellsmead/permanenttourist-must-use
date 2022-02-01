import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Component, Fragment } from '@wordpress/element';

import { BlockText } from '../_components/blocktext';

export default class Edit extends Component {
	render() {
		const { attributes, className, setAttributes } = this.props;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title='Language settings' initialOpen={true}>
						<SelectControl
							label='Select excerpt language'
							value={attributes.lang}
							options={[
								{ label: 'English', value: 'en' },
								{ label: 'German', value: 'de' },
							]}
							onChange={value => {
								setAttributes({ lang: value });
							}}
						/>
					</PanelBody>
				</InspectorControls>
				<section className={className}>
					<BlockText text={attributes.text} setAttributes={setAttributes} />
				</section>
			</Fragment>
		);
	}
}
