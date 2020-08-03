<?php

    /*
    ==================================================
    == Manage members page
    == you can add | edit  | delete members   
    ==================================================
      */

    session_start();
    $pageTitle = 'Members';
    if(isset($_SESSION['username'])){
        include 'init.php';

        $do = '';

        if(isset($_GET['do'])){
            $do = $_GET['do'];
        }else{
            $do = 'Manage';
        }   
        // start manage page 
        if($do == 'Manage'){
            $stmt = $con->prepare('SELECT * FROM users WHERE groupeid != 1');
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_OBJ);      
            ?>
            <h1 class="edit-title">Manage Members</h1>
            <div class="container table-container">
                <a class="btn-primary" href="?do=Add"><span>+</span> New Member</a>
                <table class="table mg-auto">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>UserName</td>
                            <td>Email</td>
                            <td>Full Name</td>
                            <td>Registred Date</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo $user->userid ?></td>
                                <td><?php echo $user->username ?></td>
                                <td><?php echo $user->email ?></td>
                                <td><?php echo $user->fullname ?></td>
                                <td><?php echo $user->regdate ?></td>
                                <td>
                                    <?php if($user->regstatus == 0): ?>
                                        <a class="btn btn-purple dp-inherit" href="?do=Activate&id=<?php echo $user->userid ?>">Activate</a>
                                    <?php endif; ?> 
                                    <a  class="btn btn-danger dp-inherit" onclick="return confirm('Do You Really Want To Delete ?')" href="?do=Delete&id=<?php echo $user->userid ?>">Delete</a>
                                    <a  class="btn btn-success dp-inherit" href="?do=Edit&id=<?php echo $user->userid ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>    
                    </tbody>
                </table>
            </div>
        <?php
            }elseif($do == 'Add'){?>

            <div class="container">
                <h1 class="edit-title">Add A Member</h1> 
                <form action="?do=Insert" method="POST">
                <!-- start username field -->
                    <div class="form-groupe">
                        <label>Username</label>
                        <input type="text" name="username" autocomplete="off" required>
                    </div>
                    <!-- start password field -->
                    <div class="form-groupe">
                        <label>Password</label>
                        <input type="password" name="password" autocomplete="new-password" required>
                    </div>
                    <!-- start email field -->
                    <div class="form-groupe">
                        <label>Email</label>
                        <input type="email" name="email" autocomplete="off" required>
                    </div>
                    <!-- start Full name field -->
                    <div class="form-groupe">
                        <label>Full name</label>
                        <input type="text" name="full" required>
                    </div>
                    <!-- start save field -->
                    <div class="form-groupe">
                        <input type="submit" value="Insert" class="form-save">
                    </div>
                </form>
            </div>
    <?php
        }elseif($do == 'Edit'){ // edit page 
        
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']): 0;
        
         $query = 'SELECT * FROM users WHERE userid = ? LIMIT 1';
        $stmt =  $con->prepare($query);
        $stmt->execute(array($id));
        $row = $stmt->fetch();
        $count = $stmt->rowCount();
        if($count > 0){
        ?>    
            <div class="container">
                <h1 class="edit-title">Edit Member</h1> 
                <form action="?do=Update" method="POST">
                    <input type="hidden" name="userid" value="<?php echo $id; ?>">
                <!-- start username field -->
                    <div class="form-groupe">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo $row['username']; ?>" autocomplete="off" required>
                    </div>
                    <!-- start password field -->
                    <div class="form-groupe">
                        <label>Password</label>
                        <input type="password" name="newpassword" autocomplete="new-password" placeholder="Leave It Blank If You Do Not Want To Change It">
                        <input type="hidden" name="oldpassword" value="<?php echo $row['password']; ?>" autocomplete="new-password">
                    </div>
                    <!-- start email field -->
                    <div class="form-groupe">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo $row['email']; ?>" autocomplete="off" required>
                    </div>
                    <!-- start Full name field -->
                    <div class="form-groupe">
                        <label>Full name</label>
                        <input type="text" name="full" value="<?php echo $row['fullname']; ?>" required>
                    </div>
                    <!-- start save field -->
                    <div class="form-groupe">
                        <input type="submit" value="save" class="form-save">
                    </div>
                </form>
            </div>    

        <?php
        }else{
            redirect("<div class='alert alert-danger'>there is no such id</div>",5);
        }
        }elseif($do == 'Update'){
            //update page
        echo   '<h1 class="edit-title">Update Member</h1>';
            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                    //get variables from the post
                    $id =$_POST['userid'];
                    $username =$_POST['username'];
                    $email =$_POST['email'];
                    $full =$_POST['full'];
                    $Newpassword =  empty($_POST['newpassword']) ? $_POST['oldpassword']: sha1($_POST['newpassword']);
                    //validate the form
                    $formErros = array();
                    if(empty($username)){
                        array_push($formErros,"<div class='alert alert-danger'>Username Can't Be Empty</div>");
                    }
                    if(strlen($username) < 4){
                        array_push($formErros,"<div class='alert alert-danger'>Username Can't Be Less Than 4 Characters</div>");
                    }
                    if(empty($email)){
                        array_push($formErros,"<div class='alert alert-danger'>Email Can't Be Empty</div>");
                    }
                    if(empty($full)){
                        array_push($formErros,"<div class='alert alert-danger'>Full Name Can't Be Empty</div>");
                    }
                    echo "<div class='alert-container'>";
                        foreach($formErros as $err){
                            echo $err;
                        }
                    echo "</div>";
             // update the database
                if(count($formErros) == 0){
                    $query = "SELECT username FROM users WHERE username = ? AND userid != ?";
                    $stmt = $con->prepare($query);
                    $stmt->execute(array($username,$id));
                    if($stmt->rowCount() == 0){
                    $query = 'UPDATE users SET username=?,password=?,email=?,fullname=? WHERE userid=?';
                    $stmt = $con->prepare($query);
                    $stmt->execute(array($username,$Newpassword,$email,$full,$id));
                    //echo succes message
                    redirect("<div class='alert alert-success'>".$stmt->rowCount()." record updated</div>",5,"back");
                    }else{
                        redirect("<div class='alert alert-danger'>User Already Exists</div>",5,"back");
                    }
                }   
            }else{
                redirect("<div class='alert alert-danger'>You can\' browse this page directly</div>",6);
            }
            
        }elseif($do == 'Delete'){
            $id= is_numeric($_GET['id']) ? intval($_GET['id']): 0;
            if(is_exist($con,'userid','users',$id)){
                $stmt = $con->prepare('DELETE FROM users WHERE userid = ?');
                $stmt->execute(array($id));
                redirect("<div class='alert alert-success'>Record Deleted Successfully</div>",5,"back");
            }else{
                redirect("<div class='alert alert-danger'>User Does Not Exist</div>",5);
            }
            
        }elseif($do == 'Insert'){
            echo   '<h1 class="edit-title">Insert A Member</h1>';
            if($_SERVER['REQUEST_METHOD'] === 'POST'){

                    //get variables from the post
                    $username =$_POST['username'];
                    $email =$_POST['email'];
                    $full =$_POST['full'];
                    $password = $_POST['password'];
                    //validate the form
                    $formErros = array();
                    if(empty($username)){
                        array_push($formErros,"<div class='alert alert-danger'>Username Can't Be Empty</div>");
                    }
                    if(strlen($username) < 4){
                        array_push($formErros,"<div class='alert alert-danger'>Username Can't Be Less Than 4 Characters</div>");
                    }
                    if(empty($email)){
                        array_push($formErros,"<div class='alert alert-danger'>Email Can't Be Empty</div>");
                    }
                    if(empty($full)){
                        array_push($formErros,"<div class='alert alert-danger'>Full Name Can't Be Empty</div>");
                    }
                    if(empty($password)){
                        array_push($formErros,"<div class='alert alert-danger'>Password Can't Be Empty</div>");
                    }else{
                        $password = sha1($password);
                    }
             // update the database
                if(count($formErros) == 0 ){
                    if(!is_exist($con,'username','users',$username)){
                        $query = 'INSERT INTO users 
                        (username,password,email,fullname,groupeid,truststatus,regstatus) 
                        VALUES(?,?,?,?,?,?,?)';
                        $stmt = $con->prepare($query);
                        $stmt->execute(array($username,$password,$email,$full,0,0,1));
                        //echo succes message
                        redirect("<div class='alert alert-success'>".$stmt->rowCount()." record Inserted</div>",5,"back");
                    }else{
                        redirect("<div class='alert alert-danger'>User Already Exists</div>","back");
                    }
                }else{
                    echo "<div class='alert-container'>";
                    foreach($formErros as $err){
                        echo $err;
                    }
                    echo "</div>";  
                } 
            }else{
                redirect("<div class='alert alert-danger'>You can\' browse this page directly</div>",6);
            }
            
        }elseif($do=="Pending"){
            $stmt = $con->prepare("SELECT * FROM users WHERE regstatus = 0");
            $stmt->execute();
            $pendingMembers = $stmt->fetchAll(PDO::FETCH_OBJ);
            if($stmt->rowCount() > 0){?>
                <div class="container table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>UserName</td>
                            <td>Email</td>
                            <td>Full Name</td>
                            <td>Registred Date</td>
                            <td>Status</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingMembers as $user): ?>
                            <tr>
                                <td><?php echo $user->userid ?></td>
                                <td><?php echo $user->username ?></td>
                                <td><?php echo $user->email ?></td>
                                <td><?php echo $user->fullname ?></td>
                                <td><?php echo $user->regdate ?></td>
                                <td><?php echo $user->regstatus ?></td>
                                <td>
                                    <a class="btn btn-danger dp-inherit" onclick="return confirm('Do You Really Want To Delete ?')" href="?do=Delete&id=<?php echo $user->userid ?>">Delete</a>
                                    <a class="btn btn-success dp-inherit" href="?do=Edit&id=<?php echo $user->userid ?>">Edit</a>
                                    <a class="btn btn-purple dp-inherit" href="?do=Activate&id=<?php echo $user->userid ?>">Activate</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>    
                    </tbody>
                </table>
                </div>

            <?php
            }else{
                redirect("<div class='alert alert-info'>All Members Are Activated</div>",6);
            }
        }elseif($do=="Activate"){
            $id = is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
            if(is_exist($con,"userid","users",$id)){
                $stmt = $con->prepare('UPDATE users SET regstatus = 1 WHERE userid = ?');
                $stmt->execute(array($id));
                redirect("<div class='alert alert-success'>Member Activated Successfully</div>",6);
            }else{
                redirect("<div class='alert alert-danger'>User Does Not Exist</div>",5);
            }
        }else{
            redirect("<div class='alert alert-danger'>There is no such page with this name</div>",6);
        }

        include $template . "footer.php";
    }else{
        header('Location:index.php'); //user can't acces this page , redirect to login
        exit(); // exit script
    }
?>
<script src="layout/js/edit.js"></script>