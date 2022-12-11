<head>
    <link rel="stylesheet" href="../css/details.css">
    <link rel="stylesheet" href="../../common/css/sidebar.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="sidebar" style="width:10%; float: left">
        <div class="sidebarButton"><a href="../../main/main.php">View</a></div>
        <div class="sidebarButton"><a href="../../insert/insert.php">Insert</a></div>
        <div class="sidebarButtonCurrent"><a href="#">Details</a></div>
    </div>

    <div style="float: left; width: 90%" id="contents">
        <div id="deletionMessage">
            <?php
            echo $_REQUEST['message'];
            ?>
            <div id="deletionMessageTip">
                Use the sidebar to return.
            </div>
        </div>
    </div>
</body>