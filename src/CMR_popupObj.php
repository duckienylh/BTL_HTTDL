<?php include './layout/header.php' ?>

<style>
.map {
    border: 1px solid #000;
    height: 98vh;
    width: 80vw;
    float: left;
}

.ol-popup {
    position: absolute;
    background-color: white;
    -webkit-filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
    filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #cccccc;
    bottom: 12px;
    left: -50px;
    min-width: 180px;
}

.ol-popup:after,
.ol-popup:before {
    top: 100%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
}

.ol-popup:after {
    border-top-color: white;
    border-width: 10px;
    left: 48px;
    margin-left: -10px;
}

.ol-popup:before {
    border-top-color: #cccccc;
    border-width: 11px;
    left: 48px;
    margin-left: -11px;
}

.ol-popup-closer {
    text-decoration: none;
    position: absolute;
    top: 2px;
    right: 8px;
}
</style>

<body onload="initialize_map();" class="">
    <table>
        <tr>
            <td>
                <div id="map" class="map"></div>
                <!-- <div id="map" style="width: 80vw; height: 98vh;"></div> -->
            </td>
            <td>
                <div id="info"></div>
                <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer">X</a>
                    <div id="popup-content"></div>
                </div>
            </td>
        </tr>
    </table>
    <?php include 'CMR_pgsqlAPI.php' ?>
    <script>
    /**
     * Elements that make up the popup.
     */
    var container = document.getElementById('popup');
    var closer = document.getElementById('popup-closer');
    /**
     * Create an overlay to anchor the popup to the map.
     */
    var overlay = new ol.Overlay( /** @type {olx.OverlayOptions} */ ({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    }));
    /**
     * Add a click handler to hide the popup.
     * @return {boolean} Don't follow the href.
     */
    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
        return false;
    };
    var ctiy = document.getElementById("ctiy");
    var format = 'image/png';
    var map;
    var vectorLayer;
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
                    'VERSION': '1.1.0',
                    STYLES: '',
                    LAYERS: 'cang',
                }
            })
        });

        var view = new ol.View({
            center: ol.proj.fromLonLat([mapLng, mapLat]),
            zoom: mapDefaultZoom
        });

        var map = new ol.Map({
            target: "map",
            layers: [layerBG, layerCMR_adm1],
            overlays: [overlay],
            view: view
        });

        var image = new ol.style.Circle({
            radius: 5,
            fill: null,
            stroke: new ol.style.Stroke({
                color: "yellow",
                width: 5
            }),
        });
        var styles = {
            'Point': new ol.style.Style({
                image: image,
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
        var styleFunction = function(feature) {
            return styles[feature.getGeometry().getType()];
        };
        vectorLayer = new ol.layer.Vector({
            style: styleFunction
        });
        map.addLayer(vectorLayer);

        function createJsonObj(result) {
            var geojsonObject = '{' +
                '"type": "FeatureCollection",' +
                '"crs": {' +
                '"type": "name",' +
                '"properties": {' +
                '"name": "EPSG:4326"' +
                '}' +
                '},' +
                '"features": [{' +
                '"type": "Feature",' +
                '"geometry": ' + result +
                '}]' +
                '}';
            return geojsonObject;
        }

        function drawGeoJsonObj(paObjJson) {
            var vectorSource = new ol.source.Vector({
                features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                    dataProjection: 'EPSG:4326',
                    featureProjection: 'EPSG:3857'
                })
            });
            vectorLayer = new ol.layer.Vector({
                source: vectorSource
            });
            map.addLayer(vectorLayer);
        }

        function displayObjInfo(result, coordinate) {
            $("#popup-content").html(result);
            overlay.setPosition(coordinate);
            displayObjInfo1('');
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

        function displayObjInfo1(result, coordinate) {
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
                                functionname2: 'getGeoSearchCity',
                                name: ctiy.value
                            },
                            
                            success: function(result, status, erro) {
                                if (result == 'null')
                                    alert("không tìm thấy đối tượng");
                                else
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
                                displayObjInfo1(result);
                            },
                            error: function(req, status, error) {
                                alert(req + " " + status + " " + error);
                            }
                        });
                    }else alert("Nhập dữ liệu tìm kiếm")

                        
                });

        map.on('singleclick', (evt) => {
            var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
            var lon = lonlat[0];
            var lat = lonlat[1];
            var myPoint = 'POINT(' + lon + ' ' + lat + ')';
            $.ajax({
                type: "POST",
                url: "CMR_pgsqlAPI.php",
                data: {
                    functionname: 'getPopupCMRToAjax',
                    paPoint: myPoint
                },
                success: function(result, status, erro) {
                    displayObjInfo(result, evt.coordinate);
                },
                error: function(req, status, error) {
                    alert(req + " " + status + " " + error);
                }
            });
            $.ajax({
                type: "POST",
                url: "CMR_pgsqlAPI.php",
                data: {
                    functionname: 'getGeoPointCMRToAjax',
                    paPoint: myPoint
                },
                success: function(result, status, erro) {
                    highLightObj(result);
                },
                error: function(req, status, error) {
                    alert(req + " " + status + " " + error);
                }
            });
        });
    };
    </script>
</body>

<?php include './layout/footer.php' ?>