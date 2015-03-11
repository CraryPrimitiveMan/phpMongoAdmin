<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>PHP Mongo Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="views/css/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="views/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="index.php" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                PHP Mongo Admin
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <div class="dropdown">
                        <button class="btn btn-defalut dropdown-toggle" type="button" id="serverSelected" data-toggle="dropdown" aria-expanded="true">
                            <?php echo $serverName;?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="serverSelected">
                            <?php
                            foreach ($servers as $server) :
                            ?>
                            <li role="presentation">
                                <a role="menuitem" tabindex="-1" href="<?php echo 'index.php?server=' . $server['name'];?>"><?php echo $server['name'];?></a>
                            </li>
                            <?php
                            endforeach;
                            ?>
                        </ul>
                    </div>
                    <!-- search form -->
                    <!-- <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='search' id='search-btn' class="btn btn-flat">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form> -->
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <?php
                        foreach ($databases as $db) :
                        ?>
                        <li class="treeview" class="<?php echo $db['name'] === $dbName ? 'active' : '';?>">
                            <a href="#">
                                <span><?php echo $db['name'];?></span>
                            </a>
                            <ul class="treeview-menu" style="<?php echo $db['name'] === $dbName ? 'display:block' : '';?>">
                                <?php
                                foreach ($db['collections'] as $collection) :
                                ?>
                                <li class="<?php echo $collection === $collectionName ? 'active': ''; ?>">
                                    <a href="<?php echo 'index.php?action=collection&db=' . $db['name'] . '&collection=' . $collection . '&server=' . $serverName;?>">
                                        <?php echo $collection;?>
                                    </a>
                                </li>
                                <?php
                                endforeach;
                                ?>
                            </ul>
                        </li>
                        <?php
                        endforeach;
                        ?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header clearfix">
                    <h1>
                        <?php echo $dbName;?>
                        <small><?php echo $collectionName;?> </small>
                        <?php
                        if (!empty($collectionName)) :
                        ?>
                        <a class="add-btn btn btn-primary pull-right">Add New Document</a>
                        <?php
                        else :
                        ?>
                        All Databases
                        <?php
                        endif;
                        ?>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div>
                        <div>
                            <div class="shell">
                                <textarea id="shell" name="shell" content="<?php echo $shell; ?>"></textarea>
<!--                                <div class="btns">-->
<!--                                    <a class="execute-btn btn btn-success">Execute</a>-->
<!--                                </div>-->
                            </div>
                        </div>
                        <div>
                            <div class="add-document document" style="display:none;">
                                <div class="btns">
                                    <a class="insert-btn btn btn-success">Save</a>
                                    <a class="cancel-btn btn btn-info">Cancel</a>
                                </div>
                                <textarea id="content" class="show" name="content"></textarea>
                            </div>
                        </div>
                        <?php
                        if (!empty($collectionName)) :
                        ?>
                        <div id="show" class="box-body">
                            <?php
                             foreach ($documents as $document) :
                            ?>
                            <div class="document">
                                <div class="btns">
                                    <a class="edit-btn btn btn-primary" data-id="<?php echo $document['id'];?>">Edit</a>
                                    <a class="save-btn btn btn-success" data-id="<?php echo $document['id'];?>" style="display: none;">Save</a>
                                    <a class="delete-btn btn btn-danger" href="<?php echo 'index.php?action=delete&db=' . $dbName . '&collection=' . $collectionName . '&id=' . $document['id'] . '&server=' . $serverName;?>">Delete</a>
                                </div>
                                <textarea id="<?php echo $document['id'];?>" class="show" name="content" content='<?php echo $document['content'];?>' readonly="readonly" onpropertychange="this.style.posHeight=this.scrollHeight"></textarea>
                            </div>
                            <?php
                            endforeach;
                            ?>
                        </div><!-- /.box-body -->
                        <!-- <div class="box-footer clearfix">
                            <ul class="pagination pagination-sm no-margin pull-right">
                                <li><a href="#">«</a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">»</a></li>
                            </ul>
                        </div> -->
                        <?php
                        else :
                        ?>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th>Database Name</th>
                                    <th><div>Options</div></th>
                                </tr>
                                <?php
                                foreach ($databases as $db) :
                                ?>
                                <tr>
                                    <td><?php echo $db['name'];?></td>
                                    <td>
                                        <a class="btn btn-danger" href="<?php echo 'index.php?action=dropDb&db=' . $db['name'] . '&server=' . $serverName;?>">Delete</a>
                                    </td>
                                </tr>
                                <?php
                                endforeach;
                                ?>
                            </tbody></table>
                        </div>
                        <?php
                        endif;
                        ?>
                    </div><!-- /.row (main row) -->

                </section><!-- /.content -->

            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
        <!-- add new calendar event modal -->
        <script src="views/js/plugins/jquery/jquery.min.js"></script>
        <script src="views/js/plugins/bootstrap/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="views/js/AdminLTE/formate.js" type="text/javascript"></script>
        <script src="views/js/AdminLTE/app.js" type="text/javascript"></script>
        <script src="views/js/AdminLTE/main.js" type="text/javascript"></script>

    </body>
</html>
