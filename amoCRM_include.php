<?php
/**
 * Created by PhpStorm.
 * User: Dima
 * Date: 21.12.2015
 * Time: 10:26
 */
# ��������� (((

define('AMOCRM_LOGIN', 'nikolaev@textiloptom.net');
define('AMOCRM_SUBDOMAIN', 'domitex');
define('AMOCRM_API_KEY', '7a3aeaabf4501e6d6545140e5600b079');

define('AMOCRM_CONTACT_PHONE_CSTFID', 600364); # id ���� ������� � ��������
define('AMOCRM_CONTACT_PHONE_CSTFTYPE', 'WORK'); # ��� ���� ������� � ��������

define('AMOCRM_CONTACT_EMAIL_CSTFID', 600366); # id ���� Email � ��������
define('AMOCRM_CONTACT_EMAIL_CSTFTYPE', 'WORK'); # ��� ���� Email � ��������

define('AMOCRM_CONTACT_LGNt_CSTFID', 651536); # id ���� "����� textiloptom" � ��������
define('AMOCRM_CONTACT_LGNs_CSTFID', 651538); # id ���� "����� sailid" � ��������
define('AMOCRM_CONTACT_LGNa_CSTFID', 651540); # id ���� "����� API" � ��������

define('AMOCRM_COMPANY_INN_CSTFID', 652037); # id ���� "���" � ��������
define('AMOCRM_COMPANY_FULLN_CSTFID', 652039); # id ���� "������ ������������" � ��������
define('AMOCRM_COMPANY_YURADDR_CSTFID', 648716); # id ���� "����������� �����" � ��������
define('AMOCRM_COMPANY_DFIO_CSTFID', 652041); # id ���� "��� ������������" � ��������
define('AMOCRM_COMPANY_SITE_CSTFID', 600368); # id ���� "Web" � ��������

define('AMOCRM_TASKTYPECALL_ID', 1); # id ���� ������ "������"
define('AMOCRM_LOG_FILE', 'amolog.txt'); # ������ �����
define('AMOCRM_LOG_FILE2', 'amolog2.txt'); # ������ �����

# ������������� (((
define('AMOCRM_ID_FIXED_RESPONSIBLE', 594474);
//id ������������� 624678 - �������
$arrAmocrmIdsResponsible = array (591174, 591192, 594459, 594462, 594480, 594486, 594489, 591168);
//���������� ������������ �� 594492 - ��������, �������� - 628743
$arrAmocrmIdsResponsibleDisable = array (594492, 628743);
# ))) �������������

$strAmocrmFwritePath = $_SERVER['DOCUMENT_ROOT']. '';

$strAmocrmCookieFile = $strAmocrmFwritePath . '\\cookies.txt';

# ))) ���������

# ���� - �������

#-----------
function fncGetIdNextResponsible($arrIdsRspnsible, $strFwrtPath) {
    //������� ��������� ��������� ���������� �������������
    $strFileFullName = $strFwrtPath . 'idLatestResp.txt';

    if (is_writable($strFileFullName)) {
        //���� ���� �������� ��� ������ - �� ����� ��������� ������� ������ ��������� �� ������� ����������� � ���������� � ��������� ������ ���������� � ������� � ����
        $idNextResp = NULL;

        if (
        count($arrIdsRspnsible)
        ) {
            if (
                1 == count($arrIdsRspnsible)
            ) {
                $idNextResp = $arrIdsRspnsible[0];
            } # if
            else {

                $idLatestResp = NULL;
                //�������� ID ���������� ��������� �� �����
                if (
                is_file($strFileFullName)
                ) {
                    $strTmp = @file_get_contents($strFileFullName);
                    if (
                        $strTmp !== FALSE
                    ) {
                        $idLatestResp = $strTmp;
                    } # if
                } # if

                if (
                ! isset ($idLatestResp)
                ) {
                    $idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
                } # if
                else {
                    $kFound = NULL;
                    foreach ( $arrIdsRspnsible as $k => $v ) {
                        if (
                            $v == $idLatestResp
                        ) {
                            $kFound = $k;
                        } # if
                    } # foreach

                    if (
                    ! isset ($kFound)
                    ) {
                        $idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
                    } # if
                    else {
                        if (
                        ! isset ($arrIdsRspnsible[1+$kFound])
                        ) {
                            $idNextResp = $arrIdsRspnsible[0];
                        } # if
                        else {
                            $idNextResp = $arrIdsRspnsible[1+$kFound];
                        } # else
                    } # else

                } # else

            } # else
        } # if

        if (
        isset ($idNextResp)
        ) {
            //����� � ���� ����������
            @file_put_contents($strFileFullName, $idNextResp);
        } # if
    } else {
        //���� ������ �������� � ���� ���������� ��������� - �� ������� ���������� �� �������
        $idNextResp = NULL;

        if (
        count($arrIdsRspnsible)
        ) {
            if (
                1 == count($arrIdsRspnsible)
            ) {
                $idNextResp = $arrIdsRspnsible[0];
            } else {
                $idNextResp = $arrIdsRspnsible[array_rand($arrIdsRspnsible)];
            }
        }
        //����� ������ ������� ��������� ����� ���������
    } //else

    return $idNextResp;

} # function
#-----------

