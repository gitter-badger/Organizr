<?php 
//Set some variables
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);
date_default_timezone_set('America/Los_Angeles');
$data = false;
$databaseLocation = "databaseLocation.ini.php";
$needSetup = "Yes";
$tabSetup = "Yes";	
$hasOptions = "No";
$settingsicon = "No";
$settingsActive = "";
$action = "";
$title = "Organizr";
$topbar = "#eb6363"; 
$topbartext = "#FFFFFF";
$bottombar = "#eb6363";
$sidebar = "#000000";
$hoverbg = "#eb6363";
$activetabBG = "#eb6363";
$activetabicon = "#FFFFFF";
$activetabtext = "#FFFFFF";
$inactiveicon = "#FFFFFF";
$inactivetext = "#FFFFFF";

function registration_callback($username, $email, $userdir){
    
    global $data;
    
    $data = array($username, $email, $userdir);

}

function printArray($arrayName){
    
    foreach ( $arrayName as $item ) :
        
        echo $item . "<br/>";
        
    endforeach;
    
}

function write_ini_file($content, $path) { 
    
    if (!$handle = fopen($path, 'w')) {
        
        return false; 
    
    }
    
    $success = fwrite($handle, $content);
    
    fclose($handle); 
    
    return $success; 

}
                
if(isset($_POST['action'])) :

    $action = $_POST['action'];
    
endif;

if($action == "createLocation") :

    $databaseData = '; <?php die("Access denied"); ?>' . "\r\n";

    foreach ($_POST as $postName => $postValue) {
            
        if($postName !== "action") :
        
            if(substr($postValue, -1) == "/") : $postValue = rtrim($postValue, "/"); endif;
        
            $databaseData .= $postName . " = \"" . $postValue . "\"\r\n";
        
        endif;
        
    }

    write_ini_file($databaseData, $databaseLocation);

endif;

if(!file_exists($databaseLocation)) :

    $configReady = "No";
    $userpic = "";

else :

    $configReady = "Yes";

    require_once("user.php");

    $USER = new User("registration_callback");

    $dbfile = DATABASE_LOCATION  . constant('User::DATABASE_NAME') . ".db";

    $database = new PDO("sqlite:" . $dbfile);

    $query = "SELECT * FROM users";

    foreach($database->query($query) as $data) {

        $needSetup = "No";

    }

    $db = DATABASE_LOCATION  . constant('User::DATABASE_NAME') . ".db";
    $file_db = new PDO("sqlite:" . $db);
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbTab = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="tabs"');
    $dbOptions = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="options"');

    foreach($dbTab as $row) :

        if (in_array("tabs", $row)) :

            $tabSetup = "No";

        endif;

    endforeach;

    if($tabSetup == "Yes") :

        $settingsActive = "active";
    
    endif;

    foreach($dbOptions as $row) :

        if (in_array("options", $row)) :

            $hasOptions = "Yes";

        endif;

    endforeach;

    if($tabSetup == "No") :

        if($USER->authenticated && $USER->role == "admin") :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true"');
            $getsettings = $file_db->query('SELECT * FROM tabs WHERE active = "true"');

            foreach($getsettings as $row) :

                if(!empty($row['iconurl']) && $settingsicon == "No") :

                    $settingsicon = "Yes";

                endif;

            endforeach;

        elseif($USER->authenticated && $USER->role == "user") :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true" AND user = "true"');

        else :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true" AND guest = "true"');

        endif;

    endif;

    if($hasOptions == "Yes") :

        $resulto = $file_db->query('SELECT * FROM options');

        foreach($resulto as $row) : 

            $title = $row['title'];
            $topbartext = $row['topbartext'];
            $topbar = $row['topbar'];
            $bottombar = $row['bottombar'];
            $sidebar = $row['sidebar'];
            $hoverbg = $row['hoverbg'];
            $activetabBG = $row['activetabBG'];
            $activetabicon = $row['activetabicon'];
            $activetabtext = $row['activetabtext'];
            $inactiveicon = $row['inactiveicon'];
            $inactivetext = $row['inactivetext'];

        endforeach;

    endif;

    $userpic = md5( strtolower( trim( $USER->email ) ) );

