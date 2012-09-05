<?php
class ReportsModel extends ModelBase
{
    public function exportTiendas()
    {
        /** Include path **/
        #set_include_path(get_include_path() . PATH_SEPARATOR . '../libs/PHPExcel/');

        require_once 'libs/PHPExcel.php';
        require_once 'libs/PHPExcel/IOFactory.php';

        $Fecha = date('d-m-Y');
        $Hora = date('H:i:s');
        $fechahora = "(".$Fecha."-".$Hora.")";

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("LG ELECTRONICS CHILE");
        $objPHPExcel->getProperties()->setLastModifiedBy("LG ELECTRONICS CHILE");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX ");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX ");

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:T1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:T1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Codigo');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Codigo BTK');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Tienda');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Direccion');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Codigo Cliente');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Cliente');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Codigo Agrupación');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Agrupación');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Codigo Tipo');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Tipo');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Codigo Comuna');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Comuna');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Codigo Ciudad');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Ciudad');
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Codigo Region');
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Region');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Codigo Zona');
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Zona');
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Codigo Estado');
        $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Estado');

        $a=2; $b=2; $c=2;
        $d=2; $e=2; $f=2;
        $g=2; $h=2; $i=2;
        $j=2; $k=2; $l=2;
        $m=2; $n=2; $ni=2;
        $o=2; $p=2; $q=2;
        $r=2; $s=2; $t=2;

        $consulta = $this->db->prepare("SELECT 
        TI.COD_TIENDA,
        TI.COD_BTK,
        TI.NOM_TIENDA,
        TI.DIREC_TIENDA,
        CL.COD_CLIENTE,
        CL.NOM_CLIENTE,
        ET.COD_ESTADO,
        ET.NOM_ESTADO,
        RG.COD_REGION,
        RG.NOM_REGION,
        CT.COD_CIUDAD,
        CT.NOM_CIUDAD,
        CM.COD_COMUNA,
        CM.NOM_COMUNA,
        ZN.COD_ZONA,
        ZN.NOM_ZONA,
        TT.COD_TIPO,
        TT.NOM_TIPO,
        AG.COD_AGRUPACION,
        AG.NOM_AGRUPACION
        FROM T_TIENDA TI
        INNER JOIN T_CLIENTE CL ON CL.COD_CLIENTE = TI.CLIENTE_COD_CLIENTE
        INNER JOIN T_TIENDA_ESTADO ET ON ET.COD_ESTADO = TI.ESTADO_COD_ESTADO
        INNER JOIN T_COMUNA CM ON (CM.COD_COMUNA = TI.COMUNA_COD_COMUNA)
            AND  (CM.CIUDAD_COD_CIUDAD = TI.COMUNA_CIUDAD_COD_CIUDAD)
            AND  (CM.CIUDAD_REGION_COD_REGION = TI.COMUNA_CIUDAD_REGION_COD_REGION)
        INNER JOIN T_CIUDAD CT ON CT.COD_CIUDAD = CM.CIUDAD_COD_CIUDAD
        INNER JOIN T_REGION RG ON RG.COD_REGION = CM.CIUDAD_REGION_COD_REGION
        INNER JOIN T_TIENDA_ZONA ZN ON ZN.COD_ZONA = TI.ZONA_COD_ZONA
        INNER JOIN T_TIPO_TIENDA TT ON TT.COD_TIPO = TI.TIPO_TIENDA_COD_TIPO
        INNER JOIN T_AGRUPACION AG ON AG.COD_AGRUPACION = TI.AGRUPACION_COD_AGRUPACION 
        ORDER BY NOM_CLIENTE,NOM_TIENDA");

        if($consulta->execute())
        {
            while($row = $consulta->fetch(PDO::FETCH_ASSOC))
            {
                $objPHPExcel->getActiveSheet()->getStyle('A'.$a.':S'.$s.'')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $ctienda = $row['COD_TIENDA'];
                $cod_btk = $row['COD_BTK'];
                $tienda = $row['NOM_TIENDA'];
                $direccion = $row['DIREC_TIENDA'];
                $ccodigo = $row['COD_CLIENTE'];
                $cnombre = $row['NOM_CLIENTE'];
                $cgrupo = $row['COD_AGRUPACION'];
                $gnombre = $row['NOM_AGRUPACION'];
                $tcodigo = $row['COD_TIPO'];
                $ttipo = $row['NOM_TIPO'];
                $ccomuna = $row['COD_COMUNA'];
                $ncomuna = $row['NOM_COMUNA'];
                $cciudad = $row['COD_CIUDAD'];
                $nciudad = $row['NOM_CIUDAD'];
                $cregion = $row['COD_REGION'];
                $nregion = $row['NOM_REGION'];
                $czona = $row['COD_ZONA'];
                $nzona = $row['NOM_ZONA'];
                $cestado = $row['COD_ESTADO'];
                $nestado = $row['NOM_ESTADO'];

                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$a++.'', ''.$ctienda.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$b++.'', ''.$cod_btk.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$c++.'', ''.$tienda.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$d++.'', ''.$direccion.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$e++.'', ''.$ccodigo.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$f++.'', ''.$cnombre.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$g++.'', ''.$cgrupo.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$h++.'', ''.$gnombre.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i++.'', ''.$tcodigo.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$j++.'', ''.$ttipo.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$k++.'', ''.$ccomuna.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$l++.'', ''.$ncomuna.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('M'.$m++.'', ''.$cciudad.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('N'.$n++.'', ''.$nciudad.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('O'.$o++.'', ''.$cregion.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('P'.$p++.'', ''.$nregion.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$q++.'', ''.$czona.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('R'.$r++.'', ''.$nzona.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('S'.$s++.'', ''.$cestado.'');
                $objPHPExcel->getActiveSheet()->SetCellValue('T'.$t++.'', ''.$nestado.'');
            }
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="SOM-TIENDAS_'.$fechahora.'.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output'); 
    }

    /**
     * export tiendas to excel csv file 
     */
    public function exportTiendasToCsv(){
        $Fecha = date('d-m-Y');
        #$Hora = date('H:i:s');
        #$fechahora = "(".$Fecha."-".$Hora.")";

        $consulta = $this->db->prepare("
        SELECT
        'COD_TIENDA'
        ,'COD_BTK'
        ,'TIENDA_NAME'
        ,'DIRECCION'
        ,'COD_CLIENTE'
        ,'CLIENTE_NAME'
        ,'COD_REGION'
        ,'REGION_NAME'
        ,'COD_CIUDAD'
        ,'CIUDAD_NAME'
        ,'COD_COMUNA'
        ,'COMUNA_NAME'
        ,'COD_ZONA'
        ,'ZONA_NAME'
        ,'COD_TIPO'
        ,'TIPO_NAME'
        ,'COD_AGRUPACION'
        ,'AGRUPACION_NAME'
        ,'COD_ESTADO'
        ,'ESTADO_NAME'
        UNION ALL
        SELECT 
        TI.COD_TIENDA,
        TI.COD_BTK,
        TI.NOM_TIENDA,
        TI.DIREC_TIENDA,
        CL.COD_CLIENTE,
        CL.NOM_CLIENTE,
        RG.COD_REGION,
        RG.NOM_REGION,
        CT.COD_CIUDAD,
        CT.NOM_CIUDAD,
        CM.COD_COMUNA,
        CM.NOM_COMUNA,
        ZN.COD_ZONA,
        ZN.NOM_ZONA,
        TT.COD_TIPO,
        TT.NOM_TIPO,
        AG.COD_AGRUPACION,
        AG.NOM_AGRUPACION,
        ET.COD_ESTADO,
        ET.NOM_ESTADO
        FROM T_TIENDA TI
        INNER JOIN T_CLIENTE CL ON CL.COD_CLIENTE = TI.CLIENTE_COD_CLIENTE
        INNER JOIN T_TIENDA_ESTADO ET ON ET.COD_ESTADO = TI.ESTADO_COD_ESTADO
        INNER JOIN T_COMUNA CM ON (CM.COD_COMUNA = TI.COMUNA_COD_COMUNA)
            AND  (CM.CIUDAD_COD_CIUDAD = TI.COMUNA_CIUDAD_COD_CIUDAD)
            AND  (CM.CIUDAD_REGION_COD_REGION = TI.COMUNA_CIUDAD_REGION_COD_REGION)
        INNER JOIN T_CIUDAD CT ON CT.COD_CIUDAD = CM.CIUDAD_COD_CIUDAD
        INNER JOIN T_REGION RG ON RG.COD_REGION = CM.CIUDAD_REGION_COD_REGION
        INNER JOIN T_TIENDA_ZONA ZN ON ZN.COD_ZONA = TI.ZONA_COD_ZONA
        INNER JOIN T_TIPO_TIENDA TT ON TT.COD_TIPO = TI.TIPO_TIENDA_COD_TIPO
        INNER JOIN T_AGRUPACION AG ON AG.COD_AGRUPACION = TI.AGRUPACION_COD_AGRUPACION 
        INTO OUTFILE '".$this->apache."htdocs".$this->root."views/tmp/SOM_TIENDAS_".$Fecha.".csv'
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'");
        
        $consulta->execute();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM_TIENDAS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_TIENDAS_'.$Fecha.'.csv');
        unlink(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_TIENDAS_'.$Fecha.'.csv');
        
//        $error = $consulta->errorInfo();
//        echo $error[2];
    }
    
    /**
     * Export models to excel csv file 
     */
    public function exportModelsToCsv(){
        $Fecha = date('d-m-Y');
        #$Hora = date('H:i:s');
        #$fechahora = "(".$Fecha."-".$Hora.")";

        $consulta = $this->db->prepare("
        SELECT
        'MODEL'
        ,'MODEL_SUFFIX'
        ,'COD_GBU'
        ,'GBU_NAME'
        ,'COD_BRAND'
        ,'BRAND_NAME'
        ,'COD_SEGMENT'
        ,'SEGMEMT_NAME'
        ,'COD_SUB_SEGMENT'
        ,'SUB_SEGMENT_NAME'
        ,'COD_MICRO_SEGMENT'
        ,'MICRO_SEGMENT_NAME'
        ,'COD_ESTADO'
        ,'ESTADO_NAME'
        UNION ALL
        SELECT 
        PD.COD_MODEL 
        ,PD.COD_MODEL_SUFFIX
        ,PD.COD_GBU 
        ,LGBU.NAME_GBU 
        ,PD.COD_BRAND 
        ,BR.NAME_BRAND 
        ,PD.COD_SEGMENT 
        ,SG.NAME_SEGMENT 
        ,PD.COD_SUB_SEGMENT 
        ,SS.NAME_SUB_SEGMENT 
        ,PD.COD_MICRO_SEGMENT 
        ,MS.NAME_MICRO_SEGMENT 
        ,PD.COD_ESTADO 
        ,ST.NAME_ESTADO 
        FROM T_PRODUCT PD
        INNER JOIN T_GBU LGBU 
            ON (LGBU.COD_GBU = PD.COD_GBU 
                AND LGBU.COD_CATEGORY = PD.COD_CATEGORY)
        INNER JOIN T_SEGMENT SG 
            ON (SG.COD_SEGMENT = PD.COD_SEGMENT
                AND SG.COD_GBU = PD.COD_GBU)
        INNER JOIN T_SUB_SEGMENT SS
            ON (SS.COD_SUB_SEGMENT = PD.COD_SUB_SEGMENT 
                AND SS.COD_GBU = PD.COD_GBU)
        INNER JOIN T_MICRO_SEGMENT MS
            ON (MS.COD_MICRO_SEGMENT = PD.COD_MICRO_SEGMENT
                AND MS.COD_GBU = PD.COD_GBU)
        INNER JOIN T_BRAND BR 
            ON BR.COD_BRAND=PD.COD_BRAND
        INNER JOIN T_PRODUCT_ESTADO ST
            ON ST.COD_ESTADO=PD.COD_ESTADO
        INTO OUTFILE '".$this->apache."htdocs".$this->root."views/tmp/SOM_MODELOS_".$Fecha.".csv'
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'");
        
        $consulta->execute();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM-MODELOS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        unlink(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        
//        $error = $consulta->errorInfo();
//        echo $error[2];
    }
}
?>