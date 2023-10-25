<?php
// Require composer
require __DIR__ . '/vendor/autoload.php';

// Set up phpdotenv
$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ );
$dotenv->load();
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>fedistream</title>
	<style>
	html,
	body {
		font-family: -apple-system, BlinkMacSystemFont, avenir next, avenir,
			segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto,
			arial, sans-serif;
		background-color: #15202b;
		color: #f7f9f9;
		text-rendering: optimizeLegibility;
	}

  #statuses {
    overflow-y: scroll;
    height: 90vh;
  }

	.container {
		width: 100%;
		max-width: 600px;
		margin: auto;
	}

	#statuses {
		width: 100%;
	}

	.status {
		border-bottom: 1px solid #38444d;
		padding: 0.75em 0em;
		animation: show 600ms 100ms cubic-bezier(0.38, 0.97, 0.56, 0.76) forwards;
		opacity: 0;
	}

	@keyframes show {
		100% {
			opacity: 1;
			transform: none;
		}
	}

	p {
		margin: 0;
	}

	.content {
		font-size: 13px;
		font-weight: 400;
		word-break: break-word;
		margin-top: 0.75em;
	}

	a {
		color: #8c8dff;
		text-decoration: none;
	}

	a:hover {
		text-decoration: underline;
	}

	#preview {
		width: 50%;
	}

	.emoji {
		width: 16px;
		height: 16px;
		vertical-align: middle;
		object-fit: contain;
		margin: -0.2ex 0.15em 0.2ex;
	}

	.indicator-pulse {
		display: block;
		width: 6px;
		height: 6px;
		border-radius: 6px;
		background-color: green;
		animation: blinker 3s cubic-bezier(0.5, 0, 1, 1) infinite alternate;
	}

	@keyframes blinker {
		from {
			opacity: 1;
		}
		to {
			opacity: 0.25;
		}
	}

	.menu {
		display: block;
		margin-top: 16px;
		padding-bottom: 16px;
		border-bottom: 1px solid #38444d;
	}

	.clearfix::after {
		content: "";
		clear: both;
		display: table;
	}

	.menu > h2 {
		font-size: 20px;
		font-weight: 700;
		float: left;
		margin: 0;
		line-height: 20px;
	}

	.menu > .indicator {
		float: right;
	}

	.indicator > span {
		display: inline-block;
		font-size: 11px;
		font-weight: 500;
		text-transform: uppercase;
		line-height: 20px;
	}

	.header {
		display: flex;
	}

	.avatar {
		flex-shrink: 0;
	}

	.avatar img {
		width: 36px;
		border-radius: 3px;
		object-fit: cover;
	}

	.details {
		flex-grow: 1;
		margin-left: 10px;
	}

	.name {
		float: left;
		font-size: 13px;
		font-weight: 500;
		line-height: 22px;
		color: #f7f9f9;
	}

	.name a {
		color: #f7f9f9;
		text-decoration: none;
	}

	.name a:hover {
		color: #f7f9f9;
		text-decoration: none;
	}

	.timestamp {
		float: right;
		font-size: 13px;
		font-weight: 400;
		color: #999999;
		text-decoration: none;
	}

	.timestamp a {
		color: #999999;
	}

	.timestamp a:hover {
		color: #999999;
		text-decoration: underline;
	}

	.url {
		font-size: 13px;
		color: #999999;
		font-weight: 400;
	}

	.url a {
		color: #999999;
		text-decratione: none;
	}

	.url a:hover {
		color: #999999;
	}

	.invisible {
		display: none;
	}

	.ellipsis::after {
		content: "…";
	}

	.attachments {
		box-sizing: border-box;
		margin-top: 0.75em;
		height: 180px;
		width: 100%;
	}

	.attachments img {
		float: left;
		object-fit: cover;
		box-sizing: border-box;
		border-radius: 3px;
	}

	.attachments-1 img {
		width: 100%;
		height: 100%;
	}

	.attachments-2 img {
		width: 50%;
		height: 100%;
	}

	.attachments-3 img {
		width: 50%;
		height: 50%;
	}

	.attachments-3 img:first-child {
		width: 50%;
		height: 100%;
	}

	.attachments-4 img {
		width: 50%;
		height: 50%;
	}

	.hidden {
		display: none;
	}

	#filterForm {
		display: flex;
	}

	input {
		font-family: inherit;
		font-size: 0.8rem;
		color: #f7f9f9;
		display: inline-block;
		width: 33.333%;
		height: 2rem;
		padding: 0.25rem;
		margin-bottom: 1rem;
		margin-right: 1%;
		border: 1px solid #38444d;
		border-radius: 3px;
		box-sizing: border-box;
		background: transparent;
	}

	input[type="submit"] {
		background-color: #6364ff;
		border: 1px solid #6364ff;
	}

	select {
		font-family: inherit;
		font-size: 0.8rem;
		color: #f7f9f9;
		background: transparent;
		display: inline-block;
		width: 33.333%;
		height: 2rem;
		padding: 0.25rem;
		margin-bottom: 1rem;
		margin-right: 1%;
		border: 1px solid #38444d;
		border-radius: 3px;
		box-sizing: border-box;
	}

	input:focus,
	select:focus {
		outline: none;
	}

	.text-center {
		text-align: center;
	}

	#filters {
		margin-top: 200px;
	}
	</style>