endif;

?>

<!DOCTYPE html>

<html lang="en" class="no-js">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no" />

        <title><?=$title;?><?php if($title !== "Organizr") :  echo "- Organizr"; endif; ?></title>

        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="bower_components/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="bower_components/metisMenu/dist/metisMenu.min.css">
        <link rel="stylesheet" href="bower_components/Waves/dist/waves.min.css"> 
        <link rel="stylesheet" href="bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css"> 

        <link rel="stylesheet" href="js/selects/cs-select.css">
        <link rel="stylesheet" href="js/selects/cs-skin-elastic.css">
        <link rel="stylesheet" href="bower_components/google-material-color/dist/palette.css">
        
        <link rel="stylesheet" href="bower_components/sweetalert/dist/sweetalert.css">
        <link rel="stylesheet" href="bower_components/smoke/dist/css/smoke.min.css">


        <script src="js/menu/modernizr.custom.js"></script>
        <script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/user.js"></script>

        <link rel="stylesheet" href="css/style.css">

        
        <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="images/favicon/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="images/favicon/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="images/favicon/manifest.json">
        <link rel="mask-icon" href="images/favicon/safari-pinned-tab.svg" color="#2d89ef">
        <link rel="shortcut icon" href="images/favicon/favicon.ico">
        <meta name="msapplication-config" content="images/favicon/browserconfig.xml">
        <meta name="theme-color" content="#2d89ef">
        
        <!--[if lt IE 9]>
        <script src="bower_components/html5shiv/dist/html5shiv.min.js"></script>
        <script src="bower_components/respondJs/dest/respond.min.js"></script>
        <![endif]-->
        
    </head>

    <body style="overflow: hidden">

        <!--Preloader-->
        <div id="preloader" class="preloader table-wrapper">
            
            <div class="table-row">
                
                <div class="table-cell">
                    
                    <div class="la-ball-scale-multiple la-3x" style="color: <?=$topbar;?>">
                        
                        <div></div>
                        <div></div>
                        <div></div>
                    
                    </div>
                
                </div>
            
            </div>
        
        </div>

        <div id="main-wrapper" class="main-wrapper">
            
            <style>
                .bottom-bnts a {
                    
                    background: <?=$bottombar;?>;
                    color: <?=$topbartext;?>;
                
                }.bottom-bnts {
                    
                    background-color: <?=$bottombar;?>;
                
                }.gn-menu-main {
                    
                   
                    background-color: <?=$topbar;?>;
                
                }.gn-menu-main ul.gn-menu {
                    
                    background: <?=$sidebar;?>;
                
                }.gn-menu-wrapper {
                
                    background: <?=$sidebar;?>;
                
                }.gn-menu i {
                    
                    height: 18px;
                    width: 52px;
                
                }.la-timer.la-dark {
                    
                    color: <?=$topbartext;?>
                
                }.refresh-preloader {
                    
                    background: <?=$topbartext;?>;
                
                }.la-timer {
                    
                    width: 75px;
                    height: 75px;
                    padding-top: 20px;
                    border-radius: 100px;
                    background: <?=$sidebar;?>;
                    border: 2px solid <?=$topbar;?>;
                
                }.tab-item:hover a {
                    
                    color: <?=$sidebar;?> !important;
                    background: <?=$hoverbg;?>;
                    border-radius: 100px 0 0 100px;
                
                }.gn-menu li.active > a {
                    
                    color: <?=$activetabtext;?> !important;
                    background: <?=$activetabBG;?>;
                    border-radius: 100px 0 0 100px;
                
                }.active {
                    
                    display: block;
                
                }.hidden {
                    
                    display: none;
                    
                }.errorz {
                	
                	background-image: linear-gradient(red, red), linear-gradient(#d2d2d2, #d2d2d2);
            	    outline: none;
            	    animation: input-highlight .5s forwards;
            	    box-shadow: none;
            	    padding-left: 0;
            	    border: 0;
            	    border-radius: 0;
            	    background-size: 0 2px,100% 1px;
            	    background-repeat: no-repeat;
            	    background-position: center bottom,center calc(100% - 1px);
            	    background-color: transparent;
            	    box-shadow: none;
                	    
                }.gn-menu li.active i.fa {
                    
                    color: <?=$activetabicon;?>;
                    
                }.gn-menu li i.fa {
                    
                    color: <?=$inactiveicon;?>;
                    
                }.gn-menu-main ul.gn-menu a {
                    
                    color: <?=$inactivetext;?>;
                }li.dropdown.some-btn .mdi {
                    
                    color: <?=$topbartext;?>;
                    
                }.nav>li>a:focus, .nav>li>a:hover {
                    
                    text-decoration: none;
                    background-color: transparent;
                    
                }div#preloader {
                    
                    background-color: <?=$topbartext;?>;
                    
                }.iframe {
                    
                    -webkit-overflow-scrolling: touch;

                }.iframe iframe{

                }#menu-toggle span {
                    background: <?=$topbartext;?>;
                }
                
            </style>

            <ul id="gn-menu" class="gn-menu-main">
                
                <li class="gn-trigger">
                    
                    <a id="menu-toggle" class="menu-toggle gn-icon gn-icon-menu">
                        
                        <div class="hamburger">
                            
                            <span></span>
                            <span></span>
                            <span></span>
                        
                        </div>
                        
                        <div class="cross">
                            
                            <span></span>
                            <span></span>
                        
                        </div>
                    
                    </a>
                    
                    <nav class="gn-menu-wrapper">
                        
                        <div class="gn-scroller">
                            
                            <ul class="gn-menu metismenu">

                                <!--Start Tab List-->
                                
                                <?php if($tabSetup == "No") : foreach($result as $row) : 
                                
                                if($row['defaultz'] == "true") : $defaultz = "active"; else : $defaultz = ""; endif;?>
                                
                                <li window="<?=$row['window'];?>" class="tab-item <?=$defaultz;?>" id="<?=$row['url'];?>x">
                                    
                                    <a class="tab-link">
                                        
                                        <?php if($row['iconurl']) : ?>
                                        
                                            <i style="font-size: 19px; padding: 0 10px; font-size: 19px;">
                                                <img src="<?=$row['iconurl'];?>" style="height: 30px; margin-top: -2px;">
                                            </i>
                                        
                                        <?php else : ?>
                                        
                                            <i class="fa <?=$row['icon'];?> fa-lg"></i>
    
                                        <?php endif; ?>
                                        
                                        <?=$row['name'];?>
                                    
                                    </a>

                                </li>
                                
                                <?php endforeach; endif;?>
                                
                                <?php if($configReady == "Yes") : if($USER->authenticated && $USER->role == "admin") :?>
                                <li class="tab-item <?=$settingsActive;?>" id="settings.phpx">
                                                            
                                    <a class="tab-link">
                                        
                                        <?php if($settingsicon == "Yes") :
                                        
                                            echo '<i style="font-size: 19px; padding: 0 10px; font-size: 19px;">
                                                <img id="settings-icon" src="images/settings.png" style="height: 30px; margin-top: -2px;"></i>';
                                        
                                        else :
                                        
                                            echo '<i id="settings-icon" class="fa fa-cog"></i>';
                                        
                                        endif; ?>
                                        
                                        Settings
                                    
                                    </a>
                                
                                </li>
                                <?php endif; endif;?>
                                
                                <!--End Tab List-->
                           
                            </ul>
                        
                        </div>

                        <!-- /gn-scroller -->
                        <div class="bottom-bnts">
                            
                            <a class="fix-nav"><i class="mdi mdi-pin"></i></a>
                        
                        </div>
                    
                    </nav>
                
                </li>

                <li class="top-clock">
                    
                    <span><span style="color:<?=$topbartext;?>;"><b><?=$title;?></b></span></span>
                
                </li>

                <li class="pull-right">
                    
                    <ul class="nav navbar-right right-menu">
                        
                        <li class="dropdown notifications">
                            
                            <?php if($configReady == "Yes") : if(!$USER->authenticated) : ?>
                            
                            <a class="log-in">
                            
                            <?php endif; endif;?>
                            
                            <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
                            
                            <a class="show-members">
                                
                            <?php endif; endif;?>
                                
                                <i class="userpic"><img src="https://www.gravatar.com/avatar/<?=$userpic;?>?s=40&d=mm" class="img-circle"></i> 
                                
                            </a>
                            
                        </li>
                        
                        <li class="dropdown some-btn">
                            
                            <a class="fullscreen">
                                
                                <i class="mdi mdi-fullscreen"></i>
                            
                            </a>
                        </li>
                        
                        <li class="dropdown some-btn">
                            
                            <a id="reload" class="refresh">
                               
                                <i class="mdi mdi-refresh"></i>
                           
                            </a>
                        
                        </li>
                    
                    </ul>
                
                </li>
            
            </ul>

            <!--Content-->
            <div id="content" class="content" style="">

                <!--Load Framed Content-->
                <?php if($needSetup == "Yes" && $configReady == "Yes") : ?>
                <div class="table-wrapper">

                    <div class="table-row">

                        <div class="table-cell text-center">

                            <div class="login i-block">

                                <div class="content-box">

                                    <div class="green-bg biggest-box">

                                        <h1 class="zero-m text-uppercase">Create Admin</h1>

                                    </div>

                                    <div class="big-box text-left registration-form">

                                        <h4 class="text-center">Create an account for Admin Access</h4>

                                        <form class="controlbox" name="new user registration" id="registration" action="" method="POST" data-smk-icon="glyphicon-remove-sign">

                                            <input type="hidden" name="op" value="register"/>
                                            <input type="hidden" name="sha1" value=""/>

                                            <div class="form-group">

                                                <input type="text" class="form-control material" name="username" autofocus placeholder="Username" autocorrect="off" autocapitalize="off" minlength="3" maxlength="16" required>

                                            </div>

                                            <div class="form-group">

                                                <input type="email" class="form-control material" name="email" placeholder="E-mail">

                                            </div>

                                            <div class="form-group">

                                                <input type="password" class="form-control material" name="password1" placeholder="Password" data-smk-strongPass="weak" required>

                                            </div>

                                            <div class="form-group">

                                                <input type="password" class="form-control material" name="password2" placeholder="Retype Password">

                                            </div>

                                            <input id="registerSubmit" type="button" class="btn green-bg btn-block btn-warning text-uppercase waves waves-effect waves-float" value="Register">

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <?php endif; ?>
                
                
                <?php if($needSetup == "Yes" && $configReady == "No") : ?>
                <div class="table-wrapper">

                    <div class="table-row">

                        <div class="table-cell text-center">

                            <div class="login i-block">

                                <div class="content-box">

                                    <div class="green-bg biggest-box">

                                        <h1 class="zero-m text-uppercase">Database Path</h1>

                                    </div>

                                    <div class="big-box text-left registration-form">

                                        <h3 class="text-center">Specify the location of which you want to save your database files.</h3>
                                        <h5 class="text-left"><strong>Current Direcotry: <?php echo __DIR__; ?> <br>Parent Directory: <?php echo dirname(__DIR__); ?></strong></h5>
                                        
                                        <form class="controlbox" name="setupDatabase" id="setupDatabase" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                            
                                            <input type="hidden" name="action" value="createLocation" />

                                            <div class="form-group">

                                                <input type="text" class="form-control material" name="databaseLocation" autofocus value="<?php echo dirname(__DIR__);?>" autocorrect="off" autocapitalize="off" required>
                                                
                                                <?php if(file_exists(dirname(__DIR__) . '/users.db') || file_exists(__DIR__ . '/users.db')) : echo '<h5 class="text-center red">Don\'t worry, you\'re database is still there.  Just use the same location you have it in.</h5>'; endif;?>

                                            </div>

                                            <input id="databaseLocationSubmit" type="submit" class="btn green-bg btn-block btn-sm text-uppercase waves waves-effect waves-float" value="Save Location">

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <?php endif; ?>
                
                
                <?php if($configReady == "Yes") : if(!$USER->authenticated && $tabSetup == "Yes" && $needSetup == "No") :?>
                <div class="table-wrapper">
                
                    <div class="table-row">
                
                        <div class="table-cell text-center">
                        
                            <div class="login i-block">
                                
                                <div class="content-box">
                                    
                                    <div class="blue-bg biggest-box">
                
                                        <h1 class="zero-m text-uppercase">Awesome!</h1>
                
                                    </div>
                
                                    <div class="big-box text-left registration-form">
                
                                        <h4 class="text-center">Now that you created an Admin account, time to sign and start making some tabs...</h4>
                                        
                                        <button type="submit" class="btn log-in btn-block btn-primary text-uppercase waves waves-effect waves-float">Login</button>
                						                                    
                                    </div>
                                
                                </div>
                            
                            </div>
                        
                        </div>
                    
                    </div>
                
                </div>
                <?php endif; endif; ?>
                <?php if($tabSetup == "No" && $needSetup == "No") :?>        
                <div id="tabEmpty" class="table-wrapper" style="display: none">
                
                    <div class="table-row">
                
                        <div class="table-cell text-center">
                        
                            <div class="login i-block">
                                
                                <div class="content-box">
                                    
                                    <div class="red-bg biggest-box">
                
                                        <h1 class="zero-m text-uppercase">Hold Up!</h1>
                
                                    </div>
                
                                    <div class="big-box text-left registration-form">
                
                                        <br><br><br>
                                        <h2 class="text-center">Looks like you don't have access.</h2>
                                        
                                        <?php if(!$USER->authenticated) : ?>
                                        <button style="background:<?=$topbar;?>;" type="submit" class="btn log-in btn-block btn-primary text-uppercase waves waves-effect waves-float"><text style="color:<?=$topbartext;?>;">Login</text></button>
                                        <?php endif; ?>
        						                                    
                                    </div>
                                
                                </div>
                            
                            </div>
                        
                        </div>
                    
                    </div>
                
                </div>
                <?php endif;?>
                <!--End Load Framed Content-->
            
            </div>
            <!--End Content-->

            <!--Welcome notification-->
            <div id="welcome"></div>
            
            <div id="members-sidebar" style="background: <?=$sidebar;?>;" class="members-sidebar">
                
                <h4 class="pull-left zero-m">Options</h4>
                
                <span class="close-members-sidebar"><i class="fa fa-remove fa-lg pull-right"></i></span>
                
                <div class="clearfix"><br/></div>
                
                <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
                
                <div class="content-box profile-sidebar box-shadow">
                
                    <img src="https://www.gravatar.com/avatar/<?=$userpic;?>?s=100&d=mm" class="img-responsive img-circle center-block" alt="user" https:="" www.gravatar.com="" avatar="">
                
                    <div class="profile-usertitle">
                
                        <div class="profile-usertitle-name">
                
                            <?php echo strtoupper($USER->username); ?>
                
                        </div>
                
                        <div class="profile-usertitle-job">
                
                            <?php echo strtoupper($USER->role); ?>
                
                        </div>
                
                    </div>
                
                    <div id="buttonsDiv" class="profile-userbuttons">
                
                        <button id="editInfo" type="button" class="btn btn-primary text-uppercase waves waves-effect waves-float">Edit Info</button>
                
                        <button type="button" class="logout btn btn-warning waves waves-effect waves-float">Logout</button>
                
                    </div>
                    
                    <div id="editInfoDiv" style="display: none" class="profile-usertitle">
                         
                        <form class="content-form form-inline" name="update" id="update" action="" method="POST">

                            <input type="hidden" name="op" value="update"/>
                            <input type="hidden" name="sha1" value=""/>
                            <input type="hidden" name="role" value="<?php echo $USER->role; ?>"/>

                            <div class="form-group">

                                <input autocomplete="off" type="text" value="<?php echo $USER->email; ?>" class="form-control" name="email" placeholder="E-mail Address">

                            </div>

                            <div class="form-group">

                                <input autocomplete="off" type="password" class="form-control" name="password1" placeholder="Password">

                            </div>

                            <div class="form-group">

                                <input autocomplete="off" type="password" class="form-control" name="password2" placeholder="Password Again">

                            </div>

                            <br>

                            <div class="form-group">

                                <input type="button" class="btn btn-success text-uppercase waves-effect waves-float" value="Update" onclick="User.processUpdate()"/>
                                
                                <button id="goBackButtons" type="button" class="btn btn-primary text-uppercase waves waves-effect waves-float">Go Back</button>

                            </div>

                        </form>

                    </div>
                    
                </div>

                <?php endif; endif;?>

            </div>

        </div>
        <?php if($configReady == "Yes") : if(!$USER->authenticated && $configReady == "Yes") : ?>
        <div class="login-modal modal fade">
            
            <div style="background:<?=$sidebar;?>;" class="table-wrapper">
                
                <div class="table-row">
                    
                    <div class="table-cell text-center">
                        
                        <button style="color:<?=$topbartext;?>;" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            
                            <span aria-hidden="true">&times;</span>
                        
                        </button>
                        
                        <div class="login i-block">
                            
                            <div class="content-box">
                                
                                <div style="background:<?=$topbar;?>;" class="biggest-box">

                                    <h1 style="color:<?=$topbartext;?>;" class="zero-m text-uppercase">Welcome</h1>

                                </div>
                                
                                <div class="big-box text-left login-form">

                                    <?php if($USER->error!="") : ?>
                                    <p class="error">Error: <?php echo $USER->error; ?></p>
                                    <?php endif; ?>
                                    
                                    <form name="log in" id="login" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                        
                                        <h4 class="text-center">Login</h4>
                                        
                                        <div class="form-group">
                                            
                                            <input type="hidden" name="op" value="login">
				                            <input type="hidden" name="sha1" value="">
                                            <input type="hidden" name="rememberMe" value="false"/>
                                            <input type="text" class="form-control material" name="username" placeholder="Username" autocorrect="off" autocapitalize="off" value="" autofocus required>
                                        
                                        </div>
                                        
                                        <div class="form-group">
                                            
                                            <input type="password" class="form-control material" name="password1" placeholder="Password" required>
                                        
                                        </div>
                                        
                                        <div class="form-group">
                                            
                                            <div class="i-block"> <input id="rememberMe" name="rememberMe" class="switcher switcher-success switcher-medium pull-left" value="true" type="checkbox" checked=""> 
                                                
                                                <label for="rememberMe" class="pull-left"></label>
                                            
                                                <label class="pull-right"> &nbsp; Remember Me</label>
                                            
                                            </div>

                                        </div>

                                        <button id="loginSubmit" style="background:<?=$topbar;?>;" type="submit" class="btn btn-block btn-info text-uppercase waves" value="log in" onclick="User.processLogin()"><text style="color:<?=$topbartext;?>;">Login</text></button>

                                    </form> 
                                    
                                    <button id="switchForgot" class="btn btn-block btn-info text-uppercase waves">Forgot Password</button>
                                    
                                    <form style="display: none;" name="forgotPassword" id="forgotPassword" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                        
                                        <h4 class="text-center">Forgot Password</h4>
                                        
                                        <div class="form-group">
                                            
                                            <input type="hidden" name="op" value="reset">
                                            <input type="text" class="form-control material" name="email" placeholder="E-mail" autocorrect="off" autocapitalize="off" value="" autofocus required>
                                        
                                        </div>

                                        <button style="background:<?=$topbar;?>;" type="submit" class="btn btn-block btn-info text-uppercase waves" value="reset password"><text style="color:<?=$topbartext;?>;">Reset Password</text></button>

                                    </form> 
                                    
                                </div>
                            
                            </div>
                       
                        </div>
                    
                    </div>
                
                </div>
            
            </div>
        
        </div>
        <?php endif; endif;?>
        <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
        <div style="background:<?=$topbar;?>;" class="logout-modal modal fade">
            
            <div class="table-wrapper" style="background: <?=$topbar;?>">
            
                <div class="table-row">
                
                    <div class="table-cell text-center">
                    
                        <div class="login i-block">
                        
                            <div class="content-box">
                            
                                <div style="background:<?=$topbartext;?>;" class="biggest-box">
                                
                                    <form name="log out" id="logout" action="" method="POST">
                                        
				                        <input type="hidden" name="op" value="logout">
                                        
                                        <input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" >
			
                                        <h3 style="color:<?=$topbar;?>;" class="zero-m text-uppercase">Do you want to logout?</h3>
                                        
                                        <a style="color:<?=$topbar;?>;" id="logoutSubmit" class="i-block" data-dismiss="modal">Yes</a>
                                        
                                        <a style="color:<?=$topbar;?>;" class="i-block" data-dismiss="modal">No</a>
                                
                                    </form>
                                    
                                </div>
                            
                            </div>
                    
                        </div>
                
                    </div>
            
                </div>
        
            </div>
    
        </div>
        <?php endif; endif;?>

        <!--Scripts-->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/Waves/dist/waves.min.js"></script>
        <script src="bower_components/moment/min/moment.min.js"></script>
        <script src="bower_components/jquery.nicescroll/jquery.nicescroll.min.js"></script>
        <script src="bower_components/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js"></script>
        <script src="bower_components/cta/dist/cta.min.js"></script>

        <!--Menu-->
        <script src="js/menu/classie.js"></script>
        <script src="js/menu/gnmenu.js"></script>

        <!--Selects-->
        <script src="js/selects/selectFx.js"></script>
        
        <script src="bower_components/sweetalert/dist/sweetalert.min.js"></script>

        <script src="bower_components/smoke/dist/js/smoke.min.js"></script>

        <!--Notification-->
        <script src="js/notifications/notificationFx.js"></script>

        <!--Custom Scripts-->
        <script src="js/common.js"></script>

        <script>
            
        function setHeight() {
            
            windowHeight = $(window).innerHeight();
            
            $("div").find(".iframe").css('height', windowHeight - 56 + "px");
            
            $('#content').css('height', windowHeight - 56 + "px");
            
        };
            
        $('#loginSubmit').click(function() {
            
            if ($('#login').smkValidate()) {
                
                console.log("validated");
                
            }
            
            console.log("didnt validate");
            
        });
            
        $('#registerSubmit').click(function() {
            
            if ($('#registration').smkValidate()) {
                
                console.log("validated");
                
            }
            
            console.log("didnt validate");
            User.processRegistration();
            
        });
            
        $("#editInfo").click(function(){

            $( "div[id^='editInfoDiv']" ).toggle();
            $( "div[id^='buttonsDiv']" ).toggle();
     
        });
            
        $("#goBackButtons").click(function(){

            $( "div[id^='editInfoDiv']" ).toggle();
            $( "div[id^='buttonsDiv']" ).toggle();
     
        });
            
        $("#switchForgot").click(function(){

            $( "form[id^='login']" ).toggle();
            $( "form[id^='forgotPassword']" ).toggle();
            $("#switchForgot").toggle();
     
        });
            
        //Sign in
        $(".log-in").click(function(e){
            
            var e1 = document.querySelector(".log-in"),
            
                e2 = document.querySelector(".login-modal");
            
            cta(e1, e2, {relativeToWindow: true}, function () {
                
                $('.login-modal').modal("show");
            
            });

            e.preventDefault();
        
        });

        //Logout
        $(".logout").click(function(e){
        var el1 = document.querySelector(".logout"),
        el2 = document.querySelector(".logout-modal");
        cta(el1, el2, {relativeToWindow: true}, function () {
        $('.logout-modal').modal("show");
        });

        e.preventDefault();
        });

        //Members Sidebar
        $(".show-members").click(function(e){
        var e_s1 = document.querySelector(".show-members"),
        e_s2 = document.querySelector("#members-sidebar");

        cta(e_s1, e_s2, {relativeToWindow: true}, function () {
        $('#members-sidebar').addClass('members-sidebar-open');
        });

        e.preventDefault();
        });

        $('.close-members-sidebar').click(function(){
        $('#members-sidebar').removeClass('members-sidebar-open');
        });

        $(document).ready(function(){
            
            defaultTab = $("li[class^='tab-item active']").attr("id");
           
            if (defaultTab){
           
                defaultTab = defaultTab.substr(0, defaultTab.length-1);
           
            }else{
           
                defaultTabNone = $("li[class^='tab-item']").attr("id");
                
                if (defaultTabNone){
                
                    $("li[class^='tab-item']").first().attr("class", "tab-item active");
                    defaultTab = defaultTabNone.substr(0, defaultTabNone.length-1);
           
                }
            
            }

            if (defaultTab){
                
                $("#content").html('<div class="iframe active" data-content-url="'+defaultTab+'"><iframe scrolling="auto" sandbox="allow-forms allow-same-origin allow-pointer-lock allow-scripts allow-popups allow-modals" allowfullscreen="true" webkitallowfullscreen="true" frameborder="0" style="width:100%; height:100%;" src="'+defaultTab+'"></iframe></div>');
            }
            
            if (defaultTab == null){
             
                $("div[id^='tabEmpty']").show();
                
            }
            
            setHeight();

        }); 
            
        $(function () {
            <?php if(!empty($USER->info_log)) : ?>
            $.smkAlert({
                position: 'top-left',
                text: '<?php echo printArray($USER->info_log);?>',
                type: 'info'
                
            });
            <?php endif; ?>
            
            <?php if(!empty($USER->error_log)) : ?>
            $.smkAlert({
                position: 'top-left',
                text: '<?php echo printArray($USER->error_log); ?>',
                type: 'warning'
                
            });
            <?php endif; ?>

        });
            
        $('#reload').on('click tap', function(){

            $("i[class^='mdi mdi-refresh']").attr("class", "mdi mdi-refresh fa-spin");

            var activeFrame = $('#content').find('.active').children('iframe');

            activeFrame.attr('src', activeFrame.attr('src'));

            var refreshBox = $('#content').find('.active');

            $("<div class='refresh-preloader'><div class='la-timer la-dark'><div></div></div></div>").appendTo(refreshBox).fadeIn(10);

            setTimeout(function(){

                var refreshPreloader = refreshBox.find('.refresh-preloader'),
                deletedRefreshBox = refreshPreloader.fadeOut(300, function(){

                    refreshPreloader.remove();
                    $("i[class^='mdi mdi-refresh fa-spin']").attr("class", "mdi mdi-refresh");

                });

            },500);

        });
            
        $("li[id^='settings.phpx']").on('click tap', function(){

            $("img[id^='settings-icon']").attr("class", "fa-spin");
            $("i[id^='settings-icon']").attr("class", "fa fa-cog fa-spin");

            setTimeout(function(){

                $("img[id^='settings-icon']").attr("class", "");
                $("i[id^='settings-icon']").attr("class", "fa fa-cog");

            },1000);

        });

        $('#logoutSubmit').on('click tap', function(){

            $( "#logout" ).submit();

        });
            
        $(window).resize(function(){
            
            setHeight();

        });
            
        $("li[class^='tab-item']").on('click vclick', function(){
                
            var thisidfull = $(this).attr("id");

            var thisid = thisidfull.substr(0, thisidfull.length-1);

            var currentframe = $("div[data-content-url^='"+thisid+"']");

            if (currentframe.attr("class") == "iframe active") {

                console.log(thisid + " is active already");

            }else if (currentframe.attr("class") == "iframe hidden") {

                console.log(thisid + " is active already but hidden");

                $("div[class^='iframe active']").attr("class", "iframe hidden");

                currentframe.attr("class", "iframe active");
                
                setHeight();

                $("li[class^='tab-item active']").attr("class", "tab-item");

                $(this).attr("class", "tab-item active");

            }else {

                
                
                if ($(this).attr("window") == "true") {
                    
                    window.open(thisid,'_blank');
                    
                }else {
                
                    console.log(thisid + " make new div");

                    $("div[class^='iframe active']").attr("class", "iframe hidden");

                    $( '<div class="iframe active" data-content-url="'+thisid+'"><iframe scrolling="auto" sandbox="allow-forms allow-same-origin allow-pointer-lock allow-scripts allow-popups allow-modals" allowfullscreen="true" webkitallowfullscreen="true" frameborder="0" style="width:100%; height:100%;" src="'+thisid+'"></iframe></div>' ).appendTo( "#content" );

                    setHeight();

                    $("li[class^='tab-item active']").attr("class", "tab-item");

                    $(this).attr("class", "tab-item active");
                    
                }

            }

        });
        </script>


    </body>

</html>