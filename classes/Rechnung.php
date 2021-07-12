<?php

require "fpdf.php";
class Rechnung extends FPDF{
   public function addLogo($datei){
       if(file_exists($datei)){
           $this->Image($datei, 10, 10);
       }else{
           die("Fehler:Logo-Datei $datei nicht gefunden");
       }
   }
   public function addAbsender(array &$absender){
       $this->SetFont('Arial', 'BU', 10);
       $this->setXY(10,40);
       $txt = $absender['Firma'].', '.$absender['Inhaber'].', '.$absender['Strasse'].', '
                .$absender['PLZ'].' '.$absender['Ort'];
       $this->Cell(10,10,$txt,0);
   }
   public function addEmpf(&$empf){
       $this->addMultiZeilen($empf,'',16,10,50);
   }
   public function addKontakt(&$kontakt){
       $this->addMultiZeilen($kontakt, '',14, 140, 40, 10, 8, 0, 2,
               true,true);
   }
   public function addTitle(&$title){
       $this->addMultiZeilen($title, 'B', 18, 10, 140);
   }
   public function addRechDaten(&$rechDaten){
       $this->addMultiZeilen($rechDaten,'B',14, 140, 136, 40, 8, 0, 2, 
               true,false);
   }
   public function addRechTable(&$rechTable,$w1,$w2){
       $this->setXY(10,170);
       $lastRow = array_key_last($rechTable);//letzte Zeile
       $last = array_key_last($rechTable[0]);//Letzte Element in jeder Zeile
       foreach ($rechTable as $keyRow => $row) {
           foreach($row as $key => $value){
                if($keyRow == 0){
                    if($key == 0){
                        $this->SetFont('Arial', 'B', 12);
                        $this->Cell($w1,8,$value,'TBL',0);
                    }
                    else if($key == $last){
                        $this->Cell($w2,8,$value,'TRB',1);
                    }
                    else{
                        $this->Cell($w2,8,$value,'TB',0);
                    }
                }
                else if($keyRow == $lastRow){
                    if($key == 0){
                        $this->SetFont('Arial', '', 12);
                        $this->Cell($w1,8,'Pos.'.($key + 1)." ".$value,'LB',0);
                    }
                    else if($key == $last){
                        $this->Cell($w2,8,$value,'BRL',1,'R');
                    }
                    else{
                        if($key==2)
                             $this->Cell($w2,8,$value,'LB',0,'L');
                        else 
                            $this->Cell($w2,8,$value,'LB',0,'R');
                    }
                }     
                else{
                    if($key == 0){
                        $this->SetFont('Arial', '', 12);
                        $this->Cell($w1,8,'Pos.'.($key + 1)." ".$value,'L',0);
                    }
                    else if($key == $last){
                        $this->Cell($w2,8,$value,'RL',1,'R');
                    }
                    else{
                        if($key==2)
                             $this->Cell($w2,8,$value,'L',0,'L');
                        else 
                            $this->Cell($w2,8,$value,'L',0,'R');
                    }    
                }
            }
                      
       }
   }
   public function rechBetrag(&$rechTable):array{
       $nettobetrag = 0;
       $steuer = 0;
       $bruttobetrag = 0;
       $last = array_key_last($rechTable[0]);
       foreach ($rechTable as $key => $value) {
           if($key != 0){
               $preis = str_replace(',','.',str_replace('.','',$value[$last]));
               $nettobetrag += sscanf($preis, '%f')[0];
           }   
       }
       $steuer = round($nettobetrag * 0.19,2);
       $bruttobetrag = $steuer + $nettobetrag;
       //float to string
       $nettobetrag = 'EUR '.number_format($nettobetrag,2,",",".");
       $steuer = 'EUR '.number_format($steuer,2,",",".");
       $bruttobetrag = 'EUR '.number_format($bruttobetrag,2,",",".");
       
       return [
           ['','Nettobetrag',$nettobetrag],
           ['zuzueglich 19% Mwst aus',$nettobetrag, $steuer],
           ['','Endbetrag',$bruttobetrag],
           ];         
//       return [
//           'Nettobetrag' => $nettobetrag,
//           'zuzueglich 19% Mwst aus' => [$nettobetrag, $steuer],
//           'Endbetrag' => $bruttobetrag,
//           ];
   }
   public function addBetrag(&$betrag,$y){
       
       foreach ($betrag as $keyRow => $row) {
           $this->setXY(70,$y);
           if($keyRow != 1)
               $this->setFont('Arial', 'B', 12);
           else
               $this->setFont('Arial', '', 12);
           foreach ($row as $key => $value) {
                  $this->Cell(40,8,$value,0,0,'R');
           }
           $this->Ln();
           $y+=8;
       }
   }
//   public function addBetrag(&$betrag,$y){
//       foreach ($betrag as $key => $value) {
//           if($key != 'zuzueglich 19% Mwst aus'){
//               $this->setFont('Arial', 'B', 12);
//               $this->setXY(110,$y+=8);
//               $this->Cell(40,8,$key,0,0,'R');
//               $this->Cell(40,8,$value,0,1,'R');
//           }
//           else{
//               $this->setFont('Arial', '', 12);
//               $this->setXY(10,$y+=8);
//               $this->Cell(100,8,$key,0,0,'R');
//               foreach ($value as $betragElement) {
//                   $this->Cell(40,8,$betragElement,0,0,'R');
//               }
//               $this->Ln();
//           }
//        }
//    }
   private function addMultiZeilen(
           array &$datei,$style,$fontSize,$x,$y,
           $w = 10,$h = 10,$border = 0,$pos = 2,
           $withKey = false,$keyValueSpace = false){
       /*
        * für 1-dimensional array(indiziertes Array oder associatives Array)
        */
       /* $withKey->true:output mit keys, false:output ohne keys
        * Die keys sind immer mit die values in einer Zeile.
        * $keyValueSpace->true:nur ein Leerzeichen,false->wie einer Tabelle
        * das Key und value sing immer in einer Zeile
        */
        $this->SetFont('Arial', $style, $fontSize);
        $this->setXY($x,$y);
        foreach ($datei as $key => $value) {        
            if($withKey == false){
                 $this->Cell($w,$h,$value,$border,$pos);
            }
            else if($keyValueSpace == true){
                  $this->Cell($w,$h,$key." ".$value,$border,$pos);               
            }
            else {                
                 $this->Cell($w,$h,$key,$border,0);
                 $this->Cell($w,$h,$value,$border,1);
                 $this->setX($x);//unter dem Key cell eine neu Zeile beginnen
            }
                
        } 
    }
}
