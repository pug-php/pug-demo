<?php

$engine = isset($_GET['engine']) ? $_GET['engine'] : 'pug-php';
$inputLanguage = isset($_GET['language']) ? $_GET['language'] : 'jade';
$varsHeight = isset($_GET['vars-height']) ? floatval($_GET['vars-height']) : 33.3;
$vResizeBottom = 13 - $varsHeight * 1.34;
$hasOptions = isset($_GET['options']);
$options = $hasOptions ? @json_decode($_GET['options']) : (object) [];

function getOption($option, $default = null) {
    global $options;

    if (property_exists($options, $option)) {
        return $options->$option;
    }
    
    return $default;
}

if (is_writeable(__FILE__)) {
    shell_exec('chmod -R 0555 .');
}

include_once __DIR__ . '/allow-csrf.php';

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
    #h-resize,
    #v-resize,
    #input,
    #output,
    #preview,
    #vars {
        position: absolute;
        bottom: 20px;
        top: 120px;
    }
    #input,
    #output,
    #preview,
    #vars {
        border-radius: 2px;
        border: 1px solid <?php echo isset($_GET['border']) ? $_GET['border'] : '#232323'; ?>;
        box-sizing: border-box;
    }
    #vars {
        top: auto;
        height: calc(<?php echo $varsHeight; ?>% - 53px);
        left: 20px;
        right: calc(50% + 10px);
    }
    #input {
        bottom: auto;
        height: calc(<?php echo 100 - $varsHeight; ?>% - 106px);
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
    #right-buttons a,
    #options a {
        color: white;
        float: right;
        padding: 10px;
        text-decoration: none;
    }
    #right-buttons a {
        position: absolute;
        top: 120px;
        right: 20px;
        z-index: 2;
    }
    #right-buttons a:hover,
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
    #preview,
    #output {
        right: 20px;
        left: calc(50% + 10px);
    }
    #preview {
        display: none;
        color: gray;
    }
    #preview pre.line-numbers {
        background: gray;
    }
    #preview pre {
        overflow: auto;
    }
    #h-resize {
        left: calc(50% - 7px);
        width: 14px;
        cursor: col-resize;
    }
    #v-resize {
        top: auto;
        bottom: calc(<?php echo $varsHeight . '% ' . ($vResizeBottom < 0 ? '-' : '+') . ' ' . abs($vResizeBottom); ?>px);
        left: 20px;
        right: calc(50% + 7px);
        height: 14px;
        cursor: row-resize;
    }
    <?php if (isset($_GET['hide-output'])) { ?>
        #right-buttons,
        #output {
            display: none;
        }
    <?php } ?>
    <?php if (isset($_GET['embed'])) { ?>
        html,
        body {
            background: transparent;
        }
        #vars {
            height: calc(<?php echo $varsHeight; ?>% - 5px);
            left: 0;
            bottom: 0;
            right: calc(50% + 7px);
        }
        #right-buttons a {
            top: 0;
            right: 0;
        }
        #input {
            top: 0;
            height: calc(<?php echo 100 - $varsHeight; ?>% - 5px);
            left: 0;
            right: calc(50% + 7px);
        }
        #h-resize {
            top: 0;
            bottom: 0;
        }
        #v-resize {
            top: calc(<?php echo 100 - $varsHeight; ?>% - 7px);
            left: 0;
            width: calc(50% - 7px);
        }
        #preview,
        #output {
            top: 0;
            bottom: 0;
            right: 0;
            left: calc(50% + 7px);
        }
        #right-buttons a,
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
            #v-resize,
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
                <td><input type="checkbox" <?php echo getOption('allowMixedIndent', true) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>allowMixinOverride</td>
                <td><input type="checkbox" <?php echo getOption('allowMixinOverride', true) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>classAttribute</td>
                <td><input type="text" value="<?php echo getOption('classAttribute', ''); ?>" onkeyup="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>scopeEachVariables</td>
                <td><input type="text" value="<?php
                    $option = getOption('scopeEachVariables', true);

                    if (is_bool($option)) {
                        $option = $option ? 'true' : 'false';
                    }

                    echo $option;
                    ?>" onkeyup="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>expressionLanguage</td>
                <td><select onchange="convertToPug(event)">
                    <option value="auto" <?php echo getOption('expressionLanguage', 'auto') === 'auto' ? 'selected' : ''; ?>>auto</option>
                    <option value="php" <?php echo getOption('expressionLanguage', 'auto') === 'php' ? 'selected' : ''; ?>>php</option>
                    <option value="js" <?php echo getOption('expressionLanguage', 'auto') === 'js' ? 'selected' : ''; ?>>js</option>
                </select></td>
            </tr>
            <tr>
                <td>prettyprint</td>
                <td><input type="checkbox" <?php echo getOption('prettyprint', true) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>pugjs</td>
                <td><input type="checkbox" <?php echo getOption('pugjs', false) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>restrictedScope</td>
                <td><input type="checkbox" <?php echo getOption('restrictedScope', false) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td>singleQuote</td>
                <td><input type="checkbox" <?php echo getOption('singleQuote', false) ? 'checked' : ''; ?> onclick="convertToPug(event)"></td>
            </tr>
            <tr>
                <td style="border-top: 1px solid rgba(255, 255, 255, 0.5); margin-top: 2px; padding-top: 5px;">mode</td>
                <td style="border-top: 1px solid rgba(255, 255, 255, 0.5); margin-top: 2px; padding-top: 5px;"><select name="mode" onchange="convertToPug(event)">
                    <option value="" <?php echo getOption('mode', '') === '' ? 'selected' : ''; ?>>render</option>
                    <option value="format" <?php echo getOption('mode', '') === 'format' ? 'selected' : ''; ?>>format</option>
                    <option value="compile" <?php echo getOption('mode', '') === 'compile' ? 'selected' : ''; ?>>compile</option>
                    <option value="parse" <?php echo getOption('mode', '') === 'parse' ? 'selected' : ''; ?>>parse</option>
                    <option value="lex" <?php echo getOption('mode', '') === 'lex' ? 'selected' : ''; ?>>lex</option>
                </select></td>
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
} else { ?>[
  'pageTitle' => 'Try Pug.php and never recode HTML again',
  'youAreUsingJade' => true,
]<?php }
?></div><?php }
?>

