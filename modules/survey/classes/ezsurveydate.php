<?php
class eZSurveyDate extends eZSurveyQuestion
{
    /*
     * constructor
     */
    function __construct( $row = false )
    {
        $row[ 'type' ] = 'Date';
        $this->eZSurveyQuestion( $row );
    }

    /*
     * called when a question is created / edited in the admin
     * In this case we only have to save the question text and the mandatory checkbox value
     */
    function processEditActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params[ 'contentobjectattribute_id' ];

        //title of the question
        $postQuestionText = $prefix . '_ezsurvey_question_' . $this->ID . '_text_' . $attributeID;
        if( $http->hasPostVariable( $postQuestionText ) and $http->postVariable( $postQuestionText ) != $this->Text )
        {
            $this->setAttribute( 'text', $http->postVariable( $postQuestionText ) );
        }

        $postQuestionMandatoryHidden = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_hidden_' . $attributeID;
        
        if( $http->hasPostVariable( $postQuestionMandatoryHidden ) )
        {
            $postQuestionMandatory = $prefix . '_ezsurvey_question_' . $this->ID . '_mandatory_' . $attributeID;
            if( $http->hasPostVariable( $postQuestionMandatory ) )
                $newMandatory = 1;
            else
                $newMandatory = 0;

            if( $newMandatory != $this->Mandatory )
                $this->setAttribute( 'mandatory', $newMandatory );
        }
    }

    /*
     * Checks if a date has been selected in the case the question is mandatory
     * If the answer is 'other' this means the actual answer is in the input field, not in the dropdown
     */
    function processViewActions( &$validation, $params )
    {
        $http = eZHTTPTool::instance();
        $variableArray = array();

        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
        $attributeID = $params[ 'contentobjectattribute_id' ];

        $postSurveyAnswer   = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;
        $postCustomAnswer   = $prefix . '_ezsurvey_answer2_' . $this->ID . '_' . $this->contentObjectAttributeID();
        $answer             = $http->postVariable( $postSurveyAnswer, '' );
        
        $postDay = $prefix . '_ezsurvey_answer_day_' . $this->ID . '_' . $this->contentObjectAttributeID();
        $postMonth = $prefix . '_ezsurvey_answer_month_' . $this->ID . '_' . $this->contentObjectAttributeID();
        $postYear = $prefix . '_ezsurvey_answer_year_' . $this->ID . '_' . $this->contentObjectAttributeID();

        if( $http->hasPostVariable( $postDay ) and $http->hasPostVariable( $postMonth ) and $http->hasPostVariable( $postYear ) and ($this->attribute( 'mandatory' ) == 1) )
        {
            $validation['error'] = true;
            $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please enter a datein question %number', null,
                                             array( '%number' => $this->questionNumber() ) ),
                                             'question_number' => $this->questionNumber(),
                                             'code' => 'general_answer_number_as_well',
                                             'question' => $this );
            return false;
        }
        
        $answer = array(
            'day' => $http->variable( $postDay ),
            'month' => $http->variable( $postMonth ),
            'year' => $http->variable( $postYear )
        );
        
        $this->setAnswer( $answer );
        $variableArray[ 'answer' ] = $answer;

        return $variableArray;
    }

    /*
     * Called when a user answers a question on the public side
     */
    function answer()
    {
        if( $this->Answer !== false )
            return $this->Answer;

        $http = eZHTTPTool::instance();
        $prefix = eZSurveyType::PREFIX_ATTRIBUTE;

        $postDay = $prefix . '_ezsurvey_answer_day_' . $this->ID . '_' . $this->contentObjectAttributeID();
        $postMonth = $prefix . '_ezsurvey_answer_month_' . $this->ID . '_' . $this->contentObjectAttributeID();
        $postYear = $prefix . '_ezsurvey_answer_year_' . $this->ID . '_' . $this->contentObjectAttributeID();

        $answer = false;

        if( $http->hasPostVariable( $postDay ) and $http->hasPostVariable( $postMonth ) and $http->hasPostVariable( $postYear ))
        {
            $answer = array(
                'day' => $http->variable( $postDay ),
                'month' => $http->variable( $postMonth ),
                'year' => $http->variable( $postYear )
            );
        }

        return $answer;
    }
}
eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Date' ), 'Date' );
?>
