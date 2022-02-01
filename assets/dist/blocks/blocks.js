!function(){"use strict";var e=window.wp.i18n,t=window.wp.editPost,i=window.wp.components,n=window.wp.compose,o=window.wp.data,r=window.wp.plugins,a=["post"],l=function(e){return a.includes(e)},s=(0,n.compose)([(0,o.withSelect)((function(e){var t=e("core/editor").getCurrentPostType(),i={post_type:t};return l(t)&&(i.hide_title=e("core/editor").getEditedPostAttribute("meta").hide_title),i})),(0,o.withDispatch)((function(e){return{onUpdateHideTitle:function(t){e("core/editor").editPost({meta:{hide_title:t}})}}}))])((function(n){var o=n.hide_title,r=n.post_type,a=n.onUpdateHideTitle;return r&&l(r)?React.createElement(t.PluginDocumentSettingPanel,{title:(0,e._x)("Page options","Editor sidebar panel title","sht"),initialOpen:!0,icon:"invalid-name-no-icon"},React.createElement(i.ToggleControl,{label:(0,e._x)("Hide title","ToggleControl label","sha"),help:o?(0,e._x)("The title is hidden for the current post. It is recommended that you add a H1 to the content in order to better support search engines.","Warning text","sha"):"",checked:o,onChange:function(e){return a(e)}})):null}));(0,r.registerPlugin)("sht-page-controls",{render:s});var c=window.wp.blocks;(0,c.registerBlockVariation)("core/heading",{name:"sht/h1",title:"H1",attributes:{level:1}}),(0,c.registerBlockVariation)("core/heading",{name:"sht/h2",title:"H2",attributes:{level:2}}),(0,c.registerBlockVariation)("core/heading",{name:"sht/h3",title:"H3",attributes:{level:3}}),(0,c.registerBlockVariation)("core/heading",{name:"sht/h4",title:"H4",attributes:{level:4}}),(0,c.registerBlockVariation)("core/heading",{name:"sht/h5",title:"H5",attributes:{level:5}}),(0,c.registerBlockVariation)("core/heading",{name:"sht/h6",title:"H6",attributes:{level:6}})}();