</head>
<body>

<div id="filters" class="container">
	<h2 class="text-center">fedistream</h2>
	<form id="filterForm">
		<input id="filter" name="filter" type="text" pattern="[A-Za-z0-9\S]{1,25}" placeholder="Filter word (required)" required>
		<select id="lang" name="lang">
		   <option value="any" selected>Any Language</option>
		   <option value="aa">Afaraf</option>
		   <option value="ab">аҧсуа бызшәа</option>
		   <option value="ae">avesta</option>
		   <option value="af">Afrikaans</option>
		   <option value="ak">Akan</option>
		   <option value="am">አማርኛ</option>
		   <option value="an">aragonés</option>
		   <option value="ar">اللغة العربية</option>
		   <option value="as">অসমীয়া</option>
		   <option value="av">авар мацӀ</option>
		   <option value="ay">aymar aru</option>
		   <option value="az">azərbaycan dili</option>
		   <option value="ba">башҡорт теле</option>
		   <option value="be">беларуская мова</option>
		   <option value="bg">български език</option>
		   <option value="bh">भोजपुरी</option>
		   <option value="bi">Bislama</option>
		   <option value="bm">bamanankan</option>
		   <option value="bn">বাংলা</option>
		   <option value="bo">བོད་ཡིག</option>
		   <option value="br">brezhoneg</option>
		   <option value="bs">bosanski jezik</option>
		   <option value="ca">Català</option>
		   <option value="ce">нохчийн мотт</option>
		   <option value="ch">Chamoru</option>
		   <option value="co">corsu</option>
		   <option value="cr">ᓀᐦᐃᔭᐍᐏᐣ</option>
		   <option value="cs">čeština</option>
		   <option value="cu">ѩзыкъ словѣньскъ</option>
		   <option value="cv">чӑваш чӗлхи</option>
		   <option value="cy">Cymraeg</option>
		   <option value="da">dansk</option>
		   <option value="de">Deutsch</option>
		   <option value="dv">Dhivehi</option>
		   <option value="dz">རྫོང་ཁ</option>
		   <option value="ee">Eʋegbe</option>
		   <option value="el">Ελληνικά</option>
		   <option value="en">English</option>
		   <option value="eo">Esperanto</option>
		   <option value="es">Español</option>
		   <option value="et">eesti</option>
		   <option value="eu">euskara</option>
		   <option value="fa">فارسی</option>
		   <option value="ff">Fulfulde</option>
		   <option value="fi">suomi</option>
		   <option value="fj">Vakaviti</option>
		   <option value="fo">føroyskt</option>
		   <option value="fr">Français</option>
		   <option value="fy">Frysk</option>
		   <option value="ga">Gaeilge</option>
		   <option value="gd">Gàidhlig</option>
		   <option value="gl">galego</option>
		   <option value="gu">ગુજરાતી</option>
		   <option value="gv">Gaelg</option>
		   <option value="ha">هَوُسَ</option>
		   <option value="he">עברית</option>
		   <option value="hi">हिन्दी</option>
		   <option value="ho">Hiri Motu</option>
		   <option value="hr">Hrvatski</option>
		   <option value="ht">Kreyòl ayisyen</option>
		   <option value="hu">magyar</option>
		   <option value="hy">Հայերեն</option>
		   <option value="hz">Otjiherero</option>
		   <option value="ia">Interlingua</option>
		   <option value="id">Bahasa Indonesia</option>
		   <option value="ie">Interlingue</option>
		   <option value="ig">Asụsụ Igbo</option>
		   <option value="ii">ꆈꌠ꒿ Nuosuhxop</option>
		   <option value="ik">Iñupiaq</option>
		   <option value="io">Ido</option>
		   <option value="is">Íslenska</option>
		   <option value="it">Italiano</option>
		   <option value="iu">ᐃᓄᒃᑎᑐᑦ</option>
		   <option value="ja">日本語</option>
		   <option value="jv">basa Jawa</option>
		   <option value="ka">ქართული</option>
		   <option value="kg">Kikongo</option>
		   <option value="ki">Gĩkũyũ</option>
		   <option value="kj">Kuanyama</option>
		   <option value="kk">қазақ тілі</option>
		   <option value="kl">kalaallisut</option>
		   <option value="km">ខេមរភាសា</option>
		   <option value="kn">ಕನ್ನಡ</option>
		   <option value="ko">한국어</option>
		   <option value="kr">Kanuri</option>
		   <option value="ks">कश्मीरी</option>
		   <option value="ku">Kurmancî</option>
		   <option value="kv">коми кыв</option>
		   <option value="kw">Kernewek</option>
		   <option value="ky">Кыргызча</option>
		   <option value="la">latine</option>
		   <option value="lb">Lëtzebuergesch</option>
		   <option value="lg">Luganda</option>
		   <option value="li">Limburgs</option>
		   <option value="ln">Lingála</option>
		   <option value="lo">ລາວ</option>
		   <option value="lt">lietuvių kalba</option>
		   <option value="lu">Tshiluba</option>
		   <option value="lv">latviešu valoda</option>
		   <option value="mg">fiteny malagasy</option>
		   <option value="mh">Kajin M̧ajeļ</option>
		   <option value="mi">te reo Māori</option>
		   <option value="mk">македонски јазик</option>
		   <option value="ml">മലയാളം</option>
		   <option value="mn">Монгол хэл</option>
		   <option value="mr">मराठी</option>
		   <option value="ms">Bahasa Melayu</option>
		   <option value="mt">Malti</option>
		   <option value="my">ဗမာစာ</option>
		   <option value="na">Ekakairũ Naoero</option>
		   <option value="nb">Norsk bokmål</option>
		   <option value="nd">isiNdebele</option>
		   <option value="ne">नेपाली</option>
		   <option value="ng">Owambo</option>
		   <option value="nl">Nederlands</option>
		   <option value="nn">Norsk Nynorsk</option>
		   <option value="no">Norsk</option>
		   <option value="nr">isiNdebele</option>
		   <option value="nv">Diné bizaad</option>
		   <option value="ny">chiCheŵa</option>
		   <option value="oc">occitan</option>
		   <option value="oj">ᐊᓂᔑᓈᐯᒧᐎᓐ</option>
		   <option value="om">Afaan Oromoo</option>
		   <option value="or">ଓଡ଼ିଆ</option>
		   <option value="os">ирон æвзаг</option>
		   <option value="pa">ਪੰਜਾਬੀ</option>
		   <option value="pi">पाऴि</option>
		   <option value="pl">Polski</option>
		   <option value="ps">پښتو</option>
		   <option value="pt">Português</option>
		   <option value="qu">Runa Simi</option>
		   <option value="rm">rumantsch grischun</option>
		   <option value="rn">Ikirundi</option>
		   <option value="ro">Română</option>
		   <option value="ru">Русский</option>
		   <option value="rw">Ikinyarwanda</option>
		   <option value="sa">संस्कृतम्</option>
		   <option value="sc">sardu</option>
		   <option value="sd">सिन्धी</option>
		   <option value="se">Davvisámegiella</option>
		   <option value="sg">yângâ tî sängö</option>
		   <option value="si">සිංහල</option>
		   <option value="sk">slovenčina</option>
		   <option value="sl">slovenščina</option>
		   <option value="sn">chiShona</option>
		   <option value="so">Soomaaliga</option>
		   <option value="sq">Shqip</option>
		   <option value="sr">српски језик</option>
		   <option value="ss">SiSwati</option>
		   <option value="st">Sesotho</option>
		   <option value="su">Basa Sunda</option>
		   <option value="sv">Svenska</option>
		   <option value="sw">Kiswahili</option>
		   <option value="ta">தமிழ்</option>
		   <option value="te">తెలుగు</option>
		   <option value="tg">тоҷикӣ</option>
		   <option value="th">ไทย</option>
		   <option value="ti">ትግርኛ</option>
		   <option value="tk">Türkmen</option>
		   <option value="tl">Wikang Tagalog</option>
		   <option value="tn">Setswana</option>
		   <option value="to">faka Tonga</option>
		   <option value="tr">Türkçe</option>
		   <option value="ts">Xitsonga</option>
		   <option value="tt">татар теле</option>
		   <option value="tw">Twi</option>
		   <option value="ty">Reo Tahiti</option>
		   <option value="ug">ئۇيغۇرچە‎</option>
		   <option value="uk">Українська</option>
		   <option value="ur">اردو</option>
		   <option value="uz">Ўзбек</option>
		   <option value="ve">Tshivenḓa</option>
		   <option value="vi">Tiếng Việt</option>
		   <option value="vo">Volapük</option>
		   <option value="wa">walon</option>
		   <option value="wo">Wollof</option>
		   <option value="xh">isiXhosa</option>
		   <option value="yi">ייִדיש</option>
		   <option value="yo">Yorùbá</option>
		   <option value="za">Saɯ cueŋƅ</option>
		   <option value="zh">中文</option>
		   <option value="zu">isiZulu</option>
		   <option value="asst">Asturianu</option>
		   <option value="ckkb">سۆرانی</option>
		   <option value="jbbo">la .lojban.</option>
		   <option value="kaab">Taqbaylit</option>
		   <option value="kmmr">Kurmancî</option>
		   <option value="lddn">Láadan</option>
		   <option value="lffn">lingua franca nova</option>
		   <option value="scco">Scots</option>
		   <option value="took">toki pona</option>
		   <option value="zbba">باليبلن</option>
		   <option value="zggh">ⵜⴰⵎⴰⵣⵉⵖⵜ</option>
		</select>
		<input class="submit" type="submit" value="Begin Streaming">
	</form>
