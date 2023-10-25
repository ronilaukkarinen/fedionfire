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
  <meta property="og:title" content="Fedi on Fire!">
  <meta property="og:description" content="Watch every Mastodon/Fediverse post in real-time - filter the firehose">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://fedionfire.stream">
  <meta property="og:image" content="https://fedionfire.stream/fedionfire-og.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="title" content="Fedi on Fire!">
  <meta name="description" content="Watch every Mastodon/Fediverse post in real-time - filter the firehose">
	<title>Fedi on Fire</title>
  <link rel="icon" href="dynamic-favicon.svg">
	<style>
    :root {
      --color-bg: #1e2028;
      --color-fg: #f7f9f9;
      --color-dim: rgb(255 255 255 / .5);
      --color-mastodon-light: #6364ff;
      --color-mastodon-dark: #563acc;
      --font: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      --font-size-16: 14px;
      --font-size-14: 14px;
      --font-size-12: 12px;
    }

    html,
    body {
      font-family: var(--font);
      background-color: var(--color-bg);
      color:  var(--color-fg);
      text-rendering: optimizeLegibility;
      line-height: 1.5;
      margin: 0;
    }

    a {
      color: var(--color-fg);
      font-size: var(--font-size-14);
      text-decoration: none;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      padding: 10px;
    }

    .top-bar a {
      color: var(--color-mastodon-light);
    }

    .top-bar a:hover {
      color: var(--color-mastodon-dark);
    }

    .top-bar p {
      margin: 0;
    }

    h1 {
      position: relative;
      display: inline-flex;
      align-items: center;
    }

    .beta {
      text-transform: uppercase;
      font-size: 10px;
      font-weight: 700;
      color: var(--color-bg);
      background-color: var(--color-fg);
      padding: 2px 4px;
      border-radius: 4px;
      margin-left: 4px;
    }

    #stream {
      height: 100vh;
      display: grid;
      grid-template-columns: 1fr;
      grid-template-rows: 50px 4fr 0.1fr;
      gap: 0px 0px;
      grid-template-areas:
      "."
      "."
      ".";
    }

    #statuses {
      grid-auto-rows: max-content;
      grid-auto-flow: row;
      grid-template-columns: 20rem 2rem 1fr;
      display: grid;
      min-height: calc(100vh - (calc(70px * 2)));
      max-height: calc(100vh - (calc(70px * 2)));
      height: calc(100vh - (calc(70px * 2)));
      max-width: 100vw;
      overflow-y: scroll;
      overflow-x: hidden;
      gap: 10px;
    }

    @media (max-width: 600px) {
      #statuses {
        grid-template-columns: 7rem 3rem 1fr;
      }

      .bottom-bar p,
      .top-bar p {
        font-size: 11px;
      }
    }

    #statuses * {
      overflow-anchor: none;
    }

    #catch-up {
      align-self: end;
      justify-self: center;
      background-color: var(--color-mastodon-light);
      border-radius: 4px;
      color: #fff;
      font-size: var(--font-size-14);
      padding: 10px 12px;
      border: 0;
      font-weight: 700;
      position: absolute;
      bottom: 60px;
    }

    #catch-up:hover {
      cursor: pointer;
      background-color: var(--color-mastodon-dark);
    }

    .circle {
      background-color: #fff;
      border-radius: 50%;
      display: inline-block;
      height: 9px;
      min-width: 9px;
      position: relative;
      width: 9px;
      z-index: 2;
    }

    .bottom-bar {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      padding: 10px;
      align-items: center;
    }

    .bottom-bar p {
      margin: 0;
      color: var(--color-dim);
      font-size: 12px;
    }

    .bottom-bar a {
      font-size: 12px;
      color: var(--color-dim);
      text-decoration: underline;
    }

    .bottom-bar a:hover {
      color: var(--color-fg);
    }

    .live-indicator-block {
      display: inline-flex;
      min-width: 50.73px;
    }

    .live-indicator-block .live-indicator {
      background: #ea2429;
      border-radius: 6px;
      color: #fff;
      display: inline-block;
      font-size: 12px;
      font-weight: 500;
      line-height: 1;
      padding: 6px 7px;
      text-transform: uppercase;
      vertical-align: middle;
      width: auto;
    }

    .live-indicator-block .live-indicator .blink {
      -webkit-animation: blinker 1s cubic-bezier(0.5, 0, 1, 1) infinite alternate;
      animation: blinker 1s cubic-bezier(0.5, 0, 1, 1) infinite alternate;
      font-size: 10px;
      margin-right: 5px;
    }

    @-webkit-keyframes blinker {
      from {
          opacity: 1;
      }
      to {
          opacity: 0;
      }
    }

    @keyframes blinker {
      from {
          opacity: 1;
      }
      to {
          opacity: 0;
      }
    }

    /* Text styles */
    .text-neutral {
      color: var(--color-dim);
    }

    .text-truncate {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .text-clip {
      text-overflow: clip;
    }

    .whitespace-pre-line {
      color: var(--color-fg);
      font-size: var(--font-size-14);
      white-space: pre-line;
    }

    .break-words {
      overflow-wrap: break-word;
    }

    .overflow-x-clip {
      overflow-x: clip;
    }

    .text-block {
      display: block;
    }

    .text-block,
    p,
    .target p,
    .text-normal {
      font-size: var(--font-size-14);
    }

    .text-xs {
      font-size: var(--font-size-12);
    }

    .text-right {
      text-align: right;
    }

    .name {
      margin-bottom: 4px;
    }

    .avatar {
      width: 2rem;
      justify-content: center;
      display: flex;
    }

    .target {
      position: relative;
    }

    .global-link {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    .global-link:focus ~ * > *,
    .global-link:focus ~ *,
    .global-link:hover ~ * > *,
    .global-link:hover ~ * {
      color: #f2610d;
      text-decoration-line: underline;
      text-decoration-offset: 2px;
    }

    a[rel] {
      color: var(--color-dim);
    }

    .target .whitespace-pre-line > p:first-child,
    .reply-indicator + p,
    .target > a + p:first-child,
    .target > p:first-child {
      margin-top: 0;
    }

    .target > p:last-child {
      margin-bottom: 0;
    }

    #anchor {
      overflow-anchor: auto;
      height: 1px;
    }

    /* Hidden things for backup */
    .timestamp {
      display: none;
    }

    /* Hide for now */
    #filters {
      display: none;
    }

    .emoji {
      width: 16px;
      height: 16px;
      vertical-align: middle;
      object-fit: contain;
      margin: -0.2ex 0.15em 0.2ex;
    }

    .avatar img {
      width: 32px;
      height: 32px;
      object-fit: cover;
      border-radius: 50%;
      min-width: 32px;
    }

    .screen-reader-text {
      position: absolute;
      left: -10000px;
      top: auto;
      width: 1px;
      height: 1px;
      overflow: hidden;
    }
	</style>
