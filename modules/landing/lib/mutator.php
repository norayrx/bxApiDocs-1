<? namespace Bitrix\Landing;$GLOBALS['____942006441']= array(base64_decode('Z'.'GVmaW'.'5'.'l'),base64_decode('ZXhwbG9kZQ=='),base64_decode('c'.'GFjaw'.'='.'='),base64_decode(''.'bWQ1'),base64_decode('Y29uc3R'.'h'.'bn'.'Q'.'='),base64_decode('a'.'G'.'FzaF'.'9o'.'bWFj'),base64_decode('c3RyY21w'),base64_decode('ZX'.'hwbG9k'.'ZQ=='),base64_decode('bWt'.'0aW1l'),base64_decode(''.'d'.'G'.'ltZQ=='),base64_decode('a'.'W5f'.'YX'.'JyYXk='),base64_decode('c'.'HJlZ1'.'9yZ'.'XBsYWNlX2NhbGxiYWNr'),base64_decode('c3RydG'.'9sb3'.'dl'.'cg=='),base64_decode('cHJl'.'Z1'.'9t'.'Y'.'X'.'Rj'.'aA=='),base64_decode('cHJlZ19yZXB'.'sYW'.'N'.'lX2'.'NhbGxiYWNr'));if(!function_exists(__NAMESPACE__.'\\___22600766')){function ___22600766($_75392773){static $_1929274017= false; if($_1929274017 == false) $_1929274017=array('TEFORE'.'l'.'OR1'.'9'.'NVVR'.'BVE9SX01PR'.'EU'.'=','bGFu'.'Z'.'Gl'.'uZ'.'w'.'==',''.'b25MYW5'.'ka'.'W5n'.'UHV'.'ib'.'GljY'.'XR'.'pb24=','aW'.'Q'.'=','REI=','U0VMRUN'.'UIFZBT'.'FVFIEZST00gYl9vcHRp'.'b'.'2'.'4gV0'.'hFUk'.'UgTkF'.'NR'.'T0'.'nfl'.'B'.'B'.'Uk'.'FNX0ZJTklTSF9EQVRFJyBBT'.'kQ'.'gTU9EV'.'UxF'.'X0'.'lEPSdt'.'Y'.'WluJyBBTkQgU0lURV9JRCBJUyBOVU'.'xM','VkFMVUU=','L'.'g='.'=','SCo=','Ym'.'l0cm'.'l'.'4','TElDRU'.'5'.'T'.'RV9LRV'.'k=','c2hhM'.'jU2','MjAx'.'OC0wMS0wMQ==','MjAxOC'.'0wMS0wM'.'Q==','LQ'.'==',''.'TEl'.'DRU5TRV'.'9FWFB'.'JUkVE','TEFO'.'R'.'El'.'OR1'.'9MSUNFT'.'lNF'.'X0VYU'.'ElSRU'.'Q=','UFVCTElD','W'.'Q==','REF'.'URV9N'.'T0RJRlk=','REFURV9QVUJMSUM=','L'.'yIj'.'YmxvY2'.'soW1xkX'.'SspIi9pcw'.'==','c2VsZWN0','SUQ'.'=','UFVCTE'.'lD',''.'UEFSRU5'.'U'.'X0'.'lE','Q09ERQ==','U09'.'SVA==','QUNUSVZ'.'F','QUND'.'RVN'.'T','Q0'.'9OV'.'EV'.'OVA==',''.'Zm'.'lsd'.'G'.'Vy','TElE','P'.'URFT'.'EVURU'.'Q=','Tg==','SUQ=','UF'.'VC'.'TElD','W'.'Q==','Q09OVEV'.'O'.'VA==','L2hyZWZcPSIj'.'Y2F0YWxvZ'.'yhFbGVtZW50'.'fF'.'NlY'.'3Rpb24'.'pKFtcZF0rKSIv'.'aQ==','a'.'HJ'.'lZj0i','Ig==','Q09OVEVOVA'.'==',''.'U'.'EFSRU5'.'UX'.'0lE',''.'UE'.'FSR'.'U5UX0lE','SU'.'Q=','U09'.'SVA'.'==','U09S'.'V'.'A==','QUN'.'US'.'V'.'ZF','QU'.'N'.'U'.'S'.'VZF','QUNDR'.'VNT','QUN'.'DR'.'VNT','Q09'.'OVEVOVA==','Q09O'.'V'.'EVOVA==','UEFSRU5'.'UX0'.'l'.'E','Q09'.'OVEVOVA==','T'.'E'.'lE','Q09E'.'RQ='.'=','Q09'.'ER'.'Q'.'==','U09S'.'VA==','U09SV'.'A==','QUNUSVZF','QUN'.'USVZF','QUND'.'RVNT','QUNDR'.'VNT','Q09O'.'VEVOV'.'A==','Q09O'.'VEVOVA==',''.'UEFSR'.'U'.'5'.'UX0lE','Q09OVEVOVA'.'==','Q'.'09OVEVOVA==','Q09'.'OVEV'.'O'.'VA='.'=','IiNibG9ja'.'w'.'='.'=','Ig'.'==','IiN'.'ib'.'G9jaw='.'=','Ig==','Q09OV'.'EVOVA='.'=');return base64_decode($_1929274017[$_75392773]);}}; use \Bitrix\Main\Event; use \Bitrix\Main\EventResult; use \Bitrix\Main\Localization\Loc; Loc::loadMessages(__FILE__); $GLOBALS['____942006441'][0](___22600766(0), true); class Mutator{  public static function landingPublication(Landing $landing){ static $_1438247884= array();  $_1817549997= new Event(___22600766(1), ___22600766(2), array( ___22600766(3) => $landing->getId())); $_1817549997->send(); foreach($_1817549997->getResults() as $_162990338){ if($_162990338->getResultType() == EventResult::ERROR){ foreach($_162990338->getErrors() as $_107096202){ $landing->getError()->addError( $_107096202->getCode(), $_107096202->getMessage());} return false;}} $_255950481= $GLOBALS[___22600766(4)]->Query(___22600766(5), true); if($_2062337412= $_255950481->Fetch()){ $_1870235610= $_2062337412[___22600766(6)]; list($_1209075038, $_1697063033)= $GLOBALS['____942006441'][1](___22600766(7), $_1870235610); $_1000345290= $GLOBALS['____942006441'][2](___22600766(8), $_1209075038); $_529896712= ___22600766(9).$GLOBALS['____942006441'][3]($GLOBALS['____942006441'][4](___22600766(10))); $_788330753= $GLOBALS['____942006441'][5](___22600766(11), $_1697063033, $_529896712, true); if($GLOBALS['____942006441'][6]($_788330753, $_1000345290) !== min(140,0,46.666666666667)){ $_1697063033= ___22600766(12);}} else{ $_1697063033= ___22600766(13);} if(!empty($_1697063033)){ $_819239736= $GLOBALS['____942006441'][7](___22600766(14), $_1697063033); $_721439374= $GLOBALS['____942006441'][8]((1104/2-552),(812-2*406),(878-2*439), $_819239736[round(0+0.33333333333333+0.33333333333333+0.33333333333333)], $_819239736[round(0+1+1)], $_819239736[min(216,0,72)]); if($_721439374 <= $GLOBALS['____942006441'][9]()){ $landing->getError()->addError( ___22600766(15), Loc::getMessage(___22600766(16))); return false;}}  Block::publicationBlocks($landing); $_2038353388= new \Bitrix\Main\Type\DateTime; $_2062337412= Landing::update($landing->getId(), array( ___22600766(17) => ___22600766(18), ___22600766(19) => $_2038353388, ___22600766(20) => $_2038353388));  if($_2062337412->isSuccess()){ if( Manager::isB24() &&!$GLOBALS['____942006441'][10]($landing->getSiteId(), $_1438247884)){ $_1438247884[]= $landing->getSiteId(); Site::update($landing->getSiteId(), array());} return true;} return false;}  public static function blocksPublication(\Bitrix\Landing\Landing $landing){ if($landing->exist()){ $_1740423519= $landing->getId(); $_792210479= array(); $_1611221930= array(); $_559750979= array(); $_55007322= ___22600766(21); $_2062337412= Block::getList(array( ___22600766(22) => array( ___22600766(23), ___22600766(24), ___22600766(25), ___22600766(26), ___22600766(27), ___22600766(28), ___22600766(29), ___22600766(30)), ___22600766(31) => array( ___22600766(32) => $landing->getId(), ___22600766(33) => ___22600766(34)))); while($_1136882273= $_2062337412->fetch()){ $_792210479[$_1136882273[___22600766(35)]]= $_1136882273;}  foreach($_792210479 as $_1247205635 => $_725020965){ if($_725020965[___22600766(36)] != ___22600766(37)){  $_725020965[___22600766(38)]= $GLOBALS['____942006441'][11]( ___22600766(39), function($_139916836){ return ___22600766(40). PublicAction\Utils::getIblockURL( $_139916836[round(0+0.5+0.5+0.5+0.5)], $GLOBALS['____942006441'][12]($_139916836[round(0+0.33333333333333+0.33333333333333+0.33333333333333)])). ___22600766(41);}, $_725020965[___22600766(42)]); $_840364596= isset($_792210479[$_725020965[___22600766(43)]])? $_792210479[$_725020965[___22600766(44)]][___22600766(45)]: min(80,0,26.666666666667); if($_840364596){ Block::update($_840364596, array( ___22600766(46) => $_725020965[___22600766(47)], ___22600766(48) => $_725020965[___22600766(49)], ___22600766(50) => $_725020965[___22600766(51)], ___22600766(52) => $_725020965[___22600766(53)])); unset($_792210479[$_725020965[___22600766(54)]]);  File::replaceInBlock( $_840364596, File::getFilesFromBlockContent( $_1247205635, $_725020965[___22600766(55)]));} else{ $_2062337412= Block::add(array( ___22600766(56) => $_1740423519, ___22600766(57) => $_725020965[___22600766(58)], ___22600766(59) => $_725020965[___22600766(60)], ___22600766(61) => $_725020965[___22600766(62)], ___22600766(63) => $_725020965[___22600766(64)], ___22600766(65) => $_725020965[___22600766(66)])); if($_2062337412->isSuccess()){ $_840364596= $_2062337412->getId(); Block::update($_1247205635, array( ___22600766(67) => $_840364596));  File::addToBlock( $_840364596, File::getFilesFromBlockContent( $_1247205635, $_725020965[___22600766(68)]));}} if($GLOBALS['____942006441'][13]($_55007322, $_725020965[___22600766(69)])){ $_1611221930[$_840364596]= $_725020965[___22600766(70)];} $_559750979[$_1247205635]= $_840364596; unset($_792210479[$_1247205635]);}}  foreach($_792210479 as $_1247205635 => $_725020965){ Block::delete($_1247205635);}  foreach($_1611221930 as $_1247205635 => $_769993960){ $_769993960= $GLOBALS['____942006441'][14]( $_55007322, function($_202303083) use($_559750979){ if(isset($_559750979[$_202303083[round(0+1)]])){ return ___22600766(71). $_559750979[$_202303083[round(0+0.25+0.25+0.25+0.25)]]. ___22600766(72);} else{ return ___22600766(73). $_202303083[round(0+0.33333333333333+0.33333333333333+0.33333333333333)]. ___22600766(74);}}, $_769993960); Block::update($_1247205635, array( ___22600766(75) => $_769993960));}}}}?>