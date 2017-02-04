/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){"use strict";var i="focusmanager",u="focusmanager_handlers",r=!1,n,t;CKEDITOR.plugins.add("focusmanager",{init:function(n){n.elementMode!=CKEDITOR.ELEMENT_MODE_INLINE&&(r=!0)},afterInit:function(n){if(!r&&(CKEDITOR.focusManager.prototype.blur=function(n,t){function i(){if(this.hasFocus){this.hasFocus=!1;var n=this._.editor.container;n&&n.removeClass("cke_focus");this._.editor.fire("blur")}}if(!this._.locked&&(!t||!CKEDITOR.document.focused)){this._.timer&&clearTimeout(this._.timer);var r=CKEDITOR.focusManager._.blurDelay;n||!r?i.call(this):this._.timer=CKEDITOR.tools.setTimeout(function(){delete this._.timer;i.call(this)},r,this)}},CKEDITOR.focusManager.prototype.add=function(n,t){var r=n.getCustomData(i),e,o,f;if(!r||r!=this){r&&r.remove(n);e="focus";o="blur";t&&(CKEDITOR.env.ie?(e="focusin",o="focusout"):CKEDITOR.event.useCapture=1);f={blur:function(){!CKEDITOR.uploadInProgress&&n.equals(this.currentActive)&&this.blur(50,!0)},focus:function(){this.focus(n)}};n.on(e,f.focus,this);n.on(o,f.blur,this);t&&(CKEDITOR.event.useCapture=0);n.setCustomData(i,this);n.setCustomData(u,f)}},CKEDITOR.env.gecko)){var t,f=function(i){var r=n.editable();if(!r){t(i);return}r.attachListener(n.editable(),"mouseup",function(){CKEDITOR.document.focused=n.editable().getCustomData("type")=="title"?!1:!0})};t=function(n){n.removeListener("instanceCreated",f)};CKEDITOR.once("instanceCreated",f)}}});CKEDITOR.document.focused=!0;n="focus";t="blur";(CKEDITOR.env.ie||CKEDITOR.env.webkit&&!CKEDITOR.env.chrome)&&(n="focusin",t="focusout");document.addEventListener?(document.addEventListener(t,function(){CKEDITOR.document.focused=!1}),document.addEventListener(n,function(){CKEDITOR.document.focused=!0})):(document.attachEvent("on"+t,function(){CKEDITOR.document.focused=!1}),document.attachEvent("on"+n,function(){CKEDITOR.document.focused=!0}))})()