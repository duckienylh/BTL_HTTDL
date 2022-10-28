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
        else if($functionname == 'getGeoPointoAjax' )
            $aResult = getGeoPointoAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getInfoPointoAjax' )
            $aResult = getInfoPointoAjax($paPDO, $paSRID, $paPoint);
        else if($functionname == 'getGeoRailWayoAjax' )
            $aResult = getGeoRailWayoAjax($paPDO, $paSRID, $paPoint);
        echo $aResult;
    
        closeDB($paPDO);
    }

    if (isset($_POST['name'])) {
        $paPDO = initDB();
        $paSRID = '4326';
        $name = $_POST['name'];
        if($name != null)
            $aResult = seacherCity($paPDO, $paSRID, $name);
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
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT gid, shape_leng, shape_area from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>gid_1: '.$item['gid'].'</td></tr>';
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
    function getGeoPointoAjax($paPDO,$paSRID,$paPoint)
    {
        
        // $paPoint = str_replace(',', ' ', $paPoint);
        
        // $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cang\" where geom like '0101000020E6100000F0F1DA56C74C5B40806F47AA82742840'";
        // // $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cang\" where ST_Buffer('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        // // $mySQLStr = "SELECT ST_AsGeoJson(ST_Buffer('SRID=".$paSRID.";".$paPoint."'::geometry,50)) as geo";
        // // Select
        // //     ST_AsGeoJson(ST_Buffer(
        // //     'POINT(107.84179687499999 16.291142264799106)'::geometry,50));
    
        // $result = query($paPDO, $mySQLStr);
        
        // if ($result != null)
        // {
        //     // Lặp kết quả
        //     foreach ($result as $item){
        //         return $item['geo'];
        //     }
        // }
        // else
        //     return "null";

        $paPoint = str_replace(',', ' ', $paPoint);   
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from cang";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from cang where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } else
            return "null";
    }

    function getInfoPointoAjax($paPDO,$paSRID,$paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from cang";
        $mySQLStr = "SELECT *  from cang where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
        $result = query($paPDO, $mySQLStr);
    
        if ($result != null) {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item) {
                $resFin = $resFin . '<tr><td>G_ID: ' . $item['gid'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Tên cảng : ' . $item['ten_cang'] . '</td></tr>';
                $resFin = $resFin . '<tr><td>Loại cảng : ' . $item['loai'] . '</td></tr>';
                break;
            }
            $resFin = $resFin . '</table>';
            return $resFin;
        } else
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

    function seacherCity($paPDO, $paSRID, $name)
    {   
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gadm41_vnm_1 where name_1 like '$name'";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } else
            return "null";
    }
?>