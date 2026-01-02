<?php //route: ctr/ctrql

//Add codes here...

use Classes\Collection;
use Classes\DB;
use Classes\File;
use Classes\Request;
use Classes\Response;
use Classes\Validator;

$action = Request::ql("action");
$param = Request::ql("param") ?? [];
$table = Request::ql("table");
$encodeImages = Request::ql("encodeImages");
$extra = Request::ql("extra");
$accept = Request::ql("accept") ?? "*";
$update = Request::ql("update");
$query =  Request::ql("query");
$validation = Request::ql("validation");
$validationType = Request::ql("validationType");

if ($validation) {
    if (! $validationType) {
        Response::code(badrequest_code)->message("ctrql: validationType is required.!")->send(badrequest_code);
    }
    if (! is_array($validation)) {
        Response::code(badrequest_code)->message("ctrql: validation should be an Object/array")->send(badrequest_code);
    }
    $dim = array_is_list($validation);
    if ($dim) {
        foreach ($dim as $d => $v) {
            $exp = explode("||", $d);
            $name = $exp[0];
            $label = $exp[1];
            $pst = Validator::check($name, $label, $v);
        }
    } else {
        foreach ($dim as $d) {
            $exp = explode("||", $d);
            $name = $exp[0];
            $label = $exp[1];
            $pst = Validator::check($name, $label, "required");
        }
    }

    if (Validator::failed()) {
        $errors = Validator::errors();
        if ($errors) {
            if ($validationType == "default") {
                foreach ($errors as $k => $v) {
                    Response::code(badrequest_code)->message($v)->var(["field" => $k])->send();
                }
            }
            if ($validationType == "detailed") {
                Response::code(badrequest_code)->message("Validation failed.!")->errors($errors)->send();
            }
        }
    }
}

if (! $action) {
    Response::code(badrequest_code)->message("ctrql: action field is required.!")->send(badrequest_code);
}

if (! $table) {
    Response::code(badrequest_code)->message("ctrql: table field is required")->send(badrequest_code);
}

if ($action == "create" || $action == "insert") {
    $id = DB::insert($table, $param);
    Response::code(success_code)->message("OK")->var(["_id" => $id])->data($param)->send();
} else if ($action == "read" || $action == "select" || $action == "find" || $action == "get" || $action == "findOne") {
    $result = [];
    if ($action == "findOne") {
        $result = DB::findOne($table, $param, $extra);
    } else {
        $result = DB::find($table, $param, $extra);
    }
    if ($encodeImages) {
        $result = File::encode_blob($result, $encodeImages);
    }
    if ($accept == "*") {
        Response::code(success_code)->message("OK")->var(["empty" => $result ? true : false])->data($result)->send();
    }
    $result = Collection::data($result)->get($accept)->exec();
    Response::code(success_code)->message("OK")->data($result)->var(["empty" => $result ? true : false])->send();
} else if ($action == "delete") {
    if (! $param) {
        Response::code(badrequest_code)->message("ctrql: param/where field is required.!")->send(badrequest_code);
    }
    $result = DB::delete($table, $param);
    Response::code(success_code)->message("OK")->var(["rows" => $result ?? 0])->send();
} else if ($action == "update") {
    if (! $update) {
        Response::code(badrequest_code)->message("ctrql: update field is required.!")->send(badrequest_code);
    }
    if (! $param) {
        Response::code(badrequest_code)->message("ctrql: param/where field is required.!")->send(badrequest_code);
    }

    $result = DB::update($table, $update, $param);
    Response::code(success_code)->message("OK")->var(["rows" => $result ?? 0])->send();
} else if ($action == "query") {
    $result = DB::query($query, $param);
    if ($encodeImages) {
        $result = File::encode_blob($result, $encodeImages);
    }
    if ($accept == "*") {
        Response::code(success_code)->message("OK")->var(["empty" => $result ? true : false])->data($result)->send();
    }
    $result = Collection::data($result)->get($accept)->exec();
    Response::code(success_code)->message("OK")->data($result)->var(["empty" => $result ? true : false])->send();
} else {
    Response::code(badrequest_code)->message("Unknown action '$action'.!")->send(badrequest_code);
}
