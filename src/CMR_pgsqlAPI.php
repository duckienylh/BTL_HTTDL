<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getPopupCMRToAjax')
            $aResult = getPopupCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getGeoPointCMRToAjax')
            $aResult = getGeoPointCMRToAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getGeoRailWayoAjax' )
            $aResult = getGeoRailWayoAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getInfoAirportCMRToAjax' )
            $aResult = getInfoAirportCMRToAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getGeoAirportCMRToAjax' )
            $aResult = getGeoAirportCMRToAjax($paPDO, $paSRID, $paPoint);
        echo $aResult;
    
        closeDB($paPDO);
    }

    if (isset($_POST['functionname2'])) {
        $paPDO = initDB();
        $paSRID = '4326';
        $name = $_POST['name'];
        $functionname2 = $_POST['functionname2'];
        if($functionname2 == 'getGeoSearchCity')
            $aResult = getGeoSearchCity($paPDO, $paSRID, $name);
        else if($functionname2 == 'getInfoSearchoAjax')
            $aResult = getInfoSearchoAjax($paPDO, $paSRID, $name);
        
        echo $aResult;
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=BTL;port=5432', 'postgres', '20122001');
        return $paPDO;  
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $mySQLStr = "SELECT gid, shape_leng, shape_area, name_1 from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>gid_1: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên tỉnh: '.$item['name_1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.$item['shape_leng'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['shape_area'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
    function getPopupCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"cang\" ";
        $mySQLStr = "SELECT * from \"cang\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);
        if ($result != null)
        {
        
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên cảng: '.$item['ten_cang'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Loại: '.$item['loai'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
    function getGeoPointCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"cang\" ";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cang\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }

    function getInfoAirportCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"sanbay\" ";
        $mySQLStr = "SELECT * from \"sanbay\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);
        if ($result != null)
        {
        
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên sân bay: '.$item['ten'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Loại: '.$item['loai'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }

    function getGeoAirportCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from \"sanbay\" ";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"sanbay\" where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function getGeoRailWayoAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);   
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from railways";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from railways where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        // Lặp kết quả
        foreach ($result as $item) {
            return $item['geo'];
        }
    } else
        return "null";
            
    }
    
    function getGeoSearchCity($paPDO, $paSRID, $name)
    {   
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gadm41_vnm_1 where name_1 like '$name'";
        $mySQLStr1 = "SELECT ST_AsGeoJson(geom) as geo from cang where ten_cang like '$name'";
        $mySQLStr2 = "SELECT ST_AsGeoJson(geom) as geo from sanbay where ten like '$name'";


        $result = query($paPDO, $mySQLStr);
        $result1 = query($paPDO, $mySQLStr1);
        $result2 = query($paPDO, $mySQLStr2);
       
        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } 
        if ($result1 != null) {
            // Lặp kết quả
            foreach ($result1 as $item) {
                return $item['geo'];
            }
        }
        if ($result2 != null) {
            // Lặp kết quả
            foreach ($result2 as $item) {
                return $item['geo'];
            }
        }
        else
            return "null";
    }

    function getInfoSearchoAjax($paPDO, $paSRID, $name)
    {   
        $mySQLStr = "SELECT gid, shape_leng, shape_area, name_1 as geo from gadm41_vnm_1 where name_1 like '$name'";
        $mySQLStr1 = "SELECT * from cang where ten_cang like '$name'";
        $mySQLStr2 = "SELECT * from sanbay where ten like '$name'";

        $result = query($paPDO, $mySQLStr);
        $result1 = query($paPDO, $mySQLStr1);
        $result2 = query($paPDO, $mySQLStr2);

        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>gid_1: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên tỉnh: '.$name.'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.$item['shape_leng'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.$item['shape_area'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        if ($result1 != null)
        {
        
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result1 as $item){
                $resFin = $resFin.'<tr><td>gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên cảng: '.$item['ten_cang'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Loại: '.$item['loai'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        if ($result2 != null)
        {
        
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result2 as $item){
                $resFin = $resFin.'<tr><td>gid: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tên sân bay: '.$item['ten'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Loại: '.$item['loai'].'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
?>