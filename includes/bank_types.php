<?php
// PDO Options
$options = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];
$host = 'localhost';
$db = 'major_assignment';
$user = 'postgres';
$password = 'galaga286';
$post = '5432';

$dsn = "pgsql:host=$host; port=$post; dbname=$db";
try {
    // Create pdo connection
    $myPdo = new PDO($dsn, $user, $password);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
// Query
$result = $myPdo->query("SELECT DISTINCT CASE WHEN brand IS NULL THEN 'Các ngân hàng chưa rõ nhãn' ELSE brand END FROM hanoi_bank_point");

// Loop query
$resFin =  '<option value="">Chọn nhãn ngân hàng</option>';
foreach ($result as $key => $row) {
    $name = $row['brand'];
    $value = $row['brand'] == 'Các ngân hàng chưa rõ nhãn' ? "others" : $row['brand'];

    $resFin = $resFin . '<option value = "' . $value  . '">' . $name . '</option>';
}
$resFin = $resFin;
echo $resFin;