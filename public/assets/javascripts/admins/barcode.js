$(function () {
  function barcode (value, btype, renderer, target) {
    var quietZone = false;
    var settings = {
  output:renderer,
  bgColor: "#FFFFFF", // 배경색
  color: "#000000", // 바 색상
  barWidth: 3, // 바 기본 폭
  barHeight: 50, // 바 높이
  fontSize: "1em", // 폰트크기
  marginHRI: "0.25em",

  // For MATRIX
  moduleSize: 5, // moduleSize
  addQuietZone: 1, // quietZoneSize
    };

    // For For MATRIX
    if (btype == "datamatrix") {
  value = {code:value, rect: true};
  quietZone = true;
    }

    target.html("").show().barcode(value, btype, settings);
  };

  barcode(card, btype, renderer, $("#barcodeTarget"));  
});
