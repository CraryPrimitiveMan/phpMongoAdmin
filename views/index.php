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
                    <!-- search form -->
                    <!-- <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form> -->
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <?php foreach ($databases as $db) {?>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span><?php echo $db['name'];?></span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <?php foreach ($db['collections'] as $collection) {?>
                                <li>
                                    <a href="<?php echo 'index.php?action=collection&db=' . $db['name'] . '&collection=' . $collection?>">
                                        <i class="fa fa-angle-double-right"></i><?php echo $collection;?>
                                    </a>
                                </li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php echo $dbName;?>
                        <small><?php echo $collectionName;?></small>
                        <?php if (!empty($collectionName)):?>
                        <a class="add-btn btn btn-primary pull-right" data-toggle="modal" data-target="#editModal">Add New Document</a>
                        <?php else:?>
                        Total Size: <?php echo $totalSize/1024.0/1024.0;?>MB
                        <?php endif;?>
                    </h1>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div>
                        <?php if (!empty($collectionName)) {?>
                        <div id="show" class="box-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <?php foreach ($keys as $key) {?>
                                    <th><?php echo $key;?></th>
                                    <?php }?>
                                    <th><div>Options</div></th>
                                </tr>
                                <?php foreach ($documents as $document) {?>
                                <tr>
                                    <?php foreach ($keys as $key) {?>
                                    <td>
                                    <?php
                                        if (isset($document[$key])) {
                                            if (is_bool($document[$key])) {
                                                echo $document[$key] ? 'true' : 'false';
                                            } else if ($document[$key] instanceof MongoDate) {
                                                echo date('Y-m-d H:i:s', $document[$key]->sec);
                                            } else {
                                                echo $document[$key];
                                            }
                                        }
                                    ?>
                                    </td>
                                    <?php }?>
                                    <td>
                                        <a class="edit-btn btn btn-primary" data-id="<?php echo $document['_id'];?>" data-toggle="modal" data-target="#editModal">Edit</a>
                                        <a class="btn btn-danger" href="<?php echo 'index.php?action=delete&db=' . $dbName . '&collection=' . $collectionName . '&id=' . $document['_id'];?>">Delete</a>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody></table>
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
                        <?php } else { ?>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th>Name</th>
                                    <th>Size On Disk(MB)</th>
                                    <th>Empty</th>
                                    <th><div>Options</div></th>
                                </tr>
                                <?php foreach ($databases as $db) {?>
                                <tr>
                                    <td><?php echo $db['name'];?></td>
                                    <td><?php echo $db['sizeOnDisk']/1024.0/1024.0;?></td>
                                    <td><?php echo $db['empty'] ? 'true' : 'false';?></td>
                                    <td>
                                        <a class="btn btn-danger" href="<?php echo 'index.php?action=dropDb&db=' . $db['name'];?>">Delete</a>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody></table>
                        </div>
                        <?php }?>
                    </div><!-- /.row (main row) -->

                </section><!-- /.content -->

            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form class="modal-content" method='post' action='<?php echo 'index.php?action=update&db=' . $dbName . '&collection=' . $collectionName;?>'>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="editModalLabel">Content</h4>
              </div>
              <div class="modal-body">
                <textarea id="content" name="content"></textarea>
              </div>
              <div class="modal-footer">
                <button id="save" type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>
        <!-- add new calendar event modal -->
        <script src="views/js/plugins/jquery/jquery.min.js"></script>
        <script src="views/js/plugins/bootstrap/bootstrap.min.js" type="text/javascript"></script>
        <!-- AdminLTE App -->
        <script src="views/js/AdminLTE/formate.js" type="text/javascript"></script>
        <script src="views/js/AdminLTE/app.js" type="text/javascript"></script>
        <script src="views/js/AdminLTE/main.js" type="text/javascript"></script>

    </body>
</html>
