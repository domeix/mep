<?php
namespace mep;
require 'API.class.php';
class MyAPI extends API{
    protected $User;

    /**
     * @param $request
     * @param $origin
     * @throws \Exception
     */
    public function __construct($request, $origin) {
        parent:: __construct($request);

        //Abstracted out for example
        $APIKey = "123";
        $User = "Dominik";

        /*if(!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } elseif($origin == $APIKey)     {     //!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        }  elseif (array_key_exists('token', $this->request) && !$User->get('token', $this->request['token'])) {
            throw new Exception ('Invalid User Token');
        }*/

      #  echo "Hallo ".$origin."! ";

        $this->User = $User;
    }

    protected function example() {
        echo "Welcome at Endpoint: $this->endpoint.\n";
        switch ($this->method):
            case 'GET':
                // ------ Fehler abfangen
                return "Your name is $this->User you're ID is ".$this->args[0];
            case 'PUT':

            case 'DELETE':

            default:
                return "Only accepts GET requests.";
        endswitch;
    }


    protected function dbBenutzer() {
        echo "Welcome at Endpoint: $this->endpoint.\n";

        echo $this->args[0]."\n";
        //// ------ Fehler abfangen
        $db = mysqli_connect("localhost", $this->args[0], "", "meptest1");

        switch ($this->method):
            case 'GET':
// ------ Fehler abfangen
                $query = "select ".$this->args[1]." from benutzer";

                $result = mysqli_query($db, $query);

                $toReturn = "";

                foreach ($result as $row) {
                    foreach ($row as $tupel) {
                        $toReturn = "$toReturn $tupel |";
                    }
                    $toReturn = $toReturn."\n";
                }

                return $toReturn;
            case 'PUT':
// ------ Fehler abfangen
                $query = "insert into benutzer (name, attrInt) values ('".$this->args[1]."', 4465546);";

                $result = mysqli_query($db, $query);
// ------ Fehler abfangen
                echo $_REQUEST['query'];

                return ($result == 1) ? "erfolgreich eingefuegt" : "nicht eingefuegt";
            break;

            case 'DELETE':

            default:
                return "Only accepts GET requests.";
        endswitch;
    }


    protected function hl7Import() {
        $datei = "orm.dat";
        // Termin-ID
        $line ="MSH";
        $vars = array ();
        $txt = file_get_contents($datei);
        $anf = strpos($txt, $line);
        $txt = substr($txt, $anf);
        $end = strpos($txt, "PID");
        $txt = substr($txt, 0, $end);
        $vars = explode("|",$txt);
        $vars= str_replace("<","",$vars);
        $terminID= $vars[9];

        //Nachname, Vorname, ID, Geburtsdatum
        $line ="PID";
        $vars = array ();
        $txt = file_get_contents($datei);
        $anf = strpos($txt, $line);
        $txt = substr($txt, $anf);
        $end = strpos($txt, "PV1");
        $txt = substr($txt, 0, $end);
        $vars = explode("|",$txt);
        $vars= str_replace("<","",$vars);
        $id= $vars[3];
        $geburtsdatum= $vars[7];
        $name= explode("^",$vars[5]);

        //Anfordernde pfleg. OE, anfordernde fachl. OE, Fallnummer,
        $line ="PV1";
        $vars = array ();
        $txt = file_get_contents($datei);
        $anf = strpos($txt, $line);
        $txt = substr($txt, $anf);
        $end = strpos($txt, "ORC");
        $txt = substr($txt, 0, $end);
        $vars = explode("|",$txt);
        $vars= str_replace("<","",$vars);
        $anforderndeoe= explode("^^^",$vars[3]);
        $fallnr= $vars[19];

        //Termin, erbringende pfleg. OE, erbringende fachl. OE
        $line ="ORC";
        $vars = array ();
        $txt = file_get_contents($datei);
        $anf = strpos($txt, $line);
        $txt = substr($txt, $anf);
        $end = strpos($txt, "OBR");
        $txt = substr($txt, 0, $end);
        $vars = explode("|",$txt);
        $vars= str_replace("<","",$vars);
        $termin= explode("^^^",$vars[7]);
        $erbringendeoe=explode("^^^",$vars[13]);

        //Autragsnummer, Geräteraum
        $line ="OBR";
        $vars = array ();
        $txt = file_get_contents($datei);
        $anf = strpos($txt, $line);
        $txt = substr($txt, $anf);
        $end = strpos($txt, "ZDS");
        $txt = substr($txt, 0, $end);
        $vars = explode("|",$txt);
        $vars= str_replace("<","",$vars);
        var_dump($vars);
        $auftragsnr= $vars[18];
        $geraeteraum= $vars[25];

        $patient= array($terminID,$name[0],$name[1],$id,$geburtsdatum,$anforderndeoe[0],$anforderndeoe[1],$fallnr,$termin[1],$erbringendeoe[0],$erbringendeoe[1],$auftragsnr,$geraeteraum);
        #file_put_contents('array.txt',implode(',',$patient));

        $db = mysqli_connect("localhost", "root", "", "meptest1");

        $query = "INSERT INTO Patientendaten (terminID, Nachname, Vorname, patID, Geburtsdatum, anfPflegOE, anfFachlOE, FallNr, Termin, erbrPflegOE, erbrFachlOE, AuftragsNr, Geraeteraum) VALUES ($patient[0], $patient[1], $patient[2],$patient[3],$patient[4],$patient[5],$patient[6],$patient[7],$patient[8],$patient[9],$patient[10],$patient[11],$patient[12]);";

        $result = mysqli_query($db, $query);

        return ($result == 1) ? "erfolgreich eingefuegt" : "nicht eingefuegt";
    }
}