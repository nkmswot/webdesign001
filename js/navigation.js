document.addEventListener("DOMContentLoaded", function () {

const nav = `
<nav class="wdn">

<div class="wdn-bar">

<a href="/" class="wdn-logo">
<div class="wdn-gem">◆</div>
WebDesign<em>001</em>
</a>

<button class="wdn-hamburger" id="wdnToggle">
<span></span>
<span></span>
<span></span>
</button>

<div class="wdn-menu">

<div class="wdn-dd">
<button class="wdn-ddbtn">🎨 Design Tools <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/box-shadow-generator/">🧊 Box Shadow Generator</a>

<a class="wdn-item" href="/color-palette-collections/">🎨 Color Palette Collections</a>

<a class="wdn-item" href="/color-palette-generator/">🖌️ Color Palette Generator</a>

<a class="wdn-item" href="/color-picker/">🎯 Color Picker</a>

<a class="wdn-item" href="/css-animation-generator/">🎞️ CSS Animation Generator</a>

<a class="wdn-item" href="/css-button-generator/">🔘 CSS Button Generator</a>

<a class="wdn-item" href="/css-flexbox-generator/">📦 CSS Flexbox Generator</a>

<a class="wdn-item" href="/css-framework-converter/">🔁 CSS Framework Converter</a>

<a class="wdn-item" href="/css-gradient-generator/">🌈 CSS Gradient Generator</a>

<a class="wdn-item" href="/css-minifier/">📉 CSS Minifier</a>

<a class="wdn-item" href="/css-snippets-library/">📚 CSS Snippets Library</a>

<a class="wdn-item" href="/google-fonts-pairing-guide/">🔤 Google Fonts Pairing Guide</a>

<a class="wdn-item" href="/html-color-names/">🎨 HTML Color Names</a>

<a class="wdn-item" href="/px-to-rem-converter/">📏 PX to REM Converter</a>

<a class="wdn-item" href="/svg-icon-pack/">⭐ SVG Icon Pack</a>

<a class="wdn-item" href="/svg-wave-generator/">🌊 SVG Wave Generator</a>

<a class="wdn-item" href="/typography-scale-generator/">🔠 Typography Scale Generator</a>

</div>
</div>

<div class="wdn-dd">
<button class="wdn-ddbtn">📷 Image Tools <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/favicon-generator/">🧿 Favicon Generator</a>

<a class="wdn-item" href="/image-compressor/">🗜️ Image Compressor</a>

<a class="wdn-item" href="/image-resizer/">📐 Image Resizer</a>

<a class="wdn-item" href="/image-to-webp/">🖼️ Image to WebP Converter</a>
</div>
</div>

<div class="wdn-dd">
<button class="wdn-ddbtn">📑 Legal Generators <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">
	<a class="wdn-item" href="/privacy-policy-generator/">🔒 Privacy Policy Generator</a>

<a class="wdn-item" href="/refund-policy-generator/">💰 Refund Policy Generator</a>

<a class="wdn-item" href="/terms-conditions-generator/">📜 Terms & Conditions Generator</a>
	</div>
</div>


<div class="wdn-dd">
<button class="wdn-ddbtn">🔎 SEO Tools <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/meta-tag-generator/">🏷️ Meta Tag Generator</a>

<a class="wdn-item" href="/og-preview/">🔗 Open Graph Preview</a>

<a class="wdn-item" href="/robots-txt-generator/">🤖 Robots.txt Generator</a>

<a class="wdn-item" href="/schema-generator/">📊 Schema Markup Generator</a>

<a class="wdn-item" href="/sitemap-generator/">🗺️ Sitemap XML Generator</a>

</div>
</div>

<div class="wdn-dd">
<button class="wdn-ddbtn">⚙ Dev Tools <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/base64-encoder-decoder/">🔐 Base64 Encoder / Decoder</a>

<a class="wdn-item" href="/diff-checker/">🆚 Diff Checker</a>

<a class="wdn-item" href="/form-builder/">📝 Form Builder</a>

<a class="wdn-item" href="/htaccess-generator/">⚙️ .htaccess Generator</a>

<a class="wdn-item" href="/html-css-playground/">🧪 HTML CSS Playground</a>

<a class="wdn-item" href="/html-formatter/">📄 HTML Formatter</a>

<a class="wdn-item" href="/js-formatter/">🧩 JS Formatter</a>

<a class="wdn-item" href="/json-formatter/">🗂️ JSON Formatter</a>

<a class="wdn-item" href="/js-to-ts-converter/">🔁 JS to TS Converter</a>

<a class="wdn-item" href="/live-code-editor/">💻 Live Code Editor</a>

<a class="wdn-item" href="/lorem-ipsum-generator/">📜 Lorem Ipsum Generator</a>

<a class="wdn-item" href="/markdown-editor/">✍️ Markdown Editor</a>

<a class="wdn-item" href="/mysql-query-validator/">🛢️ MySQL Query Validator</a>

<a class="wdn-item" href="/password-generator/">🔑 Password Generator</a>

<a class="wdn-item" href="/php-to-laravel-converter/">🐘 PHP to Laravel Converter</a>

<a class="wdn-item" href="/qr-code-generator/">📱 QR Code Generator</a>

<a class="wdn-item" href="/regex-tester/">🔎 Regex Tester</a>

<a class="wdn-item" href="/url-encoder-decoder/">🌐 URL Encoder / Decoder</a>

</div>
</div>

	<div class="wdn-dd">
<button class="wdn-ddbtn">⚙ Cheat Sheets <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/html-formatter/">HTML Formatter</a>

<a class="wdn-item" href="/css-minifier/">CSS Minifier</a>

<a class="wdn-item" href="/json-formatter/">JSON Formatter</a>

<a class="wdn-item" href="/js-formatter/">JS Formatter</a>

<a class="wdn-item" href="/regex-tester/">Regex Tester</a>

<a class="wdn-item" href="/htaccess-generator/">.htaccess Generator</a>

</div>
</div>

	<div class="wdn-dd">
<button class="wdn-ddbtn">&#128218; Resources <span class="wdn-arrow">▾</span></button>
<div class="wdn-panel">

<a class="wdn-item" href="/html-formatter/">HTML Formatter</a>

<a class="wdn-item" href="/css-minifier/">CSS Minifier</a>

<a class="wdn-item" href="/json-formatter/">JSON Formatter</a>

<a class="wdn-item" href="/js-formatter/">JS Formatter</a>

<a class="wdn-item" href="/regex-tester/">Regex Tester</a>

<a class="wdn-item" href="/htaccess-generator/">.htaccess Generator</a>

</div>
</div>

</div>

<div class="wdn-right">
<a href="/" class="wdn-allbtn">✨ Learn HTML</a>
</div>

</div>

</nav>
`;

document.getElementById("nav").innerHTML = nav;


/* dropdown logic */

document.querySelectorAll(".wdn-ddbtn").forEach(btn=>{
btn.addEventListener("click",function(){

const parent=this.parentElement

if(window.innerWidth<960){
parent.classList.toggle("open")
}else{

document.querySelectorAll(".wdn-dd").forEach(d=>{
if(d!==parent)d.classList.remove("open")
})

parent.classList.toggle("open")

}

})
})


document.addEventListener("click",function(e){
if(!e.target.closest(".wdn-dd")){
document.querySelectorAll(".wdn-dd").forEach(d=>d.classList.remove("open"))
}
})


/* ✅ MOBILE MENU TOGGLE */

const toggle = document.getElementById("wdnToggle")
const menu = document.querySelector(".wdn-menu")

toggle.addEventListener("click",()=>{
menu.classList.toggle("open")
})

});
