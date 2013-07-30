<?php
class eZSurveyCaptcha extends eZSurveyQuestion
{
  /*
   * constructor
   */
  function __construct( $row = false )
  {
     $row[ 'type' ] = 'Captcha';
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
     * Checks if a captcha has been provided in the case the question is mandatory
     */
   function processViewActions( &$validation, $params )
   {

       $http = eZHTTPTool::instance();
       $variableArray = array();
 
       $prefix = eZSurveyType::PREFIX_ATTRIBUTE;
       $attributeID = $params[ 'contentobjectattribute_id' ];
 
       $postSurveyAnswer = $prefix . '_ezsurvey_answer_' . $this->ID . '_' . $attributeID;

       if ( $this->attribute( 'mandatory' ) == 1 )
       {
            
          $captcha_valid = false;

          //check for captcha form field
          if( $http->hasPostVariable( 'recaptcha_response_field' ) && $http->hasPostVariable( 'recaptcha_challenge_field' ))
          {
            
            include_once( 'extension/ezsurvey/classes/bfrecaptchalib.php' );
            $ini = eZINI::instance( 'bfsurveycaptcha.ini' );
            $privateKey = $ini->variable( 'RecaptchaSetting', 'PrivateKey' );

            $captcha_answer = trim($http->postVariable( 'recaptcha_response_field' ));
            $captcha_challenge = $http->postVariable( 'recaptcha_challenge_field' ) ;
            $server_addr = $_SERVER["REMOTE_ADDR"];            
            
            //request validation if populated
            if($privateKey && $captcha_answer && $captcha_challenge && $server_addr) {          

              $captcha_resp = recaptcha_check_answer ($privateKey,
                          $server_addr,
                          $captcha_challenge,
                          $captcha_answer
                          );
              if ($captcha_resp->is_valid) {
                //echo "You got it!";
                $captcha_valid = true;
              } else {
                # set the error code so that we can display it
                //SKIP $captcha_error = $captcha_resp->error;
              }

            }   

          }

           if( !$captcha_valid )
           {
               $validation['error'] = true;
               $validation['errors'][] = array( 'message' => ezpI18n::tr( 'survey', 'Please re-enter the captcha value', null,
                                                array( '%number' => $this->questionNumber() ) ),
                                                'question_number' => $this->questionNumber(),
                                                'code' => 'general_answer_number_as_well',
                                                'question' => $this );
               return false;
           }
       }

      //SKIP saving: $this->setAnswer( $http->postVariable( $postSurveyAnswer, '' ) );
      //SKIP saving: $variableArray[ 'answer' ] = $http->postVariable( $postSurveyAnswer, '' );
 
       return $variableArray;
   }
 
   /*
     * called when a user answers a question on the public side
     */
   function answer()
   {
      /* answer is not stored */      
       return false;
   }
}
eZSurveyQuestion::registerQuestionType( ezpI18n::tr( 'survey', 'Captcha' ), 'Captcha' );
?>