#-----------
function fncAmocrmAuth($strLogin, $strSubdomain, $strApiKey, $strCookieFileName) {

    # ����� copy-paste �� ������������ (((

    #������ � �����������, ������� ����� �������� ������� POST � API �������
    $user=array(
        'USER_LOGIN'=>$strLogin, #��� ����� (����������� �����)
        'USER_HASH'=>$strApiKey #��� ��� ������� � API (�������� � ������� ������������)
    );

    $subdomain=$strSubdomain; #��� ������� - ��������

    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #������� HTTP-��� ������ �������
    curl_close($curl); #��������� ����� cURL

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    $Response=$Response['response'];
    if(isset($Response['auth'])) #���� ����������� �������� � �������� "auth"
        return array (
            'boolOk' => TRUE,
        );
    return array (
        'boolOk' => FALSE,
        'strErrDevelopUtf8' => 'AmoCRM error: ' . '����������� �� �������',
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

#-----------
function fncAmocrmContactsSet(
    $strSubdomain,
    $strCookieFileName,
    $arrContactsSet,
    $addORupdate # 'add' ��� 'update'
) {

    # ����� copy-paste �� ������������ (((

    $contacts['request']['contacts'][$addORupdate] = $arrContactsSet;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

#-----------
function fncAmocrmContactsListByResponsibleID(
    $strSubdomain,
    $strCookieFileName,
    $strresponsibleid = ''
) {
    //example - domitex.amocrm.ru/private/api/v2/json/contacts/list?responsible_user_id=628743
    # ����� copy-paste �� ������������ (((

    $subdomain = $strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $strresponsibleid != ''
    ) {
        $link .= '?responsible_user_id=' . urlencode($strresponsibleid);
    } # if

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------
#-----------
function fncAmocrmContactsList(
    $strSubdomain,
    $strCookieFileName,
    $query = ''
) {

    # ����� copy-paste �� ������������ (((

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $query != ''
    ) {
        $link .= '?query=' . urlencode($query);
    } # if

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------
#-----------�������� ����� ����� ���������� � ��������
function fncAmocrmContactsGet(
    $strSubdomain,
    $strCookieFileName,
    $query11512 = ''
) {
    //mail("rsdim@rambler.ru","Subj hook started","query11512".$query11512);
    # ����� copy-paste �� ������������ (((

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/links';
    if (
        $query11512 != ''
    ) {
        $link .= '?deals_link=' . urlencode($query11512);
    } # if

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------
//�������� ������� �� id ��������
function fncAmocrmContactsListById(
    $strSubdomain,
    $strCookieFileName,
    $query1512 = ''
) {

    # ����� copy-paste �� ������������ (((

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list';
    if (
        $query1512 != ''
    ) {
        $link .= '?id=' . urlencode($query1512);
    } # if
    mail("rsdim@rambler.ru","Subj hook started  3.3 fncAmocrmContactsListById","query1512: ".$query1512." link:".$link);
    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $strout=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    mail("rsdim@rambler.ru","Subj hook started  3.4 fncAmocrmContactsListById","query1512: ".$query1512." link:!".$link."! code:".$code);
    $code=(int)$code;
    //$resout = gettype($out);
    @file_put_contents("curl.txt",$strout);
    //$out2 = quotemeta($out);
    // --- 628 - 600,500,525 -> 530
    $outpos = strpos($strout,'"custom_fields":[{');
    $outpos = $outpos - 1;
    $out2 = substr($strout, 0, $outpos);
    $out2 .= '}]}}';
    //$out2 = strlen($out);
    mail("rsdim@rambler.ru","Subj hook started  3.5 fncAmocrmContactsListById","query1512: ".$query1512." link:!".$link."! Out:".$out2);
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */


    $Response=json_decode($out2,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------
#-----------
function fncAmocrmLeadsCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrLeadsCreate
) {

    # ����� copy-paste �� ������������ (((
    $leads['request']['leads']['add'] = $arrLeadsCreate;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

#-----------
function fncAmocrmLeadsUpdate(
    $strSubdomain,
    $strCookieFileName,
    $arrLeadsCreate
) {

    # ����� copy-paste �� ������������ (((
    $leads['request']['leads']['update'] = $arrLeadsCreate;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------
function fncAmocrmLeadsGetById(
    $strSubdomain,
    $strCookieFileName,
    $leadid1712
) {

    # ����� copy-paste �� ������������ (((

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list';
    if (
        $leadid1712 != ''
    ) {
        $link .= '?id=' . urlencode($leadid1712);
    } # if

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

} # function
#-----------

#-----------
function fncAmocrmTasksCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrTasksCreate
) {

    # ����� copy-paste �� ������������ (((

    $tasks['request']['tasks']['add'] = $arrTasksCreate;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($tasks));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

#-----------
function fncAmocrmNotesCreate(
    $strSubdomain,
    $strCookieFileName,
    $arrNotesCreate
) {

    # ����� copy-paste �� ������������ (((

    $notes['request']['notes']['add']= $arrNotesCreate;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/notes/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($notes));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

#-----------
function fncAmocrmCompaniesSet(
    $strSubdomain,
    $strCookieFileName,
    $arrCompaniesSet,
    $addORupdate # 'add' ��� 'update'
) {

    # ����� copy-paste �� ������������ (((

    $companies['request']['contacts'][$addORupdate] = $arrCompaniesSet;

    $subdomain=$strSubdomain; #��� ������� - ��������
    #��������� ������ ��� �������
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/company/set';

    $curl=curl_init(); #��������� ���������� ������ cURL
    #������������� ����������� ����� ��� ������ cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($companies));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR, $strCookieFileName); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

    $out=curl_exec($curl); #���������� ������ � API � ��������� ����� � ����������
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    $code=(int)$code;
    $errors=array(
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    );
    try
    {
        #���� ��� ������ �� ����� 200 ��� 204 - ���������� ��������� �� ������
        if($code!=200 && $code!=204)
            return array (
                'boolOk' => FALSE,
                'strErrDevelopUtf8' => 'AmoCRM error: ' . (isset($errors[$code]) ? $errors[$code] : 'Undescribed error ' . $code),
            );
    }
    catch(Exception $E)
    {
        return array (
            'boolOk' => FALSE,
            'strErrDevelopUtf8' => 'AmoCRM error: ' . $E->getMessage().PHP_EOL.'��� ������: '.$E->getCode(),
        );
    }

    /**
     * ������ �������� � ������� JSON, �������, ��� ��������� �������� ������,
     * ��� ������� ��������� ����� � ������, �������� PHP
     */
    $Response=json_decode($out,true);

    return array (
        'boolOk' => TRUE,
        'arrResponse' => $Response['response'],
    );

    # ))) ����� copy-paste �� ������������

} # function
#-----------

//$strQstn - ��� ������
function fncAmocrmFaqForm($strName, $strQstn, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
    echo "start fncAmocrmFaqForm";
    //������������ �������
    // fncAmocrmAuth - �����������
    // fncGetIdNextResponsible -
    // fncAmocrmContactsList - ��������� ������ ���������
    // fncAmocrmContactsSet - !!!��������\update �������� ������� - ������������� �����
    // fncAmocrmTasksCreate - !!!�������� �������� ������ - ������������� �����
    // fncAmocrmNotesCreate - !!!�������� �������� ���������� - ������������� �� �����

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        //return array("error"=>"not_auth");
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        //��������� ������� �������� � update ���� �����
        $arrContUpdate = fncUpdateContactAmo($strEmail, "", $idNextResponsible, "", $strCookieFile);
        if ($arrContUpdate[2]!="") {
            $idNextResponsible = $arrContUpdate[2];
        }
        # �������� ��������
        $arrAmocrmContactsList = fncAmocrmContactsList(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strEmail
        );

        if (
        ! $arrAmocrmContactsList['boolOk']
        ) {
            # $arrAmocrmContactsList['strErrDevelopUtf8']
        } # if
        else {

            $idContactExists = NULL;

            if (
                isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                &&
                count($arrAmocrmContactsList['arrResponse']['contacts'])
            ) {
                # ��� ������� ���������� ��������
                # ���� break!
                foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                    # �������� ��� email�
                    $arrCntEmails = array ();
                    if (
                        isset ($arrCntct['custom_fields'])
                        &&
                        count($arrCntct['custom_fields'])
                    ) {
                        foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                            if (
                                AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                            ) {
                                # ��� email
                                if (
                                    isset ($arrCF['values'])
                                    &&
                                    count($arrCF['values'])
                                ) {
                                    foreach ( $arrCF['values'] as $arrV ) {
                                        if (
                                            isset ($arrV['value'])
                                            &&
                                            trim($arrV['value']) != ''
                                        ) {
                                            $arrCntEmails[] = trim($arrV['value']);
                                        } # if
                                    } # foreach
                                } # if
                            } # if
                        } # foreach
                    } # if

                    if (
                    in_array($strEmail, $arrCntEmails)
                    ) {

                        $idContactExists = $arrCntct['id'];

                        break; # !!!

                    } # if

                } # foreach
            } # if

            if (
            ! isset ($idContactExists)
            ) {

                # �������� ������� �������
                $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => $strName,
                            'tags' => '���',
                            'responsible_user_id' => $idNextResponsible,
                            'custom_fields' => array (
                                array (
                                    'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                    'values' => array (
                                        array (
                                            'value' => $strEmail,
                                            'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                        ),
                                    ),
                                ),
                            ),
                        )
                    ),
                    'add'
                );

                if (
                ! $arrAmocrmContactsSetAdd['boolOk']
                ) {
                    # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                } # if
                else {
                    $idContactExists = $arrAmocrmContactsSetAdd['arrResponse']['contacts']['add'][0]['id'];
                } # else

            } # if

            # �������� ������� ������
            $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'element_id' => $idContactExists, # id ��������
                        'responsible_user_id' => $idNextResponsible,
                        'element_type' => 1, # 1 ������, ��� � element_id - �������
                        'task_type' => AMOCRM_TASKTYPECALL_ID,
                        'text' => '����� ������ � ����� textiloptom.ru',
                        'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                    ),
                )
            );

            if (
            ! $arrAmocrmTasksCreate['boolOk']
            ) {
                # $arrAmocrmTasksCreate['strErrDevelopUtf8']
            } # if

            if (
                $strQstn != ''
            ) {

                # �������� ������� ����������
                $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $idContactExists,
                            'element_type' => 1, # 1 == �������
                            'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                            'text' => $strQstn,
                        )
                    )
                );

                if (
                ! $arrAmocrmNotesCreate['boolOk']
                ) {
                    # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                } # if

            } # if

        } # else

    } # else

}
#-----------
function fncAmocrmFaqForm0($strName, $strQstn, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);

        # �������� ������� ������
        $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            array (
                array (
                    'name' => '����� ������. ' . date('d.m.Y'),
                    'responsible_user_id' => $idNextResponsible,
                )
            )
        );

        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� email�
                        $arrCntEmails = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                                ) {
                                    # ��� email
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntEmails[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strEmail, $arrCntEmails)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {

                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '�����������',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strQstn != ''
                ) {

                    # �������� ������� ����������
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == ������
                                'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => $strQstn,
                            )
                        )
                    );

                    if (
                    ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmPartnerForm($strName, $strPhone, $strEmail, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
    //������������ �������
    // fncAmocrmAuth - �����������
    // fncGetIdNextResponsible -
    // fncAmocrmLeadsCreate - !!!�������� �������� ������ - ������������� �����
    // fncAmocrmContactsList - ��������� ������ ���������
    // fncAmocrmContactsSet - !!!�������� �������� ������� - ������������� �����
    // fncAmocrmTasksCreate - !!!�������� �������� ������ - ������������� �����

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        //��������� ������� �������� � update ���� �����
        $arrContUpdate = fncUpdateContactAmo($strEmail, AMOCRM_LOGIN, $idNextResponsible, $strPhone, $strCookieFile);
        //
        # �������� ������� ������ � ��������� �� � ������ �������
        if ($arrContUpdate[0] == "") {
            if ($arrContUpdate[1] == "1") {
                //��� ������� c ����� ��� � ������� ��� 'pipeline_id'=>40290
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ������ c textiloptom.ru. ' . date('d.m.Y'),
                            'pipeline_id'=>40290,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            } else {
                //��� ������� ������� � ������� ������ 'pipeline_id'=>10476
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ������ c textiloptom.ru. ' . date('d.m.Y'),
                            'pipeline_id'=>10476,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            }
        } else {
            //��� ������ ������� � ������� ��� 'pipeline_id'=>40290
            $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'name' => '����� ������ c textiloptom.ru. ' . date('d.m.Y'),
                        'pipeline_id'=>40290,
                        'responsible_user_id' => $idNextResponsible,
                    )
                )
            );
        }
        //$arrContUpdate[0]!="" - ����� ������
        //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���
        //===================


        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� ��������
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # ��� �������
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {

                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'tags' => "���",
                                'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    )
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '���������� (textiloptom.ru)',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

            } # else

        } # else

    } # else

} # function !
#-----------

