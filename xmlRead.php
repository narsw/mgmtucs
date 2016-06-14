<?php

$str = ' <aaaLogin cookie="" response="yes" outCookie="1463106646/339d4405-8885-452c-8199-ef9de68b3cba" outRefreshPeriod="600" outPriv="admin,read-only" outDomains="" outChannel="noencssl" outEvtChannel="noencssl" outSessionId="web_9013_A" outVersion="2.2(1c)" outName="admin"> </aaaLogin>';

//echo $str;

$object = simplexml_load_string($str);
var_dump($object);

?>
