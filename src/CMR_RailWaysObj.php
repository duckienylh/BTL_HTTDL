<?php include './layout/header.php' ?>
        <style>
            .map, .righ-panel {
                height: 98vh;
                width: 80vw;
                float: left;
            }
            .map {
                border: 1px solid #000;
            }
        </style>
    <body onload="initialize_map();">
        <table>
            <tr>
                <td>
                    <div id="map" class="map" style="width: 80vw; height: 100vh;"></div>
                </td>
                <td>
                    <div id="info"></div>
                </td>
            </tr>
        </table>
        <?php include 'CMR_pgsqlAPI.php' ?>
        <script>
            var ctiy = document.getElementById("ctiy");
            var format = 'image/png';
            var map;
            var minX = 102.107955932617;
            var minY = 8.30629730224609;
            var maxX = 109.505798339844;
            var maxY = 23.4677505493164;
            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 6;
            function initialize_map() {
             
                layerBG = new ol.layer.Tile({
                    source: new ol.source.OSM({})
                });
    
                var layerCMR_adm1 = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                        ratio: 1,
                        url: 'http://localhost:8080/geoserver/btl/wms?',
                        params: {
                            'FORMAT': format,
                            'VERSION': '1.1.1',
                            STYLES: '',
                            LAYERS: 'railways',
                        }
                    })
                });
                var viewMap = new ol.View({
                    center: ol.proj.fromLonLat([mapLng, mapLat]),
                    zoom: mapDefaultZoom
                });
                map = new ol.Map({
                    target: "map",
                    layers: [layerBG, layerCMR_adm1],
                    view: viewMap
                });
                
                var styles = {
                    'MultiLineString': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'red',
                        width: 3
                    })
                    }),
                    'MultiPolygon': new ol.style.Style({
                        fill: new ol.style.Fill({
                            color: 'orange'
                        }),
                        stroke: new ol.style.Stroke({
                            color: 'yellow', 
                            width: 2
                        })
                    })
                };
                var styleFunction = function (feature) {
                    console.log(feature.getGeometry().getType());
                    return styles[feature.getGeometry().getType()];
                };
                var vectorLayer = new ol.layer.Vector({
                    style: styleFunction
                });
                map.addLayer(vectorLayer);

                function createJsonObj(result) {                    
                    var geojsonObject = '{'
                            + '"type": "FeatureCollection",'
                            + '"crs": {'
                                + '"type": "name",'
                                + '"properties": {'
                                    + '"name": "EPSG:4326"'
                                + '}'
                            + '},'
                            + '"features": [{'
                                + '"type": "Feature",'
                                + '"geometry": ' + result
                            + '}]'
                        + '}';
                    return geojsonObject;
                }
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					vectorLayer.setSource(vectorSource);
                   
                }
                function highLightObj(result) {                   
                    var strObjJson = createJsonObj(result);
                    var objJson = JSON.parse(strObjJson);
                    highLightGeoJsonObj(objJson);
                }
                
                function displayObjInfo(result, coordinate) {
                    $("#info").html(result);
                }

                var button = document.getElementById("btnSeacher").addEventListener("click",
                        () => {
                            vectorLayer.setStyle(styleFunction);
                            if(ctiy.value.length) {
                                $.ajax({
                                    type: "POST",
                                    url: "CMR_pgsqlAPI.php",
                                    data: {
                                        functionname2: 'seacherCity',
                                        name: ctiy.value
                                    },
                                    
                                    success: function(result, status, erro) {
                                        console.log('abc');
                                        if (result == 'null')
                                            alert("không tìm thấy đối tượng");
                                        else
                                            console.log(result);
                                            highLightObj(result);
                                    },
                                    error: function(req, status, error) {
                                        alert(req + " " + status + " " + error);
                                    }
                                });
                                $.ajax({
                                    type: "POST",
                                    url: "CMR_pgsqlAPI.php",
                                    data: {
                                        functionname2: 'getInfoSearchoAjax',
                                        name: ctiy.value
                                    },
                                    success: function(result, status, erro) {
                                        displayObjInfo(result);
                                    },
                                    error: function(req, status, error) {
                                        alert(req + " " + status + " " + error);
                                    }
                                });
                            }else alert("Nhập dữ liệu tìm kiếm")

                                
                        });

                map.on('singleclick', function (evt) {
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    $.ajax({
                        type: "POST",
                        url: "CMR_pgsqlAPI.php",
                        data: {functionname: 'getGeoRailWayoAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            highLightObj(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                });
            };
        </script>
    </body>
</html>
<?php include './layout/footer.php' ?>