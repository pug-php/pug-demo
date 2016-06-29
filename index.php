<!DOCTYPE html>
<html lang="en">
<head>
<title>Try Pug.php and never recode HTML again</title>
<style type="text/css" media="screen">
    html, body {
        padding: 0;
        margin: 0;
        font-family: sans-serif;
        background: #c9c9c9;
    }
    aisde,
    h1,
    p {
        padding: 0;
        margin: 20px;
    }
    h1 {
        font-weight: normal;
        text-align: center;
    }
    p {
        color: #454545;
    }
    aside {
        float: right;
        padding-right: 20px;
    }
    aside a {
        color: #2030c0;
        text-decoration: none;
    }
    aside a:hover {
        text-decoration: underline;
    }
    #input,
    #output,
    #vars {
        position: absolute;
        bottom: 20px;
        top: 120px;
        box-shadow: -1px -1px 1px #848484;
    }
    #vars {
        top: auto;
        height: calc(33% - 53px);
        left: 20px;
        right: calc(50% + 10px);
    }
    #input {
        bottom: auto;
        height: calc(66% - 106px);
        left: 20px;
        right: calc(50% + 10px);
    }
    #output {
        right: 20px;
        left: calc(50% + 10px);
    }
</style>
<link rel="icon" href="/favicon.ico">
</head>
<body>
    
<h1>Pug.php demonstration</h1>

<aside><a href="http://pug-filters.selfbuild.fr/">Pug.php filters</a></aside>

<p>Type code in the left-hand panel to test Pug.php render.</p>

<div id="input">doctype html
html(lang="en")
  head
    title= pageTitle
    script(type='text/javascript').
      if (foo) {
         bar(1 + 5)
      }
  body
    h1 Jade - node template engine
    #container.col
      if youAreUsingJade
        p You are amazing
      else
        p Get on it!
      p.
        Jade is a terse and simple
        templating language with a
        strong focus on performance
        and powerful features.</div>

<div id="vars">array(
  'pageTitle' => 'Try Pug.php and never recode HTML again',
  'youAreUsingJade' => true,
)</div>

<div id="output">&lt;!DOCTYPE html>
&lt;html lang="en">
  &lt;head>
    &lt;title>Try Pug.php and never recode HTML again&lt;/title>
    &lt;script type="text/javascript">
      if (foo) {
         bar(1 + 5)
      }
    &lt;/script>
  &lt;/head>
  &lt;body>
    &lt;h1>Pug.php is PHP port Pug (JS) the node template engine (previously named Jade)&lt;/h1>
    &lt;div id="container" class="col">
      &lt;p>You are amazing&lt;/p>
      &lt;p>
        Pug is a terse and simple
        templating language with a
        strong focus on performance
        and powerful features.
      &lt;/p>
    &lt;/div>
  &lt;/body>
&lt;/html></div>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-jade.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-html.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-php.js" type="text/javascript" charset="utf-8"></script>
<script>
    function convertToPug(e) {
        var xhr;

        if (typeof XMLHttpRequest !== 'undefined') {
            xhr = new XMLHttpRequest();
        } else {
            var versions = [
                "MSXML2.XmlHttp.5.0", 
                "MSXML2.XmlHttp.4.0",
                "MSXML2.XmlHttp.3.0", 
                "MSXML2.XmlHttp.2.0",
                "Microsoft.XmlHttp"
            ];
            for (var i = 0, len = versions.length; i < len; i++) {
                try {
                    /* global ActiveXObject */
                    xhr = new ActiveXObject(versions[i]);
                    break;
                }
                catch (e) {}
             }
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState < 4) {
                return;
            }
             
            if (xhr.status !== 200) {
                return;
            }

            if (xhr.readyState === 4) {
                output.setValue(xhr.responseText);
            }           
        };
         
        xhr.open('POST', '/api/', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            'pug=' + encodeURIComponent(input.getValue()) +
            '&vars=' + encodeURIComponent(vars.getValue())
        );
    }
    
    function editor(id, mode, readonly) {
        /* global ace */
        var editor = ace.edit(id);
        editor.setTheme("ace/theme/monokai");
        var session = editor.getSession();
        session.setMode(mode);
        session.setTabSize(2);
        editor.setShowPrintMargin(false);
        if (readonly) {
            editor.setReadOnly(true);
        }

        return editor;
    }

    var input = editor("input", "ace/mode/jade");

    var vars = editor("vars", {
        path:"ace/mode/php",
        inline: true
    });

    var output = editor("output", "ace/mode/html", true);

    input.getSession().on('change', convertToPug);
    vars.getSession().on('change', convertToPug);
</script>

<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["setDomains", ["*.pug-php-demo-kylekatarn.c9users.io","*.jade-filters.selfbuild.fr","*.pug-filters.selfbuild.fr"]]);
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//piwik.selfbuild.fr/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 18]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//piwik.selfbuild.fr/piwik.php?idsite=18" style="border:0;" alt="" /></p></noscript>

</body>
</html>