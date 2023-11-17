import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const PostOrderControls = ({ attributes, setAttributes }) => {
    const { query: { orderBy } = {} } = attributes;
    return (
        <SelectControl
            label={__('Order By', 'permanenttourist-must-use')}
            value={orderBy}
            options={[
                {
                    label: __('Date', 'permanenttourist-must-use'),
                    value: 'date',
                },
                // {
                //     label: __('Author', 'permanenttourist-must-use'),
                //     value: 'author',
                // },
                // {
                //     label: __('Date', 'permanenttourist-must-use'),
                //     value: 'date',
                // },
                // {
                //     label: __('Last Modified Date', 'permanenttourist-must-use'),
                //     value: 'modified',
                // },
                // {
                //     label: __('Title', 'permanenttourist-must-use'),
                //     value: 'title',
                // },
                // {
                //     label: __('Meta Value', 'permanenttourist-must-use'),
                //     value: 'meta_value',
                // },
                // {
                //     label: __('Meta Value Num', 'permanenttourist-must-use'),
                //     value: 'meta_value_num',
                // },
                // {
                //     label: __('Random', 'permanenttourist-must-use'),
                //     value: 'rand',
                // },
                {
                    label: __('ID', 'permanenttourist-must-use'),
                    value: 'ID',
                },
            ]}
            onChange={newOrderBy => {
                setAttributes({
                    query: {
                        ...attributes.query,
                        orderBy: newOrderBy,
                    },
                });
            }}
        />
    );
};
