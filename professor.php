<?php
    include 'auth.php';
 ?>   

<?php $pageTitle="For Prof Page"; include 'header.php';  ?>    
<body class="login-body">
    <div class="container"  style="width: 1024px;" >
        <h2>For Professor</h2>

        <div class="embed-container">
            <h3>Code Explanation Video</h3>
            <iframe 
                width="560" 
                height="315" 
                src="https://www.youtube.com/embed/YOUR_VIDEO_ID" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>

        <div class="embed-container">
            <h3>SQL Backup File</h3>
            <iframe src="sqlbackup.txt"></iframe>
        </div>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
<?php include 'footer.php'; ?>
