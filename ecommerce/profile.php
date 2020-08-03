<?php
    session_start();
    $pageTitle="Profile";
    include 'init.php';
    if(isset($_SESSION['user'])){
        $stmt = $con->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute(array($_SESSION['user']));
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $items = getItems($user->userid,"userid");
        $comments = getComments($user->userid,"userid"); 
        ?>
    <h1 class="edit-title">My Profile</h1>
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Information</div>
            <div class="panel-body">
                <ul class="user-info">
                    <li>   
                        <span><i class="fa fa-unlock-alt fa-fw"></i> Name</span><?php echo ": ".$user->username; ?>
                    </li>
                    <li>
                        <span><i class="fa fa-envelope fa-fw"></i> Email</span><?php echo ": ".$user->email; ?>
                    </li>
                    <li>
                        <span><i class="fa fa-user-alt fa-fw"></i> Full Name</span><?php echo ": ".$user->fullname; ?>
                    </li>
                    <li>
                        <span><i class="fa fa-calendar fa-fw"></i> Registred Date</span>
                        <time datetime="<?php echo $user->regdate;?>"><?php echo ": ".$user->regdate;?></time>
                    </li>
                    <li>
                        <span><i class="fa fa-tags fa-fw"></i> Favourite Category :</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Latest Ads</div>
            <div class="panel-body">
                <?php if(!empty($items)){ ?>
                <div class="card-container">
                <?php foreach($items as $item): ?>
                    <div class="card">
                        <div class="card-header">
                            <img src="avatar.png" alt="image">
                            <div class="card-overlay">
                                <?php echo $item->price ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3><?php echo $item->name ?></h3>
                            <p><?php echo $item->description ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php 
                }else{
                    echo 'There Is No Items To Show , <a href="ads.php">New Add</a>';
                } ?>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Latest Comments</div>
            <div class="panel-body">
                <?php 
                    if(!empty($comments)){
                        foreach($comments as $comment): ?>
                            <p><?php echo $comment->comment; ?></p>
                            <span><time datetime="<?php echo $comment->date;?>"><?php echo $comment->date;?></time></span>
                        <?php endforeach;
                    }else{
                        echo 'There Is No Comments To Show';
                    }
                ?>
            </div>
        </div>
    </div>
<?php
    }else{
        header("Location:login.php");
        exit();
    }
    include $template."footer.php";

    /* $do = isset($_GET['do']) ? $_GET['do']:"Edit";

        if($do=="Edit"){
            
            $stmt = $con->prepare("SELECT * FROM users WHERE username=?");
            $stmt->execute(array($_SESSION['user']));
            if($stmt->rowCount() > 0){
                $user = $stmt->fetch(PDO::FETCH_OBJ);
            ?>
            <h1 class="edit-title">Edit Profile</h1>
            <div class="container">
                <form action="?do=Update" method="POST">
                    <div class="form-groupe">
                        <label>Username : </label>
                        <input type="text" value="<?php echo $user->username;?>" name="username" required autofocus autocomplete="off">
                    </div>
                    <div class="form-groupe">
                        <label>Password : </label>
                        <input type="password" placeholder="Leave It Black If You Do Not Want To Update It" name="password" required autocomplete="new-password">
                    </div>
                    <div class="form-groupe">
                        <label>Email : </label>
                        <input type="email" value="<?php echo $user->email;?>" name="email" required>
                    </div>
                    <div class="form-groupe">
                        <label>Fullname : </label>
                        <input type="text" value="<?php echo $user->fullname;?>" name="fullname">
                    </div>
                    <div class="form-groupe">
                        <input type="submit" class="form-save" name="submit" value="Save">
                    </div>
                </form>
            </div>
        <?php
            }else{

            }
        }elseif($do=="Update"){

        }else{
            redirect('<div class="alert alert-danger">There Is No Such Page</div>',5,'back');
        }*/