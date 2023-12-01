<?php

namespace App;

class UserController
{

    /* Controller callt Gateway functions */
    public function __construct(private UserGateway $gateway)
    {
    }

    /* FUNCTION TO SWITCH BEETWEN REQUESTS AND PASS PARAMS */
    public function processRequest(string $method, ?string $id, ?string $route, ?string $query_params, ?string $permission): void
    {
        /* get request header */
        $header = apache_request_headers();

        /* switch beetwen $routes with param route */
        switch ($route) {
                /* User Route -> CRUD for User */
            case $_ENV["USER_API_KEY"]:
                if ($id) {
                    /* ID -> GET && PATCH && DELETE -> ID, header Autj and permission */
                    $this->processResourceRequest($method, $id, $header, $permission);
                } else {
                    /* !ID Create  */
                    $this->processCollectionRequest($method);
                }
                break;
            case $_ENV["LOGIN_API_KEY"]:
                $this->processResourceLoginRequest();
                break;
            case $_ENV["MAILAGAIN_API_KEY"]:
                $this->processResourceEmailAgainRequest();
                break;
            case $_ENV["VERIFY_API_KEY"]:
                $this->processResourceVerifyUser($query_params);
                break;
            case $_ENV["SEARCH_API_KEY"]:
                $this->processResourceSearchUser($header);
                break;
            case $_ENV["SEARCHCOUNTER_API_KEY"]:
                $this->processResourceSearchUserCounter();
                break;
            case $_ENV["LOGGOUT_API_KEY"]:
                $this->processLoggoutUser($id);
                break;
            case $_ENV["REFRESH_API_KEY"]:
                $this->processRefreshToken();
                break;
        }
    }

