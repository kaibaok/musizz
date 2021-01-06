 <?php 

    require("vendor/autoload.php");
    require("mp3zing.php");

    use GuzzleHttp\Client;

    $listSong = null;

    if(isset($_POST['song'])) {
        $zing = new Zing();
        $msg = $_POST['song'];
        $listSong = $zing->searchSong($msg);
    }

    echo json_encode($listSong);
    die;
    ?>