<!DOCTYPE html>
<html lang="en">
<head>
<title>ACE in Action</title>
<style type="text/css" media="screen">
    html, body {
        padding: 0;
        margin: 0;
        font-family: sans-serif;
    }
    h1 {
        font-weight: normal;
        text-align: center;
        padding: 0;
        margin: 20px;
    }
    p {
        color: #454545;
        padding: 0;
        margin: 20px;
    }
    #input, #output, #vars {
        position: absolute;
        bottom: 20px;
        top: 120px;
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
</head>
<body>
    
<h1>Pug.php demonstration</h1>

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
    &lt;h1>Jade - node template engine&lt;/h1>
    &lt;div id="container" class="col">
      &lt;p>You are amazing&lt;/p>
      &lt;p>
        Jade is a terse and simple
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
    /* global ace */

    function convertToPug(e) {
        var xhr;

        if(typeof XMLHttpRequest !== 'undefined') {
            xhr = new XMLHttpRequest();
        } else {
            var versions = ["MSXML2.XmlHttp.5.0", 
                            "MSXML2.XmlHttp.4.0",
                            "MSXML2.XmlHttp.3.0", 
                            "MSXML2.XmlHttp.2.0",
                            "Microsoft.XmlHttp"];

            for(var i = 0, len = versions.length; i < len; i++) {
                try {
                    /* global ActiveXObject */
                    xhr = new ActiveXObject(versions[i]);
                    break;
                }
                catch(e){}
             }
        }

        xhr.onreadystatechange = function () {
            if(xhr.readyState < 4) {
                return;
            }
             
            if(xhr.status !== 200) {
                return;
            }

            if(xhr.readyState === 4) {
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

    var input = ace.edit("input");
    input.setTheme("ace/theme/monokai");
    input.getSession().setMode("ace/mode/jade");

    var vars = ace.edit("vars");
    vars.setTheme("ace/theme/monokai");
    vars.getSession().setMode({
        path:"ace/mode/php",
        inline: true
    });

    var output = ace.edit("output");
    output.setTheme("ace/theme/monokai");
    output.getSession().setMode("ace/mode/html");
    output.setReadOnly(true);

    input.getSession().on('change', convertToPug);
    vars.getSession().on('change', convertToPug);
</script>
</body>
</html>