<?php

// Url parameter fields
$id = null;
$quote = null;
$author = null;
$author_id = null;
$category_id = null;
$category = null;

// HTTP header stuff.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");


// Class definition for database operations.
class Database {
    private $host = "localhost";
    private $port = '5432';
    private $db_name = "TestDB";
    private $username = "postgres";
    private $password = "pass";
    private $conn = null;

    public function connect() {
        $this->conn = null;
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";


        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;

    }

}




echo "Hello World.";

//Obtain connection to database
$db = new Database();
$con = $db->connect();
//echo $con->getAttribute(PDO::ATTR_CONNECTION_STATUS);
//echo $con->getAttribute(PDO::ATTR_SERVER_INFO);


//$query = 'SELECT * FROM authors;';
//echo $query;

echo "<br/>";


//$stm = $con->query($query);

//var_dump($stm->fetchall(PDO::FETCH_ASSOC));

echo "<br/>";



// Main server request loop
switch($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        echo "GET Request <br/>";
        Get();
        break;

    case 'POST':
        echo "POST Request <br/>";
        Post();
        break;
    
    case 'PUT':
        echo "PUT Request <br/>";
        Put();
        break;

    case 'DELETE':
        echo "DELETE Request <br/>";
        Delete();
        break;

    default:
        echo "Bad Request <br/>";
        break;
}


// This is just to create some whitespace. delete at some point.
echo "<br/><br/>";
echo "<br/><br/>";


// Debug stuff (delete at some point)

/*

$keys = array_keys($_REQUEST);

foreach($keys as $s) {
    echo $s . "<br/>";
}

echo "<br/><br/>";

*/



/*

$keys = array_keys($_SERVER);

foreach($keys as $s) {
    echo $s . "<br/>";
}



echo "<br/><br/>";

foreach($_SERVER as $s) {
    echo $s. "<br/>";
}

echo "<br/><br/>";

*/


//$data = json_decode(file_get_contents("php://input"));

/*
$data = json_decode(file_get_contents("php://input"), true);
var_dump($data);

echo $data["name"];
*/

//echo 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PATH_INFO'];


//$a = [];
//var_dump(parse_str($_SERVER['QUERY_STRING'],$a));
//var_dump($a);
//echo $_SERVER['REQUEST_URI'];


// Bind any parameters that were passed via url query string.
function BindParameters($path) {

    global $con;
    global $id;
    global $quote;
    global $author_id;
    global $author;
    global $category_id;
    global $category;



    $args = [];
    parse_str($_SERVER['QUERY_STRING'], $args);

    //Debugging stuff (delete at some point)
    //var_dump($args);
    //echo $_SERVER["PATH_INFO"] . "<br/>";


    
    if ($args) {

        //echo "We have args to bind.<br/>";

        $keys = array_keys($args);


        if ($path === "/quotes/") {

            //echo "Binding params for quotes path.<br/>";


            //Look to see if an id param was received.
            if (array_search("id", $keys) !== false) {
                $id = $args["id"];

                //echo "id param has been bound.<br/>";
            }

            //Look to see if an author_id param was received.
            if (array_search("author_id", $keys) !== false) {
                $author_id = $args["author_id"];

                //echo "author_id param has been bound.<br/>";
            }

            //Look to see if a category_id param was received.
            if (array_search("category_id", $keys) !== false) {
                $category_id = $args["category_id"];

                //echo "category_id param has been bound.<br/>";
            }



        }
        elseif ($path === "/authors/") {

            //echo "Binding params for authors path.<br/>";

            //Look to see if an id param was received.
            if (array_search("id", $keys) !== false) {
                $id = $args["id"];

                //echo "author id is: $id<br/>";
                //echo "id param has been bound.<br/>";
            }


        }
        elseif ($path === "/categories/") {


            //Look to see if a category_id param was received.
            if (array_search("id", $keys) !== false) {
                $id = $args["id"];
                //echo "id param has been bound.<br/>";
            }


        }
        else {
            echo "Invalid path";
        }
    }
    else {
        //echo "No params to bind.<br/>";
    }
    


}


