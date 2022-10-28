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
        //đường giao thông
        else if ($functionname == 'getInfoRailsToAjax')
            $aResult = getInfoRailsToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getRailsToAjax')
            $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getRailsToAjax')
            $aResult = getRailsToAjax($paPDO, $paSRID, $paPoint);
        
        
            echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=BTL_NHOM2;port=5432', 'postgres', 'hp123456#');
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
    function example1($paPDO)
    {
        $mySQLStr = "SELECT * FROM \"gadm41_vnm_1\"";
        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['name_0'] . ' - '. $item['name_1'];
                echo "<br>";
            }
        }
        else
        {
            echo "example1 - null";
            echo "<br>";
        }
    }
    function example2($paPDO)
    {
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\"";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
    function example3($paPDO,$paSRID,$paPoint)
    {
        echo $paPoint;
        echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        echo $paPoint;
        echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        echo $mySQLStr;
        echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
                echo "<br><br>";
            }
        }
        else
        {
            echo "example2 - null";
            echo "<br>";
        }
    }
    function getResult($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
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
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm41_vnm_1\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
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

    // hightlight đường
    function getRailsToAjax($paPDO, $paSRID, $paPoint)
    {
        $paPoint = str_replace(',', ' ', $paPoint);   
        $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from duongsat";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from duongsat where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.1";
        $result = query($paPDO, $mySQLStr);

        if ($result != null) {
            // Lặp kết quả
            foreach ($result as $item) {
                return $item['geo'];
            }
        } else
            return "null";
    }
    //Truy van thong tin streest
function getInfoRailsToAjax($paPDO, $paSRID, $paPoint)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    $strDistance = "ST_Distance('" . $paPoint . "',ST_AsText(geom))";
    $strMinDistance = "SELECT min(ST_Distance('" . $paPoint . "',ST_AsText(geom))) from duongsat";
    $mySQLStr = "SELECT *  from duongsat where " . $strDistance . " = (" . $strMinDistance . ") and " . $strDistance . " < 0.5";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item) {
            $resFin = $resFin . '<tr><td>G_ID: ' . $item['gid'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Tên đường : ' . $item['name'] . '</td></tr>';
            $resFin = $resFin . '<tr><td>Loại đường : ' . $item['type'] . '</td></tr>';
            break;
        }
        $resFin = $resFin . '</table>';
        return $resFin;
    } else
        return "null";
}
?>