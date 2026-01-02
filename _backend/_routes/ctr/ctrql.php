<?php //route: ctr/ctrql

//Add codes here...

use Classes\Collection;
use Classes\DB;
use Classes\File;
use Classes\Request;
use Classes\Response;
use Classes\Validator;

/**
 * This is CodeTazeR CTRQL - for direct transaction
 * easy development as frontend can stand alone without any hassle.
 * created: January 2 2026
 * - CodeYro : Tyrone Limen Malocon.
 */

$action = Request::ql("action");
$param = Request::ql("param") ?? [];
$table = Request::ql("table");
$encodeImages = Request::ql("encodeImages");
$extra = Request::ql("extra");
$accept = Request::ql("columns") ?? "*";
$update = Request::ql("update");
$query =  Request::ql("query");
$validation = Request::ql("validation");
$validationType = Request::ql("validationType") ?? "default";
$unique = Request::ql("unique");

if ($validation) {
    if (! $validationType) {
        Response::code(badrequest_code)->message("ctrql: validationType is required.!")->send(badrequest_code);
    }
    if (! is_array($validation)) {
        Response::code(badrequest_code)->message("ctrql: validation should be an Object/array")->send(badrequest_code);
    }
    $dim = array_is_list($validation);
    if ($dim) {
        foreach ($validation as $d => $v) {
            $exp = explode("||", $v);
            $name = $exp[0] ?? null;
            $label = $exp[1] ?? $name;
            $pst = Validator::check($v, $label, "required", $param);
        }
    } else {
        foreach ($validation as $d => $v) {
            $exp = explode("||", $d);
            $name = $exp[0] ?? null;
            $label = $exp[1] ?? $name;
            $pst = Validator::check($name, $label, $v, $param);
        }
    }

    if (Validator::failed()) {
        $errors = Validator::errors();
        if ($errors) {
            if ($validationType == "default") {
                foreach ($errors as $k => $v) {
                    Response::code(422)->message($v)->var(["field" => $k])->send();
                }
            }
            if ($validationType == "detailed") {
                Response::code(422)->message("Validation failed.!")->errors($errors)->send();
            }
        }
    }
}

if (! $action) {
    Response::code(badrequest_code)->message("ctrql: action field is required.!")->send(badrequest_code);
}

if ($action == "query") {
    $result = DB::query($query, $param);
    if ($encodeImages) {
        $result = File::encode_blob($result, $encodeImages);
    }
    if ($accept == "*") {
        Response::code(success_code)->message("OK")->var(["empty" => $result ? false : true])->data($result)->send();
    }
    $result = Collection::data($result)->get($accept)->exec();
    Response::code(success_code)->message("OK")->data($result)->var(["empty" => $result ? false : true])->send();
}

if (! $table) {
    Response::code(badrequest_code)->message("ctrql: table field is required")->send(badrequest_code);
}

if ($unique) {
    if (is_string($unique)) {
        $exp = explode("||", $unique);
        $unique = $exp[0] ?? null;
        $label = $exp[1] ?? $unique;
        if (! isset($param[$unique])) {
            Response::code(badrequest_code)->message("ctrql: $unique field not found @ request body.!")->send(badrequest_code);
        }
        $value = $param[$unique] ?? null;
        $msg = $exp[2] ?? "$label '$value' is already exist.!";
        $find = DB::findOne($table, [$unique => $value]);
        if ($find) {
            Response::code(failed_code)->message($msg)->send();
        }
    } else if (is_array($unique)) {
        foreach ($unique as $u => $v) {
            $unique = $v;
            $exp = explode("||", $unique);
            $unique = $exp[0] ?? null;
            $label = $exp[1] ?? $unique;
            if (! isset($param[$unique])) {
                Response::code(badrequest_code)->message("ctrql: $unique field not found @ request body.!")->send(badrequest_code);
            }
            $value = $param[$unique] ?? null;
            $msg = $exp[2] ?? "is already exist.!";
            $find = DB::findOne($table, [$unique => $value]);
            if ($find) {
                Response::code(failed_code)->message("$label '$value' $msg")->send();
            }
        }
    }
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
        Response::code(success_code)->message("OK")->var(["empty" => $result ? false : true])->data($result)->send();
    }
    $result = Collection::data($result)->get($accept)->exec();
    Response::code(success_code)->message("OK")->data($result)->var(["empty" => $result ? false : true])->send();
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
} else if ($action == "count") {
    $result = DB::find($table, $param, $extra);
    if ($result) {
        Response::code(success_code)->message("OK")->count(sizeof($result))->send();
    } else {
        Response::code(success_code)->message("OK")->count(0)->send();
    }
} else {
    Response::code(badrequest_code)->message("Unknown action '$action'.!")->send(badrequest_code);
}
