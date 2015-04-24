<?php
    namespace mep;
    require 'MyApi.class.php';
        if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
        }


        try {
            $API = new MyAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
            $str = $API->processAPI();

            $strAr = explode('\\n', trim($str,'"'));        //um Zeilenumbrueche zu ermoeglichen
                                                            //und ohne laestige Anfuehrungszeichen vorne und hinten
            foreach ($strAr as $strAus) {
                echo $strAus."\n";
            }                                               //bis hier.

        } catch (\Exception $e) {
            echo json_encode(Array("error" => $e->getMessage()));
        }

