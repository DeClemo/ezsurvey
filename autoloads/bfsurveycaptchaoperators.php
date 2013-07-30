 <?php
class BFSurveyCaptchaOperators
{
    function BFSurveyCaptchaOperators()
    {
    }

    function operatorList()
    {
        return array( 'recaptcha_form');
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
       return array( 'recaptcha_form' => array() );
                                              
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'recaptcha_form':
            {   
    			//load library
    			include_once( 'extension/ezsurvey/classes/bfrecaptchalib.php' );
    		
    			//get recatcha public key
    			$publicKey = '';
    			$ini = eZINI::instance( 'bfsurveycaptcha.ini' );
    			$publicKeyIni = $ini->variable( 'RecaptchaSetting', 'PublicKey' );
    			$publicKey =  $publicKeyIni;

    			$operatorValue = recaptcha_get_html($publicKey);
                break;
                
            } break;
        }
    }
}
?>
 