#-----------
#-----------
function fncAmocrmApiForm($strEmail, $strLogin, $strName, $strSiteurl, $strCookieFile, $strFwritePath, $arrIdsResponsible) {

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        //��������� ������� �������� � update ���� �����
        $arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone = "", $strCookieFile);
        if ($arrContUpdate[2]!="") {
            $idNextResponsible = $arrContUpdate[2];
        }
        # �������� ������� ������ � ��������� �� � ������ �������
        if ($arrContUpdate[0] == "") {
            if ($arrContUpdate[1] == "1") {
                //��� ������� c ����� ��� � ������� ��� 'pipeline_id'=>40290
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ������������ API. ' . date('d.m.Y'),
                            'pipeline_id'=>40290,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            } else {
                //��� ������� ������� � ������� ������ 'pipeline_id'=>10476
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ������������ API. ' . date('d.m.Y'),
                            'pipeline_id'=>10476,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            }
        } else {
            //��� ������ ������� � ������� ��� 'pipeline_id'=>40290
            $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'name' => '����� ������������ API. ' . date('d.m.Y'),
                        'pipeline_id'=>40290,
                        'responsible_user_id' => $idNextResponsible,
                    )
                )
            );
        }
        //$arrContUpdate[0]!="" - ����� ������
        //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���
        //================================================================

        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� email�
                        $arrCntEmails = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_EMAIL_CSTFID == $arrCF['id']
                                ) {
                                    # ��� email
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntEmails[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strEmail, $arrCntEmails)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {

                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'tags' => "���",
                                'responsible_user_id' => $idNextResponsible,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNa_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '�����������',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strSiteurl != ''
                ) {

                    # �������� ������� ����������
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == ������
                                'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => '����� �����: ' . $strSiteurl,
                            )
                        )
                    );

                    if (
                    ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmSailidRegForm($strLogin, $strEmail, $strPhone, $strSurname, $strName, $strMiddlename, $strCookieFile) {

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = AMOCRM_ID_FIXED_RESPONSIBLE;

        # �������� ������� ������
        $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            array (
                array (
                    'name' => 'sailid �����������. ' . date('d.m.Y'),
                    'responsible_user_id' => $idNextResponsible,
                )
            )
        );

        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� ��������
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # ��� �������
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {

                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strSurname . ( $strSurname != '' && ( $strName != '' || $strMiddlename != '' ) ? ' ' : '' ) . $strName . ( ( $strSurname != '' || $strName != '' ) && $strMiddlename != '' ? ' ' : '' ) . $strMiddlename,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'responsible_user_id' => $idNextResponsible,
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNs_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {

                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '�����������',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

            } # else

        } # else

    } # else

} # function
#-----------

#-----------
function fncAmocrmCartForm($strName, $Strbudget, $strEmail, $strPhone, $txtOtherForLeadNote, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
    //������������ �������
    // fncAmocrmAuth - �����������
    // fncGetIdNextResponsible -
    // fncAmocrmLeadsCreate - !!!�������� �������� ������ - ������������� �����
    // fncAmocrmContactsList - ��������� ������ ���������
    // fncAmocrmContactsSet - !!!�������� �������� ������� - ������������� �����
    // fncAmocrmTasksCreate - !!!�������� �������� ������ - ������������� �����
    // fncAmocrmNotesCreate - !!!�������� �������� ���������� - ������������� �� �����

    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        //��������� ������� �������� � update ���� �����
        $arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone, $strCookieFile);
        # �������� ������� ������ � ��������� �� � ������ �������
        if ($arrContUpdate[0] == "") {
            if ($arrContUpdate[1] == "1") {
                //��� ������� c ����� ��� � ������� ��� 'pipeline_id'=>40290
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ����� (textiloptom.ru). ' . date('d.m.Y'),
                            'price' => $Strbudget,
                            'pipeline_id'=>40290,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            } else {
                //��� ������� ������� � ������� ������ 'pipeline_id'=>10476
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => '����� ����� (textiloptom.ru). ' . date('d.m.Y'),
                            'price' => $Strbudget,
                            'pipeline_id'=>10476,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            }
        } else {
            //��� ������ ������� � ������� ��� 'pipeline_id'=>40290
            $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'name' => '����� ����� (textiloptom.ru). ' . date('d.m.Y'),
                        'price' => $Strbudget,
                        'pipeline_id'=>40290,
                        'responsible_user_id' => $idNextResponsible,
                    )
                )
            );
        }
        //$arrContUpdate[0]!="" - ����� ������
        //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���

        //===============
        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {

            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� ��������
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # ��� �������
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {
                    //���� �������� ���
                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'tags' => '���',
                                'responsible_user_id' => $idNextResponsible,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    )
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                } # if
                else {
                    //������� ����
                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '���������� ����� ����� (textiloptom.ru).',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );

                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $txtOtherForLeadNote != ''
                ) {

                    # �������� ������� ����������
                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == ������
                                'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => $txtOtherForLeadNote,
                            )
                        )
                    );

                    if (
                    ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else

} # function

function fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible2, $strPhone, $strCookieFile) {
    echo "<br>start fncUpdateContactAmo data strEmail:".$strEmail." strPhone".$strPhone;
    //===============��������� ������� 18122015===============================
    //
    //���������� ������ �������� ������ �������� id �������������� ����������
    //���� id ����� ������������ � �������� ������
    //========================================================================
    $newclient = "";
    $leed1012 = "";	//������ �������� �������� ���� � �������� ���� ��� ���
    $contactid10122015 = "";
    $plen09122015 = strlen($strPhone); //��� ������� � email ��� ������� 6 ��������
    if ($plen09122015>=6) {
        //�������� ��� ������� � �������� ��� ������� 6 ��������
        $arrAmocrmContactsList = fncAmocrmContactsList(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strPhone
        );

        if (array_key_exists('arrResponse',$arrAmocrmContactsList)) {
            if ($arrAmocrmContactsList['arrResponse']===null) {
                //���� �� �������� ������ �� ����� ���� �� email
                $arrAmocrmContactsListE = fncAmocrmContactsList(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    $strEmail
                );
                $contactdata10122015 = $arrAmocrmContactsListE; //������ ��� ������ � ���������� ������
                if ($arrAmocrmContactsListE['arrResponse']===null) {
                    //�� ����� �� email
                    $idNextResponsible = $idNextResponsible2;
                    $newclient = "1";
                }	else {
                    //����� �� email
                    $idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];
                    $contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id ��������
                }
            } else {
                //����� �� ��������
                $idNextResponsible = $arrAmocrmContactsList['arrResponse']["contacts"][0]['responsible_user_id'];
                $contactdata10122015 = $arrAmocrmContactsList;//������ ��� ������ � ���������� ������
                $contactid10122015 = "".$arrAmocrmContactsList['arrResponse']["contacts"][0]['id'];//id ��������
            }
        } else {
            $arrAmocrmContactsListE = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );
            $contactdata10122015 = $arrAmocrmContactsListE;//������ ��� ������ � ���������� ������
            if (array_key_exists('arrResponse',$arrAmocrmContactsListE)) {
                if ($arrAmocrmContactsListE['arrResponse']===null) {
                    $idNextResponsible = $idNextResponsible2;
                    $newclient = "1";
                }	else {
                    //����� �� email
                    $idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];
                    $contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id ��������
                }
            }	else {
                $idNextResponsible = $idNextResponsible2;
                $newclient = "1";
            }
        }
    } else {
        //�������� ��� ������� � email ��� ������� 5 ��������
        $plen09122015 = strlen($strEmail);
        if ($plen09122015>=5) {
            $arrAmocrmContactsListE = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strEmail
            );
            $contactdata10122015 = $arrAmocrmContactsListE;//������ ��� ������ � ���������� ������
            if (array_key_exists('arrResponse',$arrAmocrmContactsListE)) {
                if ($arrAmocrmContactsListE['arrResponse']===null) {
                    $idNextResponsible = $idNextResponsible2;
                    $newclient = "1";
                }	else {
                    //����� �� email
                    $idNextResponsible = $arrAmocrmContactsListE['arrResponse']["contacts"][0]['responsible_user_id'];
                    $contactid10122015 = "".$arrAmocrmContactsListE['arrResponse']["contacts"][0]['id'];//id ��������
                }
            }	else {
                $idNextResponsible = $idNextResponsible2;
                $newclient = "1";
            }
        } else {
            //if ���� ������������ ����� � email � ��������
            $idNextResponsible = $idNextResponsible2;
        }
    }
    //==============================================
    //==============================================
    $leed1012 = "";	//������ �������� �������� ���� � �������� ���� ��� ���
    $sdelka1012 = ""; //������ �������� �������� ���� � �������� ���� ��� ������
    echo "<br>Finish fncUpdateContactAmo point1";
    //���� ������ �� ����� - �� �������� ����� Update ��� � ���������
    if ($newclient == "") {
        echo "<br>Finish fncUpdateContactAmo point2";
        //======================update ��������=============================
        if ($contactid10122015!="") {
            echo "<br>Finish fncUpdateContactAmo point3";
            //������� ���������� � ����� ��������� �������� � �������� ����� ��� ���������� UPDATE � ������ ���� ���

            //���� ���� ��� � ������
            $tags1012 = $contactdata10122015['arrResponse']["contacts"][0]['tags'];
            foreach($tags1012 as $onetag1012) {
                //���� ��� ���
                if($onetag1012['name']=="���") {
                    $leed1012 = "1";
                }
                //���� ��� ������
                if($onetag1012['name']=="������") {
                    $sdelka1012 = "1";
                }
            }



            $needupdate1012 = ""; //���� ����������� �� ������������� UPDATE
            $customfields = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'];
            $i1012 = 0;
            $flagphone = ""; //���� ��������� ������ - ����� update
            $flagemail = ""; //���� ��������� ������ - ����� update
            $flaglogin = ""; //���� ��������� ������ - ����� update
            $newemailarr = array();
            $newphonearr = array();
            echo "<br>Finish fncUpdateContactAmo point4";
            foreach($customfields as $value1012) {
                //�������� �� �������
                if($strPhone!="") {
                    echo "<br>Finish fncUpdateContactAmo point5 - strPhone";
                    if (array_key_exists('code',$value1012)) {
                        $strtmpcodeval = "".$value1012['code'];
                        if($strtmpcodeval==='PHONE') {
                            //���������� ������� �������� � ������� �� �����������

                            $arramovalue = $value1012['values'];
                            foreach($arramovalue as $arrcustomelement1812) {
                                $strphonenum = "".$arrcustomelement1812['value'];
                                //$strPhone
                                if (($strPhone!="") and ($strphonenum!="")) {
                                    if ($strPhone===$strphonenum) {
                                        $flagphone = "1";
                                    }
                                }
                                $arrTmpPhone =  array (
                                    'value' => $strphonenum,
                                    'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                );
                                array_push($newphonearr,$arrTmpPhone);
                            }
                            //$amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];
                            //if ($amovalue!="") {
                            //�� update ����
                            //	$flagphone = "1"; //������ ������� ��� update �������� �� �����
                            //}
                        }
                    }
                }
                //�������� �� email
                if($strEmail!="") {
                    echo "<br>Finish fncUpdateContactAmo point5 - strEmail";
                    if (array_key_exists('code',$value1012)) {
                        $strtmpcodeval = "".$value1012['code'];
                        if($strtmpcodeval==='EMAIL') {
                            //���������� email �������� � email �� �����������

                            $arramovalue = $value1012['values'];

                            foreach($arramovalue as $arrcustomelement1812) {
                                $stremailval = "".$arrcustomelement1812['value'];
                                //$strEmail
                                if (($strEmail!="") and ($stremailval!="")) {
                                    if ($strEmail===$$stremailval) {
                                        $flagemail = "1";
                                    }
                                }
                                $arrTmpEmail =  array (
                                    'value' => $stremailval,
                                    'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                );
                                array_push($newemailarr,$arrTmpEmail);
                            }

                            //$amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];
                            //if ($amovalue!="") {
                            //�� update ����
                            //	$flagemail = "1";
                            //}
                        }
                    }
                }
                //�������� �� �����
                if($strLogin!="") {
                    echo "<br>Finish fncUpdateContactAmo point5 - strLogin";
                    if (array_key_exists('id',$value1012)) {
                        if($value1012['id']=='651538') {
                            //���������� ����� �������� � ����� �� �����������
                            $amovalue = $contactdata10122015['arrResponse']["contacts"][0]['custom_fields'][$i1012]['values'][0]['value'];
                            if ($amovalue!="") {
                                //�� update ����
                                $flaglogin = "1";
                            }
                        }
                    }
                }
                $i1012++;
            }
            echo "<br>Finish fncUpdateContactAmo check passed";
            //�� ����� ����������� ������� �� �������� ������ - �� ��� ������� ���� ������� - ������ ���� �������
            //update ��������

            if( $flagphone != "1") {
                //������ update
                $arrTmpPhone =  array (
                    'value' => $strPhone,
                    'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                );
                array_push($newphonearr,$arrTmpPhone);
                $arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'id' => $contactid10122015,
                            'last_modified' => time(),
                            'custom_fields' => array (
                                array (
                                    'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                    'values' => $newphonearr,
                                ),
                            ),
                        )
                    ),
                    'update'
                );
            }
            //update email
            if( $flagemail != "1") {
                //������ update
                $arrTmpEmail =  array (
                    'value' => $strEmail,
                    'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                );
                array_push($newemailarr,$arrTmpEmail);
                $arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'id' => $contactid10122015,
                            'last_modified' => time(),
                            'custom_fields' => array (
                                array (
                                    'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                    'values' =>$newemailarr,
                                ),
                            ),
                        )
                    ),
                    'update'
                );
            }

            //update login
            if( $flaglogin != "1") {
                //������ update
                $arrAmocrmContactsSetUpdate1012 = fncAmocrmContactsSet(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'id' => $contactid10122015,
                            'last_modified' => time(),
                            'custom_fields' => array (
                                array (
                                    'id' => AMOCRM_CONTACT_LGNs_CSTFID,
                                    'values' => array (
                                        array (
                                            'value' => $strLogin,
                                        ),
                                    ),
                                ),
                            ),
                        )
                    ),
                    'update'
                );
            }

        }
    }
    echo "<br>Finish fncUpdateContactAmo data strEmail:".$strEmail." strPhone:".$strPhone." idNextResponsible:".$idNextResponsible;
    //===============��������� 18122015===============================
    if($sdelka1012=="1") {
        //�� ���� ��� ������ ����
        $outTag1812 = "";
    } else {
        $outTag1812 = "1";
    }
    $rarray = array($newclient,$outTag1812,$idNextResponsible);
    return $rarray;
}
//-----------
function fncAmocrmTextiloptomRegForm($strEmail, $strLogin, $strName, $strPhone, $strInn, $strCmp_name, $strCmp_name_full, $strCmp_regplace, $strCmp_fio, $strCmp_site, $strCookieFile, $strFwritePath, $arrIdsResponsible) {
    echo "Start fncAmocrmTextiloptomRegForm";
//������������ �������
// fncAmocrmAuth - �����������
// fncGetIdNextResponsible -
// fncAmocrmLeadsCreate - !!!�������� �������� ������ - ������������� �����
// fncAmocrmContactsList - ��������� ������ ���������
// fncAmocrmContactsSet - !!!�������� �������� ������� - ������������� �����
// fncAmocrmCompaniesSet - !!!�������� �������� �������� - ������������� �����
// fncAmocrmNotesCreate - !!!�������� �������� ���������� - ������������� �� �����
// fncAmocrmTasksCreate - !!!�������� �������� ������ - ������������� �����
    # �������� ���������������� � amoCRM
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {

        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);

        //��������� ������� �������� � update ���� �����
        $arrContUpdate = fncUpdateContactAmo($strEmail, $strLogin, $idNextResponsible, $strPhone, $strCookieFile);
        echo "<br>Start fncAmocrmTextiloptomRegForm.fncUpdateContactAmo";
        if ($arrContUpdate[2]!="") {
            $idNextResponsible = $arrContUpdate[2];
        }
        # �������� ������� ������ � ��������� �� � ������ �������
        if ($arrContUpdate[0] == "") {
            if ($arrContUpdate[1] == "1") {
                //��� ������� c ����� ��� � ������� ��� 'pipeline_id'=>40290
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => 'textiloptom.ru �����������. ' . date('d.m.Y'),
                            'pipeline_id'=>40290,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            } else {
                //��� ������� ������� � ������� ������ 'pipeline_id'=>10476
                $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'name' => 'textiloptom.ru �����������. ' . date('d.m.Y'),
                            'pipeline_id'=>10476,
                            'responsible_user_id' => $idNextResponsible,
                        )
                    )
                );
            }
        } else {
            //��� ������ ������� � ������� ��� 'pipeline_id'=>40290
            $arrAmocrmLeadsCreate = fncAmocrmLeadsCreate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'name' => 'textiloptom.ru �����������. ' . date('d.m.Y'),
                        'pipeline_id'=>40290,
                        'responsible_user_id' => $idNextResponsible,
                    )
                )
            );
        }
        //$arrContUpdate[0]!="" - ����� ������
        //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���
        if (
        ! $arrAmocrmLeadsCreate['boolOk']
        ) {
            # $arrAmocrmLeadsCreate['strErrDevelopUtf8']
        } # if
        else {
            //���� ������ ������� �� ��������� ������
            # �������� ��������
            $arrAmocrmContactsList = fncAmocrmContactsList(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                $strPhone
            );

            if (
            ! $arrAmocrmContactsList['boolOk']
            ) {
                # $arrAmocrmContactsList['strErrDevelopUtf8']
            } # if
            else {

                $idContactExists = NULL;
                $arrContactExistsLeads = array ();

                if (
                    isset ($arrAmocrmContactsList['arrResponse']['contacts'])
                    &&
                    count($arrAmocrmContactsList['arrResponse']['contacts'])
                ) {
                    # ��� ������� ���������� ��������
                    # ���� break!
                    foreach ( $arrAmocrmContactsList['arrResponse']['contacts'] as $arrCntct ) {

                        # �������� ��� ��������
                        $arrCntPhones = array ();
                        if (
                            isset ($arrCntct['custom_fields'])
                            &&
                            count($arrCntct['custom_fields'])
                        ) {
                            foreach ( $arrCntct['custom_fields'] as $arrCF ) {
                                if (
                                    AMOCRM_CONTACT_PHONE_CSTFID == $arrCF['id']
                                ) {
                                    # ��� �������
                                    if (
                                        isset ($arrCF['values'])
                                        &&
                                        count($arrCF['values'])
                                    ) {
                                        foreach ( $arrCF['values'] as $arrV ) {
                                            if (
                                                isset ($arrV['value'])
                                                &&
                                                trim($arrV['value']) != ''
                                            ) {
                                                $arrCntPhones[] = trim($arrV['value']);
                                            } # if
                                        } # foreach
                                    } # if
                                } # if
                            } # foreach
                        } # if

                        if (
                        in_array($strPhone, $arrCntPhones)
                        ) {

                            $idContactExists = $arrCntct['id'];

                            if (
                            isset ($arrCntct['linked_leads_id'])
                            ) {
                                $arrContactExistsLeads = $arrCntct['linked_leads_id'];
                            } # if

                            break; # !!!

                        } # if

                    } # foreach
                } # if

                if (
                ! isset ($idContactExists)
                ) {
                    //���� ������� �� ������
                    # �������� ������� �������
                    $arrAmocrmContactsSetAdd = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'name' => $strName,
                                'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                'responsible_user_id' => $idNextResponsible,
                                'tags' => '���',
                                'custom_fields' => array (
                                    array (
                                        'id' => AMOCRM_CONTACT_EMAIL_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strEmail,
                                                'enum' => AMOCRM_CONTACT_EMAIL_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_PHONE_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strPhone,
                                                'enum' => AMOCRM_CONTACT_PHONE_CSTFTYPE,
                                            ),
                                        ),
                                    ),
                                    array (
                                        'id' => AMOCRM_CONTACT_LGNt_CSTFID,
                                        'values' => array (
                                            array (
                                                'value' => $strLogin,
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        ),
                        'add'
                    );

                    if (
                    ! $arrAmocrmContactsSetAdd['boolOk']
                    ) {
                        # $arrAmocrmContactsSetAdd['strErrDevelopUtf8']
                    } # if

                    # �������� ������� ��������
                    //$arrContUpdate[0]!="" - ����� ������
                    //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���
                    if( $arrContUpdate[0]!="" ) {
                        $arrAmocrmCompaniesSetAdd = fncAmocrmCompaniesSet(
                        //��� ������ ������� - �������� �������� � ����� ���
                            AMOCRM_SUBDOMAIN,
                            $strCookieFile,
                            array (
                                array (
                                    'name' => $strCmp_name,
                                    'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                    'responsible_user_id' => $idNextResponsible,
                                    'tags' => '���',
                                    'custom_fields' => array (
                                        array (
                                            'id' => AMOCRM_COMPANY_INN_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strInn,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_FULLN_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_name_full,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_YURADDR_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_regplace,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_DFIO_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_fio,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_SITE_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_site,
                                                ),
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'add'
                        );
                    } else {
                        //��� �� ������ ������� - �������� �������� ��� �����
                        $arrAmocrmCompaniesSetAdd = fncAmocrmCompaniesSet(
                            AMOCRM_SUBDOMAIN,
                            $strCookieFile,
                            array (
                                array (
                                    'name' => $strCmp_name,
                                    'linked_leads_id' => array ($arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id']),
                                    'responsible_user_id' => $idNextResponsible,
                                    'custom_fields' => array (
                                        array (
                                            'id' => AMOCRM_COMPANY_INN_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strInn,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_FULLN_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_name_full,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_YURADDR_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_regplace,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_DFIO_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_fio,
                                                ),
                                            ),
                                        ),
                                        array (
                                            'id' => AMOCRM_COMPANY_SITE_CSTFID,
                                            'values' => array (
                                                array (
                                                    'value' => $strCmp_site,
                                                ),
                                            ),
                                        ),
                                    ),
                                )
                            ),
                            'add'
                        );
                    }


                    if (
                    ! $arrAmocrmCompaniesSetAdd['boolOk']
                    ) {
                        # $arrAmocrmCompaniesSetAdd['strErrDevelopUtf8']
                    } // if

                } // if ��� ������ ��������
                else {
                    //id �������� ����������
                    $arrContactExistsLeads[] = $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'];

                    # �������� �������� �������
                    $arrAmocrmContactsSetUpdate = fncAmocrmContactsSet(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'id' => $idContactExists,
                                'linked_leads_id' => $arrContactExistsLeads,
                                'last_modified' => time(),
                            )
                        ),
                        'update'
                    );

                    if (
                    ! $arrAmocrmContactsSetUpdate['boolOk']
                    ) {
                        # $arrAmocrmContactsSetUpdate['strErrDevelopUtf8']
                    } # if

                    # �������� ������� ����������

                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == ������
                                'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => '����������������� �����.',
                            )
                        )
                    );



                    if (
                    ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # else

                # �������� ������� ������
                //$arrContUpdate[0]!="" - ����� ������
                //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���

                $arrAmocrmTasksCreate = fncAmocrmTasksCreate(
                    AMOCRM_SUBDOMAIN,
                    $strCookieFile,
                    array (
                        array (
                            'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'], # id ������
                            'responsible_user_id' => $idNextResponsible,
                            'tags' => '���',
                            'element_type' => 2, # 2 ������, ��� � element_id - ������
                            'task_type' => AMOCRM_TASKTYPECALL_ID,
                            'text' => '���������� ����������� � textiloptom.ru',
                            'complete_till' => mktime(23, 59, 30, date('n'), date('j'), date('Y')),
                        ),
                    )
                );



                if (
                ! $arrAmocrmTasksCreate['boolOk']
                ) {
                    # $arrAmocrmTasksCreate['strErrDevelopUtf8']
                } # if

                if (
                    $strInn != ''
                ) {

                    # �������� ������� ����������
                    //$arrContUpdate[0]!="" - ����� ������
                    //$arrContUpdate[1]=="1" - ������������ ������ � ����� ���

                    $arrAmocrmNotesCreate = fncAmocrmNotesCreate(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        array (
                            array (
                                'element_id' => $arrAmocrmLeadsCreate['arrResponse']['leads']['add'][0]['id'],
                                'element_type' => 2, # 2 == ������
                                'note_type' => 4, # 4 == ������� ���������� https://developers.amocrm.ru/rest_api/notes_list.php#notetypes
                                'text' => '���: ' . $strInn,
                            )
                        )
                    );


                    if (
                    ! $arrAmocrmNotesCreate['boolOk']
                    ) {
                        # $arrAmocrmNotesCreate['strErrDevelopUtf8']
                    } # if

                } # if

            } # else

        } # else

    } # else


} # function


