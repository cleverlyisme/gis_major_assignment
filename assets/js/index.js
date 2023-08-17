var format = "image/png";
var map;
var minX = 105.41895568847656;
var minY = 20.68451416015625;
var maxX = 106.02007293701172;
var maxY = 21.385278701782227;
var cenX = (minX + maxX) / 2;
var cenY = (minY + maxY) / 2;
var mapLat = cenY;
var mapLng = cenX;
var mapDefaultZoom = 12;

function initialize_map() {
  layerBG = new ol.layer.Tile({
    source: new ol.source.OSM({}),
  });

  //Các đường đi của thành phố Hà Nội
  hanoi_route = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      url: "http://localhost:8000/geoserver/major_assignment/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        STYLES: "hanoi_route_style",
        LAYERS: "hanoi_route",
      },
    }),
  });

  //Các ngân hàng thành phố Hà Nội
  var hanoi_bank = new ol.layer.Image({
    source: new ol.source.ImageWMS({
      url: "http://localhost:8000/geoserver/major_assignment/wms?",
      params: {
        FORMAT: format,
        VERSION: "1.1.1",
        // STYLES: "hanoi_bank_style",
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
    layers: [layerBG, hanoi_boundary, hanoi_route, hanoi_bank],
    view: viewMap,
  });
}
