<?php
$engine = isset($_GET['engine']) ? $_GET['engine'] : 'pug-php';
?><!DOCTYPE html>
<html lang="en">
<head>
<title>Try Pug.php and never recode HTML again</title>
<style type="text/css" media="screen">
    html,
    body {
        padding: 0;
        margin: 0;
        font-family: sans-serif;
        background: #454545;
        color: #c9c9c9;
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
    h1 select {
        width: 220px;
        font-size: 32px;
        padding: 4px;
        border: 1px solid #232323;
        background: #454545;
        color: #c9c9c9;
        border-radius: 2px;
    }
    aside {
        float: right;
        padding-right: 20px;
    }
    aside a {
        color: #90a0ff;
        text-decoration: none;
    }
    aside a:hover {
        text-decoration: underline;
    }
    sup {
        color: gray;
    }
    #input,
    #output,
    #vars {
        position: absolute;
        bottom: 20px;
        top: 120px;
        border-radius: 2px;
        border: 1px solid <?php echo isset($_GET['border']) ? $_GET['border'] : '#232323'; ?>;
        box-sizing: border-box;
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
    #options {
        position: absolute;
        top: 120px;
        height: 0;
        right: calc(50% + 10px);
        overflow: visible;
        z-index: 8;
    }
    #options a {
        color: white;
        float: right;
        padding: 10px;
        text-decoration: none;
    }
    #options a:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    #options .list {
        display: none;
        clear: right;
        color: white;
        background: <?php echo isset($_GET['options-color']) ? $_GET['options-color'] : 'rgba(75, 75, 75, 0.8)'; ?>;
        padding: 5px;
    }
    #options input[type="text"],
    #options input[type="number"],
    #options select {
        width: 60px;
        box-sizing: border-box;
    }
    #output {
        right: 20px;
        left: calc(50% + 10px);
    }
    <?php if (isset($_GET['embed'])) { ?>
        html,
        body {
            background: transparent;
        }
        #vars {
            height: calc(33% - 5px);
            left: 0;
            bottom: 0;
            right: calc(50% + 7px);
        }
        #input {
            top: 0;
            height: calc(66% - 5px);
            left: 0;
            right: calc(50% + 7px);
        }
        #output {
            top: 0;
            bottom: 0;
            right: 0;
            left: calc(50% + 7px);
        }
        #options a {
            color: gray;
            background: rgba(0, 0, 0, 0.1);
        }
        #options {
            top: 0;
            height: 0;
            right: calc(50% + 7px);
        }
        <?php if (isset($_GET['hide-vars'])) { ?>
            #vars {
                display: none;
            }
            #input {
                height: 100%;
            }
        <?php } ?>
    <?php } ?>
</style>
<link rel="icon" href="/favicon.ico">
</head>
<body>

<?php if (!isset($_GET['embed'])) { ?>
    <h1>
        Try
        <select id="engine" onchange="convertToPug(event)">
            <option value="pug-php"<?php if ($engine === 'pug-php') { ?> selected="selected"<?php } ?>>Pug-php</option>
            <option value="tale-pug"<?php if ($engine === 'tale-pug') { ?> selected="selected"<?php } ?>>Tale-pug</option>
            <option value="phug"<?php if ($engine === 'phug') { ?> selected="selected"<?php } ?>>Phug</option>
        </select>
        <select id="version-pug-php" onchange="convertToPug(event)"<?php if ($engine !== 'pug-php') { ?> style="display: none;"<?php } ?>>
            <?php include __DIR__ . '/var/cache/pug-php-versions-options.html'; ?>
        </select>
        <select id="version-tale-pug" onchange="convertToPug(event)"<?php if ($engine !== 'tale-php') { ?> style="display: none;"<?php } ?>>
            <?php include __DIR__ . '/var/cache/tale-pug-versions-options.html'; ?>
        </select>
        <select id="version-phug" onchange="convertToPug(event)"<?php if ($engine !== 'phug') { ?> style="display: none;"<?php } ?>>
            <?php include __DIR__ . '/var/cache/phug-versions-options.html'; ?>
        </select>
    </h1>
    
    <aside><a href="http://pug-filters.selfbuild.fr/">Pug.php filters</a></aside>
    
    <p>Type code in the left-hand panel to test Pug.php render.</p>
<?php } ?>

