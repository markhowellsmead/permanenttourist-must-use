(()=>{"use strict";const e=window.wp.i18n,t=window.wp.blockEditor,o=window.wp.components,n=window.wp.data,r=window.wp.editor,a=window.wp.plugins,l=["page"];(0,a.registerPlugin)("pt-must-use-page-controls",{render:()=>{const a=(0,n.useSelect)((e=>e("core/editor").getCurrentPostType()));if(!a||(s=a,!l.includes(s)))return null;var s;const c=(0,n.useSelect)((e=>e("core/editor").getEditorSettings().colors)),{content_behind_masthead:i,masthead_color:d}=(0,n.useSelect)((e=>{const t=e("core/editor").getEditedPostAttribute("meta");return console.log(t),t||{}})),{editPost:u}=(0,n.useDispatch)("core/editor");return React.createElement(r.PluginDocumentSettingPanel,{title:(0,e._x)("Masthead","Editor sidebar panel title","pt-must-use"),initialOpen:!0},React.createElement(o.ToggleControl,{label:"Content behind masthead",onChange:()=>{u({meta:{content_behind_masthead:!i}})},checked:!!i}),!!i&&React.createElement(o.BaseControl,{label:(0,e.__)("Masthead text colour","pt-must-use")},React.createElement(t.ColorPaletteControl,{colors:c,value:d,onChange:e=>{console.log("color",e),u({meta:{masthead_color:e}})}})))}})})();