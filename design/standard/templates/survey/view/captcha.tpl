<div class="survey-choices">
   <label>{$question.question_number}. {$question.text|wash('xhtml')} {if $question.mandatory}<strong class="required">*</strong>{/if}</label>
    {recaptcha_form()}	
   <input type="hidden" name="{$prefix_attribute}_ezsurvey_answer_{$question.id}_{$attribute_id}" value="">
 </div>