//��������� UPDATE ������ � �������� �� ID ���������� ��������� ����� ���������� ����������
//������� �������� - id ������
function fncAmocrmUpdateLeadContact($strLeadID,$arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {
    $strnewline = '
	================================
	';
    @file_put_contents(AMOCRM_LOG_FILE2,'Start fncAmocrmUpdateLeadContact'.$strnewline);
    # �������� ���������������� � amoCRM
    $flagneedupdate1612 = "";
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl1 - "." New idNextResponsible:".$idNextResponsible.$strnewline, FILE_APPEND);
        //mail("rsdim@rambler.ru","Subj hook started incl1","1:".$idNextResponsible);
        // �������� �� ������ �������� ������ � ��������
        $arrAmocrmContactsGet = fncAmocrmContactsGet(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strLeadID
        );
        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl2 - "."2:".implode("!",$arrAmocrmContactsGet).$strnewline, FILE_APPEND);
        //mail("rsdim@rambler.ru","Subj hook started incl2","2:".implode("!",$arrAmocrmContactsGet));
        if (
        ! $arrAmocrmContactsGet['boolOk']
        ) {

        } # if
        else {
            //�������� id �������� ���������� � �������
            if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
                $contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];
                $strcontactid512 = "".$contactid1512;
                if ($strcontactid512!="") {
                    @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 - "."3:".json_encode($arrAmocrmContactsGet).$strnewline, FILE_APPEND);
                    //mail("rsdim@rambler.ru","Subj hook started incl3 yes contact","3:".json_encode($arrAmocrmContactsGet));
                    //�������� ������� �� id ��������
                    $arrAmocrmContactsListById = fncAmocrmContactsListById(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        $strcontactid512
                    );
                    if (
                    ! $arrAmocrmContactsListById['boolOk']
                    ) {

                    } # if
                    else {
                        //�������� �� �������� �������������� �� �������
                        //mail("rsdim@rambler.ru","Subj hook started incl4 no contact","4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById));
                        $strcontactRespId = "".$arrAmocrmContactsListById['arrResponse']['contacts'][0]['responsible_user_id'];
                        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.0 - "."From contact id:".$strcontactid512." responsible_user_id = ".$strcontactRespId.$strnewline, FILE_APPEND);

                        if ($strcontactRespId!="") {
                            if	(in_array($strcontactRespId,$arrAmocrmIdsResponsibleDisable)) {
                                //mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
                                @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.1"."5.1".$strnewline, FILE_APPEND);
                                //�������� �������������� � �������� ���� �����
                                $arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
                                    AMOCRM_SUBDOMAIN,
                                    $strCookieFile,
                                    array (
                                        array (
                                            'id' => $contactid1512,
                                            'responsible_user_id' => $idNextResponsible,
                                            'last_modified' => time(),
                                        )
                                    ),
                                    'update'
                                );
                                $flagneedupdate1612 = "1";
                            } else {
                                //mail("rsdim@rambler.ru","Subj hook started incl5.2","5.2");
                                @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.2 "."5.2".$strnewline, FILE_APPEND);
                                //����� �� �������� �������������� ��� ������� � ������
                                $idNextResponsible = $strcontactRespId;
                                $flagneedupdate1612 = "1";
                            }
                        } else {
                            @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.3"."5.3".$strnewline, FILE_APPEND);
                            //mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");
                            // idNextResponsible - �� �������� �.�. � �������� ������
                        }
                    }
                } else {
                    //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                    //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
                    @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
                }

            } else {
                //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
                @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
            }

        }
        if ($flagneedupdate1612 == "1") {
            # �������� update ������
            $arrAmocrmLeadsUpdate = fncAmocrmLeadsUpdate(
                AMOCRM_SUBDOMAIN,
                $strCookieFile,
                array (
                    array (
                        'id' => $strLeadID,
                        'last_modified' => time(),
                        'responsible_user_id' => $idNextResponsible,
                    )
                )
            );
            @file_put_contents(AMOCRM_LOG_FILE2,"Lead - updated!".$strnewline, FILE_APPEND);
        } else {
            @file_put_contents(AMOCRM_LOG_FILE2,"Lead - not need update!".$strnewline, FILE_APPEND);
        }

        //mail("rsdim@rambler.ru","Subj hook started incl10 leadsupdate","idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate));
        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl10 leadsupdate - "."idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate).$strnewline, FILE_APPEND);
    }
    return $strLeadID;
}

