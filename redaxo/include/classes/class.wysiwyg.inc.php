<?php
class rex_wysiwyg_editor {

    var $id;
    var $content;
    var $width = '';
    var $height = '';
    var $stylesheet = '';
    var $styles = '';
    var $lang = '';
    var $plugins = 'redaxo_default';
    var $theme = '';
    var $buttonrow1 = '';
    var $buttonrow2 = '';
    var $buttonrow3 = 'empty';
    var $buttonrow4 = 'empty';

    function show(){
        echo MEDIA_HTMLAREA(
                $this->id,
                $this->content,
                $this->width,
                $this->height,
                $this->stylesheet,
                $this->styles,
                $this->lang,
                $this->buttonrow1,
                $this->buttonrow2,
                $this->buttonrow3,
                $this->buttonrow4,
                $this->plugins,
                $this->theme
        );
    }

}
?>