</head>
<body>

<div id="filters">
	<h2 class="text-center">Fire on fedi</h2>
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

<main id="stream">

	<header class="top-bar">
		<h1 style="margin: 0; line-height: 1; display: flex; align-items: center; gap: 4px;"; aria-label="Fedi on Fire"><svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path aria-hidden="true" fill="#f2610d" d="m17.66 11.2c-.23-.3-.51-.56-.77-.82-.67-.6-1.43-1.03-2.07-1.66-1.49-1.46-1.82-3.87-.87-5.72-.95.23-1.78.75-2.49 1.32-2.59 2.08-3.61 5.75-2.39 8.9.04.1.08.2.08.33 0 .22-.15.42-.35.5-.23.1-.47.04-.66-.12-.06-.05-.1-.1-.14-.17-1.13-1.43-1.31-3.48-.55-5.12-1.67 1.36-2.58 3.66-2.45 5.83.06.5.12 1 .29 1.5.14.6.41 1.2.71 1.73 1.08 1.73 2.95 2.97 4.96 3.22 2.14.27 4.43-.12 6.07-1.6 1.83-1.66 2.47-4.32 1.53-6.6l-.13-.26c-.21-.46-.77-1.26-.77-1.26m-3.16 6.3c-.28.24-.74.5-1.1.6-1.12.4-2.24-.16-2.9-.82 1.19-.28 1.9-1.16 2.11-2.05.17-.8-.15-1.46-.28-2.23-.12-.74-.1-1.37.17-2.06.19.38.39.76.63 1.06.77 1 1.98 1.44 2.24 2.8.04.14.06.28.06.43.03.82-.33 1.72-.93 2.27z"/></svg><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="125" height="16.08" viewBox="0 0 886 114"><path fill="currentColor" d="M6 55.5v51.6l15.8-.3 15.7-.3.3-17.7L38 71h47V47H38V29h53V4H6v51.5zM254 21.3c0 9.6-.3 17.7-.6 18-.4.3-1.5-.4-2.4-1.6-1-1.3-4.2-3.6-7.2-5.2-5-2.8-5.9-3-16.8-3-10.6 0-11.9.2-16.7 2.7-7.5 4-11.5 7.9-15.1 14.5-7.5 14.2-6.7 33.7 1.9 46.3 7 10.3 18.6 15.5 32.5 14.7 9.1-.5 15.1-2.7 20.6-7.6 4-3.5 4.8-2.9 4.8 3.5v3.5l14.8-.3 14.7-.3.3-51.3L285 4h-31v17.3zm-9 32.5c5.4 2.7 8 7.4 8 14.6 0 10.6-5.4 16-16 16-7 0-11.4-2.5-14-7.9-2.6-5.4-2.5-11.1.3-16.5 3.8-7.5 13.6-10.3 21.7-6.2zM295 13v9h31V4h-31v9zM595 55.5V107h32V71h48V47h-48.1l.3-8.8.3-8.7 26.8-.3L681 29V4h-86v51.5zM688 13v9h31V4h-31v9zM129.8 29.1C106.4 32.2 92 47.4 92 69c0 17 9.8 30 27.1 36.1 5.4 2 8.4 2.3 19.9 2.3 17.5.1 27.2-2.7 37.2-10.6 3.9-3 9.5-12.9 8.3-14.7-.4-.7-5.8-1.1-14.4-1.1-12.4 0-14 .2-15.6 2-2.5 2.8-8 4.4-15.1 4.3-8.7-.1-14.4-3.5-15.9-9.6l-.7-2.7h62.5l-.7-8c-2-23.7-15.8-36.4-41.6-38.3-3.6-.3-9.5-.1-13.2.4zM147 50.6c3.4 1.4 7 5.4 7 7.9 0 .3-7 .5-15.5.5-16.7 0-17.6-.3-13.4-4.9 4.7-5.1 14.2-6.6 21.9-3.5zM827 29c-22.8 2.8-36.8 16.4-37.8 37.1-1.2 24.9 14.3 40 42.8 41.6 11.2.6 23.3-1.3 31.5-5.1 9.3-4.2 17.5-13.4 17.5-19.5 0-2-.5-2.1-13.6-2.1-12.9 0-13.7.1-16.8 2.5-8.3 6.3-25.3 4.8-29.1-2.6-3.2-6.2-4.5-5.9 29-5.9H881v-6.8c0-27-22.1-43-54-39.2zm16.3 21.4c3.4 1.4 6 4 7 6.8.6 1.7-.6 1.8-15.4 1.8h-16l1.6-3.1c3.1-5.8 15.1-8.8 22.8-5.5zM393 30.6c-12.6 3.3-22.7 11.2-27.3 21.2-1.9 4-2.2 6.5-2.2 16.7 0 11.8.1 12.1 3.4 18.3 1.9 3.5 5.3 7.9 7.5 9.9 8.1 7.1 21.4 11.3 35.6 11.3 26.4 0 44.4-13.6 46.6-35 2.1-21.4-9.6-37.5-30.8-42.5-8-1.9-25.6-1.8-32.8.1zm24.9 23.2c5 2.5 7.4 6.3 7.9 12.6.3 4.2-.1 6.5-1.7 9.9-2.7 5.6-6.8 7.7-14.8 7.7-6.9 0-11.4-2.3-13.9-7.2-2.3-4.4-2.2-13.7.2-17.7 3.7-6.3 15.1-9 22.3-5.3zM514.4 29.9c-5.5 1.3-12.3 5.1-15 8.4-1.5 1.8-3 3.2-3.3 3.2-.3 0-.8-2.5-1.1-5.5l-.5-5.5-14.7-.3-14.8-.3V107h31V85.2c0-25.2.5-27 8.1-31 5.1-2.7 11.9-2.4 15.2.6 4.2 3.7 4.7 7 4.7 30.2v22h31V81.7c0-27.5-.8-33.2-5.5-40.7-5.9-9.4-21.6-14.3-35.1-11.1zM295.2 68.2l.3 38.3 15.3.3 15.2.3V30h-31l.2 38.2zM688 68.5v38.6l15.3-.3 15.2-.3.3-38.3.2-38.2h-31v38.5zM729.7 30.6c-.4.4-.7 17.8-.7 38.6V107h31V90.2c0-9.2.5-18.7 1.1-21.2 1.9-8.3 7.7-12 18.7-12h5.2V30h-6.1c-7.4 0-11.8 1.9-16.3 7.1-1.8 2.2-3.6 3.9-3.9 3.9-.3 0-.7-2.4-.9-5.3l-.3-5.2-13.6-.3c-7.4-.1-13.8.1-14.2.4z"/></svg><span class="beta">beta</span></h1>
    <span id="filter-title" style="display: none;"></span>
		<div class="indicator" style="display: none;">
			<span class="indicator-pulse"></span>
			<span class="indicator-text">LIVE</span>
		</div>

    <div class="author-info">
      <p>👀 the <a href="https://jointhefediverse.net">Fediverse</a> firehose. Filtering coming soon.</p>
    </div>
	</header>

	<div id="statuses">
    <div id="anchor"></div>
	</div>

  <button id="catch-up" hidden type="button">↓ Catch up ↓</button>

  <footer class="bottom-bar">
    <div class="repo-info">
      <p>Fedi on Fire built by <a href="https://github.com/ronilaukkarinen">Roni Laukkarinen</a>, based on fedistream gist by <a href="https://github.com/ummjackson" target="_blank">Jackson Palmer</a>, inspired by <a href="https://firesky.tv">firesky.tv</a> and <a href="https://github.com/cscape/mastodon-firehose">mastodon-firehose</a>. <a href="https://github.com/ronilaukkarinen/fedionfire">View code</a></p>
    </div>
    <div class="live-indicator-block"><span class="live-indicator"><span class="circle blink" aria-hidden="true"></span>Live</span></div>
  </footer>