//��������� ����� � UPDATE ��������  - ��� ��� ���������� �� ������
//������� �������� - id ������
function fncAmocrmUpdateContactTag($strLeadID,$arrIdsResponsible,$strCookieFile) {
    $strnewline = '
	================================
	';
    @file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmUpdateContactTag'.$strnewline);

    # �������� ���������������� � amoCRM
    $flagneedupdate1612 = "";
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        @file_put_contents(AMOCRM_LOG_FILE,"Subj hook started hook1612 incl1:"."1:".$idNextResponsible.$strnewline,FILE_APPEND);

        // �������� �� ������ �������� ������ � ��������
        $arrAmocrmContactsGet = fncAmocrmContactsGet(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strLeadID
        );

        if (
        ! $arrAmocrmContactsGet['boolOk']
        ) {

        } # if
        else {
            //�������� id �������� ���������� � �������
            if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
                $contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];
                $strcontactid512 = "".$contactid1512;
                if ($strcontactid512!="") {
                    @file_put_contents(AMOCRM_LOG_FILE,"Subj hook started Lid-Klient 3.1. Contactid: ".$strcontactid512."  3:".json_encode($arrAmocrmContactsGet).$strnewline,FILE_APPEND);
                    //�������� ������� �� id ��������
                    $arrAmocrmContactsListById = fncAmocrmContactsListById(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        $strcontactid512
                    );
                    @file_put_contents(AMOCRM_LOG_FILE,"Subj hook started Lid-Klient 3.2"."Contactid: ".$strcontactid512."  3:".json_encode($arrAmocrmContactsListById).$strnewline,FILE_APPEND);

                    if (
                    ! $arrAmocrmContactsListById['boolOk']
                    ) {

                    } # if
                    else {
                        //�������� �� �������� ������ �����
                        $arrcontactTag = $arrAmocrmContactsListById['arrResponse']['contacts'][0]['tags'];
                        @file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.0 - "."4: json: ".json_encode($arrcontactTag).$strnewline,FILE_APPEND);
                        $strnewtags = "";
                        $flagnewtags = "";
                        $flagnewtags2 = "";
                        foreach($arrcontactTag as $arkey => $arrtags1612) {
                            foreach($arrtags1612 as $arkey1612 => $val16120) {
                                $strnewtags .= "# key2:".$arkey1612." - ".$val16120."#";
                                if($arkey1612=="name") {
                                    $strtagsearch = "".$val16120;
                                    if ( $strtagsearch === "���" ) {
                                        $strnewtags2 .= "������,";
                                        $flagnewtags = "1";
                                    } elseif($strtagsearch === "������") {
                                        $flagnewtags2 = "1";
                                    } else
                                    {
                                        $strnewtags2 .= "".$val16120.",";
                                    }
                                }
                            }
                        }
                        if (($flagnewtags2 === "") and ($flagnewtags === "")) {
                            $strnewtags2 .= "������";
                        }
                        @file_put_contents(AMOCRM_LOG_FILE,$strnewtags." tags2 = ".$strnewtags2.$strnewline,FILE_APPEND);
                        if ($flagnewtags == "1") {
                            @file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.2"."4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById).$strnewline,FILE_APPEND);
                            //mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
                            //�������� �������������� � �������� ���� �����
                            $arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
                                AMOCRM_SUBDOMAIN,
                                $strCookieFile,
                                array (
                                    array (
                                        'id' => $contactid1512,
                                        'tags' => $strnewtags2,
                                        'last_modified' => time(),
                                    )
                                ),
                                'update'
                            );
                        } else {
                            @file_put_contents(AMOCRM_LOG_FILE, "Subj hook started Lid-Klient 4.3"."4: contactID: ".$strcontactid512." tags - ".$strnewtags.$strnewline,FILE_APPEND);

                            //mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");
                            // idNextResponsible - �� �������� �.�. � �������� ������
                        }
                    }
                } else {
                    //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                    //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
                }

            } else {
                //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
            }

        }

        //mail("rsdim@rambler.ru","Subj hook started incl10 leadsupdate","idNextResponsible:".$idNextResponsible."  10:".json_encode($arrAmocrmLeadsUpdate));
    }
    return $strLeadID;
}

