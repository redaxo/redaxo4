<?php

class rex_form_restrictons_element extends rex_form_select_element
{
  var $chkbox_element;
  
  // 1. Parameter nicht genutzt, muss aber hier stehen,
  // wg einheitlicher Konstrukturparameter
  function rex_form_restrictons_element($tag = '', &$table, $attributes = array())
  {
    global $I18N;
    
    parent::rex_form_select_element('', $table, $attributes);
    
//    $this->chkbox_element = $table->addCheckboxField('restrictions');
    $this->chkbox_element = new rex_form_checkbox_element('', $dummy = null);
    $this->chkbox_element->setAttribute('name', 'enable_restrictions');
    $this->chkbox_element->setAttribute('id', 'enable_restrictions_chkbx');
    $this->chkbox_element->addOption($I18N->msg('minfo_field_label_no_restrictions'), '');
    
    $categorySelect = new rex_category_select();
    $categorySelect->setMultiple(true);
    $this->setSelect($categorySelect);
  }

  function get()
  {
    $slctDivId = $this->getAttribute('id'). '_div';
    
    // Wert aus dem select in die checkbox übernehmen
    $this->chkbox_element->setValue($this->getValue());
    
    $html = '';
    
    $html .= '
    <script type="text/javascript">
    <!--

    jQuery(function($) {

      $("#enable_restrictions_chkbx").click(function() {
        $("#'. $slctDivId .'").slideToggle("slow");
      });
      
      if($("#enable_restrictions_chkbx").is(":checked")) {
        $("#'. $slctDivId .'").hide();
      }

    });

    //-->
    </script>';
    
    $html .= $this->chkbox_element->get();
    
    $html .= '
    <!-- DIV nötig fuer JQuery slideIn -->
    <div id="'. $slctDivId .'">
      '. parent::get() .'
    </div>';
    
    return $html;
  }
  
}