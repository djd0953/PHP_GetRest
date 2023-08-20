<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

    header('Content-Type: application/json; charset=UTF-8');
    $data = file_get_contents('php://input');
    $data = preg_replace('/[\x00-\x1F\x7F]/u', '', $data);

    $dao = new LPR_DAO;
    $lpr = new LPR_VO;
    $img = new LPRIMG_VO;
    $res = array();

    try
    {
        if (strlen($data) < 1) throw new Exception("Body내용이 없습니다", 400);

        $json = array();
        $json = json_decode($data, true);
        
        $errString = array("ID", "TYPE", "POS", "CAR-NUM", "CAR-BIN", "TIME-STAMP");
        foreach ($errString as $s)
        {
            if (!isset($json[$s])) throw new Exception("{$s} 값이 없습니다.", 400);
        }

        $lpr->id = $json["ID"];
        $lpr->type = $json["TYPE"];
        $lpr->pos = $json["POS"];
        $lpr->carNum = $json["CAR-NUM"];
        $lpr->timeStamp = date("Y-m-d H:i:s", strtotime($json["TIME-STAMP"]));
        $img->carNum = $json["CAR-NUM"];
        $img->carBin = $json["CAR-BIN"];

        $dao->INSERT_LPR($lpr, $img);

        $res["code"] = "200";
        $res["msg"] = "OK";
    }
    catch (Exception $ex)
    {
        $lpr->timeStamp = date("Y-m-d H:i:s");
        $lpr->retData = $data;
        if ($data) $dao->INSERT($lpr);

        $res["code"] = $ex->getCode();
        $res["msg"] = $ex->getMessage();
    }

    echo json_encode($res);
?>