function fncAmocrmUpdateAllContacts($arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {
    $countUpdated = 0; //����� update ���������
    $strnewline = '
	================================
	';
    @file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmUpdateAllContacts');

    # �������� ���������������� � amoCRM
    $flagneedupdate1612 = "";
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);
        //fncAmocrmContactsListByResponsibleID
        //3� �������� - id ���������� ��� �������� ����� �������� ��������
        $arrAmocrmContactsListByResponsibleID = fncAmocrmContactsListByResponsibleID(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            '628743'
        );
        if (
        ! $arrAmocrmContactsListByResponsibleID['boolOk']
        ) {

        } # if
        else {
            //�������� �� �������� ������ �����
            $arrcontactTag = $arrAmocrmContactsListByResponsibleID['arrResponse']['contacts'];
            $countUpdated = count($arrcontactTag);
        }
    }

    return $countUpdated;
    //return $idNextResponsible;
}
function fncAmocrmCheckTag($strLeadTag,$strLeadId,$arrIdsResponsible,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {
    $countUpdated = 0; //����� update ���������
    $strnewline = '
	================================
	';
    @file_put_contents(AMOCRM_LOG_FILE,'Start fncAmocrmCheckTag'.$strnewline);

    # �������� ���������������� � amoCRM

    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
        $idNextResponsible = fncGetIdNextResponsible($arrIdsResponsible, $strFwritePath);

        $arrLeadData = fncAmocrmLeadsGetById(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strLeadId
        );

        if (
        ! $arrLeadData['boolOk']
        ) {

        } # if
        else {
            $arrTags = $arrLeadData['arrResponse']['leads'][0]['tags'];
            @file_put_contents(AMOCRM_LOG_FILE,"���� ���:".$strLeadTag.$strnewline,FILE_APPEND);
            foreach($arrTags as $subarrtag) {
                if($subarrtag['name']==$strLeadTag) {
                    //������ ��� ������
                    $countUpdated = 1;
                } else {
                    @file_put_contents(AMOCRM_LOG_FILE,"tags:".$subarrtag['name'].$strnewline,FILE_APPEND);
                }
            }
        }
    }

    return $countUpdated;
    //return $idNextResponsible;
}

