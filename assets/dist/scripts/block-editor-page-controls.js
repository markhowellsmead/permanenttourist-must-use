(()=>{"use strict";const e=window.wp.i18n,t=window.wp.editor,n=window.wp.data,o=window.wp.plugins,i=window.wp["components/buildTypes/toggleControl"],a=["page"];(0,o.registerPlugin)("pt-must-use-page-controls",{render:()=>{const o=(0,n.useSelect)((e=>e("core/editor").getCurrentPostType()));if(console.log("postType",o),!o||(l=o,!a.includes(l)))return null;var l;const r=(0,n.useSelect)((e=>e("core/editor").getEditedPostAttribute("meta"))),{content_behind_masthead:s}=r||{},{editPost:d}=(0,n.useDispatch)("core/editor");return React.createElement(t.PluginDocumentSettingPanel,{title:(0,e._x)("Page layout options","Editor sidebar panel title","latrigg"),initialOpen:!0},React.createElement(i.ToggleControl,{label:"Content behind masthead",value:s,onChange:()=>{d({meta:{content_behind_masthead:!s}})}}))}})})();