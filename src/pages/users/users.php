<?php
include_once(__DIR__ . "/../../database/users-db-helpers.php");

// Setting action - view new or default
$task = $_GET["task"] ?? "default";

switch ($task) {
    case "save_new_user":
        save_user();
        break;
    case "delete":
        delete();
        break;
    case "modify":
        modify_user();
        break;
    default:
        if (!defined("MAIN_RAN")) {
            header("Location: ../?page=users");
            die();
        }

        display_default();
}

function display_default(): void
{
    $users = fetch_users();
    echo <<<HTML
        <!DOCTYPE html>
        
        <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                
                <link rel="stylesheet" href="users/users.css">
                
                <title>User List</title>
            </head>
            
            <body>
                <h1 id="tutName">User List</h1>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search users" style=" padding: 10px; margin-bottom: 20px;">
                </div>
        
                <div class="table-wrapper">
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Technical?</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        
                        <tbody>
        HTML;
    //display all users in table
    foreach ($users as $user) {
        $name = $user->full_name;
        $technical = $user->is_technical ? "Yes" : "No";
        $deleteButton = ($user->id == $_SESSION['user']->id) ? '' : '<button class="deleteBtn">Delete</button>';
        echo
        '<tr data-user-id="' . $user->id . '">
                    <td>' . $name . '</td>
                    <td>' . $user->email . '</td>
                    <td>' . $user->role . '</td>
                    <td>' . $technical . '</td>
                    
                    <td>
                        <button class="editBtn">Edit</button>
                        ' . $deleteButton . '
                    </td>
                </tr>';
    }

    //new user form
    echo <<<HTML
                        </tbody>
                    </table>
                </div>
                
                <form id="add-user-form" method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                    <input type="email" name="email" placeholder="Email" required>
        
                    <select name="role">
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Admin">Admin</option>
                    </select>
        
                    <div class="checkbox-container">
                        <label for="is_technical">Technical User?</label>
                        <input type="checkbox" id="is_technical" name="is_technical" value="1">
                    </div>
        
                    <button id="add-user-btn" type="submit">Add User</button>
                    <span class="subtext">(Email must end with "@make-it-all.co.uk")</span>
                </form>
                
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="users/users.js"></script>
            </body>
        </html>
        HTML;
}
//save user called by js
function save_user(): void
{
    header("Content-Type: application/json");

    $_POST = json_decode(file_get_contents("php://input"), true);

    $first_name = $_POST["first_name"] ?? null;
    $last_name = $_POST["last_name"] ?? null;
    $email = $_POST["email"] ?? null;
    $role = $_POST["role"] ?? null;
    $is_technical = $_POST["is_technical"] ?? false;

    if ($first_name && $email && $role) {
        $result = add_user($first_name, $last_name, $email, $role, $is_technical);

        if ($result) {
            $user = get_user_from_email($email);
            $response = [
                "success" => true,
                "userData" => [
                    "id" => $user->id,
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "email" => $email,
                    "role" => $role,
                    "is_technical" => $is_technical
                ]
            ];
            echo json_encode($response);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving user"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error: Required data not provided."]);
    }

    exit();
}
// delete user called by js
function delete(): void
{
    $user_id = $_POST["user_id"] ?? null;

    if ($user_id) {
        $result = delete_user($user_id);
        if ($result) {
            echo json_encode(["success" => true, "message" => "User successfully deleted"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete user"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    }

    exit();
}
//modify user called by js
function modify_user(): void
{
    header("Content-Type: application/json");

    $_POST = json_decode(file_get_contents("php://input"), true);

    $user_id = $_POST["user_id"] ?? null;
    $first_name = $_POST["first_name"] ?? null;
    $last_name = $_POST["last_name"] ?? null;
    $role = $_POST["role"] ?? null;
    $is_technical = $_POST["is_technical"] ?? null;

    $success = update_user($user_id, first_name: $first_name, last_name: $last_name, role: $role, is_technical: $is_technical);

    if ($success) {
        echo json_encode(["success" => true, "message" => "User successfully updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update user"]);
    }

    exit();
}