//��������� UPDATE ������ � �������� �� ID ���������� ��������� ����� ���������� ����������
//������� �������� strLeadID - id ������, strManagerId - id ���� ��������� ������� ������ ���� ������������ �� ������ � �������
function fncAmocrmUpdateLeadContactTo($strLeadID,$strManagerId,$arrAmocrmIdsResponsibleDisable,$strCookieFile) {
    $strnewline = '
	================================
	';
    @file_put_contents(AMOCRM_LOG_FILE2,'Start fncAmocrmUpdateLeadContactTo'.$strnewline);
    # �������� ���������������� � amoCRM
    $flagneedupdate1612 = "";
    $arrAmocrmAuth = fncAmocrmAuth(AMOCRM_LOGIN, AMOCRM_SUBDOMAIN, AMOCRM_API_KEY, $strCookieFile);

    if (
    ! $arrAmocrmAuth['boolOk']
    ) {
        # $arrAmocrmAuth['strErrDevelopUtf8']
    } # if
    else {
        $idNextResponsible = $strManagerId;
        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl1 - "." New idNextResponsible:".$idNextResponsible.$strnewline, FILE_APPEND);
        //mail("rsdim@rambler.ru","Subj hook started incl1","1:".$idNextResponsible);
        // �������� �� ������ �������� ������ � ��������
        $arrAmocrmContactsGet = fncAmocrmContactsGet(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            $strLeadID
        );
        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl2 - "."2:".implode("!",$arrAmocrmContactsGet).$strnewline, FILE_APPEND);
        //mail("rsdim@rambler.ru","Subj hook started incl2","2:".implode("!",$arrAmocrmContactsGet));
        if (
        ! $arrAmocrmContactsGet['boolOk']
        ) {

        } # if
        else {
            //�������� id �������� ���������� � �������
            if (isset($arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'])) {
                $contactid1512 = $arrAmocrmContactsGet['arrResponse']['links'][0]['contact_id'];
                $strcontactid512 = "".$contactid1512;
                if ($strcontactid512!="") {
                    @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 - "."3:".json_encode($arrAmocrmContactsGet).$strnewline, FILE_APPEND);
                    //mail("rsdim@rambler.ru","Subj hook started incl3 yes contact","3:".json_encode($arrAmocrmContactsGet));
                    //�������� ������� �� id ��������
                    $arrAmocrmContactsListById = fncAmocrmContactsListById(
                        AMOCRM_SUBDOMAIN,
                        $strCookieFile,
                        $strcontactid512
                    );
                    if (
                    ! $arrAmocrmContactsListById['boolOk']
                    ) {

                    } # if
                    else {
                        //�������� �� �������� �������������� �� �������
                        //mail("rsdim@rambler.ru","Subj hook started incl4 no contact","4: contactID: ".$strcontactid512." json - ".json_encode($arrAmocrmContactsListById));
                        $strcontactRespId = "".$arrAmocrmContactsListById['arrResponse']['contacts'][0]['responsible_user_id'];
                        @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.0 - "."From contact id:".$strcontactid512." responsible_user_id = ".$strcontactRespId.$strnewline, FILE_APPEND);

                        if ($strcontactRespId!="") {
                            //���� ������������ � �������� �� ��� �������� - �� ����� ��������� �������������� � ��������
                            if	($strcontactRespId!=$idNextResponsible) {
                                //mail("rsdim@rambler.ru","Subj hook started incl5.1","5.1");
                                @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.1"."5.1".$strnewline, FILE_APPEND);
                                //�������� �������������� � �������� ���� �����
                                $arrAmocrmContactsSetUpdate1512 = fncAmocrmContactsSet(
                                    AMOCRM_SUBDOMAIN,
                                    $strCookieFile,
                                    array (
                                        array (
                                            'id' => $contactid1512,
                                            'responsible_user_id' => $idNextResponsible,
                                            'last_modified' => time(),
                                        )
                                    ),
                                    'update'
                                );
                                $flagneedupdate1612 = "1";
                            } else {
                                //���� ������������� ��� �������� - �� �������� ��������� �� �����
                            }
                        } else {
                            @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl5.3"."5.3".$strnewline, FILE_APPEND);
                            //mail("rsdim@rambler.ru","Subj hook started incl5.3","5.3");
                            // idNextResponsible - �� �������� �.�. � �������� ������
                        }
                    }
                } else {
                    //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                    //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
                    @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
                }

            } else {
                //���� ������ ������� ��� �������� �� � ������ � ��� ������ �� �����
                //mail("rsdim@rambler.ru","Subj hook started incl3 no contact","3:".implode("!",$contactid1512));
                @file_put_contents(AMOCRM_LOG_FILE2,"Subj hook started incl3 no contact"."3:".implode("!",$contactid1512).$strnewline, FILE_APPEND);
            }

        }

        // �������� update ������ � ����� ������
        $arrAmocrmLeadsUpdate = fncAmocrmLeadsUpdate(
            AMOCRM_SUBDOMAIN,
            $strCookieFile,
            array (
                array (
                    'id' => $strLeadID,
                    'last_modified' => time(),
                    'responsible_user_id' => $idNextResponsible,
                )
            )
        );
        @file_put_contents(AMOCRM_LOG_FILE2,"Lead - updated!".$strnewline, FILE_APPEND);

    }
    return $strLeadID;
}
?>