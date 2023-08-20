<?php
if (isset($_POST['functionname'])) {
    $paPDO = initDB();
    $functionname = $_POST['functionname'];

    $aResult = "null";
    if ($functionname == 'getBankInfor') {
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $bank_type = $_POST['bank_type'];
        $aResult = getBankInfor($paPDO, $paSRID, $paPoint, $bank_type);
    } else if ($functionname == 'getRouteFound') {
        $x1 = $_POST['x1'];
        $y1 = $_POST['y1'];
        $x2 = $_POST['x2'];
        $y2 = $_POST['y2'];
        $aResult = getRouteFound($paPDO, $x1, $y1, $x2, $y2);
    }

    echo $aResult;

    closeDB($paPDO);
}

function initDB()
{
    // Kết nối CSDL
    $paPDO = new PDO('pgsql:host=localhost;dbname=major_assignment;port=5432', 'postgres', 'galaga286');
    return $paPDO;
}

function query($paPDO, $paSQLStr)
{
    try {
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
    } catch (PDOException $e) {
        echo "Thất bại, Lỗi: " . $e->getMessage();
        return null;
    }
}

function closeDB($paPDO)
{
    // Ngắt kết nối
    $paPDO = null;
}

function getBankInfor($paPDO, $paSRID, $paPoint, $bank_type)
{
    $paPoint = str_replace(',', ' ', $paPoint);
    if ($bank_type == "others")
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo, osm_id as id, brand, name, addr_stree as street, ST_Distance('SRID=" . $paSRID . ";" . $paPoint . "'::geometry, geom) as distance FROM \"hanoi_bank_point\" 
        WHERE brand IS NULL ORDER BY distance LIMIT 1";
    else
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo, osm_id as id, brand, name, addr_stree as street, ST_Distance('SRID=" . $paSRID . ";" . $paPoint . "'::geometry, geom) as distance FROM \"hanoi_bank_point\" WHERE brand='" . $bank_type . "' ORDER BY distance LIMIT 1";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        return json_encode($result);
    } else
        return "null";
}

function getRouteFound($paPDO, $x1, $y1, $x2, $y2)
{
    $mySQLStr = "SELECT ST_AsGeoJson(route.geom) as geo, cost FROM (
        SELECT geom, cost FROM pgr_fromAtoB('hanoi_route', " . $x1 . ", " . $y1 . ", " . $x2 . ", " . $y2 . "
        ) ORDER BY seq) AS route";
    $result = query($paPDO, $mySQLStr);

    if ($result != null) {
        return json_encode($result);
    } else
        return "null";
}