</main>

<template id="statusTemplate">

  <a href="${status.account.url}" target="_blank" class="text-right">
    <span class="text-truncate text-normal text-block name">
      ${status.account.display_name}
    </span>

    <span class="text-xs text-block text-neutral">
      ${status.account.acct}
    </span>
  </a>

  <div class="avatar" style="justify-content: center;">
    <img src="${status.account.avatar_static}" alt="">
  </div>

  <div class="target">
    <a href="${status.url}" target="_blank" class="global-link break-words text-clip overflow-x-clip" aria-hidden="true" tabindex="-1"></a>

    <span class="whitespace-pre-line">${status.content}</span>
    <span class="text-block">
      ${status.media_attachments}
      <p class="screen-reader-text">
        <a href="${status.url}" target="_blank" class="break-words text-clip overflow-x-clip" aria-hidden="true" tabindex="-1">Link to status</a>
      </p>
    </span>
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

	const evtSource = new EventSource("https://mastodon.social/api/v1/streaming/public?access_token=<?php echo $_ENV['MASTODONSOCIAL_ACCESS_TOKEN']; ?>");

  let anchor = document.querySelector('#anchor');

	evtSource.addEventListener("update", (event) => {
		var status = JSON.parse(event.data);

    // Constantly calculate the height in total of the avatars inside statuses div
    var contentHeight = 0;

    document.querySelectorAll('.avatar').forEach(avatar => {
      contentHeight += avatar.offsetHeight;
    });

    // When height of content reach the height of the window, scroll to bottom but not until it's double over the window height
    if (contentHeight >= window.innerHeight - 400 && contentHeight <= window.innerHeight * 1.5) {
      statusesContainer.scrollTop = statusesContainer.scrollHeight;
    }

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

      // Reply indicator
      if (status.in_reply_to_id != null) {
        // Prepend ↰ to status
        status.content = '<span class="text-neutral reply-indicator" style="float: left; margin-right: 4px;">↰</span> ' + status.content;
      }

			// Render images
			// if (status.media_attachments.length != 0) { status.media_attachments = `<div class="attachments attachments-` + status.media_attachments.length + `">` + status.media_attachments.reduce((updated, current) => updated.concat(`<img src="${current.preview_url}" class="attachment"/>`), '') + '</div>'}

      // Show only image placeholder
      if (status.media_attachments.length != 0) { status.media_attachments = `<span class="text-neutral">[image]</span>`}

			statusHTML = interpolate(statusTemplate, {status});
			anchor.insertAdjacentHTML('beforebegin', statusHTML);
		}
	});
}

// If statuses div is not scrolled to bottom, show button, otherwise hide
statusesContainer.addEventListener('scroll', function(event) {
  // Add safe zone to bottom of div
  if (statusesContainer.scrollTop < statusesContainer.scrollHeight - statusesContainer.offsetHeight - 200) {
    document.getElementById("catch-up").hidden = false;
  }

  else {
    document.getElementById("catch-up").hidden = true;
  }
});

// Add click event to scroll to bottom of div statuses
document.getElementById("catch-up").addEventListener('click', function(event) {
  statusesContainer.scrollTop = statusesContainer.scrollHeight;

  // Hide button
  document.getElementById("catch-up").hidden = true;
});

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
