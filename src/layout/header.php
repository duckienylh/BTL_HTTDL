<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Openlayers test</title>
    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-1.12.3.min.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid  ">
            <div class="col-9 d-flex justify-content-center" id="navbarExample01">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="../src/CMR_highLightObj.php">Thành phố</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../src/CMR_popupObj.php">Cảng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../src/CMR_RailWaysObj.php">Đường sắt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../src/CMR_airportObj.php">Sân bay</a>
                    </li>
                    
                </ul>
            </div>
            <div class="col-3">
                <div class="d-flex justify-content-end">
                    <input class="form-control me-2" placeholder="Tìm kiếm tên tỉnh, sân bay, hải cảng,..." aria-label="Search" id="ctiy">
                    <button class="btn btn-outline-success" id="btnSeacher">Search</button>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar -->
</head>
