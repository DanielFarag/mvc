<?php
function dd($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
define("APP_PATH",dirname(dirname(__FILE__)));

require APP_PATH . '/vendor/autoload.php';
$dbFactory = new Framework\Database([
										"type" =>"mysql",
										"options" =>[
											"host" =>"localhost",
											"database" =>"test",
											"username" =>"root",
											"password" =>"",
										]
									]);

$database = $dbFactory->initialize();
$database->connect();
echo $database->query()->from("books")->where('publisher=?',2)->delete();

/*$all = $database- >query()
- >from("users", array(
"first_name",
"last_name" = >"surname"
))
- >join("points", "points.id = users.id", array(
"points" = >"rewards"
))
- >where("first_name = ?", "chris")
- >order("last_name", "desc")
- >limit(100)
- >all();*/