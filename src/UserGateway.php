<?php
/* Gateway Pattern */

namespace App;

require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use \PDO;
use \DateTimeImmutable;
use \Exception;

class UserGateway
{

    /* Variable connect with PDO */
    private PDO $conn;

    /* constructor with Var database, connection db - check with getConnaction */
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }


    /* Get All Users (GET without id) */
    public function getAll(): array
    {

        /* Select some fields from join tables User&Registry&Userspermission */
        $sql = "SELECT email,name, postcode, place, telefon, passwort, permission FROM users
        INNER JOIN registry ON users.id=registry.userID
        INNER JOIN userspermission ON users.id=userspermission.userID";

        /* use connection and query function with the build sql and send request to DB  */
        $stmt = $this->conn->query($sql);
        $data = [];

        /* return a Array of Rows */
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        /* return result to the UserController */
        return $data;
    }


    /* Create a new User (POST) */
    public function create(array $data): string
    {
        /* hash the pw */
        $password_hash = password_hash($data["passwort"], PASSWORD_DEFAULT);

        /* create mailToken */
        $mailToken = $this->createToken($data["email"], true);

        /* build query to post in UserTable */
        $sql = "INSERT INTO users (email, passwort, mailToken)
                VALUES (:email, :passwort, :mailToken)";

        /* Src -> https://stackoverflow.com/questions/4700623/pdos-query-vs-execute */
        /* prepare vs query -> prepare with parameterized data  */
        $stmt = $this->conn->prepare($sql);

        /* bind parameters to avoid the need to escape or quote the parameter */
        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $password_hash, PDO::PARAM_STR);
        $stmt->bindValue(":mailToken", $mailToken, PDO::PARAM_STR);

        /* execute -> Send Request to DB -> execute perform better when repeating a query multiple times */
        $stmt->execute();

        /* get the last insertet ID to return  */
        $userID = $this->conn->lastInsertId();

        /* userID -> setPermission */
        if ($userID) {
            /* build query to post in UsersPermission */
            $sql = "INSERT INTO `userspermission` (userID, permission)
             VALUES (:userID, :permission)";
            $stmt = $this->conn->prepare($sql);
            $permission = 1;
            $stmt->bindValue(":userID", (int)$userID, PDO::PARAM_INT);
            $stmt->bindValue(":permission", $permission, PDO::PARAM_INT);
            $stmt->execute();

            /* build query to post in Registry */
            $sql = "INSERT INTO `registry` (userID,postcode, place, name, telefon, terms, mailConfirmed)
             VALUES (:userID, :postcode, :place, :name, :telefon, :terms, :mailConfirmed)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":userID", (int)$userID, PDO::PARAM_INT);
            $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
            $stmt->bindValue(":telefon", $data["telefon"], PDO::PARAM_INT);
            $stmt->bindValue(":postcode", $data["postcode"], PDO::PARAM_INT);
            $stmt->bindValue(":place", $data["place"], PDO::PARAM_STR);
            $stmt->bindValue(":terms", (bool) ($data["terms"] ?? false), PDO::PARAM_BOOL);
            $stmt->bindValue(":mailConfirmed", (bool) ($data["mailConfirmed"] ?? false), PDO::PARAM_BOOL);
            $stmt->execute();
        } else {
            /* delete User if something went wrong */
            $this->delete($userID);
        }

        /* Call helperfunction sendEmail to send a Confirm EMail to new User */
        $this->sendEmail($data["email"], $data["name"], $mailToken);


        /* return String with last Insert */
        return $this->conn->lastInsertId();
    }

    /* get a user with its id */
    public function get(string $id): array | bool
    {

        /* build query */
        $sql = "SELECT users.id,email,name, postcode, place, telefon, passwort, permission, mailConfirmed FROM users
                INNER JOIN registry ON users.id=registry.userID
                INNER JOIN userspermission ON users.id=userspermission.userID
                WHERE users.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        /* return User or false if no User founded */
        return $data;
    }


    /* SEARCHUSER HELPER FUNCTION -> GET ARRAY WITH INPUT DATAS AND SEARCH IN DB */
    public function searchUser(array $data): array | false
    {

        /* Offset, orderBy and sc(ASC/DESC) doenst need a LIKE */
        $offset = $data["offset"];
        $orderBy = $data["orderBy"];
        $sc = $data["sc"];

        /* build query -> Join Tables and search params Like and % behind the param
        example -> Ti as name param will find Ti% -> Timothy etc
        -> Limit of 10 Users */
        $sql =
            "SELECT users.id,email,name, postcode, place, telefon, passwort, permission, mailConfirmed FROM users
        INNER JOIN registry ON users.id=registry.userID
        INNER JOIN userspermission ON users.id=userspermission.userID
        WHERE email LIKE CONCAT(:email, '%') AND name LIKE CONCAT(:name, '%') AND postcode LIKE CONCAT(:postcode, '%') AND place LIKE CONCAT(:place, '%') AND telefon LIKE CONCAT(:telefon, '%')
        ORDER BY $orderBy $sc
        LIMIT 10 OFFSET $offset";


        /* Check if Inputparam is empty, if empty -> set Value to "%" -> find all */
        if (isset($data["name"]))
            $name = $data["name"];
        else {
            $name = "%";
        }

        if (isset($data["email"]))
            $email = $data["email"];
        else {
            $email = "%";
        }
        if (isset($data["postcode"]))
            $postcode = $data["postcode"];
        else {
            $postcode = "%";
        }
        if (isset($data["place"]))
            $place = $data["place"];
        else {
            $place = "%";
        }
        if (isset($data["telefon"]))
            $telefon = $data["telefon"];
        else {
            $telefon = "%";
        }

        /* Prepare and bind Value */
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":postcode", $postcode, PDO::PARAM_INT);
        $stmt->bindValue(":place", $place, PDO::PARAM_INT);
        $stmt->bindValue(":telefon", $telefon, PDO::PARAM_STR);

        /* execute request */
        $stmt->execute();
        $result = [];

        /* return a Array of Rows */
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        /* return result */
        return $result;
    }


    /* SAME AS SEARCHUSER FUNCTION -> BUT RETURN INTEGER OF ALL FOUNDED USERS -> FOR THE PAGINTION */
    public function searchUserCounter(array $data): array | int
    {

        $offset = $data["offset"];
        $orderBy = $data["orderBy"];
        $sc = $data["sc"];

        $sql =
            "SELECT COUNT(*) FROM view_name
        WHERE email LIKE CONCAT(:email, '%') AND name LIKE CONCAT(:name, '%') AND postcode LIKE CONCAT(:postcode, '%') AND place LIKE CONCAT(:place, '%') AND telefon LIKE CONCAT(:telefon, '%')
        ORDER BY $orderBy $sc
        LIMIT 10 OFFSET $offset";



        if (isset($data["name"]))
            $name = $data["name"];
        else {
            $name = "%";
        }

        if (isset($data["email"]))
            $email = $data["email"];
        else {
            $email = "%";
        }
        if (isset($data["postcode"]))
            $postcode = $data["postcode"];
        else {
            $postcode = "%";
        }
        if (isset($data["place"]))
            $place = $data["place"];
        else {
            $place = "%";
        }
        if (isset($data["telefon"]))
            $telefon = $data["telefon"];
        else {
            $telefon = "%";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":postcode", $postcode, PDO::PARAM_INT);
        $stmt->bindValue(":place", $place, PDO::PARAM_INT);
        $stmt->bindValue(":telefon", $telefon, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["COUNT(*)"];
    }


    /* Get RefreshToken */
    public function getRefreshToken(string $id): int
    {

        /* Get refreshToken for given ID */
        $sql = "SELECT refreshToken FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        /* Chek if Valid */
        try {
            $secretKey  = $_ENV["REFRESHTOKEN_KEY"];
            JWT::decode($data["refreshToken"], new Key($secretKey, 'HS256'));
            $valid = 0;
        } catch (\Firebase\JWT\ExpiredException $exception) {
            $valid = 1;
            echo $exception;
        }

        /* return 0 => true oder 1= false*/
        return $valid;
    }


    /* UPDATE A USER WITH GIVEN ID */
    public function update(array $current, array $new): int
    {

        /* check if Values are empty -> If empty -> set old Value */
        if (isset($new["passwort"]) && $new["passwort"] !== "") {
            $password_hash = password_hash($new["passwort"], PASSWORD_DEFAULT);
        } else {
            $password_hash = $current["passwort"];
        }
        if (isset($new["email"]) && $new["email"] !== "") {
            $email = $new["email"];
        } else {
            $email = $current["email"];
        }
        if (isset($new["name"]) && $new["name"] !== "") {
            $name = $new["name"];
        } else {
            $name = $current["name"];
        }
        if (isset($new["postcode"]) && $new["postcode"] !== "") {
            $postcode = $new["postcode"];
        } else {
            $postcode = $current["postcode"];
        }
        if (isset($new["place"]) && $new["place"] !== "") {
            $place = $new["place"];
        } else {
            $place = $current["place"];
        }
        if (isset($new["telefon"]) && $new["telefon"] !== "") {
            $telefon = $new["telefon"];
        } else {
            $telefon = $current["telefon"];
        }
        if (isset($new["permission"]) && $new["permission"] !== "") {
            $permission = $new["permission"];
        } else {
            $permission = $current["permission"];
        }


        /* Build query -> bindValue -> execute */
        $sql = "UPDATE users
         SET email = :email, passwort = :passwort
         WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":passwort", $password_hash ?? $current["passwort"], PDO::PARAM_STR);
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->execute();


        /* update userspermissiontable */
        $sql = "UPDATE userspermission
            SET permission = :permission
            WHERE userspermission.userID = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":permission",  $permission, PDO::PARAM_INT);
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->execute();

        /* update registry */
        $sql = "UPDATE registry
        SET postcode = :postcode, place = :place, name = :name, telefon = :telefon
        WHERE registry.userID = :id";

        /* update registry */
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":postcode",  $postcode, PDO::PARAM_INT);
        $stmt->bindValue(":place",  $place, PDO::PARAM_STR);
        $stmt->bindValue(":telefon", $telefon, PDO::PARAM_INT);
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->execute();

        /* reutn updated User */
        return $stmt->rowCount();
    }


    /* UPDATE USER AFTER MAILVERIFICATION */
    public function updateVerification(string $mailToken): bool
    {
        /* Check EmailToken */
        try {
            $key = $_ENV["EMAIL_TOKEN"];
            $decoded = JWT::decode($mailToken, new Key($key, 'HS256'));

            /* get the username */
            $array = json_decode(json_encode($decoded), true);
            $email = $array["username"];

            /* Build query to get mailToken */
            $sql = "SELECT id
                FROM users
                WHERE email = :email AND mailToken=:mailToken";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":mailToken", $mailToken, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            /* Check if user exists */
            if ($user) {
                $userID = $user["id"];

                /* build query to update isMailConfirmed */
                $sql = "UPDATE registry
                SET mailConfirmed = :mailConfirmed
                WHERE registry.userID = :userID";

                /* update registry */
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(":mailConfirmed", true, PDO::PARAM_INT);
                $stmt->bindValue(":userID", $userID, PDO::PARAM_INT);
                $stmt->execute();
                $confirmed = true;
            } else {
                $confirmed = false;
                echo "no User";
            }
        } catch (\Exception $e) {
            $confirmed = false;
            echo $e;
        }

        return $confirmed;
    }


    /* UPDATE REFRESHTOKEN IN DB */
    public function updateRefreshToken(string $id, string $refreshToken): void
    {
        $sql = "UPDATE users
         SET refreshToken = :refreshToken
         WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->bindValue(":refreshToken", $refreshToken, PDO::PARAM_STR);
        $stmt->execute();
    }



    /* DELETE USER WITH PASSED ID */
    public function delete(string $id): int
    {
        $sql = "DELETE FROM users
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }



    /* LOGIN USER WITH PARAMS EMAIL AND PW */
    public function login(string $email): array | false
    {
        /* get datas from users */
        $sql = "SELECT *
                FROM users
                WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        /* return datas -> PW etc */
        return $data;
    }


    /* SEND AN EMAIL AGAIN */
    public function getUserEmailAgain(string $email): array | false
    {
        /* Find User with param Email */
        $sql = "SELECT *
                FROM users INNER JOIN registry ON users.id=registry.userID
                WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    /* SEND THE EMAIL TO A USER */
    public function sendEmail(string $email, string $name, string $emailToken): void
    {
        /* helperfunction to get my own Email PW as a JWTToken and validate to a String */
        $secretKey  = $_ENV["EMAIL_TOKEN"];
        $decoded = JWT::decode($_ENV["MAIL_PW"], new Key($secretKey, 'HS256'));
        $array = json_decode(json_encode($decoded), true);
        $emailPW = $array["username"];

        /* Build the mailconnection */
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = $_ENV["MAIL_HOST"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        /* Connect with credetials */
        $mail->Username = $_ENV["MAIL_USERNAME"];
        $mail->Password = $emailPW;
        $mail->setFrom($_ENV["MAIL_USERNAME"], "Timothy");
        $mail->addAddress($email, $name);

        /* Body with URL include the path to verify and the emailToken from the Db*/
        $mail->Subject = "Please verify your email";
        $mail->Body = "Please click this email to confirm your email: http://localhost:3000/index.php/verify/reg/?verify=$emailToken";

        /* execute sendMail */
        $mail->send();
    }



    /* CREATE JWTTOKEN SESSION*/
    public function createToken($username, $mail): string
    {
        /* Check if secretecode should be Email or SessionKey
        -> expireTime, issuedAt and username pass as payload */
        $key = $mail ? $_ENV["EMAIL_TOKEN"] : $_ENV["SESSIONTOKEN_KEY"];
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+10 minutes')->getTimestamp();
        $serverName =  "http://localhost:3000/index.php";
        $payload = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,
            "username" => $username
        ];

        /* encode the token with build Payload, Key and Algorithm */
        $token = JWT::encode($payload, $key, 'HS256');

        /* return $token; */
        return $token;
    }

    /* CREATE JWTTOKEN REFRESH */
    public function createRefreshToken($username): string
    {
        $key = $_ENV["REFRESHTOKEN_KEY"];
        $issuedAt   = new DateTimeImmutable();
        $expire     = $issuedAt->modify('+20 minutes')->getTimestamp();
        $serverName =  "http://localhost:3000/index.php";
        $payload = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,
            "username" => $username
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        /* echo $token; */
        return $token;
    }



    /* VALIDATE IF SESSIONTOKEN IS VALID OR EXPIRED */
    public function validateToken($header): string | bool
    {
        /* Check if header has the Bearer Token -> decode the Token when found and get the username(email)
         -> else  return false */
        try {
            if (isset($header['Authorization'])) {
                $token = explode(' ', $header['Authorization'])[1];
                $secretKey  = $_ENV["SESSIONTOKEN_KEY"];
                $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
                $array = json_decode(json_encode($decoded), true);
                $email = $array["username"];
            } else {
                $email = false;
            }
        } catch (\Firebase\JWT\ExpiredException $exception) {
            $email = false;
            echo "token is expired";
        }
        /* email or false */
        return $email;
    }


    /* VALIDATE IF REFRESHTOKEN IS VALID OR EXPIRED */
    public function validateRefreshToken($token): string | bool
    {
        try {
            $secretKey  = $_ENV["REFRESHTOKEN_KEY"];
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $array = json_decode(json_encode($decoded), true);
            $email = $array["username"];
        } catch (\Firebase\JWT\ExpiredException $exception) {
            $email = false;
            echo "token is expired";
        }
        return $email;
    }


    /* LOGGOUTFUNCTION -> DELETE REFRESHTOKEN FROM DB AND LOGG USER OUT */
    public function loggout(string $id): bool
    {
        try {
            $sql = "UPDATE users
         SET refreshToken = :refreshToken
         WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_STR);
            $stmt->bindValue(":refreshToken", NULL, PDO::PARAM_STR);
            $stmt->execute();
            $deletet = true;
        } catch (Exception $e) {
            echo $e;
            $deletet = false;
        }

        return $deletet;
    }


    /* CALL SENDEMAIL FUNCTION */
    public function sendMailAgain(array $data, string $name, string $mailToken): void
    {
        $this->sendEmail($data["email"], $name, $mailToken);
    }


    /* TESTFUNCTION FOR PHPUNIT */
    public function add($num1, $num2)
    {
        return $num1 + $num2;
    }
}
