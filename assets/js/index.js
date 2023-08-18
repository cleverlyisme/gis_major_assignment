var format = "image/png";
var map, vectorSource;
var minX = 105.61895568847656;
var minY = 20.68451416015625;
var maxX = 106.02007293701172;
var maxY = 21.385278701782227;
var cenX = (minX + maxX) / 2;
var cenY = (minY + maxY) / 2;
var mapLat = cenY;
var mapLng = cenX;
var mapDefaultZoom = 12;
var startPoint = null;
var startCoords,
  bankCoords = null;
var startPointFeature = null;
var apiKey = "5b3ce3597851110001cf6248c473d3c0bee443b98e12e3031e01d524";

function initialize_map() {
  layerBG = new ol.layer.Tile({
    source: new ol.source.OSM({}),
  });

  //Các ngân hàng thành phố Hà Nội
  var hanoi_bank = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      url: "http://localhost:8000/geoserver/major_assignment/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "hanoi_bank_style",
        LAYERS: "hanoi_bank_point",
      },
    }),
  });

  //Vùng bao thành phố Hà Nội
  var hanoi_boundary = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      url: "http://localhost:8000/geoserver/major_assignment/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "hanoi_boundary_style",
        LAYERS: "hanoi_boundary",
      },
    }),
  });

  var viewMap = new ol.View({
    center: ol.proj.fromLonLat([mapLng, mapLat]),
    zoom: mapDefaultZoom,
    minZoom: mapDefaultZoom,
  });

  map = new ol.Map({
    target: "map",
    layers: [layerBG, hanoi_boundary, hanoi_bank],
    view: viewMap,
  });

  var startStyle = new ol.style.Style({
    image: new ol.style.Circle({
      radius: 6,
      fill: new ol.style.Fill({
        color: "transparent",
      }),
      stroke: new ol.style.Stroke({
        color: "blue",
        width: 2,
      }),
    }),
  });

  var desStyle = {
    Point: new ol.style.Style({
      image: new ol.style.Circle({
        radius: 6,
        fill: new ol.style.Fill({
          color: "transparent",
        }),
        stroke: new ol.style.Stroke({
          color: "red",
          width: 2,
        }),
      }),
    }),
  };

  var styleFunction = function (feature) {
    return desStyle[feature.getGeometry().getType()];
  };

  vectorSource = new ol.source.Vector();
  var vectorLayer = new ol.layer.Vector({
    source: vectorSource,
    style: styleFunction,
  });

  map.addLayer(vectorLayer);

  function createJsonObj(result) {
    var geojsonObject =
      "{" +
      '"type": "FeatureCollection",' +
      '"crs": {' +
      '"type": "name",' +
      '"properties": {' +
      '"name": "EPSG:4326"' +
      "}" +
      "}," +
      '"features": [{' +
      '"type": "Feature",' +
      '"geometry": ' +
      result +
      "}]" +
      "}";
    return geojsonObject;
  }

  function highLightGeoJsonObj(paObjJson) {
    vectorSource.forEachFeature(function (feature) {
      if (feature !== startPointFeature) {
        vectorSource.removeFeature(feature);
      }
    });

    vectorSource.addFeatures(
      new ol.format.GeoJSON().readFeatures(paObjJson, {
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857",
      })
    );
  }

  function highLightObj(result) {
    var strObjJson = createJsonObj(result);
    var objJson = JSON.parse(strObjJson);
    highLightGeoJsonObj(objJson);
  }

  function calculateAndHighlightRoute(startCoords, bankCoords) {
    var routingUrl = `https://api.openrouteservice.org/v2/directions/driving-car?api_key=${apiKey}&start=${startCoords.join(
      ","
    )}&end=${bankCoords.join(",")}`;

    $.get(routingUrl, function (data) {
      var routeGeometry = data.features[0].geometry.coordinates;
      highlightRoute(routeGeometry);
    });
  }

  function highlightRoute(routeCoordinates) {
    var routeGeometry = new ol.geom.LineString(
      routeCoordinates.map(function (coord) {
        return ol.proj.fromLonLat(coord);
      })
    );

    var routeFeature = new ol.Feature({
      geometry: routeGeometry,
    });

    var routeStyle = new ol.style.Style({
      stroke: new ol.style.Stroke({
        color: "green",
        width: 4,
      }),
    });

    routeFeature.setStyle(routeStyle);
    vectorSource.addFeature(routeFeature);
  }

  map.on("click", function (evt) {
    if (!startPointFeature) {
      var point = ol.proj.transform(evt.coordinate, "EPSG:3857", "EPSG:4326");
      var x = point[0];
      var y = point[1];
      startPoint = "POINT(" + x + " " + y + ")";
      startCoords = [x, y];

      $("#startPosition").val(x + "; " + y);

      vectorSource.clear();

      startPointFeature = new ol.Feature({
        geometry: new ol.geom.Point(evt.coordinate),
      });

      startPointFeature.setStyle(startStyle);
      vectorSource.addFeature(startPointFeature);
    }
  });

  $("#btnFind").on("click", function () {
    if (!startPoint) alert("Hãy chọn vị trí bạn đang ở!");
    else if (!$("#bank_types").val())
      alert("Hãy chọn nhãn ngân hàng bạn muốn tìm");
    else {
      var bank_type = $("#bank_types").val();

      $.ajax({
        type: "POST",
        url: "pgsqlAPI.php",
        data: {
          functionname: "getBankInfor",
          paPoint: startPoint,
          bank_type: bank_type,
        },
        success: function (result, status, xhr) {
          var bank = JSON.parse(result)[0];
          bankCoords = JSON.parse(bank.geo).coordinates;
          highLightObj(bank.geo);

          var id =
            "<dt class='col-sm-5'>ID ngân hàng: </dt><dd class='col-sm-7'>" +
            bank.id +
            "</dd>";
          var type = bank.brand
            ? "<dt class='col-sm-5'>Nhãn ngân hàng: </dt><dd class='col-sm-7'>" +
              bank.brand +
              "</dd>"
            : "";
          var name = bank.name
            ? "<dt class='col-sm-5'>Tên ngân hàng: </dt><dd class='col-sm-7'>" +
              bank.name +
              "</dd>"
            : "";
          var street = bank.street
            ? "<dt class='col-sm-5'>Địa chỉ: </dt><dd class='col-sm-7'>" +
              bank.street +
              "</dd>"
            : "";
          var distance =
            "<dt class='col-sm-5'>Khoảng cách: </dt><dd class='col-sm-7'>" +
            (Number(bank.distance) * 150).toFixed(3) +
            "km</dd>";

          var html =
            "<dl class='row'>" + id + type + name + street + distance + "</dl>";

          $("#bank_infor").html(html);

          calculateAndHighlightRoute(startCoords, bankCoords);
        },
        error: function (xhr, status, error) {
          alert(xhr.responseText + " " + status + " " + error);
        },
      });
    }
  });

  $("#btnReset").on("click", function () {
    startPoint = null;
    startPointFeature = null;
    $("#startPosition").val(null);
    $("#bank_types").val(null);

    vectorSource.clear();
    $("#bank_infor").html("");
  });
}