<div id="output"><?php
if (!isset($_GET['embed'])) { ?>&lt;!DOCTYPE html>
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
<div id="h-resize"></div>
<div id="v-resize"></div>
<div id="preview"></div>
<div id="right-buttons">
    <a href="#" onclick="sources(event)" id="sources-button" style="display: none;">Sources</a>
    <a href="#" onclick="preview(event)" id="preview-button">Preview</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-jade.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-html.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-php.js" type="text/javascript" charset="utf-8"></script>
<?php if (!in_array($inputLanguage, array('jade', 'pug', 'html', 'php', 'css', 'js', 'javascript'))) { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/mode-<?php echo $inputLanguage; ?>.js" type="text/javascript" charset="utf-8"></script>
<?php } ?>
<script>
    if (/phug-lang\.com$/.test(location.host)) {
        document.domain = 'phug-lang.com';
    }
    var modeInput = document.querySelector('select[name="mode"]');
    var lastRequest;
    var saveAs = <?php echo json_encode(urldecode($_GET['save_as'] ?? '')); ?>;
    var lastInput = '';
    var lastTime = 0;
    localStorage || (localStorage = {});
    localStorage.files || (localStorage.files = '{}');
    
    setInterval(function () {
        var time = (new Date()).getTime();
        var files = JSON.parse(localStorage.files);
        Object.keys(files).forEach(function (file) {
            if (file !== saveAs) {
                var value = files[file];
                if (value > lastTime && lastInput.indexOf(file) !== -1) {
                    convertToPug();
                    lastTime = (new Date()).getTime();
                }
            }
        });
    }, 200);

    function convertToPug(e) {
        var xhr;

        if (typeof XMLHttpRequest !== 'undefined') {
            xhr = new XMLHttpRequest();
        } else {
            var versions = [
                'MSXML2.XmlHttp.5.0',
                'MSXML2.XmlHttp.4.0',
                'MSXML2.XmlHttp.3.0',
                'MSXML2.XmlHttp.2.0',
                'Microsoft.XmlHttp'
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

        lastRequest = xhr;

        xhr.onreadystatechange = function () {
            if (xhr.readyState < 4 || xhr !== lastRequest) {
                return;
            }

            if (xhr.status !== 200) {
                return;
            }

            if (xhr.readyState === 4) {
                var session = output.getSession();
                if (modeInput.value === 'format') {
                  session.setMode('ace/mode/php');
                } else if (modeInput.value) {
                  session.setMode('ace/mode/text');
                } else {
                  session.setMode('ace/mode/html');
                }
                output.setValue(xhr.responseText, 1);
                document.getElementById('preview').innerHTML = xhr.responseText;
            }
        };

        var engine = document.getElementById('engine').value || <?php echo json_encode($engine); ?>;
        ['pug-php', 'tale-pug', 'phug'].forEach(function (repository) {
            document.getElementById('version-' + repository).style.display = repository === engine ? 'inline' : 'none';
        });
        var version = document.getElementById('version-' + engine).value;

        var options = '';
        var children = document.querySelectorAll('#options tr');
        for (var index = 0; index < children.length; index++) {
            var name = children[index].querySelector('td').innerHTML;
            if (~['engine', 'version'].indexOf(name)) {
                continue;
            }
            var field = children[index].querySelector('input, select');
            options += '&' + name + '=' + (field.type === 'checkbox'
                ? (field.checked ? '1' : '')
                : field.value
            );
        }

        if (saveAs) {
            var files = JSON.parse(localStorage.files);
            files[saveAs] = (new Date()).getTime();
            localStorage.files = JSON.stringify(files);
        }
        
        lastInput = input.getValue() + ''

        xhr.open('POST', '/api/' + engine + '.php', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            'pug=' + encodeURIComponent(lastInput) +
            '&vars=' + encodeURIComponent(vars ? vars.getValue() : '[]') +
            '&engine=' + encodeURIComponent(engine) +
            '&version=' + encodeURIComponent(version) +
            '&save_as=' + encodeURIComponent(saveAs) +
            options
        );
    }
    
    function exportEmbed() {
        var _engine = document.getElementById('engine').value || <?php echo json_encode($engine); ?>;
        var _input = encodeURIComponent(input.getValue());
        var _vars = encodeURIComponent(vars ? vars.getValue() : '[]');
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
        editor.setAutoScrollEditorIntoView(true);
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

    function preview(e) {
        document.getElementById('preview').style.display = 'block';
        document.getElementById('output').style.display = 'none';
        document.getElementById('sources-button').style.display = 'block';
        document.getElementById('preview-button').style.display = 'none';
        convertToPug(e);
    }

    function sources(e) {
        document.getElementById('preview').style.display = 'none';
        document.getElementById('output').style.display = 'block';
        document.getElementById('sources-button').style.display = 'none';
        document.getElementById('preview-button').style.display = 'block';
        convertToPug(e);
    }

    var input = editor('input', 'ace/mode/<?php echo $inputLanguage; ?>');

    var vars = null;
    if (document.getElementById('vars')) {
        vars = editor('vars', {
            path: 'ace/mode/php',
            inline: true
        });
    }

    var output = editor('output', 'ace/mode/html', true);

    input.getSession().on('change', convertToPug);
    vars && vars.getSession().on('change', convertToPug);

    var dragAndDrop = {v: null, h: null};
    document.getElementById('h-resize').onmousedown = function (e) {
        dragAndDrop.h = {
            x: e.pageX,
            inputWidth: document.getElementById('input').offsetWidth,
            outputWidth: document.getElementById('output').offsetWidth
        };
        e.preventDefault();

        return false;
    };
    document.getElementById('v-resize').onmousedown = function (e) {
        dragAndDrop.v = {
            y: e.pageY,
            inputHeight: document.getElementById('input').offsetHeight,
            varsHeight: document.getElementById('vars').offsetHeight
        };
        e.preventDefault();

        return false;
    };
    var minWidth = 80;
    var minHeight = 60;
    var resize = {
        width: window.innerWidth,
        height: window.innerHeight
    };
    function setInputWidth(inputWidth, outputWidth) {
        resize.inputWidth = inputWidth;
        resize.outputWidth = outputWidth;
        inputWidth = Math.round(inputWidth);
        outputWidth = Math.round(outputWidth);
        document.getElementById('input').style.width = inputWidth + 'px';
        document.getElementById('h-resize').style.left = (<?php echo isset($_GET['embed']) ? 0 : 20; ?> + inputWidth) + 'px';
        document.getElementById('options').style.right = (<?php echo isset($_GET['embed']) ? 0 : 26; ?> + outputWidth + 14) + 'px';
        var vars = document.getElementById('vars');
        if (vars) {
            vars.style.width = inputWidth + 'px';
        }
        document.getElementById('output').style.width = outputWidth + 'px';
    }
    function setInputHeight(inputHeight, varsHeight) {
        resize.inputHeight = inputHeight;
        resize.varsHeight = varsHeight;
        inputHeight = Math.round(inputHeight);
        varsHeight = Math.round(varsHeight);
        document.getElementById('input').style.height = inputHeight + 'px';
        document.getElementById('vars').style.height = varsHeight + 'px';
        document.getElementById('v-resize').style.bottom = (<?php echo isset($_GET['embed']) ? 0 : 20; ?> + varsHeight) + 'px';
    }
    window.onresize = function (e) {
        var width = window.innerWidth;
        var height = window.innerHeight;
        var diffWidth = width - resize.width;
        var diffHeight = height - resize.height;
        if (diffWidth && resize.inputWidth && resize.outputWidth) {
            var deltaH = <?php echo isset($_GET['embed']) ? 14 : 54; ?>;
            var ratioH = (width - deltaH) / (resize.width - deltaH);
            setInputWidth(resize.inputWidth * ratioH, resize.outputWidth * ratioH);
        }
        if (diffHeight && resize.inputHeight && resize.varsHeight) {
            var deltaV = <?php echo isset($_GET['embed']) ? 14 : 154; ?>;
            var ratioV = (height - deltaV) / (resize.height - deltaV);
            setInputHeight(resize.inputHeight * ratioV, resize.varsHeight * ratioV);
        }
        resize.width = width;
        resize.height = height;
    };
    window.onmousemove = function (e) {
        if (dragAndDrop.h) {
            document.getElementById('output').style.left = 'auto';
            var inputWidth = Math.max(minWidth, dragAndDrop.h.inputWidth + e.pageX - dragAndDrop.h.x);
            var outputWidth = dragAndDrop.h.outputWidth - inputWidth + dragAndDrop.h.inputWidth;
            if (outputWidth < minWidth) {
                inputWidth -= minWidth - outputWidth;
                outputWidth = minWidth;
            }
            setInputWidth(inputWidth, outputWidth);
        } else if (dragAndDrop.v) {
            var inputHeight = Math.max(minHeight, dragAndDrop.v.inputHeight + e.pageY - dragAndDrop.v.y);
            var varsHeight = dragAndDrop.v.varsHeight - inputHeight + dragAndDrop.v.inputHeight;
            if (varsHeight < minHeight) {
                inputHeight -= minHeight - varsHeight;
                varsHeight = minHeight;
            }
            setInputHeight(inputHeight, varsHeight);
        }
        window.dispatchEvent(new Event('resize'));
    };
    window.onmouseup = function (e) {
        dragAndDrop = {};
    };

    <?php
    if (isset($_GET['embed']) || $hasOptions) {
        echo 'convertToPug()';
    }
    ?>


    var _paq = _paq || [];
    _paq.push(['setDomains', ['pug-demo.herokuapp.com', 'pug-php-demo-kylekatarn.c9users.io', 'jade-filters.selfbuild.fr', 'pug-filters.selfbuild.fr', 'phug-lang.com', '*.phug-lang.com']]);
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
