<head>
    <link rel="stylesheet" href="../css/details.css">
    <link rel="stylesheet" href="../../common/css/sidebar.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>PHPYA Delete</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="sidebar" style="width:10%; float: left">
        <div class="sidebarButton"><a href="../../main/main.php">View</a></div>
        <div class="sidebarButton"><a href="../../insert/insert.php">Insert</a></div>
        <div class="sidebarButtonCurrent"><a href="#">Details</a></div>
    </div>

    <div style="float: left; width: 90%; background-color: #272a2e; height: 100%;" id="contents">
        <div class="d-flex align-items-center justify-content-center">
            <div id="deletionMessage" class="alert alert-info" role="alert" style="width: fit-content">
                <?php
                echo $_REQUEST['message'];
                ?>
                <div id="deletionMessageTip">
                    Use the sidebar to <a href="../../main/main.php">return</a>.
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>