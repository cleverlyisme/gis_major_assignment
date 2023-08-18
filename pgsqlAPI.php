<?php
if (isset($_POST['functionname'])) {
    $paPDO = initDB();
    $paSRID = '4326';
    $paPoint = $_POST['paPoint'];
    $bank_type = $_POST['bank_type'];
    $functionname = $_POST['functionname'];

    $aResult = "null";
    if ($functionname == 'getBankInfor')
        $aResult = getBankInfor($paPDO, $paSRID, $paPoint, $bank_type);

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
