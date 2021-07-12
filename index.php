<?php

require "classes/Rechnung.php";

$absender = [
    'Firma'     => 'KIS-Computerservice',
    'Inhaber'   => 'M. Woeh',
    'Strasse'   => 'Muehleweg 3',
    'PLZ'       => '25496',
    'Ort'       => 'Kummer',
];

$empf = [
    'Title' => 'Herr',
    'Name' => 'Max Mustermann',
    'Strasse' => 'Hofweg 20',
    'PLZ' => '10115 Berlin',
];

$kontakt = [
    'Telefon' => '04101793371',
    'Telefax' => '',
    'Ust-IDNr' => 'DE XXXXXXXX',
    'Mail' => 'info@kis-com.de',
    'Internet' => 'www.kis-com.de',
    'Bank' => 'Postbank Hamburg',
    'Kto.' => '54564545',
    'BLZ.' => '200 100 20',
    
];
$title = ['title' => 'Rechnung'];
$rechDaten = [
    'Kunden-Nr.' => 'D000290',
    'Unser Vorgang' => 'R001018',
    'Datum' => '06.01.2010',
];
$rechTable = [
    ['Artikel oder Leistung','Menge','Einheit','MwSt','E-Preis','G-Preis',],
    ['Service am 24.12.2009','1.25','Std.','19%','49,00','61,25',],
    ['Nutzungesgebuehr Repro','1','Jahr','19%','156,00','156,00',],
];

$pdf = new Rechnung();
$pdf->AddPage();
$pdf->addlogo("logo.png");
$pdf->addAbsender($absender);
$pdf->addEmpf($empf);
$pdf->addKontakt($kontakt);
$pdf->addTitle($title);
$pdf->addRechDaten($rechDaten);
$pdf->addRechTable($rechTable,80,20);
$betrag = $pdf->rechBetrag($rechTable);
$pdf->addBetrag($betrag,194);
$pdf->output();

//$pdf1 = new FPDF();
//$pdf1->AddPage();
//$pdf1->SetMargins(40, 40);
//$pdf1->Line(10,20,50,60);
//$pdf1->Output();