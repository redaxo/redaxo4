#!/usr/bin/php
<?php

// modified coding_standards script, only checks line endings and encoding

if (PHP_SAPI !== 'cli') {
    echo 'error: this script may only be run from CLI', PHP_EOL;
    exit(1);
}

stream_set_blocking(STDIN, 0);

// https://github.com/symfony/symfony/blob/f53297681a7149f2a809da12ea3a8b8cfd4d3025/src/Symfony/Component/Console/Output/StreamOutput.php#L103-112
$hasColorSupport = getenv('ANSICON') !== false || DIRECTORY_SEPARATOR != '\\' && function_exists('posix_isatty') && @posix_isatty(STDOUT);

echo PHP_EOL;
echo $hasColorSupport ? "\033[1;37m\033[45m" : '';
echo 'REDAXO CODING STANDARDS CHECK';
echo $hasColorSupport ? "\033[0m" : '';
echo PHP_EOL, PHP_EOL;

if (!isset($argv[1]) || !in_array($argv[1], array('fix', 'check'))) {
    echo 'Usage:
    php ', $argv[0], ' <mode> [options]
    php ', $argv[0], ' <mode> <path> [options]
    php ', $argv[0], ' <mode> package <package> [options]

    <mode>     "check" or "fix"
    <path>     path to a directory or a file
    <package>  package id ("addonname" or "addonname/pluginname")

    options:
        --hide-process: Don\'t show current checking file path', PHP_EOL, PHP_EOL;

    exit(1);
}

class rex_coding_standards_fixer
{
    protected $content;
    protected $fixable = array();
    protected $nonFixable = array();

    public function __construct($content)
    {
        $this->content = $content;

        $this->fix();
    }

    public function hasChanged()
    {
        return !empty($this->fixable) || !empty($this->nonFixable);
    }

    public function getFixable()
    {
        return array_keys($this->fixable);
    }

    public function getNonFixable()
    {
        return array_keys($this->nonFixable);
    }

    public function getResult()
    {
        return $this->content;
    }

    protected function addFixable($fixable)
    {
        $this->fixable[$fixable] = true;
    }

    protected function addNonFixable($nonFixable)
    {
        $this->nonFixable[$nonFixable] = true;
    }

    protected function fix()
    {
        if (($encoding = mb_detect_encoding($this->content, 'UTF-8,ISO-8859-1,WINDOWS-1252')) != 'UTF-8') {
            if ($encoding === false) {
                $encoding = mb_detect_encoding($this->content);
            }
            if ($encoding !== false) {
                $this->content = iconv($encoding, 'UTF-8', $this->content);
                $this->addFixable('fix encoding from ' . $encoding . ' to UTF-8');
            } else {
                $this->addNonFixable('couldn\'t detect encoding, change it to UTF-8');
            }
        } elseif (strpos($this->content, "\xEF\xBB\xBF") === 0) {
            $this->content = substr($this->content, 3);
            $this->addFixable('remove BOM (Byte Order Mark)');
        }

        if (strpos($this->content, "\r") !== false) {
            $this->content = str_replace(array("\r\n", "\r"), "\n", $this->content);
            $this->addFixable('fix line endings to LF');
        }

        /*if (strpos($this->content, "\t") !== false) {
            $this->content = str_replace("\t", '  ', $this->content);
            $this->addFixable('convert tabs to spaces');
        }

        if (preg_match('/ $/m', $this->content)) {
            $this->content = preg_replace('/ +$/m', '', $this->content);
            $this->addFixable('remove trailing whitespace');
        }

        if (strlen($this->content) && substr($this->content, -1) != "\n") {
            $this->content .= "\n";
            $this->addFixable('add newline at end of file');
        }

        if (preg_match("/\n{2,}$/", $this->content)) {
            $this->content = rtrim($this->content, "\n") . "\n";
            $this->addFixable('remove multiple newlines at end of file');
        }*/
    }
}

$fix = $argv[1] == 'fix';