    private function processResourceRequest(string $method, string $id, array $header): void
    {
        /* Check Token and Validate */
        $valid = $this->gateway->validateToken($header);

        /* Token Valid -> get User by ID */


        if ($valid) {
            $user = $this->gateway->get($id);
            $permission = $user["permission"];

            if ($user["id"] == $id) {
                $permission = 3;
            }


            /* !User -> error 404  */
            if (!$user) {
                http_response_code(404);
                echo json_encode(["message" => "user$user not found"]);
                return;
            }

            switch ($method) {
                case "GET":
                    /* return $user and status 200 */
                    http_response_code(200);
                    echo json_encode(
                        $user
                    );
                    break;

                case "PATCH":


                    /* Check permission for patch -> permission >= 1 error 422 denied */
                    if ($permission == 1) {
                        http_response_code(422);
                        echo json_encode([
                            "errors" => $user["id"] . " " . $id
                        ]);
                        break;
                    } else {

                        /* Get the input data */
                        $data = (array) json_decode(file_get_contents("php://input"), true);

                        /* Call Validator helpfunction and Check params */
                        $errors = $this->getValidationErrors($data, false);

                        /* error ? -> 422 and a "list" of (all) errors */
                        if (!empty($errors)) {
                            http_response_code(422);
                            echo json_encode(["errors" => $errors]);
                            break;
                        }

                        /* call update Methode */
                        $rows = $this->gateway->update($user, $data);

                        /* return updated rows and user id */
                        echo json_encode([
                            "message" => "user $id updated",
                            "rows" => $rows
                        ]);
                    }
                    break;

                case "DELETE":
                    /* Check permission for patch -> permission >= 2 error 422 denied */
                    if ($permission === 1) {
                        http_response_code(422);
                        echo json_encode([
                            "errors" => "permission denied"
                        ]);
                    } else if ($permission === 2) {
                        http_response_code(422);
                        echo json_encode([
                            "errors" => "permission denied"
                        ]);
                        break;
                    } else {
                        /* Call Delete function and delete User from DB */
                        $rows = $this->gateway->delete($id);

                        /* return deleted userid and status 202 */
                        echo json_encode([
                            "code" => 202,
                            "message" => "user deleted",
                            "id" => $id,
                            "rows" => $rows
                        ]);
                    }

                    break;
                default:
                    /* return Allow Methods */
                    http_response_code(405);
                    header("Allow: GET, PATCH, DELETE");
            }
        } else {
            /* return error Invalid Token 401 */
            http_response_code(401);
            echo json_encode([
                "message" => "Token is Invalid"
            ]);
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {

            case "POST":
                /* Get the input data */
                $data = (array) json_decode(file_get_contents("php://input"), true);
                /* Call Validator helpfunction and Check params */
                $errors = $this->getValidationErrors($data);

                /* If there is an error send 422 and errorMessage */
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                /* No Error -> Creat with the given datas an return id and succesMessage */
                $id = $this->gateway->create($data);

                /* return id and status 201 */
                /* http_response_code(201); */
                echo json_encode([
                    "message" => "User created",
                    "id" => $id, "statuscode" => 201
                ]);
                break;

                /* Default not allowed error, exp delete -> 405 + allowed header */
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    /* Login middleware */
    private function processResourceLoginRequest(): void
    {
        /* get input data */
        $data = (array) json_decode(file_get_contents("php://input"), true);
        /* call search function and pass email */
        $user = $this->gateway->login($data["email"]);

        /* !user -> reurn error
        -> else check if param PW is Equal to pw in DB */
        if ($user) {
            if (password_verify($data["passwort"], $user["passwort"])) {
                /* IF pw is right -> Create Session and RefreshToken and update refreshToken in DB */
                $sessionToken = $this->gateway->createToken($user["id"], false);
                $refreshToken = $this->gateway->createRefreshToken($user["id"]);
                $this->gateway->updateRefreshToken($user["id"], $refreshToken);
                $emailcofirmed = $this->gateway->get($user["id"]);
                /*  http_response_code(200); */
                echo json_encode([
                    "message" => "Login Succes",
                    "sessionToken" => $sessionToken,
                    "refreshToken" => $refreshToken,
                    "userID" => $user["id"],
                    "isConfirmed" => $emailcofirmed["mailConfirmed"],
                    "email" => $user["email"],
                    "statuscode" => 200,
                ]);
            } else {
                /* http_response_code(401); */
                echo json_encode([
                    "message" => "Bitte erstmal registrieren",
                    "statuscode" => 401,
                ]);
            }
        } else {
            /* http_response_code(401); */
            echo json_encode([
                "message" => "Bitte erstmal registrieren",
                "statuscode" => 401
            ]);
        }
    }


    /* SEND EMAIL AGAIN */
    private function processResourceEmailAgainRequest(): void
    {

        /* Get input datas */
        $data = (array) json_decode(file_get_contents("php://input"), true);
        /* Check for errors */
        $errors = $this->getValidationErrors($data, false);

        /* If there is an error send 422 and errorMessage */
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["errors" => $errors]);
        }
        /* find User by Email */
        $user = $this->gateway->getUserEmailAgain($data["email"]);
        if ($user) {
            /* User -> Call helperfunction sendMail Again*/
            $this->gateway->sendMailAgain($data, $user["name"], $user["mailToken"]);

            http_response_code(201);
            echo json_encode([
                "message" => "Email send to " . $data["email"]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Bitte erstmal registrieren"
            ]);
        }
    }


    /* Verify a User */
    public function processResourceVerifyUser($query_params): void
    {
        /* call Verificaion Method to check the mailtoken and update isMailConfirmed */
        $confirm = $this->gateway->updateVerification($query_params);

        /* $confirm -> redirect to succesURL
            !$confirm -> redirect to errorURL */
        if ($confirm) {
            header("Location: http://localhost:5173/emailconfirmsucces");
        } else {
            header("Location: http://localhost:5173/emailconfirmerror");
        };
    }


    /* Loggout user delete refreshToken */
    private function processLoggoutUser($id): void
    {
        /* get User by ID */
        $user = $this->gateway->get($id);

        /* $User -> call loggout Function
            !$user -> error */
        if (!$user) {
            http_response_code(404);
            echo json_encode(["message" => "user$user not found"]);
            return;
        } else {
            $result = $this->gateway->loggout($id);
            http_response_code(200);
            echo json_encode([
                "message" => "user erfolgreich ausgeloggt",
                "result" => $result
            ]);
        }
    }


    /* SEARCH USERS */
    private function processResourceSearchUser($header): void
    {

        /* validate Token */
        $valid = $this->gateway->validateToken($header);

        /* User -> check Inputdatas and call searchUser function -> query
        !User -> error 401 */
        if ($valid) {
            $data = (array) json_decode(file_get_contents("php://input"), true);
            $result = $this->gateway->searchUser($data);
            http_response_code(200);
            echo json_encode([
                "result" => $result
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Token is invalid"
            ]);
        }
    }

    /* HELPER FUNCTION TO GET COUNT OF COMPLETE FOUNDET RESULT WITHOUT LIMIT(10)
    -> Used for the Paginator */
    private function processResourceSearchUserCounter(): void
    {
        /* Check Same Input datas as searchCount */
        $data = (array) json_decode(file_get_contents("php://input"), true);

        /* call searchUserCounter function -> return int */
        $result = $this->gateway->searchUserCounter($data);
        http_response_code(200);
        echo json_encode([
            "result" => $result
        ]);
    }



    /* GET A NEW SESSIONTOKEN WHEN REFRESHTOKEN IS VALID */
    private function processRefreshToken(): void
    {

        /* Get input data */
        $data = (array) json_decode(file_get_contents("php://input"), true);
        /*  call validRefreshToken function, pass refreshToken as param */
        $valid = $this->gateway->validateRefreshToken($data["token"]);

        /* !Valid -> No new SessionToken -> User should get loggedout by axios.interceptor
    Valid-> Create and return new SessionToken */
        if ($valid) {
            $sessionToken = $this->gateway->createToken($valid, false);
            http_response_code(200);
            echo json_encode([
                "sessionToken" => $sessionToken
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Not Aut"
            ]);
        }
    }




    /* Rulesenging Validate all fields bevor post or patch */
    /* is_new bool, so isemptyvalidation just works for creating a new set, Validation for the Type still works for both */
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];