</div>

<main id="stream" class="container">

	<div class="menu clearfix">
		<h2>Streaming: "<span id="filter-title"></span>"</h2>
		<div class="indicator">
			<span class="indicator-pulse"></span>
			<span class="indicator-text">LIVE</span>
		</div>
	</div>

	<div id="statuses">
	</div>

</main>

<template id="statusTemplate">
<div class="status">

	<div class="header">
	  <div class="avatar">
		<a href="${status.account.url}"><img src="${status.account.avatar_static}" alt=""></a>
	  </div>
	  <div class="details">
		<div class="header-row clearfix">
		  <div class="name"><a href="${status.account.url}">${status.account.display_name}</a></div>
		  <div class="timestamp"><a href="${status.url}">${status.created_at}</a></div>
		</div>
		<div class="url"><a href="${status.account.url}">${status.account.url}</a></div>
	  </div>
	</div>

	<div class="content">
	${status.content}

	${status.media_attachments}
	</div>

</div>
</template>

<script>

// Configure Elements
const statusesContainer = document.getElementById("statuses");
const statusTemplate = document.getElementById("statusTemplate").innerHTML;

// Helper: Get query string parameter (borrowed from https://davidwalsh.name/query-string-javascript)
function getUrlParameter(name) {
	name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
	var results = regex.exec(location.search);
	return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

// Helper: Interpololate a HTML template element as a JavaScript template literal (borrowed from: https://gomakethings.com/html-templates-with-vanilla-javascript/)
function interpolate (str, params) {
  let names = Object.keys(params);
  let vals = Object.values(params);
  return new Function(...names, `return \`${str}\`;`)(...vals);
}

// Helper: Render custom emojis
function customEmojis(str, emojis) {

  var emojiMap = {};

  emojis.forEach(emoji => {
	emojiMap[':' + emoji.shortcode + ':'] = '<img src="' + emoji.url + '" draggable="false" class="emoji"/>';
  });

  emojifiedString = str.replace(/:[\d+_a-z-]+:/g, function (m) {
	  return emojiMap[m];
  });

  return emojifiedString;

}

// Main streaming function
function beginStreaming(filter, lang) {

  // Gather multiple instances
  var instanceUrls = [
    'https://mastodon.social',
    'https://pb.todon.de',
    'https://kopimi.space',
    'https://sunnygarden.org',
    'https://mementomori.social'
  ];

	const evtSource = new EventSource("https://mastodon.social/api/v1/streaming/public?access_token=<?php echo $_ENV['MASTODONSOCIAL_ACCESS_TOKEN']; ?>");

	evtSource.addEventListener("update", (event) => {

		var status = JSON.parse(event.data);

		// Remove HTML tags and URLs from status content for search purposes
		var statusText = status.content.replace(/(<([^>]+)>)/g, "").replace(/(?:https?|ftp):\/\/[\n\S]+/g, '');

		// Check for filter text in content & that language is either set to "any" or a match
		if (statusText.toLowerCase().includes(filter) && (lang.toLowerCase() == "any" || status.language.toLowerCase().includes(lang.toLowerCase()))) {

			// Emojify content
			status.content = customEmojis(status.content, status.emojis);

			// Emojify display name
			status.account.display_name = customEmojis(status.account.display_name, status.account.emojis);

			// Convert created_at to local timestamp
			status.created_at = new Date(status.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

			// Render images
			if (status.media_attachments.length != 0) { status.media_attachments = `<div class="attachments attachments-` + status.media_attachments.length + `">` + status.media_attachments.reduce((updated, current) => updated.concat(`<img src="${current.preview_url}" class="attachment"/>`), '') + '</div>'}

			statusHTML = interpolate(statusTemplate, {status});
			statusesContainer.insertAdjacentHTML('beforeEnd', statusHTML);

      // Always keep the page at bottom
		}

	});
}

filterText = document.getElementById("filter").value.toLowerCase();

// When page is loaded, check for query string, otherwise present input
window.addEventListener('load', function(event) {

	// Grab URL parameters if they exist
	var filter = getUrlParameter('filter') ? getUrlParameter('filter') : false;
	var lang = getUrlParameter('lang') ? getUrlParameter('lang') : "any";

	if (filter) {
		localStorage["lastLang"] = lang;
		document.getElementById("filters").classList.add("hidden");
		document.getElementById("stream").classList.remove("hidden");
		document.getElementById("filter-title").innerText = filter;
		beginStreaming(filter, lang);
	}

	else {
		if (localStorage["lastLang"]) {
			document.getElementById("lang").value = localStorage["lastLang"];
		}
	}

  // Stream by default without filter
  if ( ! filter ) {
    document.getElementById("filters").classList.remove("hidden");
    document.getElementById("stream").classList.add("hidden");

    var filter = '';
  }

  localStorage["lastLang"] = lang;
	document.getElementById("filters").classList.add("hidden");
	document.getElementById("stream").classList.remove("hidden");
	document.getElementById("filter-title").innerText = filter;
	beginStreaming(filter, lang);

});

</script>
</body>
</html>