<div id="options">
    <a href="#" onclick="toggleOptions(this)">Options</a>
    <div class="list">
        <table>
            <?php if (isset($_GET['embed'])) { ?>
                <tr>
                    <td>engine</td>
                    <td>
                        <select id="engine" onchange="convertToPug(event)">
                            <option value="pug-php"<?php if ($engine === 'pug-php') { ?> selected="selected"<?php } ?>>Pug-php</option>
                            <option value="tale-pug"<?php if ($engine === 'tale-pug') { ?> selected="selected"<?php } ?>>Tale-pug</option>
                            <option value="phug"<?php if ($engine === 'phug') { ?> selected="selected"<?php } ?>>Phug</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>version</td>
                    <td>
                        <select id="version-pug-php" onchange="convertToPug(event)"<?php if ($engine !== 'pug-php') { ?> style="display: none;"<?php } ?>>
                            <?php include __DIR__ . '/var/cache/pug-php-versions-options.html'; ?>
                        </select>
                        <select id="version-tale-pug" onchange="convertToPug(event)"<?php if ($engine !== 'tale-php') { ?> style="display: none;"<?php } ?>>
                            <?php include __DIR__ . '/var/cache/tale-pug-versions-options.html'; ?>
                        </select>
                        <select id="version-phug" onchange="convertToPug(event)"<?php if ($engine !== 'phug') { ?> style="display: none;"<?php } ?>>
                            <?php include __DIR__ . '/var/cache/phug-versions-options.html'; ?>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>allowMixedIndent</td>
                <td><input type="checkbox" checked onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>allowMixinOverride</td>
                <td><input type="checkbox" checked onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>classAttribute</td>
                <td><input type="text" onkeyup="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>expressionLanguage</td>
                <td><select onchange="convertToPug(event)">
                    <option value="auto" selected>auto</option>
                    <option value="php">php</option>
                    <option value="js">js</option>
                </select</td>
            </tr>
            <tr>
                <td>indentChar</td>
                <td><input type="text" value=" " onkeyup="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>indentSize</td>
                <td><input type="number" value="2" onkeyup="convertToPug(event)" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>keepBaseName</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>keepNullAttributes</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>phpSingleLine</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>prettyprint</td>
                <td><input type="checkbox" checked onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>pugjs</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>restrictedScope</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>singleQuote</td>
                <td><input type="checkbox" onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td style="border-top: 2px solid white;">compileOnly</td>
                <td style="border-top: 2px solid white;"><input type="checkbox" onclick="convertToPug(event)" name="compileOnly"></td>
            </tr>
        </table>
        <?php if (isset($_GET['export'])) { ?>
            <button onclick="exportEmbed()" style="width: 100%;">Export</button>
        <?php } ?>
    </div>
</div>

<div id="input"><?php if (isset($_GET['embed'])) {
    echo isset($_GET['input']) ? $_GET['input'] : '';
} else { ?>doctype html
html(lang="en")
  head
    title= pageTitle
    script(type='text/javascript').
      if (foo) {
         bar(1 + 5)
      }
  body
    h1= pageTitle
    #container.col
      if youAreUsingJade
        p You are amazing
      else
        p Get on it!
      p.
        Pug.php is PHP port Pug (JS)
        the node template engine
        (previously named Jade).<?php }
?></div>
<?php if (!isset($_GET['hide-vars'])) {
?><div id="vars"><?php if (isset($_GET['embed'])) {
    echo isset($_GET['vars']) ? $_GET['vars'] : '';
} else { ?>array(
  'pageTitle' => 'Try Pug.php and never recode HTML again',
  'youAreUsingJade' => true,
)<?php }
?></div><?php }
?>

<div id="output"><?php if (!isset($_GET['embed'])) { ?>&lt;!DOCTYPE html>
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
    &lt;h1>Try Pug.php and never recode HTML again&lt;/h1>
    &lt;div id="container" class="col">
      &lt;p>You are amazing&lt;/p>
      &lt;p>
        Pug.php is PHP port of Pug (JS)
        the node template engine
        (previously named Jade).
      &lt;/p>
    &lt;/div>
  &lt;/body>
&lt;/html><?php } ?></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-jade.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-html.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-php.js" type="text/javascript" charset="utf-8"></script>
<script>
    var compileOnlyInput = document.querySelector('input[name="compileOnly"]');

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
                var session = output.getSession();
                if (compileOnlyInput.checked) {
                  session.setMode("ace/mode/php");
                } else {
                  session.setMode("ace/mode/html");
                }
                output.setValue(xhr.responseText);
            }
        };

        var engine = document.getElementById('engine').value || <?php echo json_encode($engine); ?>;
        ['pug-php', 'tale-pug', 'phug'].forEach(function (repository) {
            document.getElementById('version-' + repository).style.display = repository === engine ? 'inline' : 'none';
        });
        var version = document.getElementById('version-' + engine).value;

        var options = '';
        var children = document.querySelectorAll('#options tr');
        for (var i = 0; i < children.length; i++) {
            var name = children[i].querySelector('td').innerHTML;
            if (~['engine', 'version'].indexOf(name)) {
                continue;
            }
            var field = children[i].querySelector('input, select');
            options += '&' + name + '=' + (field.type === 'checkbox'
                ? (field.checked ? '1' : '')
                : field.value
            );
        }

        xhr.open('POST', '/api/' + engine + '.php', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            'pug=' + encodeURIComponent(input.getValue()) +
            '&vars=' + encodeURIComponent(vars.getValue()) +
            '&engine=' + encodeURIComponent(engine) +
            '&version=' + encodeURIComponent(version) +
            options
        );
    }
    
    function exportEmbed() {
        var _engine = document.getElementById('engine').value || <?php echo json_encode($engine); ?>;
        var _input = encodeURIComponent(input.getValue());
        var _vars = encodeURIComponent(vars.getValue());
        var link = '/?embed' +
            '&theme=xcode'+
            '&border=silver' +
            '&options-color=rgba(120,120,120,0.5)' +
            '&engine=' + _engine +
            '&input=' + _input +
            '&vars=' + _vars;

        window.open(link);
    }

    function editor(id, mode, readonly) {
        /* global ace */
        var editor = ace.edit(id);
        editor.setTheme("ace/theme/<?php echo isset($_GET['theme']) ? $_GET['theme'] : 'monokai' ?>");
        var session = editor.getSession();
        session.setMode(mode);
        session.setTabSize(2);
        editor.setShowPrintMargin(false);
        if (readonly) {
            editor.setReadOnly(true);
        }

        return editor;
    }

    function toggleOptions(link) {
        var list = document.querySelector('#options .list');
        link.innerHTML = list.style.display === 'block' ? 'Options' : 'Fermer';
        list.style.display = list.style.display === 'block' ? '' : 'block';
    }

    var input = editor("input", "ace/mode/jade");

    var vars = editor("vars", {
        path:"ace/mode/php",
        inline: true
    });

    var output = editor("output", "ace/mode/html", true);

    input.getSession().on('change', convertToPug);
    vars.getSession().on('change', convertToPug);
    
    <?php
    if (isset($_GET['embed'])) {
        echo 'convertToPug()';
    }
    ?>


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