        /* is a field empty */
        if ($is_new && empty($data["email"])) {
            $errors[] = "email is required";
        }
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }
        if ($is_new && empty($data["postcode"])) {
            $errors[] = "postcode required";
        }
        if ($is_new && empty($data["place"])) {
            $errors[] = "place is required";
        }
        if ($is_new && empty($data["telefon"])) {
            $errors[] = "telephoneNumber is required";
        }
        if ($is_new && empty($data["passwort"])) {
            $errors[] = "passwort is required";
        }
        if ($is_new && empty($data["terms"])) {
            $errors[] = "terms is required";
        }


        /* check the right type */
        if (array_key_exists("email", $data) && $data["email"] !== "") {
            if (filter_var($data["email"], FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = "email must be an actuall email";
            }
        }
        if (array_key_exists("name", $data) && $data["name"] !== "") {
            if (filter_var(
                $data["name"],
                FILTER_VALIDATE_REGEXP,
                array("options" => array("regexp" => "/^[a-zA-ZäöüÄÖÜ ._-]+(?:[-'\s][a-zA-ZäöüÄÖÜ ._-]+)*$/"))
            ) === false) {
                $errors[] = "name cannot contain any special characters or numbers, except - and space";
            }
        }
        if (array_key_exists("postcode", $data) && $data["postcode"] !== "") {
            if (filter_var(
                $data["postcode"],
                FILTER_VALIDATE_REGEXP,
                array("options" => array("regexp" => "/^[a-zA-Z0-9]+(?:[-'\\s][a-zA-Z0-9]+)*$/"))
            ) === false) {
                $errors[] = "postcode cannot contain any special characters, except -";
            }
        }
        if (array_key_exists("place", $data) && $data["place"] !== "") {
            if (filter_var(
                $data["place"],
                FILTER_VALIDATE_REGEXP,
                array("options" => array("regexp" => "/^[a-zA-ZäöüÄÖÜ ._-]+(?:[-'\s][a-zA-ZäöüÄÖÜ ._-]+)*$/"))
            ) === false) {
                $errors[] = "place cannot contain any special characters, except - and space";
            }
        }
        if (array_key_exists("telefon", $data) && $data["telefon"] !== "") {
            if (filter_var(
                $data["telefon"],
                FILTER_VALIDATE_REGEXP,
                array("options" => array("regexp" => "/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{2,6}$/"))
            ) === false) {
                $errors[] = "The phone number cannot contain any special characters, except -,+ or space";
            }
        }
        return $errors;
    }
}
