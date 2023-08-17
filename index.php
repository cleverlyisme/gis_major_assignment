<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bài tập lớn nhóm 6</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
    <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
    <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">
</head>

<body onload="initialize_map();">
    <div id="main">
        <header id="header" class="header bg-primary p-3">
            <div class="container">
                <h2 class="text-center text-light text-uppercase">Bài tập lớn môn Hệ Thống Thông Tin Địa Lý</h2>
                <h2 class="text-center text-light text-uppercase">Đề tài: Web tìm ngân hàng gần nhất trong thành phố Hà Nội</h2>
            </div>
        </header>
        <div>
            <div>
                <div>
                    <div>
                        <div class=" container-fluid">
                            <div class="row g-5 mt-1">
                                <div class=" col-lg-9">
                                    <div class="border border-1 rounded p-2">
                                        <h2 class="text-center">Bản đồ thành phố Hà Nội</h2>
                                        <div id="map" style="height: 1000px;" class="map mt-3"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 border border-1 rounded ">
                                    <div style="margin-top:150px;">
                                        <div class="mb-3">
                                            <p class="d-inline">Vị trí đang ở: </p>
                                            <img src="./assets/images/start.png" alt="Start">
                                        </div>
                                        <div class="mb-3">
                                            <p class="d-inline">Ngân hàng gần nhất: </p>
                                            <img src="./assets/images/destination.png" alt="Destination">
                                        </div>
                                        <div class="mb-3">
                                            <label for="startPosition" class="form-label">Click chuột lên bản đồ để chọn vị trí bạn đang ở: </label>
                                            <input type="text" id="startPosition" class="form-control" readonly>
                                        </div>
                                        <div class="mb-3 mt-4">
                                            <label for="bank">Chọn nhãn ngân hàng bạn muốn tìm:</label>
                                            <select class="form-select mt-2" name="bank_types" id="bank_types">
                                                <?php
                                                include('./includes/bank_types.php');
                                                ?>
                                            </select>
                                        </div>
                                        <div class="search-btn d-flex justify-content-around mb-4">
                                            <button class="btn btn-primary" id="btnFind">Tìm kiếm</button>
                                            <button class="btn btn-danger" id="btnReset">Đặt lại</button>
                                        </div>
                                        <div>
                                            <div>
                                                <div>
                                                    <h2 class="text-center mb-4">Thông tin của ngân hàng</h2>
                                                    <div id="bank_infor">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/index.js"></script>
</body>

</html>