function Get() {

    global $con;
    global $id;
    global $quote;
    global $author_id;
    global $author;
    global $category_id;
    global $category;




    if($_SERVER["PATH_INFO"] === NULL) {
        echo "No Path <br/>";
        return;
    }


    BindParameters($_SERVER["PATH_INFO"]);

    //echo "<br/> " . $id . "<br/>";



    if($_SERVER["PATH_INFO"] === "/quotes/") {

        if ($id === NULL && $author_id === NULL && $category_id === NULL) {

            //echo "Getting all of the quotes. <br/>";

            $query = 'SELECT * FROM quotes;';
            $stm = $con->query($query, PDO::FETCH_ASSOC);
            //var_dump($stm->fetchall());


            $data = $stm->fetchall();

            //echo "<br/><br/>";
            //var_dump($data[0]);
            //echo "<br/><br/>";

            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }

        }
        elseif ($id !== NULL && $author_id === NULL && $category_id === NULL) {

            $query = "SELECT * FROM quotes WHERE id=$id;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();

            //echo "<br/><br/>";
            //var_dump($data[0]);
            //echo "<br/><br/>";

            if($data) {
                echo json_encode($data[0]);
            }
            else {
                echo '{"message":"no data"}';
            }

        }

        elseif($id === NULL && $author_id !== NULL && $category_id === NULL) {

            $query = "SELECT * FROM quotes WHERE author_id=$author_id;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();

            //echo "<br/><br/>";
            //var_dump($data);
            //echo "<br/><br/>";

            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }
        }
        elseif ($id === NULL && $author_id === NULL && $category_id !== NULL) {

            $query = "SELECT * FROM quotes WHERE category_id=$category_id;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();

            //echo "<br/><br/>";
            //var_dump($data);
            //echo "<br/><br/>";

            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }

        }
        elseif($id === NULL && $author_id !== NULL && $category_id !== NULL) {

            $query = "SELECT * FROM quotes WHERE (author_id=$author_id) AND (category_id=$category_id);";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();

            //echo "<br/><br/>";
            //var_dump($data);
            //echo "<br/><br/>";

            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }


            echo "Getting all of the quotes from the author for the given category.";
        }
        else {
            echo "Bad parameter combination.";
        }


    }
    elseif ($_SERVER["PATH_INFO"] === "/authors/") {


        if ($id === NULL) {

            //echo (int)$id;

            //echo "Looking for authors. <br/>";

            $query = 'SELECT * FROM authors;';
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();


            //var_dump($stm->fetchall());



            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }

        }
        else {

            //echo "Looking for author. <br/>";


            $query = "SELECT * FROM authors WHERE id=$id;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();


            //var_dump($stm->fetchall());



            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }



        }

    }
    elseif ($_SERVER["PATH_INFO"] === "/categories/") {

        if ($id === NULL) {

            $query = "SELECT * FROM categories;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();


            //var_dump($stm->fetchall());



            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }

        }
        else {
            $query = "SELECT * FROM categories WHERE id=$id;";
            $stm = $con->query($query, PDO::FETCH_ASSOC);

            $data = $stm->fetchall();


            //var_dump($stm->fetchall());



            if($data) {
                echo json_encode($data);
            }
            else {
                echo '{"message":"no data"}';
            }

        }


    }
    else {
        echo "Looking for data.";
    }


}


function Post() {

    global $con;
    global $id;
    global $quote;
    global $author_id;
    global $author;
    global $category_id;
    global $category;

    BindParameters($_SERVER["PATH_INFO"]);



    if($_SERVER["PATH_INFO"] === "/quotes/") {

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->quote) && isset($data->author_id) && isset($data->category_id)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "INSERT INTO quotes(id, quote, author_id, category_id) VALUES($data->id,  '$data->quote', $data->author_id, $data->category_id);";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }


    }
    elseif ($_SERVER["PATH_INFO"] === "/authors/") {

        //echo "Creating authors. <br/>";

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->author)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "INSERT INTO authors(id, author) VALUES($data->id, '$data->author');";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }

        //echo "The author to create:";
        //var_dump($data);

    }
    elseif ($_SERVER["PATH_INFO"] === "/categories/") {



        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->category)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "INSERT INTO categories(id, category) VALUES($data->id, '$data->category');";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }


    }
    else {
        echo "Bad path and http code combination.";
    }


}

function Put() {

    global $con;
    global $id;
    global $quote;
    global $author_id;
    global $author;
    global $category_id;
    global $category;

    BindParameters($_SERVER["PATH_INFO"]);



    if($_SERVER["PATH_INFO"] === "/quotes/") {

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->quote) && isset($data->author_id) && isset($data->category_id)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "UPDATE quotes SET quote='$data->quote', author_id=$data->author_id, category_id=$data->category_id WHERE id=$data->id;";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }


    }
    elseif ($_SERVER["PATH_INFO"] === "/authors/") {



        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->author)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "UPDATE authors SET author='$data->author' WHERE id=$data->id;";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }


    }
    elseif ($_SERVER["PATH_INFO"] === "/categories/") {


        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id) && isset($data->category)) {

            //echo "Creating author<br/>";
            //echo $data->id . "<br/>";
            //echo $data->author . "<br/>";

            $query = "UPDATE categories SET category='$data->category' WHERE id=$data->id;";

            echo "<br/>$query<br/>";

            $con->exec($query);




        }
        else {
            echo '{"message":"missing parameters"}';
        }



    }
    else {
        echo "Bad path and http code combinaton.";
    }


}

function Delete() {

    global $con;
    global $id;
    global $quote;
    global $author_id;
    global $author;
    global $category_id;
    global $category;

    BindParameters($_SERVER["PATH_INFO"]);



    if($_SERVER["PATH_INFO"] === "/quotes/") {

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id)) {


            $query = "DELETE FROM quotes WHERE id=$data->id;";

            $con->exec($query);


            echo $data->id;

        }
        else {
            echo '{"message":"missing parameters"}';
        }


    }
    elseif ($_SERVER["PATH_INFO"] === "/authors/") {

        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id)) {


            $query = "DELETE FROM authors WHERE id=$data->id;";

            $con->exec($query);


            echo $data->id;

        }
        else {
            echo '{"message":"missing parameters"}';
        }

    }
    elseif ($_SERVER["PATH_INFO"] === "/categories/") {


        $data = json_decode(file_get_contents("php://input"));

        if(isset($data->id)) {


            $query = "DELETE FROM categories WHERE id=$data->id;";

            $con->exec($query);


            echo $data->id;

        }
        else {
            echo '{"message":"missing parameters"}';
        }



    }
    else {
        echo "Bad path and http code combination";
    }


}

?> 
