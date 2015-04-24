 <?php
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

?> 