<?php
//////////////////////////////////////////////////////////////////////////
/*Functions for showing, adding & logging-in Users to the MySQL Database*/
//////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////
/////*LOGOUT & REGISTER BUTTONS*///////////
///////////////////////////////////////////

/*Close Databases & User Log-Out*/
if (isset($_POST['reset'])) {
    unset($_POST);
    $_POST = array();
    session_unset();
    $_SESSION['QuestionNumber'] = 1;
}

/*Open Users Database Button*/
if (isset($_POST['goUsers'])) {

    showAllUsers();
}

////////////////////////////////
/////*SHOW ALL USERS*///////////
///////////////////////////////

/*Show User Database Function*/
function showAllUsers()
{
    $pdo = new PDO('mysql:host=mysql;dbname=library', 'webDev', 'opport2022');
    $sql = "SELECT * FROM Users";
    foreach ($pdo->query($sql) as $row) {
        echo '<div class="container  p-5 my-5 bg-primary text-white ">';
        echo $row['UserID'] . "<br />";
        echo $row['UserName'] . "<br />";
        echo $row['UserPW'] . "<br /><br />";
        echo $row['UserStatus'] . "<br /><br />";
        echo '</div>';
    }
}

/////////////////////////////
/////*REIGSTER NEW USER*/////
////////////////////////////

/*Add new User to Database*/
if (isset($_POST['adduser'])) {

    /*Check for empty fields*/
    if (!empty($_POST['username'])) {
        $UserName = $_POST['username'];
        $_SESSION['CurrentUserName'] =  $UserName;
    } else {
        echo 'Username Missing';
        echo "<script>alert('Titel Missing')</script> ";
    }
    if (!empty($_POST['userpw'])) {
        $UserPW = $_POST['userpw'];
    } else {
        echo 'Titel Missing';
        echo "<script>alert('Password Missing')</script> ";
    }
    if (!empty($_POST['username']) && !empty($_POST['userpw'])) {

        $pdo = new PDO('mysql:host=mysql;dbname=library', 'webDev', 'opport2022');

        //Get last userID in Table
        /*
        $stmt = $pdo->query("SELECT * FROM Users ORDER BY UserID DESC LIMIT 1");
        $user = $stmt->fetch();
        $_SESSION['CurrentUserID'] = $user['UserID'];
       
        */
        
        //Create Insert with new user
        $sql = 'INSERT INTO Users(UserID, UserName, UserPW, UserStatus) 
        VALUES(:userid, :username, :userpw, :userstatus)';

        $statement = $pdo->prepare($sql);

        $statement->execute([
            ':userid' => $pdo->lastInsertId(),
            ':username' => $UserName,
            ':userpw' => $UserPW,
            ':userstatus' => 'open'
        ]);

        $publisher_id = $pdo->lastInsertId();
        showAllUsers();
        $_SESSION['loggedin'] = true;
        echo 'The User ' . $UserName . ' was added to the database';
    }
}

function addTry()
{
    $currentUserName = $_SESSION['CurrentUserName'];
    $pdo = new PDO('mysql:host=mysql;dbname=library', 'webDev', 'opport2022');

    //Get last userID in Table
    /*
    $stmt = $pdo->query("SELECT * FROM Users WHERE UserName = $currentUserName");
    $user = $stmt->fetch();
    $currentNumberOfTries = $user['NumberTries'] +1;
    echo $currentNumberOfTries;
    
*/

    $sth = $pdo->prepare('SELECT NumberTries FROM Users WHERE UserID = :id');
    $sth->bindValue(':NumberTries', 1 , PDO::PARAM_INT);
    $sth->execute();
    $row = $sth->fetch(PDO::FETCH_ASSOC);

    /*
    "UPDATE Users SET NumberTries = $currentNumberOfTries WHERE UserName = '$currentUserName'";
    */
}

///////////////////////
/////*LOGIN USER*/////
//////////////////////

//Login User
if (isset($_POST['login'])) {

    if (!empty($_POST['logusername'])) {
        $UserName = $_POST['logusername'];
        $_SESSION['CurrentUserName'] =  $UserName;
    } else {
        echo 'Username Missing';
        echo "<script>alert('Titel Missing')</script> ";
    }


    if (!empty($_POST['loguserpw'])) {
        $UserPW = $_POST['loguserpw'];
    } else {
        echo 'Titel Missing';
        echo "<script>alert('Password Missing')</script> ";
    }


    /*Select UserName from Users Database*/
    $pdo = new PDO('mysql:host=mysql;dbname=library', 'webDev', 'opport2022');
    $stmt = $pdo->query("SELECT * FROM Users WHERE UserName = '$UserName'");
    $userLoginData = $stmt->fetch();
    if ($userLoginData == null) {
        echo "Wrong Username ";
        echo "<script>alert('Username not correct')</script> ";
    } else {

        $userpassName = $userLoginData['UserName'];
        $userpassPW = $userLoginData['UserPW'];
        $publisher_id = $pdo->lastInsertId();

        //Username Check
        if ($userpassPW == $UserPW) {

            echo '<div class="container  p-5 my-5 bg-primary text-white ">';
            echo '<h1 class="text-center text-white ">Welcome ' . $userpassName . ' </h1>';
            echo '</div>';

            //Load Main Variables & Show Difficulties
            $_SESSION['QuestionNumber'] = 0;
            $questNum = $_SESSION['QuestionNumber'];
            $_SESSION['Correct'] = '';
            $_SESSION['CorrectAnswers'] = 0;
            $_SESSION['WrongAnswers'] = 0;
            $_SESSION['Q' . $questNum] = '';
            $_SESSION['UserLoggedIn'] = true;
            if($_SESSION['QuestionNumber'] == 0);
            {
            loadDifficulty();
            //addTry();
            }
            
        } else {
            echo "Wrong Password ";
            echo "<script>alert('Passwort not correct')</script> ";
        }
    }
}
