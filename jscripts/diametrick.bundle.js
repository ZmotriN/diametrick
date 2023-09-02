const ready=e=>{document.addEventListener("DOMContentLoaded",(t=>{e(t)}))},bind=(e,t,n)=>{"string"==typeof e?document.querySelectorAll(e).forEach((e=>{e.addEventListener(t,n)})):e.addEventListener(t,n)},query=(e,t=!1)=>t?document.querySelectorAll(e):document.querySelector(e),click=(e,t)=>{"string"==typeof e&&(e=query(e)),e.addEventListener("click",t)},redirect=(e="")=>{document.location.href=root+e},redirectReferer=()=>{let e=root;const t=new URL(document.baseURI),n=new URL(document.referrer,document.baseURI);n.hostname==t.hostname&&n.pathname!=t.pathname&&(e=n.href),document.location.href=e},striptags=e=>e.replace(/(<([^>]+)>)/gi,""),count=e=>e.filter((function(e){return!0})).length,lowslug=e=>e.trim().normalize("NFD").replace(/[\u0300-\u036f]/g,"").toLowerCase().replace(/[^a-z0-9\s\-]/g,"").replace(/[\s\t]+/g,""),copyToClipboard=async e=>{if(navigator.clipboard&&window.isSecureContext)await navigator.clipboard.writeText(e);else{const t=document.createElement("textarea");t.value=e,t.style.position="absolute",t.style.left="-999999px",document.body.prepend(t),t.select();try{document.execCommand("copy")}catch(e){console.error(e)}finally{t.remove()}}},forceKeyPressUppercase=e=>{let t=e.target,n=e.keyCode;if(n>=97&&n<=122&&!e.ctrlKey&&!e.metaKey&&!e.altKey){let r=n-32,o=t.selectionStart,c=t.selectionEnd;t.value=t.value.substring(0,o)+String.fromCharCode(r)+t.value.substring(c),t.setSelectionRange(o+1,o+1),e.preventDefault()}},decodeEntities=function(){let e=document.createElement("div"),t=/&(?:#x[a-f0-9]+|#[0-9]+|[a-z0-9]+);?/gi;return function(n){return n=n.replace(t,(function(t){return e.innerHTML=t,e.textContent})),e.textContent="",n}}(),isInViewport=(e,t=!1)=>{const{top:n,left:r,bottom:o,right:c}=e.getBoundingClientRect(),{innerHeight:a,innerWidth:s}=window;return t?(n>0&&n<a||o>0&&o<a||n<0&&o>a)&&(r>0&&r<s||c>0&&c<s||r<0&&c>s):n>=0&&r>=0&&o<=a&&c<=s},post=(e,t=!1)=>{let n=new URL(document.baseURI).pathname;return fetch(n,{method:"POST",body:JSON.stringify(e)}).then((e=>e.text())).then((e=>{t&&console.log(e);let n=JSON.parse(e);return null==n.success?{success:!1,errmsg:"Réponse du serveur invalide."}:n})).catch((e=>({success:!1,errmsg:"Format de réponse du serveur invalide."})))},geturl=(e,t=!1)=>fetch(e).then((e=>e.text())).then((e=>(t&&console.log(e),JSON.parse(e)))).catch((e=>console.warn(e)));!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?e(exports):"function"==typeof define&&define.amd?define(["exports"],e):e(t.pell={})}(this,(function(t){"use strict";var e=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},n="defaultParagraphSeparator",r="formatBlock",i=function(t,e,n){return t.addEventListener(e,n)},o=function(t,e){return t.appendChild(e)},u=function(t){return document.createElement(t)},l=function(t){return document.queryCommandState(t)},a=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;return setTimeout((()=>{document.execCommand(t,!1,e)}),1),!0},c={bold:{icon:"<b>B</b>",title:"Bold",state:function(){return l("bold")},result:function(){return a("bold")}},italic:{icon:"<i>I</i>",title:"Italic",state:function(){return l("italic")},result:function(){return a("italic")}},underline:{icon:"<u>U</u>",title:"Underline",state:function(){return l("underline")},result:function(){return a("underline")}},superscript:{icon:"<sup>x</sup>",title:"Superscript",state:function(){return l("superscript")},result:function(){return a("superscript")}},subscript:{icon:"<sub>y</sub>",title:"Subscript",state:function(){return l("subscript")},result:function(){return a("subscript")}},strikethrough:{icon:"<strike>S</strike>",title:"Strike-through",state:function(){return l("strikeThrough")},result:function(){return a("strikeThrough")}},heading1:{icon:"<b>H<sub>1</sub></b>",title:"Heading 1",result:function(){return a(r,"<h1>")}},heading2:{icon:"<b>H<sub>2</sub></b>",title:"Heading 2",result:function(){return a(r,"<h2>")}},paragraph:{icon:"&#182;",title:"Paragraph",result:function(){return a(r,"<p>")}},quote:{icon:"&#8220; &#8221;",title:"Quote",result:function(){return a(r,"<blockquote>")}},olist:{icon:"&#35;",title:"Ordered List",result:function(){return a("insertOrderedList")}},ulist:{icon:"&#8226;",title:"Unordered List",result:function(){return a("insertUnorderedList")}},code:{icon:"&lt;/&gt;",title:"Code",result:function(){return a(r,"<pre>")}},line:{icon:"&#8213;",title:"Horizontal Line",result:function(){return a("insertHorizontalRule")}},link:{icon:"&#128279;",title:"Link",result:function(){var t=window.prompt("Enter the link URL");t&&a("createLink",t)}},image:{icon:"&#128247;",title:"Image",result:function(){var t=window.prompt("Enter the image URL");t&&a("insertImage",t)}}},s={actionbar:"pell-actionbar",button:"pell-button",content:"pell-content",selected:"pell-button-selected"},d=function(t){var l=t.actions?t.actions.map((function(t){return"string"==typeof t?c[t]:c[t.name]?e({},c[t.name],t):t})):Object.keys(c).map((function(t){return c[t]})),d=e({},s,t.classes),p=t.defaultParagraphSeparator||"div",f=u("div");f.tabIndex=-1,f.className=d.actionbar,o(t.element,f);var m=t.element.content=u("div");return m.contentEditable=!0,m.className=d.content,m.oninput=function(e){var n=e.target.firstChild;n&&3===n.nodeType?a(r,"<"+p+">"):"<br>"===m.innerHTML&&(m.innerHTML=""),t.onChange(m.innerHTML)},m.onkeydown=function(t){var e;"Enter"===t.key&&"blockquote"===(e=r,document.queryCommandValue(e))&&setTimeout((function(){return a(r,"<"+p+">")}),0)},o(t.element,m),l.forEach((function(t){var e=u("button");if(e.className=d.button,e.innerHTML=t.icon,e.tabIndex=-1,e.title=t.title,e.setAttribute("type","button"),e.onclick=function(){return t.result()&&m.focus()},t.state){var n=function(){return e.classList[t.state()?"add":"remove"](d.selected)};i(m,"keyup",n),i(m,"mouseup",n),i(e,"click",n)}o(f,e)})),t.styleWithCSS&&a("styleWithCSS"),a(n,p),t.element},p={exec:a,init:d};t.exec=a,t.init=d,t.default=p,Object.defineProperty(t,"__esModule",{value:!0})}));class PellEditor{editor=null;value=null;elm=null;constructor(t,e=""){this.value=e,this.elm=query(t),this.elm.classList.add("pell"),this.editor=pell.init({element:this.elm,onChange:t=>{this.value=t},defaultParagraphSeparator:"p",styleWithCSS:!1,actions:["bold","underline","italic","superscript","subscript"]}),this.editor.addEventListener("paste",(function(t){if(t.stopPropagation(),t.preventDefault(),navigator.clipboard)navigator.clipboard.readText().then((t=>{var e=document.createElement("div");e.innerHTML=t;var n=e.textContent;window.pell.exec("insertText",n.replace(/[\n\r]+|[\s]{2,}/g," ").trim()),e.remove()}));else{var e=t.clipboardData||window.clipboardData,n=document.createElement("div");n.innerHTML=e.getData("text/html");var r=n.textContent;window.pell.exec("insertText",r.replace(/[\n\r]+|[\s]{2,}/g," ").trim()),n.remove()}return!0})),this.editor.content.innerHTML=this.value}getValue(){return this.editor.content.innerHTML}setValue(t){this.value=t,this.editor.content.innerHTML=this.value}focus(){setTimeout((()=>{this.editor.content.focus()}),0)}}