$dir = dirname(Phar::running(false)) ?: __DIR__;
$files = null;
if (isset($argv[2]) && $argv[2][0] !== '-') {
    if ($argv[2] == 'package') {
        if (!isset($argv[3]) || $argv[3][0] === '-') {
            echo 'ERROR: Missing package id!', PHP_EOL, PHP_EOL;
            exit(1);
        }
        $package = $argv[3];
        if (strpos($package, '/') === false) {
            $dir .= DIRECTORY_SEPARATOR . 'redaxo' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . $package;
        } else {
            list($addon, $plugin) = explode('/', $package, 2);
            $dir .= DIRECTORY_SEPARATOR . 'redaxo' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . $addon . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $plugin;
        }
        if (!is_dir($dir)) {
            echo 'ERROR: Package "', $package, '" does not exist!', PHP_EOL, PHP_EOL;
            exit(1);
        }
    } else {
        if (is_dir($argv[2])) {
            $dir = realpath($argv[2]);
        } elseif (is_file($argv[2])) {
            $file = realpath($argv[2]);
            $files = array($file => $file);
            $dir = dirname($file);
        } else {
            echo 'ERROR: Directory or file "', $argv[2], '" does not exist!', PHP_EOL, PHP_EOL;
            exit(1);
        }
    }
} elseif ($input = file('php://stdin', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
    $files = array_flip(array_map('realpath', array_filter($input, 'file_exists')));
}

if (!is_array($files)) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::CURRENT_AS_SELF | RecursiveDirectoryIterator::SKIP_DOTS));
}

$hideProcess = in_array('--hide-process', $argv);
$textExtensions = array('css', 'gitignore', 'htaccess', 'html', 'js', 'json', 'lang', 'markdown', 'md', 'php', /*'sql',*/ 'textile', 'tpl', 'txt', 'yml');
$countFiles = 0;
$countFixable = 0;
$countNonFixable = 0;

foreach ($files as $path => $_n) {
    $subPath = str_replace($dir . DIRECTORY_SEPARATOR, '', $path);
    $fileExt = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($fileExt, $textExtensions) || strpos(DIRECTORY_SEPARATOR . $subPath, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
        continue;
    }

    if (!$hideProcess) {
        $checkString = $subPath;
        if (mb_strlen($checkString) > 60) {
            $checkString = mb_substr($checkString, 0, 20) . '...' . mb_substr($checkString, -37);
        }
        echo $checkString = 'check ' . $checkString . ' ...';
    }

    $countFiles++;
    $fixer = new rex_coding_standards_fixer(file_get_contents($path));

    if (!$hideProcess) {
        echo str_repeat("\010 \010", mb_strlen($checkString));
    }

    if ($fixer->hasChanged()) {
        echo $subPath, ':', PHP_EOL;
        if ($fixable = $fixer->getFixable()) {
            echo '  > ', implode(PHP_EOL . '  > ', $fixable), PHP_EOL;
            $countFixable++;
        }
        if ($nonFixable = $fixer->getNonFixable()) {
            echo '  ! ', implode(PHP_EOL . '  ! ', $nonFixable), PHP_EOL;
            $countNonFixable++;
        }
        echo PHP_EOL;

        if ($fix) {
            file_put_contents($path, $fixer->getResult());
        }
    }
}

echo '-----------------------------------', PHP_EOL;
echo 'checked ', $countFiles, ' files', PHP_EOL;
if ($countFixable) {
    echo '', ($fix ? 'fixed' : 'found fixable'), ' problems in ', $countFixable, ' files', PHP_EOL;
}
if ($countNonFixable) {
    echo 'found non-fixable problems in ', $countNonFixable, ' files', PHP_EOL;
}

echo PHP_EOL;
if ($hasColorSupport) {
    echo ($countNonFixable + ($fix ? 0 : $countFixable)) ? "\033[1;37;41m" : "\033[1;30;42m";
}
echo 'FINISHED, ', !$countFixable && !$countNonFixable ? 'no problems' : 'found problems';
echo $hasColorSupport ? "\033[0m" : '';
echo PHP_EOL, PHP_EOL;

exit ($countNonFixable + ($fix ? 0 : $countFixable));
