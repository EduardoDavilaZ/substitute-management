<?php

/**
 * Global registry for asset stacks (scripts, styles, etc.)
 * @var array<string, array<int, string>>
 */
$GLOBALS['_stacks'] = [];

/**
 * Pushes content into a specific stack.
 * @param string $stack The name of the stack (e.g., 'scripts').
 * @param string $content The HTML string to be stored.
 * @return void
 */
function push(string $stack, string $content): void
{
    if (!isset($GLOBALS['_stacks'][$stack])) {
        $GLOBALS['_stacks'][$stack] = [];
    }
    $GLOBALS['_stacks'][$stack][] = $content;
}

/**
 * Renders and echoes all content within a specific stack.
 * @param string $stack The name of the stack to render.
 * @return void
 */
function stack(string $stack): void
{
    if (!empty($GLOBALS['_stacks'][$stack])) {
        echo implode("\n", $GLOBALS['_stacks'][$stack]);
    }
}

function push_js(string $path, string $type = 'js', string $attrs = ''): void
{
    push('scripts', js($path, $type, $attrs));
}

function push_css(string $path, string $type = 'css', string $attrs = ''): void
{
    push('styles', css($path, $type, $attrs));
}

?>